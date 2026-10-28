# A Backend

Backend for **A Radio**: a signed JSON API for the mobile app, the public website and a
legacy admin panel for editors. Plain PHP 8.2+, no framework and no runtime dependencies.

## Features

- Mobile API under `/api/v1` (top charts, chart dates, chart by date, weekly radio program,
  player and app settings).
- HMAC-SHA256 request signing with timestamp window and nonce replay protection.
- Per-IP rate limiting (default 120 requests/minute).
- Public website (top chart, radio program, posts, live stream page).
- Admin panel for charts, songs, artists, presenters, programs, posts and promotions,
  protected by session authentication, CSRF tokens and login throttling.
- Configuration and secrets only from `.env`.

## Folder layout

| Path | Purpose |
| --- | --- |
| `public/` | Web root: site pages, assets, uploads (`rsc/`), API front controller (`api/index.php`), website AJAX (`ajax/`) |
| `src/` | Application code, namespace `App\` (PSR-4, own autoloader in `src/autoload.php`) |
| `src/Http` | Request, JsonResponse, Router, ApiKernel, middleware, HTTP exceptions |
| `src/Security` | Request signature, nonce store, rate limiter, upload validator, security headers |
| `src/Repository` | SQL for top charts, radio programs and player data (prepared statements only) |
| `src/Controller/Api` | Mobile API controllers |
| `src/Admin` | Admin session, authentication, password hashing, CSRF, endpoint access policy |
| `admin/` | Legacy admin panel (served as `/admin`, outside the web root) |
| `database/schema.sql` | Database structure (no data) |
| `storage/` | Runtime data: nonces, rate counters, logs (git-ignored) |
| `tests/` | Plain-PHP test suite (`php tests/run.php`) |
| `docs/` | nginx and Apache examples |

## Architecture

Mobile API request flow:

```
public/api/index.php  (front controller, loads src/bootstrap.php)
  -> ApiKernel
     -> RateLimitMiddleware   (429 when over the per-IP limit)
     -> SignatureMiddleware   (401 bad_signature)
     -> Router                (404 / 405)
        -> Controller         (validates input, throws ValidationException / NotFoundException)
           -> Repository      (PDO prepared statements)
  <- JsonResponse             (exceptions mapped to the error envelope in one place)
```

Admin request flow: every admin page and endpoint includes `admin/bootstrap.php`, which loads
the shared bootstrap, starts the hardened session and, for `admin/webservice/*`, applies
`AccessPolicy` (authentication, POST and CSRF checks) before the endpoint code runs.

`src/bootstrap.php` is shared by all entry points: autoloader, `.env`, error and exception
handlers (log to `storage/logs`, generic message to the client) and security headers.

## Mobile API

All endpoints are `GET` and require signing. Responses use one envelope:

- success: `{"ok": true, "data": ...}`
- failure: `{"ok": false, "error": {"code": "<snake_case>", "message": "..."}}` with status
  400, 401, 404, 405, 429 or 500.

| Endpoint | Data |
| --- | --- |
| `GET /api/v1/top-charts` | `[{num_1_song, num_1_songs_artist, date}]` (date `d/m/Y`) |
| `GET /api/v1/top-charts/dates` | `[{key, label, date}]` (`key`/`label` `d/m/Y`, `date` `Y-m-d`) |
| `GET /api/v1/top-charts/{YYYY-MM-DD}` | `{songs: [{num, name, artists, lastChartNum, onChartLong}], date_formatted, date}`; 400 `invalid_date`, 404 `not_found` |
| `GET /api/v1/radio-programs[?radio=<id>]` | `{<day 0-6>: [{dj: [{name, image}], time: {start, end}}]}` or `null` when no program is active; station id defaults to 1 |
| `GET /api/v1/player` | `{title, radio: {aradio: {streaming_url, straming_url, frequency, wave, sms_number}}}` |
| `GET /api/v1/app-data` | App settings (about, contact, social, radio, links) plus `popup` list |

Field names match the previous endpoints, so existing screens keep working.

## Request signing

Every request carries `X-App-Id`, `X-Timestamp` (Unix seconds), `X-Nonce` (16-64 chars of
`[A-Za-z0-9_-]`) and `X-Signature`:

```
canonical = METHOD \n PATH \n QUERY \n TIMESTAMP \n NONCE \n hex(sha256(body))
signature = base64(HMAC-SHA256(secret, canonical))
```

`QUERY` is the query string with parameters sorted by key and RFC 3986 encoded (`%20` for
spaces), empty if none. The server checks: known app id (secrets in `MOBILE_APP_KEYS`),
`|now - timestamp| <= 300`, nonce not used in the last 600 s for that app id, then
`hash_equals`. The nonce is claimed atomically after the signature matches, so forged
requests cannot fill the nonce store and parallel replays fail. Any failure returns the same
401 `bad_signature`.

Nonces and rate counters are small files under `storage/` (names are SHA-256 hashes, expiry
stored as file mtime, expired files are garbage-collected). No extra tables are needed.

**Limits:** a key shipped inside a mobile app can be extracted. Signing stops casual
scraping and replay and lets you rotate keys (add the new key to `MOBILE_APP_KEYS`, ship the
app, then remove the old one), but it does not identify users. Per-user authentication
(for example short-lived tokens issued after login) is the stronger next step.

## Security

- Secrets (DB, app keys, SMTP) only in `.env`; `.env.example` holds placeholders.
- PDO with exceptions, native prepared statements (emulation off) and `utf8mb4`.
- Errors are logged to `storage/logs`; clients only see a generic message.
- Headers on every response: Content-Security-Policy (with `frame-ancestors`),
  X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, and HSTS on
  HTTPS.
- Admin login: `password_hash` / `password_verify`; legacy md5/sha1/plain values are
  accepted once and rehashed on login; throttled per IP and username (5 tries / 15 min).
- Admin session: `session_regenerate_id` on login, HttpOnly + Secure (on HTTPS) +
  SameSite=Strict cookie, strict mode, idle timeout (`ADMIN_IDLE_TIMEOUT`), logout destroys
  the session.
- Admin endpoints: reads need a session; writes need POST + session + CSRF token
  (`X-CSRF-Token` header, sent automatically by `admin/assets/js/main.js`).
- Output in templates is escaped with `e()`; JSON responses send proper content types.
- Uploads: MIME checked from file content (`finfo`), extension whitelist, size and
  dimension limits, random file names, stored extension derived from the content; script
  execution is denied in `public/rsc/` (see `public/rsc/.htaccess` and the nginx example).
- Debug pages and unused or duplicate third-party libraries were removed.

## Setup

Requirements: PHP 8.2+ (`pdo_mysql`, `fileinfo`, `mbstring`), MySQL 5.7+ or MariaDB 10.3+,
Apache with `mod_rewrite` or nginx.

1. Create the database and import the structure:
   `mysql -u root -p < database/schema.sql` (creates the `aradio` database).
2. `cp .env.example .env` and fill in the values. Generate app secrets with
   `php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'`.
3. Point the web root to `public/`.
   - Apache: `public/.htaccess` rewrites `/api/v1/*` to `public/api/index.php`; add
     `Alias /admin <project>/admin` (see `docs/apache-vhost.conf`).
   - nginx: see `docs/nginx.conf`.
4. Make `storage/`, `public/rsc/` and `admin/data/` writable by the PHP user.
5. Create the first admin user with a hash from
   `php -r 'echo password_hash("<password>", PASSWORD_DEFAULT), PHP_EOL;'`.

### Environment variables

| Variable | Meaning |
| --- | --- |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Database connection |
| `MOBILE_APP_KEYS` | JSON `{"aradio-ios": "<secret>", "aradio-android": "<secret>"}` |
| `RATE_LIMIT_PER_MINUTE` | Per-IP limit for the API and website AJAX (default 120) |
| `STREAM_URL` | Radio stream URL (e.g. `https://stream.example.com/live`) |
| `PUBLIC_BASE_URL` | Site origin for image URLs (e.g. `https://www.example.com`) |
| `APP_TIMEZONE`, `APP_DEBUG`, `TRUST_PROXY`, `STORAGE_PATH` | Runtime options |
| `ADMIN_IDLE_TIMEOUT`, `ADMIN_LOGIN_MAX_ATTEMPTS` | Admin session and login throttling |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | SMTP for password reset |

## Tests and tooling

```
php tests/run.php        # test suite, no dependencies (uses SQLite in memory)
composer lint            # php -l + PSR-12 (phpcs)
composer analyse         # PHPStan level 6 on src/ and tests/
```

The tests cover the contract signing vector, 401 cases (bad signature, old timestamp,
replayed nonce, unknown app id), 429 rate limiting, router 404, date validation 400, error
masking, the admin access policy (401 without session, 403 on bad CSRF), CSRF tokens,
password verification and rehashing, login throttling, idle timeout and upload validation.
CI (`.github/workflows/ci.yml`) runs lint, PHPStan and the tests on PHP 8.2 and 8.3.

## Legacy code notes

The admin panel and site templates are procedural code kept for compatibility. They share
the bootstrap, the guard, the env-based database connection and the escaping helper, but are
not yet ported to controllers. They do not declare `strict_types` because they rely on
scalar coercion. Enabling it file by file needs test coverage first. Moving the admin
endpoints into `src/Controller/Admin` behind the same router is the planned next step.
