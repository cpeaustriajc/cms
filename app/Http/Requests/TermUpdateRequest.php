<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TermUpdateRequest extends FormRequest
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
            'taxonomy_id' => ['required', 'integer', 'exists:taxonomies,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('terms', 'id')->where(fn ($query) => $query->where('taxonomy_id', $this->taxonomy_id)
                    ->where('id', '!=', $this->term->id)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('terms')->where(fn ($query) => $query->where('taxonomy_id', $this->taxonomy_id))->ignore($this->term),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, hyphens, and underscores.',
            'slug.unique' => 'This slug is already taken in this taxonomy.',
            'parent_id.exists' => 'The selected parent term must belong to the same taxonomy and cannot be the term itself.',
        ];
    }
}
