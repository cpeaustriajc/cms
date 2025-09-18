<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxonomyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('taxonomy:read');
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
