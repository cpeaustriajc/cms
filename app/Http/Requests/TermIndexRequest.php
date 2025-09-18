<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('term:read');
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'taxonomy' => ['nullable', 'string', 'exists:taxonomies,slug'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
