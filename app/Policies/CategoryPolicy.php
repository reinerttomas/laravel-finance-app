<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

final class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        if ($category->is_default) {
            return false;
        }

        return $category->user_id === $user->id;
    }

    public function delete(User $user, Category $category): bool
    {
        if ($category->is_default) {
            return false;
        }

        return $category->user_id === $user->id;
    }
}
