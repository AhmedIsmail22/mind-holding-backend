# MIND Holding — Backend API (Phase 1)

This repo is the **backend REST API only**. No Blade views, no frontend. The
public Next.js site and the admin dashboard are separate clients built later
against this API. Source of truth for requirements: `Phase 1 Requirements —
Software Home Website.md` (the SRS) in the repo root.

Known deviations from the brief, accepted as pragmatic: the installed
framework is **Laravel 12** (brief said 11 — 12 was already scaffolded and is
backward-compatible for our purposes), and local dev DB is **MariaDB 10.4**
via XAMPP (brief said MySQL 8 — same SQL dialect for everything this project
uses: JSON columns, utf8mb4, standard indexes). Both the dev DB (`mindholding`)
and test DB (`mindholding_test`) run on this MariaDB instance so tests exercise
real SQL behavior instead of SQLite.

## Architecture

Strict layering, every module follows it the same way:

```
Route -> Controller -> FormRequest (validation) -> DTO (plain data) -> Service (business logic) -> Model
                                                                              |
                                                                              v
                                                                        API Resource (output)
```

- **Controllers** are thin: resolve the request, build a DTO, call one service
  method, wrap the result with `ApiResponse`. No query building, no business
  rules in controllers.
- **Form Requests** own validation rules + authorization (`can()` checks via
  spatie/laravel-permission where relevant) and translate validated arrays
  into DTOs via a `toDto()` method.
- **DTOs** are plain `final readonly class` value objects under
  `app/DTOs/{Module}/...`. No framework dependencies. Built from a Form
  Request's validated data via a static `fromRequest()`/`fromArray()`
  factory.
- **Services** under `app/Services/{Module}/...` hold the actual logic:
  persistence orchestration, transactions, media handling, notifications.
  One public method per use case (e.g. `create`, `update`, `publish`,
  `reorder`). Services take DTOs in, return Models/Collections out.
- **Models** are Eloquent, translatable fields use
  `Spatie\Translatable\HasTranslations`, media uses
  `Spatie\MediaLibrary\InteractsWithMedia`. No business logic beyond
  relationships, casts, scopes, and accessors.
- **API Resources** under `app/Http/Resources/...` are the only place that
  shapes JSON output. Public resources return the single resolved locale;
  Admin resources return both `ar`/`en` for every translatable field so the
  dashboard can edit side by side.
- **No Repository pattern.** Services talk to Eloquent models directly.

## Response & error format

All responses (success and error) go through `app/Support/ApiResponse.php`
(static helper), used via the `app/Http/Controllers/Controller.php` base
class:

```jsonc
// success
{ "success": true, "message": "...", "data": { ... } }

// paginated success
{ "success": true, "message": "...", "data": [...], "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 68 } }

// error
{ "success": false, "message": "...", "errors": { "field": ["..."] } } // errors omitted when not applicable
```

Exceptions are normalized to this shape centrally in
`bootstrap/app.php` (`withExceptions`) — controllers never catch framework
exceptions themselves. HTTP status codes: 422 validation, 401
unauthenticated, 403 unauthorized, 404 not found, 429 throttled, 500 generic
(message hidden in production).

## Routing

Everything lives under `/api/v1`, split in two groups (see
`routes/api/v1/public.php` and `routes/api/v1/admin.php`, both included from
`routes/api.php`):

- **`public/*`** — no auth. Locale is resolved by the `ResolveLocale`
  middleware from the `Accept-Language` header (`ar` default, `en` the only
  other supported value); response content matches that one locale.
  Cacheable GETs use response caching where noted per module. Lead-submission
  endpoints are rate limited (5/hour, see Leads module) and reCAPTCHA v3 +
  honeypot protected.
- **`admin/*`** — `auth:sanctum` + spatie permission middleware
  (`permission:xxx`). Every translatable field is returned in both locales
  for editing. Roles: `Administrator` (everything), `Content editor`
  (content: services, solutions, work, pages, FAQs, media — not settings or
  users), `Sales` (view requests + change their status only).

## Packages & conventions

- `spatie/laravel-translatable` — translatable attributes declared as
  `protected $translatable = [...]` on the model; stored as JSON columns
  `{ "ar": "...", "en": "..." }`.
- `spatie/laravel-medialibrary` — every model that takes images implements
  `HasMedia` + `InteractsWithMedia`, registers a `webp` conversion (and a
  `thumb` webp conversion where a listing view needs one). Original upload
  kept; served conversions are WebP.
- `spatie/laravel-permission` — roles/permissions seeded by
  `RolesAndPermissionsSeeder` (Foundation module). Permission names are
  `{resource}.{action}` e.g. `solutions.manage`, `requests.view`,
  `requests.manage-status`, `settings.manage`, `users.manage`.
- Every table using soft-delete per the SRS ("deletion is soft and
  recoverable") uses `SoftDeletes`.
- Seed data: placeholders for solutions/services/pages content are seeded
  with an explicit `is_draft = true` flag so nothing invented looks live.
  **Never** seed fake clients, projects, testimonials, or stats — those stay
  empty until a real admin enters them (this is a hard SRS rule, see
  Decisions #4).

## Testing

Pest, feature tests per endpoint covering the happy path, validation
failures, permission boundaries (wrong role gets 403), rate limiting, and
reCAPTCHA (mocked via a bound fake — see Leads module). Tests run against
real MariaDB (`mindholding_test`), `RefreshDatabase` trait, not SQLite — this
project leans on JSON/translatable columns and we want parity with
production SQL behavior.

## Build plan (module by module, committed separately)

Each module ships complete: migrations, models, DTOs, services, form
requests, resources, routes, seeders, Pest tests — before moving to the next.
Status tracked here as the single source of truth for build progress.

- [x] **0. Foundation** — package installs, API routing skeleton, response
      helpers, central exception→JSON mapping, `ResolveLocale` middleware,
      base Controller, roles & permissions seeder, Postman/OpenAPI doc
      scaffold.
- [ ] **1. Auth & Users** — admin login/logout/me via Sanctum, Users CRUD
      (Administrator only), role assignment.
- [ ] **2. Settings** — single settings store: company info, logo, phone +
      WhatsApp (Egypt/Dubai) + Gulf country list, emails, address, social
      links, lead notification email, budget list, start-timing list, GA ID.
      Public read endpoint (subset) + admin read/update.
- [ ] **3. Solution industries & Services** — industries CRUD; services CRUD
      in two groups (Software/Marketing) with per-service FAQs.
- [ ] **4. Solutions** — solutions CRUD, flagship flag + ordering, publish
      /hide, features grouped by audience, demo link + test credentials,
      related solutions, per-solution FAQs, media (mockups).
- [ ] **5. Work / Projects** — projects CRUD, hide-client-name option,
      technologies, images, live link; section auto-hides (public endpoint
      returns empty) when nothing published.
- [ ] **6. Site content** — Home sections (enable/hide/reorder + content:
      hero, stats, differentiators, process steps, tech logos); static Pages
      (About/Privacy/Terms); general FAQs.
- [ ] **7. Leads & notifications** — quote/demo/callback request endpoints,
      rate limiting (5/hr/device), reCAPTCHA v3 + honeypot, source/UTM/
      language capture, status workflow, email notification, dashboard
      aggregate stats endpoint.
- [ ] **8. SEO & redirects** — per-entity SEO meta (title/description/share
      image) polymorphic, sitemap data endpoint, legacy `mindholding.net`
      redirects table + endpoint (301 list for the frontend to serve).

Stop and ask only when the SRS is ambiguous/contradictory or something only
the client can supply (real contact numbers, credentials, final copy) is
needed — otherwise keep building modules in order without waiting.
