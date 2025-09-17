<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk',
        'path',
        'filename',
        'ext',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'duration_seconds',
        'alt_text',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_asset')
            ->withPivot(['role', 'sort_order'])
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
