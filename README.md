# Laravel Finance App

A modern finance application built with Laravel 13, React 19, Inertia.js 3, and shadcn/ui. Based on the Laravel React Starter Kit with a complete authentication system and professional UI.

## Tech Stack

**Backend:** PHP 8.5, Laravel 13, Fortify (authentication), Wayfinder (typed route generation)

**Frontend:** React 19, TypeScript, Inertia.js 3, Tailwind CSS 4, shadcn/ui

**Testing & Quality:** Pest 4, Larastan (PHPStan), Rector, Pint, ESLint, Prettier

**Database:** SQLite (default)

## Features

- Full authentication (login, registration, password reset, email verification)
- Two-factor authentication (TOTP with recovery codes)
- User profile and security settings
- Appearance/theme preferences with dark mode
- Database-backed sessions

## Requirements

- PHP 8.5
- Composer
- Bun
- Postgres

## Installation

```bash
git clone <repository-url>
cd laravel-finance-app

composer install
bun install

cp .env.example .env
php artisan key:generate

php artisan migrate
```

## Development

```bash
# Start all dev servers (PHP, Vite, queue, logs)
composer run dev

# Or run individually
php artisan serve
bun run dev
```

The app is also available via Laravel Herd at `https://laravel-finance-app.test`.

## Testing

```bash
# Run all tests
php artisan test

# Run with filter
php artisan test --filter=testName

# Static analysis
./vendor/bin/phpstan analyse

# Code formatting
./vendor/bin/pint
```

## Code Quality

```bash
# PHP formatting
./vendor/bin/pint

# PHP static analysis
./vendor/bin/phpstan analyse

# PHP automated refactoring
./vendor/bin/rector process

# Frontend linting
bun run lint
```
