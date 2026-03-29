# Plan: Finance Tracking

> Source PRD: Conversation-based PRD from grill-me session (2026-03-29)

## Architectural decisions

Durable decisions that apply across all phases:

- **Currency**: CZK only, amounts stored as integer in smallest unit (halere). Multi-currency later.
- **Accounts**: No account/wallet separation. All transactions in one pool per user.
- **Schema**:
  - `categories` — `id`, `user_id` (nullable, null = default), `name`, `type` (enum: income/expense), `color` (hex), `is_default` (bool), `timestamps`
  - `transactions` — `id`, `user_id`, `category_id`, `subscription_id` (nullable FK), `type` (enum: income/expense), `amount` (integer, halere), `description` (nullable), `date` (date), `timestamps`
  - `subscriptions` — `id`, `user_id`, `category_id`, `name`, `amount` (integer, halere), `frequency` (enum: monthly/yearly), `url` (nullable), `description` (nullable), `next_billing_date` (date), `started_at` (date), `cancelled_at` (nullable datetime), `timestamps`
- **Key models**: Category, Transaction, Subscription
- **Authorization**: Policy on every model. `user_id` ownership check. Default categories (`user_id = null`) are shared and non-editable.
- **Routes**:
  - `GET|POST /transactions` — list + store
  - `PUT|DELETE /transactions/{transaction}` — update + delete
  - `GET|POST /subscriptions` — list + store
  - `PUT|DELETE /subscriptions/{subscription}` — update + cancel/delete
  - `PATCH /subscriptions/{subscription}/cancel` — cancel subscription
  - `GET|POST /settings/categories` — list + store
  - `PUT|DELETE /settings/categories/{category}` — update + delete
  - `GET /dashboard` — dashboard with aggregated data
- **Sidebar navigation**: Dashboard, Transactions, Subscriptions (Settings already exists)
- **Frontend patterns**: Modal/dialog for CRUD forms, shadcn/ui charts for dashboard
- **Deletion rules**:
  - Transactions: hard delete
  - Subscriptions: cancel via `cancelled_at`, delete only if no linked transactions
  - Categories: delete only if no linked transactions

---

## Phase 1: Categories

**User stories**:
- As a user, I want predefined categories for income and expenses so I can categorize transactions immediately
- As a user, I want to create my own custom categories so I can organize transactions my way
- As a user, I want to edit my custom categories (name, color) so I can keep them organized
- As a user, I want to delete a custom category so I can clean up unused ones
- As a user, I cannot delete a category that has linked transactions so I don't lose categorization
- As a user, I cannot edit or delete predefined (default) categories

### What to build

A complete categories subsystem: database table with seeded defaults, model with policy enforcing ownership and default-category protection, a settings page under `/settings/categories` where users can view all categories (default + own), create new ones with name/type/color, edit their own, and delete their own (blocked if transactions exist). This phase establishes the foundation that transactions and subscriptions depend on.

### Acceptance criteria

- [ ] Categories migration creates the table with all columns per schema
- [ ] Category model with factory, policy, and form request validation
- [ ] Seeder populates default categories (9 expense + 5 income) with `user_id = null` and `is_default = true`
- [ ] Settings page displays all categories (defaults + user's own) grouped by type
- [ ] User can create a custom category with name, type (income/expense), and color
- [ ] User can edit their own categories (name, color)
- [ ] User cannot edit or delete default categories
- [ ] User cannot delete a category that has linked transactions (validated server-side)
- [ ] Policy prevents users from accessing other users' categories
- [ ] Sidebar navigation in settings includes Categories tab
- [ ] Feature tests cover all CRUD operations, policy checks, and deletion protection

---

## Phase 2: Transactions

**User stories**:
- As a user, I want to add an income or expense transaction with amount, category, date, and optional description
- As a user, I want to see a list of all my transactions with pagination
- As a user, I want to filter transactions by type (income/expense), category, and date range
- As a user, I want to search transactions by description
- As a user, I want to edit a transaction so I can fix mistakes
- As a user, I want to delete a transaction I no longer need
- As a user, I want to create and edit transactions via a modal dialog without leaving the list page

### What to build

A complete transactions CRUD: database table, model with policy, a `/transactions` page showing a paginated list with filters (type, category, date range) and description search. Creating and editing happens in a modal dialog over the list. Transactions link to a category and optionally to a subscription (nullable FK, used in Phase 4). Amount is stored in halere, displayed in CZK.

### Acceptance criteria

- [ ] Transactions migration creates the table with all columns per schema including nullable `subscription_id` FK
- [ ] Transaction model with factory, policy, and form request validation
- [ ] Transactions page lists all user's transactions with pagination
- [ ] Filters work: type (income/expense), category, date range
- [ ] Search by description works
- [ ] Modal dialog for creating a new transaction with all fields
- [ ] Modal dialog for editing an existing transaction
- [ ] Delete transaction with confirmation
- [ ] Amount input in CZK (converted to/from halere transparently)
- [ ] Policy prevents users from accessing other users' transactions
- [ ] Sidebar navigation includes Transactions link
- [ ] Feature tests cover CRUD, filtering, search, policy, and validation

---

## Phase 3: Subscriptions

**User stories**:
- As a user, I want to add a subscription with name, amount, frequency, category, start date, and optional URL/description
- As a user, I want to see a list of all my subscriptions (active and cancelled)
- As a user, I want to edit a subscription so I can update the amount or details
- As a user, I want to cancel a subscription (sets `cancelled_at`) so it stops generating transactions
- As a user, I want to delete a subscription only if it has no linked transactions
- As a user, I want to manage subscriptions via a modal dialog

### What to build

A complete subscriptions CRUD: database table, model with policy, a `/subscriptions` page showing all subscriptions (active and cancelled, visually distinguished). Creating and editing via modal dialog. Cancel action sets `cancelled_at` and stops future billing. Delete only allowed if no linked transactions exist. `next_billing_date` is calculated from `started_at` and `frequency` on creation.

### Acceptance criteria

- [ ] Subscriptions migration creates the table with all columns per schema
- [ ] Subscription model with factory, policy, and form request validation
- [ ] Subscriptions page lists all user's subscriptions with active/cancelled distinction
- [ ] Modal dialog for creating a new subscription
- [ ] Modal dialog for editing an existing subscription
- [ ] Cancel action sets `cancelled_at` and visually marks as cancelled
- [ ] Delete only works when subscription has no linked transactions (validated server-side)
- [ ] `next_billing_date` is set correctly based on `started_at` and frequency
- [ ] Policy prevents users from accessing other users' subscriptions
- [ ] Sidebar navigation includes Subscriptions link
- [ ] Feature tests cover CRUD, cancellation, deletion protection, and policy

---

## Phase 4: Subscription Billing Command

**User stories**:
- As a user, I want my active subscriptions to automatically generate expense transactions on their billing date
- As a user, I want the next billing date to advance after each generated transaction
- As a user, I want auto-generated transactions to be linked back to their subscription

### What to build

A scheduled Artisan command that runs daily, finds all active subscriptions (not cancelled) where `next_billing_date <= today`, creates an expense transaction for each (linked via `subscription_id`, using the subscription's category and amount), and advances `next_billing_date` by the subscription's frequency. The command must handle edge cases: multiple missed billing dates (e.g., server was down), and must be idempotent within a single day.

### Acceptance criteria

- [ ] Artisan command exists and is registered in the scheduler to run daily
- [ ] Command finds active subscriptions with `next_billing_date <= today`
- [ ] Command creates expense transactions with correct amount, category, date, and `subscription_id`
- [ ] Command advances `next_billing_date` by frequency (monthly +1 month, yearly +1 year)
- [ ] Command handles multiple missed billing dates (generates one transaction per missed period, advancing each time)
- [ ] Cancelled subscriptions are skipped
- [ ] Command is idempotent — running twice on the same day does not create duplicates
- [ ] Feature tests cover normal billing, missed periods, cancelled subscriptions, and idempotency

---

## Phase 5: Dashboard

**User stories**:
- As a user, I want to see my total income, expenses, and balance for the current month
- As a user, I want to see a bar chart of income vs. expenses over the last 6 months
- As a user, I want to see a donut chart of expenses by category for the current month
- As a user, I want to see upcoming subscription payments
- As a user, I want to see my most recent transactions

### What to build

Replace the placeholder dashboard with real financial data. The controller aggregates: current month summary (total income, total expenses, balance), 6-month income/expense history for bar chart, current month expenses grouped by category for donut chart, upcoming subscription payments (active subscriptions ordered by `next_billing_date`), and last 5-10 transactions. Frontend uses shadcn/ui chart components (built on Recharts) for bar and donut charts, plus card components for summary and lists.

### Acceptance criteria

- [ ] Dashboard controller returns all required aggregated data
- [ ] Monthly summary card shows total income, total expenses, and balance (income - expenses) for current month
- [ ] Bar chart displays income vs. expenses for the last 6 months
- [ ] Donut chart displays expense breakdown by category for current month, using category colors
- [ ] Upcoming payments section shows active subscriptions ordered by next billing date
- [ ] Recent transactions section shows the last 5-10 transactions
- [ ] Dashboard handles empty state gracefully (new user with no data)
- [ ] Charts use shadcn/ui chart components with dark mode support
- [ ] Feature tests cover dashboard data aggregation with various data scenarios
