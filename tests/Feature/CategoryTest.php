<?php

declare(strict_types=1);

use App\Enums\CategoryColor;
use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;

test('categories page is displayed', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('categories.index'));

    $response->assertOk();
});

test('guest cannot view categories', function (): void {
    $response = $this->get(route('categories.index'));

    $response->assertRedirect(route('login'));
});

test('categories page shows default and user categories', function (): void {
    $user = User::factory()->create();
    Category::factory()->default()->expense()->create(['name' => 'Default Expense']);
    Category::factory()->expense()->create(['user_id' => $user->id, 'name' => 'My Expense']);

    $response = $this
        ->actingAs($user)
        ->get(route('categories.index'));

    $response->assertOk();
});

test('user can create a custom expense category', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'name' => 'Groceries',
            'type' => CategoryType::Expense->value,
            'color' => CategoryColor::Red->value,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('categories.index'));

    expect(Category::query()->where('name', 'Groceries')->first())
        ->user_id->toBe($user->id)
        ->type->toBe(CategoryType::Expense)
        ->color->toBe(CategoryColor::Red)
        ->is_default->toBeFalse();
});

test('user can create a custom income category', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'name' => 'Side Hustle',
            'type' => CategoryType::Income->value,
            'color' => '#22C55E',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('categories.index'));

    expect(Category::query()->where('name', 'Side Hustle')->first())
        ->type->toBe(CategoryType::Income);
});

test('validation rejects missing name', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'type' => CategoryType::Expense->value,
            'color' => CategoryColor::Red->value,
        ]);

    $response->assertSessionHasErrors('name');
});

test('validation rejects invalid color format', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'name' => 'Test',
            'type' => CategoryType::Expense->value,
            'color' => 'not-a-color',
        ]);

    $response->assertSessionHasErrors('color');
});

test('validation rejects invalid type', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'name' => 'Test',
            'type' => 99,
            'color' => CategoryColor::Red->value,
        ]);

    $response->assertSessionHasErrors('type');
});

test('validation rejects name longer than 100 characters', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'name' => str_repeat('a', 101),
            'type' => CategoryType::Expense->value,
            'color' => CategoryColor::Red->value,
        ]);

    $response->assertSessionHasErrors('name');
});

test('user can update their own category', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('categories.update', $category), [
            'name' => 'Updated Name',
            'color' => CategoryColor::Green->value,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('categories.index'));

    $category->refresh();

    expect($category->name)->toBe('Updated Name');
    expect($category->color)->toBe(CategoryColor::Green);
});

test('user cannot update a default category', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->default()->expense()->create();

    $response = $this
        ->actingAs($user)
        ->put(route('categories.update', $category), [
            'name' => 'Hacked',
            'color' => CategoryColor::Gray->value,
        ]);

    $response->assertForbidden();
});

test('user cannot update another users category', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('categories.update', $category), [
            'name' => 'Hacked',
            'color' => CategoryColor::Gray->value,
        ]);

    $response->assertForbidden();
});

test('user can delete their own category', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('categories.index'));

    expect(Category::query()->find($category->id))->toBeNull();
});

test('user cannot delete a default category', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->default()->expense()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response->assertForbidden();
});

test('user cannot delete another users category', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response->assertForbidden();
});
