<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CategoryColor;
use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CategoryRequest extends FormRequest
{
    public ?Category $category = null;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                Rule::enum(CategoryType::class),
                Rule::when(fn (): bool => ! $this->category instanceof Category, 'required', 'prohibited'),
            ],

            'name' => [
                'required',
                Rule::string()->max(100),
            ],

            'color' => [
                'required',
                Rule::string()->max(50),
                Rule::enum(CategoryColor::class),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $category = $this->route('category');

        if ($category instanceof Category) {
            $this->category = $category;
        }
    }
}
