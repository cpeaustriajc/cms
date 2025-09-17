<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'author_id',
        'status_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ContentStatus::class, 'status_id');
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(ContentFieldValue::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(ContentRoute::class);
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'content_term')->withTimestamps();
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'content_asset')->withTimestamps();
    }

    public function relatesTo(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_relation', 'from_content_id', 'to_content_id')
            ->withPivot(['relation_type_id', 'sort_order'])
            ->withTimestamps();
    }

    public function relatedFrom(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_relation', 'to_content_id', 'from_content_id')
            ->withPivot(['relation_type_id', 'sort_order'])
            ->withTimestamps();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContentVersion::class);
    }

    public function upsertFieldValue(string $handle, mixed $value, ?string $localeCode = null, int $sortOrder = 0): void
    {
        $field = Field::where('content_type_id', $this->content_type_id)
            ->where('handle', $handle)
            ->firstOrFail();

        $localeId = null;

        if ($localeCode) {
            $localeId = optional(Locale::where('code', $localeCode)->first())->id;
        }

        $data = [
            'content_id' => $this->id,
            'field_id' => $field->id,
            'locale_id' => $localeId,
            'sort_order' => $sortOrder,
        ];

        $valueCols = [
            'value_string' => null,
            'value_text' => null,
            'value_integer' => null,
            'value_decimal' => null,
            'value_boolean' => null,
            'value_datetime' => null,
        ];

        switch ($field->data_type) {
            case 'string':
            case 'richtext':
                $valueCols['value_string'] = is_null($value) ? null : (string) $value;
                break;
            case 'text':
                $valueCols['value_text'] = is_null($value) ? null : (string) $value;
                break;
            case 'integer':
            case 'reference':
                $valueCols['value_integer'] = is_null($value) ? null : (int) $value;
                break;
            case 'decimal':
                $valueCols['value_decimal'] = is_null($value) ? null : (float) $value;
                break;
            case 'boolean':
                $valueCols['value_boolean'] = is_null($value) ? null : (bool) $value;
                break;
            case 'datetime':
                $valueCols['value_datetime'] = is_null($value) ? null : ($value instanceof DateTimeInterface ? $value : now());
                break;
        }

        ContentFieldValue::updateOrCreate(
            Arr::only(
                $data,
                ['content_id', 'field_id', 'locale_id', 'sort_order'],
            ),
            array_merge($data, $valueCols)
        );
    }

    public function ensureRoute(?string $localeCode, string $path, bool $isPrimary = false): ContentRoute
    {
        $localeId = null;
        if ($localeCode) {
            $localeId = optional(Locale::where('code', $localeCode)->first())->id;
        }

        if ($isPrimary && $localeId) {
            $this->routes()->where('locale_id', $localeId)->update(['is_primary' => false]);
        }

        return ContentRoute::updateOrCreate(
            ['content_id' => $this->id, 'locale_id' => $localeId, 'path' => $path],
            ['is_primary' => $isPrimary]
        );
    }

    public function syncTerms(array $termGroups): void
    {
        $ids = [];

        foreach ($termGroups as $group) {
            $tax = Taxonomy::where('slug', $group['taxonomy'] ?? '')->first();
            if (!$tax) {
                continue;
            }

            $slugs = (array) ($group['terms'] ?? []);

            $ids = array_merge($ids, Term::where('taxonomy_id', $tax->id)->whereIn('slug', $slugs)->pluck('id')->all());
        }
        $this->terms()->sync($ids);
    }

    public function syncAssets(array $assets): void
    {
        $map = [];
        foreach ($assets as $row) {
            $assetId = (int) ($row['asset_id'] ?? 0);
            if ($assetId <= 0) {
                $disk = (string) ($row['disk'] ?? '');
                $path = (string) ($row['path'] ?? '');

                if ($disk === '' || $path === '') {
                    continue;
                }

                $filename = (string) ($row['filename'] ?? basename($path));

                $asset = Asset::firstOrCreate(
                    ['disk' => $disk, 'path' => $path],
                    [
                        'filename' => $filename,
                        'mime_type' => null,
                        'size_bytes' => 0
                    ]
                );
                $assetId = $asset->id;
                continue;
            }

            if ($assetId > 0) {

                $map[$assetId] = [
                    'role' => (string) ($row['role'] ?? ''),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ];
            }
        }

        $this->assets()->sync($map);
    }

    public function createFromPayload(array $data): static
    {
        return DB::transaction(function () use ($data) {
            $statusId = $data['status_id'] ?? null;
            if (!$statusId && !empty($data['status'])) {
                $statusId = optional(ContentStatus::where('code', $data['status'])->first())->id;
            }

            $content = self::create([
                'content_type_id' => $data['content_type_id'],
                'author_id' => $data['author_id'] ?? null,
                'status_id' => $statusId,
                'published_at' => $data['published_at'] ?? null,
            ]);

            foreach ((array) ($data['fields'] ?? []) as $fieldValue) {
                $content->upsertFieldValue(
                    $fieldValue['handle'] ?? '',
                    $fieldValue['value'] ?? null,
                    $fieldValue['locale'] ?? null,
                    (int) ($fieldValue['sort_order'] ?? 0)
                );
            }

            if (!empty($data['routes'])) {
                $content->ensureRoute(
                    $data['routes']['locale_code'] ?? null,
                    (string) $data['routes']['path'] ?? '',
                    (bool) ($data['routes']['is_primary'] ?? false)
                );
            }

            if (!empty($data['terms'])) {
                $content->syncTerms($data['terms']);
            }

            if (!empty($data['assets'])) {
                $content->syncAssets($data['assets']);
            }

            return $content;
        });
    }

    public function updateFromPayload(array $data): static
    {
        return DB::transaction(function () use ($data) {
            if (array_key_exists('author_id', $data)) {
                $this->author_id = $data['author_id'];
            }

            if (array_key_exists('published_at', $data)) {
                $this->published_at = $data['published_at'];
            }

            if (array_key_exists('status_id', $data) || array_key_exists('status', $data)) {
                $statusId = $data['status_id'] ?? null;
                if (!$statusId && !empty($data['status'])) {
                    $statusId = optional(ContentStatus::where('code', $data['status'])->first())->id;
                }

                $this->status_id = $statusId;
            }

            $this->save();

            foreach ((array) ($data['fields'] ?? []) as $fieldValue) {
                $this->upsertFieldValue(
                    (string) $fieldValue['handle'],
                    $fieldValue['value'] ?? null,
                    $fieldValue['locale_code'] ?? null,
                    (int) ($fieldValue['sort_order'] ?? 0)
                );
            }

            if (!empty($data['route'])) {
                $this->ensureRoute(
                    $data['route']['locale_code'] ?? null,
                    (string) ($data['route']['path'] ?? ''),
                    (bool) ($data['route']['is_primary'] ?? false)
                );
            }

            if (isset($data['terms'])) {
                $this->syncTerms($data['terms']);
            }

            if (isset($data['assets'])) {
                $this->syncAssets($data['assets']);
            }

            return $this->refresh();
        });
    }
}
