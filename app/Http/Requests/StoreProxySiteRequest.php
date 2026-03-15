<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProxySiteRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:proxy_sites,domain',
            'cloudflare_zone_id' => 'required|string|max:255',
            'cloudflare_dns_record_id' => 'nullable|string|max:255|unique:proxy_sites,cloudflare_dns_record_id',
            'proxy_enabled' => 'boolean|default:true',
            'ssl_auto_renewal' => 'boolean',
            'ssl_next_renewal' => 'nullable|date|required_if:ssl_auto_renewal,1',
            'affected_by_laliga' => 'boolean',
        ];
    }
}
