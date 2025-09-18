<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TermStoreRequest extends FormRequest
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
            'taxonomy_id' => ['required', 'exists:taxonomies,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('terms', 'id')->where(fn ($query) => $query->where('taxonomy_id', $this->input('taxonomy_id'))),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-_]+$/',
                Rule::unique('terms')->where(fn ($query) => $query->where('taxonomy_id', $this->integer('taxonomy_id'))),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, hyphens, and underscores.',
            'slug.unique' => 'The slug has already been taken for this taxonomy.',
            'parent_id.exists' => 'The selected parent term is invalid for the specified taxonomy.',
        ];
    }
}
