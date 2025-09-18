<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Comment represents user-authored discussion attached to a Content entry.
 * It supports threading (parent/child), moderation via status, and soft-deletes.
 *
 * Columns derived from the comments table:
 *
 * @property int $id
 * @property int $content_id
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property string $body
 * @property int|null $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * Relations (read-only):
 * @property-read \App\Models\Content $content
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Comment|null $parent
 * @property-read \App\Models\CommentStatus|null $status
 */
class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'user_id',
        'parent_id',
        'body',
        'status_id',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CommentStatus::class, 'status_id');
    }
}
