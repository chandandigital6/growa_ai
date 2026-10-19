# Growya updated project

This is the updated application source from the uploaded ZIP. Read `docs/MODULE_STATUS.md`, `docs/PERMISSIONS.md`, `docs/API.md` and `docs/VALIDATION.md` before rollout.

## Update an existing installation

1. Back up the database and uploaded media; test against a staging copy first.
2. Merge the source changes. Retain the existing `.env`, `APP_KEY`, media and database. Do not replace an existing APP_KEY.
3. Run `composer install` using the included lock file. The original archive had no lock; this update locks the dependency versions used during validation.
4. Run `php artisan optimize:clear`.
5. Run `php artisan migrate --force`. The new catalog migration adds nullable business_id fields; existing catalog IDs remain global masters. Never use migrate:fresh on existing data.
6. Run `php artisan db:seed --class=BusinessPermissionSeeder --force`. This adds permission definitions without changing your current global roles/users.
7. Run `php artisan storage:link` if the link is not already present.
8. Prebuilt assets are included. To rebuild, use `npm ci` and `npm run build`.
9. Run `php artisan test` in your test/staging environment. If you use config/route/view caching in production, regenerate caches after validation.

No new super-admin account or password is created. Existing active super_admin roles keep platform access. The owner_id in a business is authoritative for owner access. Open a business, then Team & permissions to assign staff access, and Categories / Attributes for catalog settings.

## Fresh installation

Use PHP 8.3+ and the extensions required by Composer, a supported MySQL database, and Node for rebuilding assets. Copy `.env.example` to `.env`, set your database/mail/storage settings, run `composer install`, `php artisan key:generate`, `php artisan migrate`, then the intended seeders. The original India seeder must be run to populate business location dropdowns. A fresh install needs a deliberately assigned super_admin; this package does not seed a privileged demo account.

## Included

- Application source, migrations, dependency locks and compiled frontend assets.
- Business permission catalog and reusable server-side authorization middleware.
- Staff create/attach/update/remove and permission-change audit history.
- Business catalog management screens and scoped product/service category and attribute validation.
- Personal/business registration choice and the business application redirect.
- Versioned API routes, endpoint inventory and an importable Postman collection.
- Authorization and module smoke regression tests, module status and next-work report.

## Important limits

The API adapter uses the existing domain validators. A successful mutation returns a message and target location; use the corresponding list/show endpoint for the resource payload. CSV imports return structured invalid-row data. A web-only session error-report download remains session-bound. Existing in-app notification, manual payment-proof and delivery-tracking features are not provider-backed push, settlement or delivery OTP systems. See the validation report for the environment actually tested.

The uploaded archive omitted artisan, .env.example, dependency lockfiles and PHPUnit configuration; portable versions have been added. Runtime caches, logs, sessions, vendor/node_modules and development databases are excluded from this deliverable. Original public assets and storage/app content are preserved.
