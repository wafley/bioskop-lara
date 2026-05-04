<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $password;

        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => $password ??= bcrypt('password'),
            'status' => true,
        ];
    }

    public function admin($roleId): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => $roleId,
        ]);
    }

    public function operator($roleId): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => $roleId,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => false,
        ]);
    }
}
