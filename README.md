# NEXUS Exchange — Laravel Crypto Wallet Platform

A full-stack Laravel 10+ crypto exchange/wallet platform with dark UI, multi-coin wallets, admin approval workflows, and Google OAuth.

---

## Features

- **Authentication**: Email/password + Google OAuth (Socialite)
- **Email Verification**: Mandatory before dashboard access
- **Role System**: `user` and `admin` roles
- **6 Coin Wallets**: BTC, ETH, BNB, XRP, USDO, USD
- **Deposit Flow**: 3-step wizard → admin approval → balance credited
- **Withdrawal Flow**: Funds locked immediately → admin approval → released
- **Transaction Ledger**: Immutable credit/debit history with balance snapshots
- **Admin Panel**: Manage users, review deposits/withdrawals, configure payment settings
- **Responsive Dark UI**: Mobile-first, sidebar navigation, popup modals

---

## Requirements

- PHP 8.1+
- Composer
- MySQL or SQLite
- Node.js (optional, for assets)

---

## Installation

### 1. Create Laravel project and copy files

```bash
composer create-project laravel/laravel nexus-exchange
cd nexus-exchange
composer require laravel/socialite
```

Copy all files from this project into the Laravel root, replacing existing files where applicable.

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="NEXUS Exchange"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus_exchange
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log    # Use 'log' for local dev (check storage/logs)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=your_user
# MAIL_PASSWORD=your_pass
# MAIL_FROM_ADDRESS=noreply@nexus.com

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed   # Creates admin@nexus.com / admin123456
```

### 4. Storage

```bash
php artisan storage:link
```

### 5. Run

```bash
php artisan serve
```

Visit: http://127.0.0.1:8000

---

## Default Credentials

| Role  | Email              | Password      |
|-------|--------------------|---------------|
| Admin | admin@nexus.com    | admin123456   |

---

## Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project → Enable "Google+ API" or "Google Identity"
3. OAuth Consent Screen → External → fill required fields
4. Credentials → Create OAuth 2.0 Client ID → Web Application
5. Add authorized redirect URI: `http://127.0.0.1:8000/auth/google/callback`
6. Copy Client ID and Secret to `.env`

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   └── GoogleController.php
│   │   ├── UserController.php
│   │   ├── WalletController.php
│   │   ├── WithdrawalController.php
│   │   ├── TransactionController.php
│   │   ├── AdminController.php
│   │   └── DepositSettingsController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Kernel.php
├── Models/
│   ├── User.php
│   ├── Wallet.php
│   ├── Deposit.php
│   ├── DepositSetting.php
│   ├── Withdrawal.php
│   └── Transaction.php
config/
└── services.php         (Google OAuth config)
database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_wallets_and_deposits_table.php
│   ├── ..._create_deposit_settings_table.php
│   └── ..._create_withdrawals_and_transactions_table.php
└── seeders/
    └── AdminSeeder.php
resources/views/
├── layouts/
│   ├── wallet.blade.php     (main authenticated layout)
│   └── auth.blade.php       (login/register layout)
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── verify-email.blade.php
├── user/
│   ├── dashboard.blade.php
│   ├── wallet.blade.php
│   ├── deposit-form.blade.php
│   ├── deposit-history.blade.php
│   ├── withdraw-history.blade.php
│   ├── transactions.blade.php
│   └── profile.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── deposits.blade.php
│   ├── withdrawals.blade.php
│   ├── users.blade.php
│   └── deposit-settings.blade.php
└── welcome.blade.php
routes/
└── web.php
```

---

## Withdrawal Flow

1. User submits withdrawal → `available` decremented, `in_order` incremented (funds locked)
2. Transaction record created: direction=debit, "pending admin approval"
3. Admin **approves** → `in_order` decremented, second transaction record created
4. Admin **rejects** → `in_order` decremented, `available` re-incremented (funds returned)

---

## After Any Code Changes

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

Then hard-refresh browser: `Ctrl+Shift+R`

---

## Tech Stack

- Laravel 10+ / PHP 8.1+
- MySQL / SQLite
- Laravel Socialite (Google OAuth)
- Pure Blade templates (no Vue/React/Alpine/Livewire)
- DM Sans + DM Mono (Google Fonts)
- All CSS inline in Blade (no Tailwind/separate files)
