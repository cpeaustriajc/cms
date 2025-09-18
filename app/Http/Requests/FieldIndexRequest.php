<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FieldIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('field:read');
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'content_type' => ['nullable', 'string', 'exists:content_types,slug'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
