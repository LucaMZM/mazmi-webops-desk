<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Website;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
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
            'website_id' => ['nullable', 'integer', 'exists:websites,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['open', 'in_progress', 'waiting_client', 'resolved', 'closed'])],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($this->website_id && ! Website::whereKey($this->website_id)->where('client_id', $this->client_id)->exists()) {
                $validator->errors()->add('website_id', 'La web seleccionada no pertenece al cliente.');
            }
            if ($this->assigned_to && ! User::whereKey($this->assigned_to)->where('role', 'technician')->exists()) {
                $validator->errors()->add('assigned_to', 'Solo se puede asignar el ticket a un técnico.');
            }
        }];
    }
}
