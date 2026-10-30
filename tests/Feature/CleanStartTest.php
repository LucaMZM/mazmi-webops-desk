<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MaintenanceTask;
use App\Models\MonthlyReport;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CleanStartTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seed_produces_no_demo_records_or_credentials(): void
    {
        $this->seed();

        $this->assertSame(0, User::count());
        $this->assertSame(0, Client::count());
        $this->assertSame(0, Website::count());
        $this->assertSame(0, Ticket::count());
        $this->assertSame(0, MaintenanceTask::count());
        $this->assertSame(0, MonthlyReport::count());
    }

    public function test_demo_data_requires_explicit_opt_in(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(6, User::count());
        $this->assertSame(8, Client::count());
        $this->assertSame(12, Website::count());
    }

    public function test_clean_dashboard_can_load_without_business_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->where('metrics.websites', 0)
                ->where('metrics.open_tickets', 0)
                ->has('latestTickets', 0)
                ->has('upcomingTasks', 0));

        $this->actingAs($admin)->get(route('clients.index'))->assertOk();
        $this->actingAs($admin)->get(route('websites.index'))->assertOk();
        $this->actingAs($admin)->get(route('tickets.index'))->assertOk();
        $this->actingAs($admin)->get(route('maintenance.index'))->assertOk();
        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
    }

    public function test_initial_admin_command_refuses_when_account_already_exists(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->artisan('webops:admin')->assertExitCode(1);
    }
}
