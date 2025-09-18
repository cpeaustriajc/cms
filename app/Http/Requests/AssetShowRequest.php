<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetShowRequest extends FormRequest
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
        return [];
    }
}
