<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TicketSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_client_cannot_create_a_ticket_for_another_clients_website(): void
    {
        $clientUser = User::where('email', 'cliente1@webops.test')->firstOrFail();
        $otherWebsite = Website::where('client_id', '!=', $clientUser->client_id)->firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();
        $ticketCount = Ticket::count();

        $this->actingAs($clientUser)
            ->post(route('tickets.store'), [
                'client_id' => $otherWebsite->client_id,
                'website_id' => $otherWebsite->id,
                'assigned_to' => $technician->id,
                'title' => 'Solicitud manipulada',
                'description' => 'Intento de asociar una web que pertenece a otra empresa.',
                'priority' => 'high',
                'status' => 'resolved',
                'due_date' => now()->addDays(3)->toDateString(),
            ])
            ->assertSessionHasErrors('website_id');

        $this->assertDatabaseCount('tickets', $ticketCount);
        $this->assertDatabaseMissing('tickets', ['title' => 'Solicitud manipulada']);
    }

    public function test_client_ticket_ignores_spoofed_ownership_assignment_and_status(): void
    {
        $clientUser = User::where('email', 'cliente1@webops.test')->firstOrFail();
        $ownWebsite = Website::where('client_id', $clientUser->client_id)->firstOrFail();
        $otherWebsite = Website::where('client_id', '!=', $clientUser->client_id)->firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();

        $this->actingAs($clientUser)
            ->post(route('tickets.store'), [
                'client_id' => $otherWebsite->client_id,
                'website_id' => $ownWebsite->id,
                'assigned_to' => $technician->id,
                'title' => 'Solicitud válida del cliente',
                'description' => 'El servidor debe imponer la empresa, la asignación y el estado inicial.',
                'priority' => 'medium',
                'status' => 'closed',
                'due_date' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Solicitud válida del cliente',
            'client_id' => $clientUser->client_id,
            'website_id' => $ownWebsite->id,
            'assigned_to' => null,
            'status' => 'open',
            'resolved_at' => null,
        ]);
    }

    public function test_admin_can_create_a_ticket_with_a_matching_website_and_technician(): void
    {
        $admin = User::where('email', 'admin@webops.test')->firstOrFail();
        $website = Website::firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('tickets.store'), [
                'client_id' => $website->client_id,
                'website_id' => $website->id,
                'assigned_to' => $technician->id,
                'title' => 'Ticket asignado por administración',
                'description' => 'Solicitud válida con una web y un técnico relacionados correctamente.',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'due_date' => now()->addDay()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Ticket asignado por administración',
            'client_id' => $website->client_id,
            'website_id' => $website->id,
            'assigned_to' => $technician->id,
            'priority' => 'urgent',
            'status' => 'in_progress',
        ]);
    }

    public function test_client_ticket_form_only_receives_its_company_websites_and_no_technicians(): void
    {
        $clientUser = User::where('email', 'cliente1@webops.test')->firstOrFail();

        $this->actingAs($clientUser)
            ->get(route('tickets.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tickets/Form')
                ->has('clients', 1)
                ->where('clients.0.id', $clientUser->client_id)
                ->where('websites', fn ($websites) => collect($websites)->every(
                    fn ($website) => $website['client_id'] === $clientUser->client_id
                ))
                ->where('technicians', []));
    }
}
