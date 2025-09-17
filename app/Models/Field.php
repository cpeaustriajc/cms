<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
