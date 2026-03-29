# Guidelines

## Code Standards

- Use strict types
- Use latest code standards and Laravel features
- Comments only for complex logic
- Write defensive code
- Always follow Laravel best practices
- Always write tests for new features and bug fixes
- use AskUserQuestion to ask the user for more information if needed

## Bug fixing
- when report bug, start by writing a test that reproduces the bug. Then try to fix the bug and prove it with passing test

## Architecture & structure (Laravel)

### Database

* Always remove the `down()` method from migrations - we don't use rollbacks
* Migrations should only contain the `up()` method

### Models

* When creating model also create factory.
* Keep models lean.
* Use accessors and mutators for attribute transformations.
* Use phpdoc for model properties, always use readonly.
* Use phpdoc for relationships.
* Always specify related model and $this in generics for relationships.
* Use attributes in `Illuminate\Database\Eloquent\Attributes` like `Fillable`, `UseFactory`, etc.

```php
/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read Collection<int, Post> $posts
 */
 #[UseFactory(UserFactory::class)]
final class User extends Authenticatable
{
    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```

### Queues & Jobs

* Use queues for long-running tasks, not for code organization.
* Keep jobs focused on a single responsibility.
* Use `dispatch(new SendWelcomeEmail())` instead of `SendWelcomeEmail::dispatch()` for type safety.

### Authorization

- use the `Authorize` attribute as a convenient shortcut for the can middleware
- Keep policies small and focused
- Bind with `#[UsePolicy(Model::class)]` attribute on model
- Write tests and document complex logic
