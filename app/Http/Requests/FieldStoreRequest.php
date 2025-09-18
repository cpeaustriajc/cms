<?php

namespace App\Http\Requests;

use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldStoreRequest extends FormRequest
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
            'content_type_id' => ['required', 'integer', 'exists:content_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'handle' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('fields')->where(fn ($query) => $query->where('content_type_id', $this->string('content_type_id'))),
            ],
            'data_type' => ['required', 'string', Rule::in(Field::ALLOWED_TYPES)],
            'is_required' => ['boolean'],
            'is_unique' => ['boolean'],
            'is_translatable' => ['boolean'],
            'is_repeatable' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'handle.regex' => 'The handle must start with a letter and can only contain letters, numbers, and underscores.',
            'handle.unique' => 'The handle is already used in this content type.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
            'is_unique' => $this->boolean('is_unique'),
            'is_translatable' => $this->boolean('is_translatable'),
            'is_repeatable' => $this->boolean('is_repeatable'),
        ]);
    }
}
