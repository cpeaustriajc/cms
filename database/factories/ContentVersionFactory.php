<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentVersion>
 */
class ContentVersionFactory extends Factory
{
    protected $model = ContentVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 years', 'now');

        return [
            'content_id' => Content::factory(),
            'version' => $this->faker->numberBetween(1, 10),
            'created_by_id' => User::factor(),
            'notes' => $this->faker->optional()->sentence(),
            'snapshot' => [
                'editor' => 'json',
                'blocks' => [
                    [
                        'type' => 'paragraph',
                        'text' => $this->faker->sentence(),
                    ]
                    ],
            ],
            'created_at' => $createdAt,
        ];
    }
}
