<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('comment:read');
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
