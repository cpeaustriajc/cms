<?php

namespace App\Http\Requests;

use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use \App\Models\Content;

class ContentUpdateRequest extends FormRequest
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
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'exists:content_statuses,code'],
            'status_id' => ['nullable', 'integer', 'exists:content_statuses,id'],
            'published_at' => ['nullable', 'date'],

            'route' => ['nullable', 'array'],
            'route.locale_code' => ['nullable', 'string', 'exists:locales,code'],
            'route.path' => ['required_with:route', 'string', 'max:255'],
            'route.is_primary' => ['nullable', 'boolean'],

            'terms' => ['nullable', 'array'],
            'terms.*.taxonomy' => ['required_with:terms', 'string', 'exists:taxonomies,slug'],
            'terms.*.terms' => ['required_with:terms.*.taxonomy', 'array', 'min:1'],
            'terms.*.terms.*' => ['string'],

            'assets' => ['nullable', 'array'],
            'assets.*' => ['array'],
            'assets.*.asset_id' => ['nullable', 'integer', Rule::exists('assets', 'id')],
            'assets.*.disk' => ['required_without:assets.*.asset_id', 'string'],
            'assets.*.path' => ['required_without:assets.*.asset_id', 'string'],
            'assets.*.filename' => ['nullable', 'string', 'max:255'],
            'assets.*.role' => ['nullable', 'string', 'max:64'],
            'assets.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'fields' => ['nullable', 'array'],
            'fields.*.handle' => ['required', 'string'],
            'fields.*.value' => ['present'],
            'fields.*.locale_code' => ['nullable', 'string', 'exists:locales,code'],
            'fields.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validationInstance) {
            /** @var Content $content */
            $content = $this->route('content');
            if (!$content) {
                return;
            }

            $fields = Field::where('content_type_id', $content->content_type_id)->get()->keyBy('handle');
            $provided = collect(Arr::get($this->all(), 'fields', []))->keyBy('handle');

            // Only validate provided fields; required enforcement can be skipped on partial updates
            foreach ($provided as $index => $fieldValue) {
                $handle = (string) ($fieldValue['handle'] ?? '');
                if (!$fields->has($handle)) {
                    $validationInstance->errors()->add("fields.$index.handle", "Field '{$handle}' is not defined for this content type.");
                    continue;
                }

                $field = $fields->get($handle);
                $value = $fieldValue['value'] ?? null;
                $errorKey = "fields.$index.value";

                switch ($field->data_type) {
                    case 'string':
                    case 'richtext':
                    case 'text':
                        if (!is_null($value) && !is_string($value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be a string.");
                        }
                        break;
                    case 'integer':
                    case 'reference':
                        if (!is_null($value) && !is_numeric($value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be an integer.");
                        }
                        break;
                    case 'decimal':
                        if (!is_null($value) && !is_numeric($value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be a decimal number.");
                        }
                        break;
                    case 'boolean':
                        if (!is_null($value) && !is_bool($value) && !in_array($value, [0, 1, '0', '1'], true)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be boolean.");
                        }
                        break;
                    case 'datetime':
                        if (!is_null($value) && !strtotime((string) $value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be a valid datetime string.");
                        }
                        break;
                    case 'asset':
                        if ($field->is_repeatable) {
                            if (!is_array($value) || count(array_filter($value, 'is_numeric')) !== count($value)) {
                                $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be an array of asset IDs.");
                            }
                        } else {
                            if (!is_null($value) && !is_numeric($value)) {
                                $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be an asset ID.");
                            }
                        }
                        break;
                }
            }
        });
    }
}
