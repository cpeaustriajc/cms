<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentRoute maps a Content entry to a routable path, optionally per-locale.
 * One content may have multiple routes, with at most one primary per locale.
 *
 * Columns derived from the content_routes table:
 *
 * @property int $id
 * @property int $content_id
 * @property int|null $locale_id
 * @property string $path
 * @property bool $is_primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * Relations (read-only):
 * @property-read \App\Models\Content $content
 * @property-read \App\Models\Locale|null $locale
 */
class ContentRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'locale_id',
        'path',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
