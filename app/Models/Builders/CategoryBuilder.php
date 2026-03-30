<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Category>
 */
final class CategoryBuilder extends Builder
{
    public function forUser(User $user): self
    {
        return $this->where('user_id', $user->id)->orWhereNull('user_id');
    }
}
