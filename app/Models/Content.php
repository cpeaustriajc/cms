<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'author_id',
        'status_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ContentStatus::class, 'status_id');
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(ContentFieldValue::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(ContentRoute::class);
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'content_term')->withTimestamps();
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'content_asset')->withTimestamps();
    }

    public function relatesTo(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_relation', 'from_content_id', 'to_content_id')
            ->withPivot(['relation_type_id', 'sort_order'])
            ->withTimestamps();
    }

    public function relatedFrom(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_relation', 'to_content_id', 'from_content_id')
            ->withPivot(['relation_type_id', 'sort_order'])
            ->withTimestamps();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContentVersion::class);
    }
}
