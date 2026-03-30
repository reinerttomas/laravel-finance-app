<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryColor;
use App\Enums\CategoryType;
use App\Models\Builders\CategoryBuilder;
use App\Policies\CategoryPolicy;
use Carbon\CarbonImmutable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int|null $user_id
 * @property-read string $name
 * @property-read CategoryType $type
 * @property-read CategoryColor $color
 * @property-read bool $is_default
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'type', 'color'])]
#[UseEloquentBuilder(CategoryBuilder::class)]
#[UseFactory(CategoryFactory::class)]
#[UsePolicy(CategoryPolicy::class)]
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
            'color' => CategoryColor::class,
            'is_default' => 'boolean',
        ];
    }
}
