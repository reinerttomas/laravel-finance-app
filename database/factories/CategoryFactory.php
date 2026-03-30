<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CategoryColor;
use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->word(),
            'type' => fake()->randomElement(CategoryType::cases()),
            'color' => fake()->randomElement(CategoryColor::cases())->value,
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => null,
            'is_default' => true,
        ]);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CategoryType::Income,
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CategoryType::Expense,
        ]);
    }
}
