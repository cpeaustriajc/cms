<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentTypeDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('content-type:delete');
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
