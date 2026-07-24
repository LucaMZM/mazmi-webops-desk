<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);
        $tickets = Ticket::query()->with(['client:id,company_name', 'website:id,name', 'assignee:id,name'])
            ->when($request->user()->isClient(), fn (Builder $q) => $q->where('client_id', $request->user()->client_id))
            ->when($request->user()->isTechnician(), fn (Builder $q) => $q->where('assigned_to', $request->user()->id))
            ->when($request->search, fn (Builder $q, $value) => $q->where(fn (Builder $i) => $i->where('title', 'like', "%{$value}%")->orWhere('description', 'like', "%{$value}%")))
            ->when($request->status, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($request->priority, fn (Builder $q, $value) => $q->where('priority', $value))
            ->when($request->client_id, fn (Builder $q, $value) => $q->where('client_id', $value))
            ->when($request->assigned_to, fn (Builder $q, $value) => $q->where('assigned_to', $value))
            ->latest()->paginate(12)->withQueryString();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only('search', 'status', 'priority', 'client_id', 'assigned_to'),
            'clients' => $request->user()->isClient() ? [] : Client::orderBy('company_name')->get(['id', 'company_name']),
            'technicians' => $request->user()->isClient() ? [] : User::where('role', 'technician')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Ticket::class);

        return Inertia::render('Tickets/Form', $this->formData($request));
    }

    public function store(TicketRequest $request)
    {
        $this->authorize('create', Ticket::class);
        $data = $request->validated();
        if ($request->user()->isClient()) {
            $data['client_id'] = $request->user()->client_id;
            $data['assigned_to'] = null;
            $data['status'] = 'open';
        }
        $data['resolved_at'] = in_array($data['status'], ['resolved', 'closed'], true) ? now() : null;
        $ticket = Ticket::create($data);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket creado correctamente.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return Inertia::render('Tickets/Show', ['ticket' => $ticket->load(['client', 'website', 'assignee:id,name,email'])]);
    }

    public function edit(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        return Inertia::render('Tickets/Form', [...$this->formData($request), 'ticket' => $ticket]);
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $data = $request->validated();
        if ($request->user()->isTechnician()) {
            $data['client_id'] = $ticket->client_id;
            $data['website_id'] = $ticket->website_id;
            $data['assigned_to'] = $ticket->assigned_to;
        }
        $data['resolved_at'] = in_array($data['status'], ['resolved', 'closed'], true) ? ($ticket->resolved_at ?? now()) : null;
        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket actualizado correctamente.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $data = $request->validate(['status' => ['required', Rule::in(['open', 'in_progress', 'waiting_client', 'resolved', 'closed'])]]);
        $ticket->update(['status' => $data['status'], 'resolved_at' => in_array($data['status'], ['resolved', 'closed'], true) ? ($ticket->resolved_at ?? now()) : null]);

        return back()->with('success', 'Estado del ticket actualizado.');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado.');
    }

    private function formData(Request $request): array
    {
        $clientId = $request->user()->isClient() ? $request->user()->client_id : null;

        return [
            'clients' => Client::query()->when($clientId, fn (Builder $q) => $q->whereKey($clientId))->orderBy('company_name')->get(['id', 'company_name']),
            'websites' => Website::query()->when($clientId, fn (Builder $q) => $q->where('client_id', $clientId))->orderBy('name')->get(['id', 'client_id', 'name']),
            'technicians' => User::where('role', 'technician')->orderBy('name')->get(['id', 'name']),
        ];
    }
}
