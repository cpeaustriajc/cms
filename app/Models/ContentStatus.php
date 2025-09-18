<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ContentStatus defines workflow states for Content (e.g., draft, published).
 *
 * Columns derived from the content_statuses table:
 *
 * @property int $id
 * @property string $code
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ContentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
    ];
}
