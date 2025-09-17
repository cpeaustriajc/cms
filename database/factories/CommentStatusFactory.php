<?php

namespace Database\Factories;

use App\Models\CommentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentStatus>
 */
class CommentStatusFactory extends Factory
{
    protected $model = CommentStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->slug(2),
            'label' => $this->faker->words(2, true),
        ];
    }
}
