<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CommentStatus defines moderation states for comments (e.g., pending, approved).
 *
 * Columns derived from the comment_statuses table:
 *
 * @property int $id
 * @property string $code
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CommentStatus extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'label'];
}
