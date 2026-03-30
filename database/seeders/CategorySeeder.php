<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CategoryColor;
use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->getCategories() as $category) {
            Category::query()->firstOrCreate([
                'name' => $category['name'],
                'type' => $category['type'],
                'is_default' => true,
            ], [
                'color' => $category['color'],
                'user_id' => null,
            ]);
        }
    }

    /**
     * @return list<array{name: string, type: CategoryType, color: CategoryColor}>
     */
    private function getCategories(): array
    {
        return [
            // Expense categories
            [
                'name' => 'Housing',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Red,
            ],
            [
                'name' => 'Food & Groceries',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Orange,
            ],
            [
                'name' => 'Transportation',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Yellow,
            ],
            [
                'name' => 'Utilities',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Lime,
            ],
            [
                'name' => 'Entertainment',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Cyan,
            ],
            [
                'name' => 'Healthcare',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Violet,
            ],
            [
                'name' => 'Shopping',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Pink,
            ],
            [
                'name' => 'Education',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Indigo,
            ],
            [
                'name' => 'Other Expense',
                'type' => CategoryType::Expense,
                'color' => CategoryColor::Gray,
            ],
            // Income categories
            [
                'name' => 'Salary',
                'type' => CategoryType::Income,
                'color' => CategoryColor::Green,
            ],
            [
                'name' => 'Freelance',
                'type' => CategoryType::Income,
                'color' => CategoryColor::Emerald,
            ],
            [
                'name' => 'Investments',
                'type' => CategoryType::Income,
                'color' => CategoryColor::Teal,
            ],
            [
                'name' => 'Gifts',
                'type' => CategoryType::Income,
                'color' => CategoryColor::Amber,
            ],
            [
                'name' => 'Other Income',
                'type' => CategoryType::Income,
                'color' => CategoryColor::Slate,
            ],
        ];
    }
}
