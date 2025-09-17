<?php

namespace Database\Factories;

use App\Models\CommentStatus;
use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_id' => Content::factor(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'body' => $this->faker->paragraph(),
            'status_id' => CommentStatus::factory(),
        ];
    }
}
