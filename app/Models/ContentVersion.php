<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentVersion stores immutable snapshots of a Content entry for auditing
 * and rollback. Version is unique per content; records track the creator.
 * Note: timestamps disabled; uses a custom created_at column in DB.
 *
 * Columns derived from the content_versions table:
 *
 * @property int $id
 * @property int $content_id
 * @property int $version
 * @property int|null $created_by_id
 * @property string|null $notes
 * @property array $snapshot
 * @property \Illuminate\Support\Carbon $created_at
 *
 * Relations (read-only):
 * @property-read \App\Models\Content $content
 * @property-read \App\Models\User|null $creator
 */
class ContentVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'content_id',
        'version',
        'created_by_id',
        'notes',
        'snapshot',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
