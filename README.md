# 💰 HaushaltsHub

A modern multi-user household expense tracker built with Laravel + Vue.js + Inertia.js.

## Features

- 🏠 **Multi-Household** – Create households and invite other users
- 👥 **Multi-User** – Each user manages their own income & expenses
- 📊 **Shared Dashboard** – Beautiful charts showing combined household finances
- 🔐 **Role System** – Owner, Admin, Member roles per household
- 📱 **Responsive** – Works on mobile and desktop
- 🌙 **Dark Mode** – Built-in dark mode support

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 |
| Charts | Chart.js + vue-chartjs |
| Database | SQLite / MySQL / PostgreSQL |
| Auth | Laravel Breeze (Inertia) |

## Quick Start

```bash
# Clone & install
git clone https://github.com/crackscout123/haushaltshub
cd haushaltshub
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database (default: SQLite)
php artisan migrate --seed

# Build & run
npm run build
php artisan serve
```

## Docker

```bash
docker compose up -d
```

## Default Users (after seeding)

| Email | Password | Role |
|---|---|---|
| admin@example.com | password | Admin |
| user@example.com | password | User |

## Environment Variables

```env
# SQLite (default, zero config)
DB_CONNECTION=sqlite

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haushaltshub
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=haushaltshub
DB_USERNAME=postgres
DB_PASSWORD=
```

## License

MIT
