<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

final class CategoryController extends Controller
{
    #[Authorize('viewAny', Category::class)]
    public function index(Request $request): Response
    {
        $categories = Category::query()->forUser($request->user())
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Category $category): string => $category->type->name);

        return Inertia::render('categories/index', [
            'categories' => $categories,
        ]);
    }

    #[Authorize('create', Category::class)]
    public function store(CategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create([
            ...$request->validated(),
            'is_default' => false,
        ]);

        return to_route('categories.index');
    }

    #[Authorize('update', 'category')]
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->only(['name', 'color']));

        return to_route('categories.index');
    }

    #[Authorize('delete', 'category')]
    public function destroy(Category $category): RedirectResponse
    {
        /**
         * TODO: Cannot delete categories that have linked transactions and default categories.
         */
        $category->delete();

        return to_route('categories.index');
    }
}
