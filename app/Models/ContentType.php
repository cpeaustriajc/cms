<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
