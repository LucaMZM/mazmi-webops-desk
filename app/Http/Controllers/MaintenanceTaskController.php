<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceTaskRequest;
use App\Models\MaintenanceTask;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceTaskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MaintenanceTask::class);
        $tasks = MaintenanceTask::query()->with(['website:id,client_id,name', 'website.client:id,company_name', 'assignee:id,name'])
            ->when($request->user()->isClient(), fn (Builder $q) => $q->whereHas('website', fn (Builder $w) => $w->where('client_id', $request->user()->client_id)))
            ->when($request->user()->isTechnician(), fn (Builder $q) => $q->where('assigned_to', $request->user()->id))
            ->when($request->search, fn (Builder $q, $value) => $q->where('title', 'like', "%{$value}%"))
            ->when($request->status, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($request->priority, fn (Builder $q, $value) => $q->where('priority', $value))
            ->when($request->category, fn (Builder $q, $value) => $q->where('category', $value))
            ->when($request->website_id, fn (Builder $q, $value) => $q->where('website_id', $value))
            ->when($request->schedule === 'overdue', fn (Builder $q) => $q->where('scheduled_at', '<', now())->whereNotIn('status', ['completed']))
            ->when($request->schedule === 'upcoming', fn (Builder $q) => $q->whereBetween('scheduled_at', [now(), now()->addDays(30)])->whereNotIn('status', ['completed']))
            ->orderByRaw('scheduled_at IS NULL, scheduled_at asc')->paginate(12)->withQueryString();

        return Inertia::render('Maintenance/Index', [
            'tasks' => $tasks,
            'filters' => $request->only('search', 'status', 'priority', 'category', 'website_id', 'schedule'),
            'websites' => Website::query()->when($request->user()->isClient(), fn (Builder $q) => $q->where('client_id', $request->user()->client_id))->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', MaintenanceTask::class);

        return Inertia::render('Maintenance/Form', $this->formData());
    }

    public function store(MaintenanceTaskRequest $request)
    {
        $this->authorize('create', MaintenanceTask::class);
        $data = $request->validated();
        $data['completed_at'] = $data['status'] === 'completed' ? now() : null;
        $task = MaintenanceTask::create($data);

        return redirect()->route('maintenance.show', $task)->with('success', 'Tarea de mantenimiento creada.');
    }

    public function show(MaintenanceTask $task)
    {
        $this->authorize('view', $task);

        return Inertia::render('Maintenance/Show', ['task' => $task->load(['website.client', 'assignee:id,name,email'])]);
    }

    public function edit(MaintenanceTask $task)
    {
        $this->authorize('update', $task);

        return Inertia::render('Maintenance/Form', [...$this->formData(), 'task' => $task]);
    }

    public function update(MaintenanceTaskRequest $request, MaintenanceTask $task)
    {
        $this->authorize('update', $task);
        $data = $request->validated();
        if ($request->user()->isTechnician()) {
            $data['website_id'] = $task->website_id;
            $data['assigned_to'] = $task->assigned_to;
        }
        $data['completed_at'] = $data['status'] === 'completed' ? ($task->completed_at ?? now()) : null;
        $task->update($data);

        return redirect()->route('maintenance.show', $task)->with('success', 'Tarea actualizada correctamente.');
    }

    public function complete(MaintenanceTask $task)
    {
        $this->authorize('update', $task);
        $task->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'Tarea marcada como completada.');
    }

    public function destroy(MaintenanceTask $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('maintenance.index')->with('success', 'Tarea eliminada.');
    }

    private function formData(): array
    {
        return [
            'websites' => Website::with('client:id,company_name')->orderBy('name')->get(['id', 'client_id', 'name']),
            'technicians' => User::where('role', 'technician')->orderBy('name')->get(['id', 'name']),
        ];
    }
}
