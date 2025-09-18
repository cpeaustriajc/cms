<?php

namespace App\Http\Requests;

use App\Models\ContentType;
use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ContentStoreRequest extends FormRequest
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
            'content_type' => ['required_without:content_type_id', 'string', 'exists:content_types,slug'],
            'content_type_id' => ['required_without:content_type', 'integer', 'exists:content_types,id'],

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
            $payload = $this->all();

            $type = null;
            if ($slug = Arr::get($payload, 'content_type')) {
                $type = ContentType::where('slug', $slug)->first();
            } elseif ($id = Arr::get($payload, 'content_type_id')) {
                $type = ContentType::find($id);
            }

            if (!$type) {
                $validationInstance->errors()->add('content_type', 'Content type not found.');
                return;
            }

            $fields = Field::where('content_type_id', $type->id)->get()->keyBy('handle');

            // Required fields must be present
            $provided = collect(Arr::get($payload, 'fields', []))->keyBy('handle');

            foreach ($fields as $handle => $field) {
                if ($field->is_required) {
                    if (!$provided->has($handle)) {
                        $validationInstance->errors()->add("fields", "Required field '{$handle}' is missing.");
                    }
                }
            }

            // Validate each provided field value matches the field type
            foreach ($provided as $index => $fieldValue) {
                $handle = (string) ($fieldValue['handle'] ?? '');
                if (!$fields->has($handle)) {
                    $validationInstance->errors()->add("fields.$index.handle", "Field '{$handle}' is not defined for type '{$type->slug}'.");
                    continue;
                }

                $field = $fields->get($handle);
                $value = $fieldValue['value'] ?? null;

                $errorKey = "fields.$index.value";
                switch ($field->data_type) {
                    case 'string':
                    case 'richtext':
                        if (!is_null($value) && !is_string($value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be a string.");
                        }
                        break;
                    case 'text':
                        if (!is_null($value) && !is_string($value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be text.");
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
                        if (!is_null($value) && !is_bool($value)) {
                            // Allow "0"/"1"/0/1 too
                            if (!in_array($value, [0, 1, '0', '1'], true)) {
                                $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be boolean.");
                            }
                        }
                        break;
                    case 'datetime':
                        if (!is_null($value) && !strtotime((string) $value)) {
                            $validationInstance->errors()->add($errorKey, "Field '{$handle}' must be a valid datetime string.");
                        }
                        break;
                    case 'asset':
                        // Expect asset id or array of ids if repeatable
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
