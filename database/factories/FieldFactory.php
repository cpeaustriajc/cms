<?php

namespace Database\Factories;

use App\Models\ContentType;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'content_type_id' => ContentType::factory(),
            'name' => Str::title($name),
            'handle' => Str::snake($name),
            'data_type' => $this->faker->randomElement(Field::ALLOWED_TYPES),
            'is_required' => $this->faker->boolean(20),
            'is_unique' => $this->faker->boolean(10),
            'is_translatable' => $this->faker->boolean(20),
            'is_repeatable' => $this->faker->boolean(10),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
