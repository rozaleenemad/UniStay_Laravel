# StayUni — Student Housing Platform

A Laravel full-stack platform connecting university students with nearby housing owners, with admin oversight and verification.

## Roles

| Role    | Description |
|---------|-------------|
| Admin   | Verifies owners, approves/rejects properties |
| Owner   | Lists properties, goes through verification |
| Student | Browses approved properties |

---

## Local Setup

### 1. Clone & install dependencies

```bash
git clone <your-repo-url>
cd project-unistay

composer install
npm install && npm run build
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` and fill in:
```
DB_DATABASE=stayuni
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=your_strong_password_here
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Create the admin account

**⚠️ Never use the seeder for production admin.** Use this command instead:

```bash
php artisan admin:create
```

It will ask you for email, name, and password interactively. Nothing is stored in code.

### 5. (Optional) Seed test users for local development only

```bash
php artisan db:seed --class=TestUsersSeeder
```

This creates:
- `owner@stayuni.test` / `password`
- `student@stayuni.test` / `password`

### 6. Run the app

```bash
php artisan serve
```

---

## Storage

Link storage so uploaded images are accessible:

```bash
php artisan storage:link
```

---

## Production Checklist

Before deploying to production:

```bash
# 1. Set these in .env:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# 2. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 3. Create admin (interactive — password never touches code)
php artisan admin:create

# 4. NEVER run:
# php artisan db:seed                     ← runs AdminUserSeeder (reads from .env, safe)
# php artisan db:seed --class=TestUsersSeeder  ← local dev ONLY
```

---

## Security Notes

- `role` and `status` fields are NOT in `$fillable` — preventing mass-assignment attacks
- Admin routes are protected by the `admin` middleware (role check at route level)
- Student routes are protected by the `student` middleware
- Owner routes require `owner.active` middleware (checks pending/rejected status)
- Owners start as `pending` — admin must approve before they can list properties
- Properties go back to `pending` after any edit — admin must re-approve
- File uploads are validated: jpg/jpeg/png only, max 4MB per image
- `.env` is excluded from git via `.gitignore`

---

## Social Login (Google OAuth)

To enable "Continue with Google":

```bash
composer require laravel/socialite
```

Add to `.env`:
```
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

Then create `SocialAuthController` with `redirectToGoogle()` and `handleGoogleCallback()` methods.

---

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Database:** MySQL (SQLite for local dev)
- **Frontend:** Blade, Tailwind CSS
- **Auth:** Laravel Breeze
- **Storage:** Laravel Storage (local/S3)
