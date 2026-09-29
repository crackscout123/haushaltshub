# 💰 HaushaltsHub

Ein moderner Multi-User Haushaltstracker mit gemeinsamem Dashboard, Einnahmen/Ausgaben-Verwaltung und dynamischen Charts.

## Features

- 🏠 **Multi-Haushalt** – Haushalte erstellen und per Einladungscode beitreten
- 👥 **Multi-User** – Jeder verwaltet seine eigenen Einnahmen & Ausgaben
- 📊 **Gemeinsames Dashboard** – Monatliche KPIs, Trends-Chart, Kategorien-Doughnut, Budgetbalken, Mitgliederstatistiken
- 🌙 **Dark Mode** – Automatisch via localStorage
- 📱 **Responsiv** – Desktop & Mobile
- 🔐 **Einfaches Rollensystem** – Owner, Member (v2: feingranulare Rechte)

## Tech Stack

| Layer | Technologie |
|---|---|
| Backend | Laravel 11 |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 |
| Charts | Chart.js + vue-chartjs |
| Datenbank | SQLite (default) / MySQL / PostgreSQL |
| Auth | Custom (Laravel Breeze-Style) |
| Deployment | Docker + Apache |

## Schnellstart (lokal)

```bash
git clone https://github.com/crackscout123/haushaltshub
cd haushaltshub

composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite anlegen
touch database/database.sqlite

php artisan migrate --seed

npm run build
php artisan serve
```

Dann öffne: http://localhost:8000

**Demo-Nutzer nach Seeding:**
| E-Mail | Passwort |
|---|---|
| admin@example.com | password |
| user@example.com | password |

## Docker (empfohlen für Self-Hosting)

```bash
# .env anlegen
cp .env.example .env
# APP_KEY setzen:
php artisan key:generate --show
# oder: openssl rand -base64 32

# Starten
docker compose up -d

# Datenbank migieren & seeden (einmalig)
docker compose exec app php artisan migrate --seed
```

### Mit MySQL statt SQLite

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=haushaltshub
DB_USERNAME=haushalt
DB_PASSWORD=secret
```

### Hinter Nginx Proxy Manager

Einfach den `haushaltshub`-Container auf Port 8000 als Proxy-Host eintragen, SSL via Let's Encrypt aktivieren.

## Mit bestehendem Docker-Netzwerk verbinden

```yaml
# Am Ende von docker-compose.yml das externe Netzwerk einbinden:
networks:
  haushaltshub:
    external: true
    name: dein-proxy-netzwerk
```

## Projektstruktur

```
haushaltshub/
├── app/
│   ├── Http/Controllers/       # HouseholdController, TransactionController, Auth/
│   ├── Models/                 # Household, Transaction, Category, Budget, User
│   └── Policies/               # HouseholdPolicy
├── database/
│   ├── migrations/             # 5 Migrationen
│   └── seeders/                # Demo-Daten
├── resources/js/
│   ├── Pages/
│   │   ├── Auth/               # Login, Register
│   │   ├── Households/         # Index, Create, Dashboard
│   │   └── Transactions/       # Index
│   ├── Layouts/AppLayout.vue
│   └── Components/             # Card, StatCard, Modal, NavLink, Icons
├── routes/
│   ├── web.php
│   └── auth.php
├── docker-compose.yml
└── Dockerfile
```

## Roadmap

- [x] Auth (Login / Register)
- [x] Haushalte erstellen & beitreten (Invite Code)
- [x] Einnahmen & Ausgaben eintragen
- [x] Dashboard mit Charts & Stats
- [x] Dark Mode
- [x] Docker-Support
- [ ] Budgets UI (CRUD im Frontend)
- [ ] Kategorien verwalten
- [ ] CSV-Export
- [ ] v2: Feingranulares Rechtesystem
- [ ] v2: Wiederkehrende Transaktionen
- [ ] v2: Jahresauswertung

## Lizenz

MIT
