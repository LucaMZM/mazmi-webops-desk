<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebsiteRequest;
use App\Models\Client;
use App\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Website::class);
        $websites = Website::query()->with('client:id,company_name')
            ->when($request->user()->isClient(), fn (Builder $q) => $q->where('client_id', $request->user()->client_id))
            ->when($request->search, fn (Builder $q, $search) => $q->where(fn (Builder $i) => $i->where('name', 'like', "%{$search}%")->orWhere('url', 'like', "%{$search}%")))
            ->when($request->technology, fn (Builder $q, $value) => $q->where('technology', $value))
            ->when($request->status, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($request->maintenance_plan, fn (Builder $q, $value) => $q->where('maintenance_plan', $value))
            ->when($request->expiring, fn (Builder $q) => $q->where(fn (Builder $i) => $i->whereBetween('domain_expires_at', [today(), today()->addDays(30)])->orWhereBetween('hosting_expires_at', [today(), today()->addDays(30)])))
            ->withCount(['tickets', 'maintenanceTasks'])->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Websites/Index', ['websites' => $websites, 'filters' => $request->only('search', 'technology', 'status', 'maintenance_plan', 'expiring')]);
    }

    public function create()
    {
        $this->authorize('create', Website::class);

        return Inertia::render('Websites/Form', ['clients' => Client::where('status', 'active')->orderBy('company_name')->get(['id', 'company_name'])]);
    }

    public function store(WebsiteRequest $request)
    {
        $this->authorize('create', Website::class);
        $website = Website::create($request->validated());

        return redirect()->route('websites.show', $website)->with('success', 'Web añadida correctamente.');
    }

    public function show(Website $website)
    {
        $this->authorize('view', $website);
        $website->load(['client', 'tickets' => fn ($q) => $q->with('assignee:id,name')->latest()->limit(10), 'maintenanceTasks' => fn ($q) => $q->with('assignee:id,name')->orderBy('scheduled_at')->limit(10)]);

        return Inertia::render('Websites/Show', ['website' => $website]);
    }

    public function edit(Website $website)
    {
        $this->authorize('update', $website);

        return Inertia::render('Websites/Form', ['website' => $website, 'clients' => Client::orderBy('company_name')->get(['id', 'company_name'])]);
    }

    public function update(WebsiteRequest $request, Website $website)
    {
        $this->authorize('update', $website);
        $website->update($request->validated());

        return redirect()->route('websites.show', $website)->with('success', 'Web actualizada correctamente.');
    }

    public function destroy(Website $website)
    {
        $this->authorize('delete', $website);
        if ($website->tickets()->exists() || $website->maintenanceTasks()->exists()) {
            return back()->with('error', 'No se puede eliminar una web con tickets o tareas asociadas.');
        }
        $website->delete();

        return redirect()->route('websites.index')->with('success', 'Web eliminada.');
    }
}
