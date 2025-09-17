<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    protected $model = Term::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words();

        return [
            'taxonomy_id' => Taxonomy::factory(),
            'parent_id' => null,
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
