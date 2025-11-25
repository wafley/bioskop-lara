<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Studio>
 */
class StudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $index = 1;

        $rows = $this->faker->numberBetween(7, 9);
        $cols = $this->faker->numberBetween($rows + 1, 16);

        $name = "Studio {$index}";

        $index++;

        return [
            'name' => $name,
            'rows'     => $rows,
            'cols'     => $cols,
            'capacity' => $rows * $cols,
            'status' => $this->faker->boolean(),
        ];
    }

    public function active()
    {
        return $this->state(fn() => ['status' => true]);
    }

    public function inactive()
    {
        return $this->state(fn() => ['status' => false]);
    }
}
