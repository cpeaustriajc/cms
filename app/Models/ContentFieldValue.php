<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
