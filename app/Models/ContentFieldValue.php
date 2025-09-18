<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentFieldValue stores the typed value for a Field on a specific Content.
 * Supports locales, repeatable values via sort_order, and multiple data types.
 *
 * @property int $id
 * @property int $content_id
 * @property int $field_id
 * @property int|null $locale_id
 * @property int $sort_order
 * @property string|null $value_string
 * @property string|null $value_text
 * @property int|null $value_integer
 * @property string|null $value_decimal
 * @property bool|null $value_boolean
 * @property \Illuminate\Support\Carbon|null $value_datetime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Content $content
 * @property-read \App\Models\Field $field
 * @property-read \App\Models\Locale|null $locale
 */
class ContentFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'field_id',
        'locale_id',
        'sort_order',
        'value_string',
        'value_text',
        'value_integer',
        'value_decimal',
        'value_boolean',
        'value_datetime',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'value_integer' => 'integer',
            'value_decimal' => 'decimal:2',
            'value_boolean' => 'boolean',
            'value_datetime' => 'datetime',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
