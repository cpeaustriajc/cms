<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Taxonomy groups Terms into a named classification (e.g., Categories, Tags).
 * Content entries can be tagged with Terms from one or more Taxonomies.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * Relations (read-only):
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $terms
 */
class Taxonomy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
