<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Term represents a single classification within a Taxonomy (e.g., a category
 * or tag). Supports hierarchical relationships via parent/children.
 *
 * Columns derived from the terms table:
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
/**
 * Relations (read-only):
 *
 * @property-read \App\Models\Taxonomy $taxonomy
 * @property-read \App\Models\Term|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $children
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content> $contents
 */
class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxonomy_id',
        'parent_id',
        'name',
        'slug',
        'description',
    ];

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_term')->withTimestamps();
    }
}
