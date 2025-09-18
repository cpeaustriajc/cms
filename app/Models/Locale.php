<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Locale represents a language/region code (e.g. en-US) used for translatable
 * content and routes.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Locale extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];
}
