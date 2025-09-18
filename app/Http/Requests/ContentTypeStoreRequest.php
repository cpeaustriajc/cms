<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentTypeStoreRequest extends FormRequest
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
                'unique:content_types,slug',
                'alpha_dash',
            ],
            'description' => ['nullable', 'string', 'max:1000']
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, hyphens, and underscores.',
            'slug.unique' => 'The slug has already been taken.'
        ];
    }
}
