<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ContentType defines a schema for Content entries, including which Fields
 * are available and organizational metadata like name/slug/description.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content> $contents
 */
class ContentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
