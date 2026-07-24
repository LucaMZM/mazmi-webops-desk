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

class CrudWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@webops.test')->firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_admin_can_create_update_and_delete_an_unrelated_client(): void
    {
        $this->post(route('clients.store'), [
            'company_name' => 'Órbita Digital Studio',
            'contact_name' => 'Laura Serra',
            'email' => 'laura@orbita-digital.test',
            'phone' => '+34 960 000 001',
            'city' => 'Valencia',
            'status' => 'active',
            'notes' => 'Cliente creado para validar el flujo CRUD.',
        ])->assertRedirect();

        $client = Client::where('company_name', 'Órbita Digital Studio')->firstOrFail();

        $this->put(route('clients.update', $client), [
            'company_name' => 'Órbita Digital Lab',
            'contact_name' => 'Laura Serra',
            'email' => 'laura@orbita-digital.test',
            'phone' => '+34 960 000 001',
            'city' => 'Valencia',
            'status' => 'inactive',
            'notes' => 'Ficha actualizada durante el test.',
        ])->assertRedirect(route('clients.show', $client));

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'company_name' => 'Órbita Digital Lab', 'status' => 'inactive']);
        $this->delete(route('clients.destroy', $client))->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_admin_can_create_update_and_delete_a_website(): void
    {
        $client = Client::create([
            'company_name' => 'Prisma Demo', 'contact_name' => 'Noa Vidal', 'email' => 'noa@prisma-demo.test',
            'phone' => null, 'city' => 'Valencia', 'status' => 'active', 'notes' => null,
        ]);

        $payload = [
            'client_id' => $client->id, 'name' => 'Prisma Corporate', 'url' => 'https://prisma-demo.test',
            'technology' => 'Laravel', 'hosting_provider' => 'Demo Cloud',
            'domain_expires_at' => now()->addMonths(6)->toDateString(),
            'hosting_expires_at' => now()->addMonths(5)->toDateString(),
            'ssl_status' => 'active', 'maintenance_plan' => 'standard', 'status' => 'stable',
            'notes' => 'Sin credenciales reales.',
        ];

        $this->post(route('websites.store'), $payload)->assertRedirect();
        $website = Website::where('name', 'Prisma Corporate')->firstOrFail();
        $this->put(route('websites.update', $website), [...$payload, 'status' => 'review'])->assertRedirect(route('websites.show', $website));
        $this->assertDatabaseHas('websites', ['id' => $website->id, 'status' => 'review']);
        $this->delete(route('websites.destroy', $website))->assertRedirect(route('websites.index'));
        $this->assertDatabaseMissing('websites', ['id' => $website->id]);
    }

    public function test_admin_can_manage_ticket_status_and_delete_it(): void
    {
        $website = Website::firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();

        $this->post(route('tickets.store'), [
            'client_id' => $website->client_id, 'website_id' => $website->id, 'assigned_to' => $technician->id,
            'title' => 'Ticket funcional de auditoría', 'description' => 'Descripción suficientemente detallada para validar el flujo completo.',
            'priority' => 'high', 'status' => 'open', 'due_date' => now()->addWeek()->toDateString(),
        ])->assertRedirect();

        $ticket = Ticket::where('title', 'Ticket funcional de auditoría')->firstOrFail();
        $this->patch(route('tickets.status', $ticket), ['status' => 'resolved'])->assertSessionHas('success');
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'resolved']);
        $this->assertNotNull($ticket->fresh()->resolved_at);
        $this->delete(route('tickets.destroy', $ticket))->assertRedirect(route('tickets.index'));
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_admin_can_manage_and_complete_a_maintenance_task(): void
    {
        $website = Website::firstOrFail();
        $technician = User::where('role', 'technician')->firstOrFail();

        $this->post(route('maintenance.store'), [
            'website_id' => $website->id, 'assigned_to' => $technician->id,
            'title' => 'Revisión funcional programada', 'description' => 'Checklist creado por la auditoría.',
            'category' => 'security', 'priority' => 'medium', 'status' => 'pending',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ])->assertRedirect();

        $task = MaintenanceTask::where('title', 'Revisión funcional programada')->firstOrFail();
        $this->patch(route('maintenance.complete', $task))->assertSessionHas('success');
        $this->assertNotNull($task->fresh()->completed_at);
        $this->delete(route('maintenance.destroy', $task))->assertRedirect(route('maintenance.index'));
        $this->assertDatabaseMissing('maintenance_tasks', ['id' => $task->id]);
    }

    public function test_admin_can_create_view_and_delete_a_monthly_report(): void
    {
        $client = Client::findOrFail(8);

        $this->post(route('reports.store'), [
            'client_id' => $client->id, 'month' => now()->month, 'year' => now()->year,
            'summary' => 'Resumen mensual creado durante la auditoría funcional.',
            'completed_tasks_count' => 4, 'resolved_tickets_count' => 3, 'pending_tickets_count' => 1,
            'recommendations' => 'Mantener el calendario preventivo y revisar rendimiento.', 'general_status' => 'good',
        ])->assertRedirect();

        $report = MonthlyReport::where('client_id', $client->id)->where('month', now()->month)->firstOrFail();
        $this->get(route('reports.show', $report))->assertOk()->assertInertia(fn (Assert $page) => $page->component('Reports/Show'));
        $this->delete(route('reports.destroy', $report))->assertRedirect(route('reports.index'));
        $this->assertDatabaseMissing('monthly_reports', ['id' => $report->id]);
    }

    public function test_filters_and_validation_return_expected_results(): void
    {
        $this->get(route('clients.index', ['search' => 'Clínica']))
            ->assertInertia(fn (Assert $page) => $page->has('clients.data', 1)->where('filters.search', 'Clínica'));

        $this->get(route('websites.index', ['technology' => 'PrestaShop']))
            ->assertInertia(fn (Assert $page) => $page->where('websites.data', fn ($items) => collect($items)->count() === 2
                && collect($items)->every(fn ($website) => $website['technology'] === 'PrestaShop')));

        $this->get(route('reports.index', ['general_status' => 'critical']))
            ->assertInertia(fn (Assert $page) => $page->has('reports.data', 1)->where('filters.general_status', 'critical'));

        $this->post(route('clients.store'), [])->assertSessionHasErrors([
            'company_name' => 'El campo empresa es obligatorio.',
            'contact_name',
            'email',
            'status',
        ]);
        $this->post(route('websites.store'), ['url' => 'url-no-valida'])->assertSessionHasErrors(['client_id', 'name', 'url', 'technology']);
    }
}
