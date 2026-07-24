<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
            ->when($request->user()->isClient(), fn (Builder $q) => $q->whereKey($request->user()->client_id))
            ->when($request->search, fn (Builder $q, $search) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($request->status, fn (Builder $q, $status) => $q->where('status', $status))
            ->withCount(['websites', 'tickets'])
            ->orderBy('company_name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Clients/Index', ['clients' => $clients, 'filters' => $request->only('search', 'status')]);
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return Inertia::render('Clients/Form');
    }

    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);
        $client = Client::create($request->validated());

        return redirect()->route('clients.show', $client)->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        $client->load([
            'websites' => fn ($q) => $q->latest()->limit(8),
            'tickets' => fn ($q) => $q->with('assignee:id,name')->latest()->limit(8),
            'reports' => fn ($q) => $q->latest('year')->latest('month')->limit(6),
        ])->loadCount(['websites', 'tickets', 'reports']);

        return Inertia::render('Clients/Show', ['client' => $client]);
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        return Inertia::render('Clients/Form', ['client' => $client]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);
        $client->update($request->validated());

        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        if ($client->websites()->exists() || $client->tickets()->exists() || $client->reports()->exists() || $client->users()->exists()) {
            return back()->with('error', 'No se puede eliminar: el cliente tiene datos asociados. Puedes marcarlo como inactivo.');
        }
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado.');
    }
}
