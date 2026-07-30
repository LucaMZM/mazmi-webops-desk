<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MaintenanceTask;
use App\Models\MonthlyReport;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_open_operational_dashboard(): void
    {
        $this->actingAs(User::where('role', 'admin')->first())
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->where('metrics.websites', 12)
                ->has('latestTickets', 6));
    }

    public function test_client_only_receives_its_own_company_in_client_list(): void
    {
        $user = User::where('email', 'cliente1@webops.test')->firstOrFail();

        $this->actingAs($user)->get('/clients')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('clients.data', 1)
                ->where('clients.data.0.id', $user->client_id));
    }

    public function test_client_cannot_open_another_company(): void
    {
        $user = User::where('email', 'cliente1@webops.test')->firstOrFail();
        $otherClient = Client::whereKeyNot($user->client_id)->firstOrFail();

        $this->actingAs($user)->get(route('clients.show', $otherClient))->assertForbidden();
    }

    public function test_client_cannot_open_resources_from_another_company(): void
    {
        $user = User::where('email', 'cliente1@webops.test')->firstOrFail();
        $otherWebsite = Website::where('client_id', '!=', $user->client_id)->firstOrFail();
        $otherTicket = Ticket::where('client_id', '!=', $user->client_id)->firstOrFail();
        $otherReport = MonthlyReport::where('client_id', '!=', $user->client_id)->firstOrFail();
        $otherTask = MaintenanceTask::whereHas('website', fn ($query) => $query->where('client_id', '!=', $user->client_id))->firstOrFail();

        $this->actingAs($user)->get(route('websites.show', $otherWebsite))->assertForbidden();
        $this->get(route('tickets.show', $otherTicket))->assertForbidden();
        $this->get(route('reports.show', $otherReport))->assertForbidden();
        $this->get(route('maintenance.show', $otherTask))->assertForbidden();
    }

    public function test_client_ticket_list_only_contains_its_company(): void
    {
        $user = User::where('email', 'cliente1@webops.test')->firstOrFail();

        $this->actingAs($user)->get(route('tickets.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tickets.data', fn ($tickets) => collect($tickets)->isNotEmpty()
                    && collect($tickets)->every(fn ($ticket) => $ticket['client_id'] === $user->client_id)));
    }

    public function test_technician_operational_views_only_contain_assigned_work(): void
    {
        $technician = User::where('email', 'tech1@webops.test')->firstOrFail();

        $this->actingAs($technician)->get(route('tickets.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tickets.data', fn ($tickets) => collect($tickets)->isNotEmpty()
                    && collect($tickets)->every(fn ($ticket) => $ticket['assigned_to'] === $technician->id)));

        $this->get(route('maintenance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tasks.data', fn ($tasks) => collect($tasks)->isNotEmpty()
                    && collect($tasks)->every(fn ($task) => $task['assigned_to'] === $technician->id)));
    }

    public function test_technician_cannot_update_a_ticket_assigned_to_another_technician(): void
    {
        $technician = User::where('email', 'tech1@webops.test')->firstOrFail();
        $ticket = Ticket::whereNot('assigned_to', $technician->id)->whereNotNull('assigned_to')->firstOrFail();

        $this->actingAs($technician)
            ->patch(route('tickets.status', $ticket), ['status' => 'resolved'])
            ->assertForbidden();
    }

    public function test_technician_cannot_create_tickets_for_clients(): void
    {
        $technician = User::where('email', 'tech1@webops.test')->firstOrFail();
        $website = Website::firstOrFail();

        $this->actingAs($technician)
            ->get(route('tickets.create'))
            ->assertForbidden();

        $this->post(route('tickets.store'), [
            'client_id' => $website->client_id,
            'website_id' => $website->id,
            'assigned_to' => $technician->id,
            'title' => 'Ticket no autorizado',
            'description' => 'Un técnico no debe crear tickets en nombre de un cliente.',
            'priority' => 'medium',
            'status' => 'open',
            'due_date' => null,
        ])->assertForbidden();

        $this->assertDatabaseMissing('tickets', ['title' => 'Ticket no autorizado']);
    }

    public function test_client_ticket_cannot_self_assign_or_start_resolved(): void
    {
        $user = User::where('email', 'cliente1@webops.test')->firstOrFail();
        $website = $user->client->websites()->firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();

        $this->actingAs($user)->post(route('tickets.store'), [
            'client_id' => $user->client_id,
            'website_id' => $website->id,
            'assigned_to' => $technician->id,
            'title' => 'Solicitud creada desde el portal',
            'description' => 'Descripción suficiente para reproducir el problema comunicado.',
            'priority' => 'high',
            'status' => 'resolved',
            'due_date' => now()->addDays(3)->toDateString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Solicitud creada desde el portal',
            'client_id' => $user->client_id,
            'assigned_to' => null,
            'status' => 'open',
        ]);
    }
}
