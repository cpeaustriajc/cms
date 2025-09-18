<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('asset:read');
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'mime_type' => ['nullable', 'string', 'max:64'],
        ];
    }
}
