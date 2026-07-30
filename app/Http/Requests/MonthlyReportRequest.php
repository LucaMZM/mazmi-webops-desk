<?php

namespace App\Http\Requests;

use App\Models\MonthlyReport;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonthlyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'summary' => ['required', 'string', 'max:5000'],
            'completed_tasks_count' => ['required', 'integer', 'min:0', 'max:100000'],
            'resolved_tickets_count' => ['required', 'integer', 'min:0', 'max:100000'],
            'pending_tickets_count' => ['required', 'integer', 'min:0', 'max:100000'],
            'recommendations' => ['required', 'string', 'max:5000'],
            'general_status' => ['required', Rule::in(['good', 'attention', 'critical'])],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            $exists = MonthlyReport::query()
                ->where('client_id', $this->client_id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->when($this->route('report'), fn ($query, $report) => $query->whereKeyNot($report->id))
                ->exists();
            if ($exists) {
                $validator->errors()->add('month', 'Ya existe un reporte para este cliente y periodo.');
            }
        }];
    }
}
