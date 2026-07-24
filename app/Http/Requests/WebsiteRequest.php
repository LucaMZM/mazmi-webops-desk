<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string', 'max:150'],
            'url' => ['required', 'url:http,https', 'max:255'],
            'technology' => ['required', Rule::in(['WordPress', 'PrestaShop', 'Laravel', 'PHP custom', 'Static HTML', 'Other'])],
            'hosting_provider' => ['nullable', 'string', 'max:150'],
            'domain_expires_at' => ['nullable', 'date'],
            'hosting_expires_at' => ['nullable', 'date'],
            'ssl_status' => ['required', Rule::in(['active', 'expiring', 'expired', 'unknown'])],
            'maintenance_plan' => ['required', Rule::in(['basic', 'standard', 'premium', 'none'])],
            'status' => ['required', Rule::in(['stable', 'review', 'incident', 'critical'])],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
