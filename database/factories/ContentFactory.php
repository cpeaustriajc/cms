<?php

namespace Database\Factories;

use App\Models\ContentStatus;
use App\Models\ContentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_type_id' => ContentType::factory(),
            'author_id' => User::factory(),
            'status_id' => ContentStatus::factory(),
            'published_at' => $this->faker->optional(60)->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
