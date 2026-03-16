<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProxyScheduleRequest extends FormRequest
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
            'type'        => 'required|string|in:laliga_match,ssl_renewal,manual',
            'description' => 'nullable|string|max:255',
            'disable_at'  => 'required|date',
            'enable_at'   => 'required|date|after:disable_at',
            'status'      => 'sometimes|string|in:pending,active,completed,failed',
            'site_ids'    => 'nullable|array',
            'site_ids.*'  => 'integer|exists:proxy_sites,id',
        ];
    }
}
