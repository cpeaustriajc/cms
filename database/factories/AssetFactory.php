<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->unique()->filePath();

        return [
            'disk' => 'public',
            'path' => 'uploads/' . basename($filename),
            'ext' => pathinfo($filename, PATHINFO_EXTENSION),
            'mime_type' => 'image/jpeg',
            'size_bytes' => $this->faker->numberBetween(10_000, 4_000_000),
            'width' => $this->faker->numberBetween(200, 1920),
            'height' => $this->faker->numberBetween(200, 1080),
            'duration_seconds' => null,
            'alt_text' => $this->faker->optional()->sentence(3),
            'created_by_id' => User::factory(),
        ];
    }
}
