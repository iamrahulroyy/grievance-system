# Public Grievance Management System

A production-grade REST API where citizens file complaints and administrators manage, assign, and resolve them through a structured workflow — built with Laravel.

## Architecture

```
Request → routes/api.php
  │
  ├── Middleware (auth:sanctum, throttle)
  ├── FormRequest (validates input → 422 if invalid)
  ├── Policy (authorizes action → 403 if denied)
  ├── Controller (thin — delegates to Service)
  ├── Service (business logic, state machine)
  ├── Model / Eloquent (DB queries, relationships)
  │     └── Observer (auto-logs to activities table)
  ├── Event → Queued Listener → Notification (email)
  └── API Resource (shapes JSON response)
```

## Tech Stack

- **Backend:** PHP 8.3, Laravel 13
- **Database:** MySQL
- **Auth:** Laravel Sanctum (token-based)
- **Queue:** Database driver (swappable to Redis)
- **API Docs:** Scramble (auto-generated OpenAPI at `/docs/api`)
- **Email:** SMTP (Mailpit for local dev)
- **Tests:** PHPUnit — 34 feature tests, 80 assertions

## Features

- **Token authentication** — register, login, logout, token revocation via Sanctum
- **Role-based access** — citizen vs admin with PHP 8.1 backed enums
- **Row-level authorization** — Policies ensure citizens only access own data
- **Complaint lifecycle** — state machine: `open → in_progress → resolved | rejected`, illegal transitions rejected with 422
- **Admin assignment** — assign complaints to any admin, validates target is actually an admin
- **Comments** — threaded conversation between citizen and admin on each complaint
- **File attachments** — upload (jpeg/png/gif/pdf/doc, max 5MB), download with auth check, delete
- **Filtering & search** — `?status=open`, `?search=keyword`, `?sort=-created_at`, `?assigned_to=me`
- **Pagination** — all list endpoints return `data`, `links`, `meta`
- **Event-driven notifications** — status change dispatches event → queued listener sends email to citizen
- **Audit trail** — model Observer logs every create/update/delete with old/new value diffs
- **Soft deletes** — complaints are never permanently removed
- **Rate limiting** — login endpoint throttled to prevent brute force
- **Eager loading** — `->with()` prevents N+1 queries, `whenLoaded()` in Resources

## API Endpoints

| Method | Endpoint | Auth | Purpose |
|---|---|---|---|
| POST | `/api/auth/register` | Public | Register citizen |
| POST | `/api/auth/login` | Public | Login, get token |
| POST | `/api/auth/logout` | Token | Revoke token |
| GET | `/api/auth/me` | Token | View own profile |
| GET | `/api/admins` | Token | List admin users |
| GET | `/api/complaints` | Token | List (citizen=own, admin=all) |
| POST | `/api/complaints` | Token | File complaint |
| GET | `/api/complaints/{id}` | Owner/Admin | View single |
| PATCH | `/api/complaints/{id}` | Admin | Update status |
| DELETE | `/api/complaints/{id}` | Owner(open)/Admin | Soft delete |
| POST | `/api/complaints/{id}/assign` | Admin | Assign to admin |
| GET | `/api/complaints/{id}/comments` | Owner/Admin | List comments |
| POST | `/api/complaints/{id}/comments` | Owner/Admin | Add comment |
| POST | `/api/complaints/{id}/attachments` | Owner/Admin | Upload file |
| GET | `/api/complaints/{id}/activity` | Owner/Admin | Audit trail |
| GET | `/api/attachments/{id}` | Owner/Admin | Download file |
| DELETE | `/api/attachments/{id}` | Admin | Delete file |

## Setup

```bash
# Clone
git clone https://github.com/iamrahulroyy/grievance-system.git
cd grievance-system

# Install dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=grievance_system
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations + seed test data
php artisan migrate --seed

# Start the server
php artisan serve
```

Open `http://localhost:8000/docs/api` for interactive API documentation.

### Email notifications (local dev)

```bash
# Install Mailpit
brew install mailpit

# Start Mailpit
mailpit

# Update .env
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025

# Run the queue worker in a separate terminal
php artisan queue:work
```

Open `http://localhost:8025` to see emails when complaint status changes.

## Seeded Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Citizen | citizen@example.com | password |

## Run Tests

```bash
php artisan test
```

```
Tests:    34 passed (80 assertions)
Duration: <1s
```

Tests cover: auth flow, registration validation, role-based access, row-level authorization, state machine transitions, event dispatch, filtering, pagination, admin assignment validation, and activity logging.

## Project Structure

```
app/
├── Enums/           ComplaintStatus, UserRole
├── Events/          ComplaintStatusChanged
├── Http/
│   ├── Controllers/ AuthController, ComplaintController, CommentController, AttachmentController
│   ├── Requests/    RegisterRequest, LoginRequest, StoreComplaintRequest, UpdateComplaintStatusRequest, StoreCommentRequest
│   └── Resources/   UserResource, ComplaintResource, CommentResource, AttachmentResource, ActivityResource
├── Listeners/       SendStatusChangedNotification (queued)
├── Models/          User, Complaint, Comment, Attachment, Activity
├── Notifications/   ComplaintStatusChangedNotification (email)
├── Observers/       ComplaintObserver (audit log)
├── Policies/        ComplaintPolicy
├── Providers/       AppServiceProvider (rate limits + Scramble auth)
└── Services/        ComplaintService (state machine + business logic)

database/
├── migrations/      11 migration files
├── factories/       UserFactory, ComplaintFactory, CommentFactory
└── seeders/         DatabaseSeeder (1 admin + 4 citizens + 16 complaints)

tests/Feature/       AuthTest, ComplaintTest, CommentTest (34 tests)
```

## Design Decisions

- **Service layer** — business logic (state machine, validation) lives in `ComplaintService`, not controllers. Controllers are thin glue.
- **PHP 8.1 enums** — `ComplaintStatus` and `UserRole` are backed enums with `canTransitionTo()` method. Type-safe, no magic strings.
- **Policies over inline checks** — authorization is declarative (`Gate::authorize('view', $complaint)`), not `if ($user->id !== $complaint->user_id)` scattered in controllers.
- **FormRequests over controller validation** — validation runs before the controller method. Reusable, testable, keeps controllers clean.
- **API Resources over raw model output** — explicit JSON contract. `whenLoaded()` prevents accidental N+1 queries.
- **Observer for audit trail** — model events fire automatically on every save. No manual logging calls needed anywhere.
- **Queued notifications** — status change emails don't block the API response. Listener implements `ShouldQueue`, processed by the queue worker.
- **Soft deletes** — complaints are never permanently lost. `SoftDeletes` trait adds `deleted_at` column, queries auto-exclude deleted rows.
- **Local storage (swappable)** — attachments use Laravel's `Storage` facade with `local` disk. Production swap to `s3` is a one-word config change.
