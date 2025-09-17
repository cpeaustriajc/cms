<?php

namespace Database\Factories;

use App\Models\ContentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentStatus>
 */
class ContentStatusFactory extends Factory
{
    protected $model = ContentStatus::class;

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
