# Parcel Proxy — Setup & Deployment Guide

## Overview

Parcel Proxy is a purchase-forwarding platform built with **Laravel 12** (PHP 8.2), **Filament 3**, and **Stripe**. Customers paste a product URL, pay upfront, and staff manually buys and ships the item to them.

---

## Requirements

- PHP 8.2+ (via Laravel Herd)
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ (for Vite)
- A Stripe account (test or live)

---

## Installation

### 1. Clone & Install Dependencies

```bash
# Using Herd's PHP 8.2
C:\Users\<you>\.config\herd\bin\php82\php.exe C:\composer\composer.phar install

npm install
npm run build
```

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and fill in:

```ini
APP_NAME="Parcel Proxy"
APP_URL=http://order-proxy.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parcel_proxy
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@parcelproxy.com
MAIL_FROM_NAME="Parcel Proxy"
```

### 3. Create Database & Run Migrations

```bash
# Create the database (XAMPP must be running)
mysql -u root -e "CREATE DATABASE parcel_proxy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed fee rules and admin user
php artisan db:seed
```

### 4. Start the Queue Worker

```bash
php artisan queue:work --tries=3
```

> ⚠️ The queue worker must be running for product fetch jobs and email notifications to work.

### 5. Access the Application

- **Public order form**: `http://order-proxy.test/` (or localhost)
- **Admin panel**: `http://order-proxy.test/admin`
  - Email: `admin@parcelproxy.com`
  - Password: `password`

---

## Stripe Configuration

### Test Mode

Use Stripe test card `4242 4242 4242 4242` with any future expiry and any CVC.

### Webhook Setup

1. Install the [Stripe CLI](https://stripe.com/docs/stripe-cli):
   ```bash
   stripe listen --forward-to http://order-proxy.test/stripe/webhook
   ```

2. Copy the webhook secret printed by the CLI and add it to `.env`:
   ```ini
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

### Production Webhook

In the Stripe Dashboard → Webhooks:
- Add endpoint: `https://yourdomain.com/stripe/webhook`
- Events to listen for:
  - `checkout.session.completed`
  - `payment_intent.succeeded`
  - `charge.refunded`

---

## Editing Fee Rules (No Code Changes Needed!)

The fee engine is **data-driven** and fully editable from the Filament admin panel:

1. Log in to `/admin`
2. Go to **Settings → Fee Rules**
3. Edit the default 12% rule or add new price brackets

**Example: Lower fee for expensive items**
| Min Price | Max Price | Type | Value |
|---|---|---|---|
| $0 | $99.99 | percentage | 15% |
| $100 | $499.99 | percentage | 12% |
| $500 | (unbounded) | percentage | 8% |

To edit **size handling fees**, go to **Settings → Size Fee Rules**.

> No deployment required — changes take effect immediately.

---

## Running Tests

```bash
# Unit tests (fee engine + reconciliation logic)
php artisan test --filter FeeCalculatorServiceTest
php artisan test --filter ReconcilePaymentServiceTest

# All tests
php artisan test
```

> Note: Tests use `RefreshDatabase` and require a test database configured in `phpunit.xml`.

---

## Architecture Overview

```
app/
├── Http/Controllers/
│   ├── OrderController.php      # Public order flow, Stripe webhook
│   └── TrackingController.php   # Magic link tracking + resend
├── Services/
│   ├── FeeCalculatorService.php # Data-driven fee engine
│   ├── ProductFetchService.php  # OG/JSON-LD scraper (Guzzle)
│   └── ReconcilePaymentService.php # Stripe refunds/additional charges
├── Jobs/
│   └── FetchProductJob.php      # Queued product fetch job
├── Notifications/               # 8 queued email notification classes
├── Models/                      # Order, Payment, FeeRule, SizeFeeRule, etc.
└── Filament/
    ├── Resources/
    │   ├── OrderResource.php    # Full CRUD with status actions
    │   ├── FeeRuleResource.php
    │   └── SizeFeeRuleResource.php
    └── Widgets/
        └── OrderStatsWidget.php # Dashboard counts by status
```

---

## Upgrade Path (Future Iterations)

### Higher Product Fetch Reliability

The current scraper (Guzzle + OG tags + JSON-LD) works for most sites but is blocked by Amazon/eBay bot detection. Upgrade options:

1. **Browsershot/Puppeteer** — headless Chrome for JS-heavy pages:
   ```bash
   composer require spatie/browsershot
   ```

2. **Paid Product API** — e.g., [Rainforest API](https://www.rainforestapi.com/) for Amazon, [ScraperAPI](https://www.scraperapi.com/) as a proxy.

### Multi-Currency Support

Replace `usd` hardcoding with a `currencies` table and exchange rate API (e.g., Fixer.io).

### Customer Accounts (Optional)

If customer logins are needed later, use Laravel Fortify or Breeze with the existing email-based tracking system as a fallback.

---

## Security Notes

- Stripe webhook signatures are verified via `Stripe\Webhook::constructEvent()`
- Magic tracking links use Laravel's `URL::temporarySignedRoute()` — no database token storage needed
- CSRF is properly excluded only for `/stripe/webhook`
- All customer data is encrypted in transit (HTTPS required in production)
