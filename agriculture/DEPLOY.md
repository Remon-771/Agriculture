# Deploy (Frontend → Vercel, Backend + MySQL → Railway)

## 1. Backend + MySQL on Railway (railway.app)
1. New Project → Deploy from GitHub repo → set **Root Directory** = `agriculture/backend` (uses the Dockerfile).
2. Add a **MySQL** database to the same project.
3. Backend service → Variables (use Railway references for DB):
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=            # run locally: php artisan key:generate --show  (or any base64:32-byte key)
APP_URL=https://<your-backend>.up.railway.app
FRONTEND_URL=https://<your-vercel-app>.vercel.app   # exact origin, no trailing slash
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=smtp       # needed for OTP emails (e.g. Gmail app password / Brevo / Resend SMTP)
MAIL_HOST= MAIL_PORT=587 MAIL_USERNAME= MAIL_PASSWORD= MAIL_FROM_ADDRESS=
WEATHER_API_KEY=       # free key from weatherapi.com
SSLCOMMERZ_STORE_ID= SSLCOMMERZ_STORE_PASSWORD=   # your NEW sandbox creds
```
4. Settings → Networking → Generate Domain. On boot it auto-runs migrations + seeds demo data.
5. Test: `https://<backend>/api/v1/products` should return JSON.

## 2. Frontend on Vercel
1. Import the GitHub repo → **Root Directory** = `agriculture/ams-frontend`.
2. Env vars:
```
NEXT_PUBLIC_API_URL=https://<your-backend>.up.railway.app
NEXT_PUBLIC_DISABLE_MOCK=true
```
3. Deploy, then put the Vercel URL into backend `FRONTEND_URL` and redeploy backend (CORS).

## Demo logins (password for all: `password123`)
admin@ams.com.bd · rahim@dae.gov.bd (officer) · vendor@ams.com.bd · company@ams.com.bd · abdul.karim@farmer.com
More: farmer4..farmer15@farmer.com, officer3..6@dae.gov.bd, vendor3..5@ams.com.bd, company2..3@ams.com.bd

## Security
Rotate the SSLCommerz credentials that were committed earlier; change demo passwords before real use.
