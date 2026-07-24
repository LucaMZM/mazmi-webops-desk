<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyReportRequest;
use App\Models\Client;
use App\Models\MonthlyReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MonthlyReport::class);
        $reports = MonthlyReport::query()->with('client:id,company_name')
            ->when($request->user()->isClient(), fn (Builder $q) => $q->where('client_id', $request->user()->client_id))
            ->when($request->client_id, fn (Builder $q, $value) => $q->where('client_id', $value))
            ->when($request->month, fn (Builder $q, $value) => $q->where('month', $value))
            ->when($request->year, fn (Builder $q, $value) => $q->where('year', $value))
            ->when($request->general_status, fn (Builder $q, $value) => $q->where('general_status', $value))
            ->latest('year')->latest('month')->paginate(12)->withQueryString();

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'filters' => $request->only('client_id', 'month', 'year', 'general_status'),
            'clients' => $request->user()->isClient() ? [] : Client::orderBy('company_name')->get(['id', 'company_name']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', MonthlyReport::class);

        return Inertia::render('Reports/Form', ['clients' => Client::where('status', 'active')->orderBy('company_name')->get(['id', 'company_name'])]);
    }

    public function store(MonthlyReportRequest $request)
    {
        $this->authorize('create', MonthlyReport::class);
        $report = MonthlyReport::create($request->validated());

        return redirect()->route('reports.show', $report)->with('success', 'Reporte mensual creado correctamente.');
    }

    public function show(MonthlyReport $report)
    {
        $this->authorize('view', $report);

        return Inertia::render('Reports/Show', ['report' => $report->load('client')]);
    }

    public function destroy(MonthlyReport $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Reporte eliminado.');
    }
}
