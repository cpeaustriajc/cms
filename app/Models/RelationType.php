<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_bidirectional' ,
    ];

    protected function casts(): array
    {
        return [
            'is_bidirectional' => 'boolean',
        ]
    }
}
