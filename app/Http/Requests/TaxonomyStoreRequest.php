<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxonomyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->expectsJson() || $this->routeIs('api.*')) {
            $user = $this->user();

            return $user !== null && $user->tokenCan('taxonomy:write');
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:taxonomies:slug',
                'regex',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens, and must start and end with a letter or number.',
            'slug.unique' => 'The slug has already been taken. Please choose a different one.',
        ];
    }
}
