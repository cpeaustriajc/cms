<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Field defines a piece of structured data that can be attached to a ContentType.
 * Fields have a data_type and constraints (required/unique/translatable...).
 *
 * @property int $id
 * @property int $content_type_id
 * @property string $name
 * @property string $handle
 * @property string $data_type
 * @property bool $is_required
 * @property bool $is_unique
 * @property bool $is_translatable
 * @property bool $is_repeatable
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ContentType $contentType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContentFieldValue> $values
 */
class Field extends Model
{
    use HasFactory;

    public const ALLOWED_TYPES = [
        'string',
        'text',
        'integer',
        'decima',
        'boolean',
        'datetime',
        'reference',
        'richtext',
        'asset',
    ];

    protected $fillable = [
        'content_type_id',
        'name',
        'handle',
        'data_type',
        'is_required',
        'is_unique',
        'is_translatable',
        'is_repeatable',
        'sort_orrder',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_translatable' => 'boolean',
            'is_repeatable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function values()
    {
        return $this->hasMany(ContentFieldValue::class);
    }
}
