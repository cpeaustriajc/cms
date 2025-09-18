<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('content:read');
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'exists:content_types,slug'],
            'status' => ['nullable', 'string', 'exists:content_statuses,code'],
        ];
    }
}
