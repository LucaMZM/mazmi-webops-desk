<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MaintenanceTask;
use App\Models\Ticket;
use App\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $clientId = $user->isClient() ? $user->client_id : null;
        $technicianId = $user->isTechnician() ? $user->id : null;

        $clients = Client::query()->when($clientId, fn (Builder $q) => $q->whereKey($clientId));
        $websites = Website::query()->when($clientId, fn (Builder $q) => $q->where('client_id', $clientId));
        $tickets = Ticket::query()
            ->when($clientId, fn (Builder $q) => $q->where('client_id', $clientId))
            ->when($technicianId, fn (Builder $q) => $q->where('assigned_to', $technicianId));
        $tasks = MaintenanceTask::query()
            ->when($clientId, fn (Builder $q) => $q->whereHas('website', fn (Builder $w) => $w->where('client_id', $clientId)))
            ->when($technicianId, fn (Builder $q) => $q->where('assigned_to', $technicianId));

        $metrics = [
            'active_clients' => (clone $clients)->where('status', 'active')->count(),
            'websites' => (clone $websites)->count(),
            'open_tickets' => (clone $tickets)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'urgent_tickets' => (clone $tickets)->where('priority', 'urgent')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'pending_tasks' => (clone $tasks)->whereIn('status', ['pending', 'in_progress', 'blocked'])->count(),
            'overdue_tasks' => (clone $tasks)->whereIn('status', ['pending', 'in_progress', 'blocked'])->where('scheduled_at', '<', now())->count(),
            'expiring_domains' => (clone $websites)->whereBetween('domain_expires_at', [today(), today()->addDays(30)])->count(),
            'expiring_hosting' => (clone $websites)->whereBetween('hosting_expires_at', [today(), today()->addDays(30)])->count(),
        ];

        return Inertia::render('Dashboard/Index', [
            'metrics' => $metrics,
            'latestTickets' => (clone $tickets)->with(['client:id,company_name', 'website:id,name', 'assignee:id,name'])->latest()->limit(6)->get(),
            'upcomingTasks' => (clone $tasks)->with(['website:id,name,client_id', 'website.client:id,company_name', 'assignee:id,name'])->whereIn('status', ['pending', 'in_progress'])->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(6)->get(),
            'ticketsByStatus' => (clone $tickets)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'websitesByTechnology' => (clone $websites)->selectRaw('technology, count(*) as total')->groupBy('technology')->orderByDesc('total')->get(),
        ]);
    }
}
