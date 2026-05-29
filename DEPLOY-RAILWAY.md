# SketchLib — Deploy backend to Railway

Deploy **sketchup-store-api** only (Laravel API + Filament). Next.js and the SketchUp plugin are separate.

## What is in this repo for Railway

- `railway.json` — Nixpacks build + migrate on release + start command
- `nixpacks.toml` — PHP 8.3, composer `--no-dev`, Filament optimize
- `bootstrap/app.php` — trusts Railway HTTPS proxy

**Do not add** a Heroku `Procfile` with `heroku-php-apache2` — that is for Heroku, not Railway Nixpacks.

## 1. Push code to GitHub

```bash
cd "/home/jack/Documents/furtniure 3d models/sketchup-store-api"
git init
git add .
git commit -m "Prepare Laravel backend for Railway deployment"
gh repo create sketchup-store-api --private --source=. --push
```

(Or create the repo on GitHub manually and `git remote add origin ... && git push -u origin main`.)

`.env` is gitignored — secrets go in Railway dashboard only.

## 2. Generate APP_KEY locally

```bash
php artisan key:generate --show
```

Copy output → Railway variable `APP_KEY`.

## 3. Railway project

1. [railway.com](https://railway.com) → **New Project** → **Deploy from GitHub** → select `sketchup-store-api`
2. **+ New** → **Database** → **MySQL**
3. Open the **Laravel service** → **Variables** → **Add reference** from MySQL:

| Variable | Value |
|----------|--------|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |

(Service name must match — if MySQL service is not named `MySQL`, use Railway’s variable picker.)

## 4. Required environment variables

Set on the **Laravel service** (not MySQL):

```env
APP_NAME=SketchLib
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...from key:generate --show
APP_URL=https://YOUR-SERVICE.up.railway.app

FRONTEND_URL=https://sketchlib.com
SANCTUM_STATEFUL_DOMAINS=sketchlib.com,www.sketchlib.com

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.sketchlib.com

FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID=...
R2_SECRET_ACCESS_KEY=...
R2_BUCKET=sketchlib-models
R2_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
R2_DEFAULT_REGION=auto

QUEUE_CONNECTION=database
MAIL_MAILER=log
CACHE_STORE=database
```

Use Railway’s `.railway.app` URL for `APP_URL` until custom domain is ready, then update `APP_URL` and redeploy.

## 5. First deploy

Push to GitHub → Railway builds automatically.

Check **Deploy logs** for:

```
Running releaseCommand...
Migrating...
```

## 6. Seed production data (once)

From Railway CLI or **Settings → Run command**:

```bash
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=PlanSeeder
```

Optional demo content:

```bash
php artisan db:seed --class=DemoDataSeeder
```

Do **not** run `migrate:fresh` on production.

## 7. Smoke tests

Replace `YOUR-URL` with Railway domain:

```bash
curl https://YOUR-URL/up
curl https://YOUR-URL/api/categories
curl https://YOUR-URL/api/plans
```

Browser:

- `https://YOUR-URL/admin` → Filament login (`admin@sketchlib.com` / `sketchlib-admin` after seed)

## 8. Custom domain (later)

Railway service → **Settings** → **Domains** → add `api.sketchlib.com`

Then update:

- `APP_URL=https://api.sketchlib.com`
- Next.js `NEXT_PUBLIC_API_URL=https://api.sketchlib.com/api`
- Plugin `VITE_API_URL=https://api.sketchlib.com/api` → rebuild `ui/dist/`

## 9. Queue worker (optional — when Resend is enabled)

Add a **second Railway service** from the same repo:

- **Start command:** `php artisan queue:work --sleep=3 --tries=3`
- Same env vars as web service (or shared variables)

Not needed while `MAIL_MAILER=log`.

## 10. Plugin reminder

The plugin has **no server**. After API is live:

1. Set `VITE_API_URL=https://api.sketchlib.com/api` in `sketchlib-plugin/ui/.env`
2. `npm run build`
3. Distribute zip / folder to users

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 on all routes | Check `APP_KEY` set; read deploy logs |
| DB connection refused | Verify MySQL reference vars on Laravel service |
| Filament CSS broken | Redeploy; `filament:optimize` runs in build |
| Wrong URLs / http redirects | Set `APP_URL`; trust proxies is in `bootstrap/app.php` |
| CORS errors from frontend | Set `FRONTEND_URL` to exact Next.js origin (no trailing slash) |
| Session/cookie login fails | `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE=true` |
