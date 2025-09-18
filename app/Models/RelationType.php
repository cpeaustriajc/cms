<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RelationType defines semantic relationships between Content entries (e.g.,
 * Related, Featured In). Used by the content_relation pivot to type links.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_bidirectional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class RelationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_bidirectional',
    ];

    protected function casts(): array
    {
        return [
            'is_bidirectional' => 'boolean',
        ];
    }
}
