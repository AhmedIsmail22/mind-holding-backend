# Bitcodak — Backend API (Phase 1)

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

**Any "update" route that accepts a file upload (media) uses `POST`, never
`PUT`/`PATCH`** — PHP does not populate `$_FILES` on PUT requests, so a
multipart file update has to go through POST. `Settings` is the first
example (`app/Models/Setting.php`, a one-row singleton via `Setting::current()`).

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
  (`permission:xxx`), applied per route group inside `routes/api/v1/admin.php`
  (not blanket at the `admin` prefix level — the login route lives under this
  same prefix and must stay reachable unauthenticated). Every translatable
  field is returned in both locales for editing. Roles: `Administrator`
  (everything), `Content editor` (content: services, solutions, work, pages,
  FAQs, media — not settings or users), `Sales` (view requests + change their
  status only).

## Packages & conventions

- `spatie/laravel-translatable` — translatable attributes declared as
  `protected $translatable = [...]` on the model; stored as JSON columns
  `{ "ar": "...", "en": "..." }`. An **ordered list** of translatable items
  (service deliverables, FAQ-adjacent step lists, solution features, budget
  options, ...) is stored as a plain JSON array of `{ar, en}` objects (not
  spatie-translatable — just a cast `array` column), e.g.
  `[{"ar": "...", "en": "..."}, {"ar": "...", "en": "..."}]`. Resources
  resolve this to the current locale for public output and leave it as-is
  for admin output, same as every other translatable field.
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

## API spec (OpenAPI)

`docs/openapi.json` is generated by `dedoc/scramble` from the routes, resources, and form requests. Regenerate it after any route, resource, or request change:

```
php artisan scramble:export --path=docs/openapi.json
```

`tests/Feature/OpenApi/OpenApiSpecTest.php` fails when the committed file is stale, when a public route is missing, or when a success response lacks the `{success, message, data}` envelope. Scramble cannot see the exception handler, so `app/Support/OpenApi/ApiDocumentation.php` adds the error responses, the bearer scheme, and the `Accept-Language` header. Request rules must not query the database, or the spec depends on the data present when it is generated.

## Build plan (module by module, committed separately)

Each module ships complete: migrations, models, DTOs, services, form
requests, resources, routes, seeders, Pest tests — before moving to the next.
Status tracked here as the single source of truth for build progress.

- [x] **0. Foundation** — package installs, API routing skeleton, response
      helpers, central exception→JSON mapping, `ResolveLocale` middleware,
      base Controller, roles & permissions seeder, Postman/OpenAPI doc
      scaffold.
- [x] **1. Auth & Users** — admin login/logout/me via Sanctum, Users CRUD
      (Administrator only), role assignment.
- [x] **2. Settings** — single settings store: company info, logo, phone +
      WhatsApp (Egypt/Dubai) + Gulf country list, emails, address, social
      links, lead notification email, budget list, start-timing list, GA ID.
      Public read endpoint (subset) + admin read/update.
- [x] **3. Solution industries, Services & FAQs** — industries CRUD; services
      CRUD in two groups (Software/Marketing). Introduces the shared `Faq`
      model (polymorphic `faqable`, nullable = general FAQ) since services
      need it first — general-FAQ admin/public endpoints ship here too, so
      module 6 only adds Home-page FAQ *selection*, not FAQ CRUD. Module 4
      reuses the same model for solution-linked FAQs via its own nested
      routes.
- [x] **4. Solutions** — solutions CRUD, flagship flag + ordering, publish
      /hide, features grouped by audience, demo link + test credentials,
      related solutions (pivot to services too), per-solution FAQs (nested
      on the module 3 `Faq` model), media (mockups).
- [x] **5. Work / Projects** — projects CRUD, hide-client-name option,
      technologies, images, live link; section auto-hides (public endpoint
      returns empty) when nothing published. Ships with **zero seeded
      rows** per SRS Decision #4 (no fictional projects) — the empty state
      is the correct initial state, not a gap. Also backfills the
      `service_project` pivot deferred from module 3, so Service's public
      detail now returns `related_solutions` and `related_projects`.
- [x] **6. Site content** — Home sections (enable/hide/reorder + content:
      hero, stats, differentiators, process steps, tech logos); static Pages
      (About/Privacy/Terms). Home is one singleton row (`home_content`), like
      Settings. Stats ship **empty** (SRS: real numbers only). Process steps
      mirror the SRS §9 timeline. Technologies ship **empty**: listing a stack
      nobody confirmed would misrepresent the company, same reasoning as
      projects. The 10 home sections are a fixed set (toggle + order only).
- [x] **7. Leads & notifications** — quote/demo/callback request endpoints,
      rate limiting (5/hr/device), reCAPTCHA v3 + honeypot, source/UTM/
      language capture, status workflow, email notification, dashboard
      aggregate stats endpoint. Decisions: honeypot hits are stored as
      `spam` and answered as success (no signal to bots); reCAPTCHA fails
      closed when no secret is set; mail failures are logged and never lose
      the lead; the notification is a plain-text body in `NewLeadMail`
      (`Mailable::html`, escaped, no Blade). Two permissions were added to the
      seeder, admin-only: `requests.assign` and `requests.delete`. Note: the
      SRS does not give the expected response time, so `config/leads.php`
      holds a placeholder to confirm with the client.
- [x] **8. SEO & redirects** — per-entity SEO meta (title/description/share
      image) polymorphic, sitemap data endpoint, legacy `mindholding.net`
      redirects table + endpoint (301 list for the frontend to serve).
      Decisions: SEO attaches to services, solutions, and pages (morph) and to
      five route keys (home, services, solutions, work, contact); when an
      entity has no SEO row, its own name and description are the fallback.
      Share images are converted to 1200x630 WebP. The sitemap is data only:
      alternates are locale-prefixed for hreflang, `/work` is omitted while no
      project is published, and project URLs are assumed to be `/work/{id}`
      (to confirm with the frontend). No redirect rows are seeded because the
      old URLs are not in the SRS. Redirect paths are unique among active rows,
      validated in the request, because MySQL allows repeated NULLs in a unique
      index.

Stop and ask only when the SRS is ambiguous/contradictory or something only
the client can supply (real contact numbers, credentials, final copy) is
needed — otherwise keep building modules in order without waiting.

## Pre-launch checklist

- `APP_URL` in production must be the real API host (for example `https://api.bitcodak.com`). Media URLs are built from it, so images won't resolve otherwise.
- Run `php artisan media:record-dimensions` once on production. It backfills the width and height that public image objects return. Uploads made after this release record their own dimensions.
- The interactive API docs stay disabled in production. Scramble serves `/docs/api` and `/docs/api.json` only in the `local` environment (tested in `DocsAccessTest`). The committed `docs/openapi.json` is the contract for the frontend. Regenerate it with `php artisan scramble:export --path=docs/openapi.json` after any API change.
- Set `PUBLIC_SITE_URL=https://bitcodak.com` (the default). `mindholding.net` must 301 to the primary domain at the host level.
- Set `RECAPTCHA_SECRET_KEY`. Until it is set, every lead form is rejected (fail closed) — in every environment except `local`/`testing`, where a missing secret is allowed through so the frontend can be exercised manually without real keys. The same local/testing-only allowance covers a literal `recaptcha_token: "test-token"` (`GoogleRecaptchaVerifier::PLACEHOLDER_TOKEN`), for a frontend dev build that hasn't wired up the real widget yet; outside local/testing that token is always rejected, even if a secret is configured.
- Set real SMTP credentials. `MAIL_MAILER=log` means lead notifications are written to the log only.
- Confirm the expected response time in `config/leads.php` with the client.
- Client content still outstanding: stats (none seeded), projects, and technology logos. Mobile technologies are pending the client's choice of Flutter or React Native, or both.
- Technology category labels in Arabic (الخلفية, الويب, السحابة والتشغيل, التسويق والتحليلات) are our translations and need review.
