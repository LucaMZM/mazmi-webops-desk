<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceTaskRequest extends FormRequest
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
            'website_id' => ['required', 'integer', 'exists:websites,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['required', Rule::in(['backups', 'updates', 'security', 'performance', 'content', 'seo', 'other'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'blocked'])],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($this->assigned_to && ! User::whereKey($this->assigned_to)->where('role', 'technician')->exists()) {
                $validator->errors()->add('assigned_to', 'Solo se puede asignar la tarea a un técnico.');
            }
        }];
    }
}
