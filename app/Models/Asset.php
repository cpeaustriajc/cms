<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Asset represents a stored media file (images, video, audio, docs) that can be
 * attached to Content entries via the content_asset pivot. It stores metadata
 * like mime type, size, dimensions, and optional alt text for accessibility.
 *
 * @property int $id
 * @property string $disk
 * @property string $path
 * @property string $filename
 * @property string|null $ext
 * @property string|null $mime_type
 * @property int $size_bytes
 * @property int|null $width
 * @property int|null $height
 * @property int|null $duration_seconds
 * @property string|null $alt_text
 * @property int|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content> $contents Pivot: role, sort_order
 * @property-read \App\Models\User|null $creator
 */
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
