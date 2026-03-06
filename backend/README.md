# Backend Symfony - Horse Race Dashboard

## Prérequis
- PHP 8.2+
- Composer 2+
- MySQL 8+ ou MariaDB 10.6+
- Symfony CLI (optionnel)

## Installation
```bash
cd backend
composer install
cp .env .env.local
# Adapter DATABASE_URL dans .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

## Lancer l'API
```bash
symfony server:start --port=8000
# ou
php -S 127.0.0.1:8000 -t public
```

## Endpoints principaux
- `GET /api/dashboard/kpis`
- `GET /api/dashboard/performance-over-time`
- `GET /api/dashboard/by-racecourse`
- `GET /api/dashboard/by-distance`
- `GET /api/dashboard/heatmap`
- `GET /api/dashboard/odds-vs-results`
- `GET /api/horses`
- `GET /api/horses/{id}`
- `GET /api/jockeys-drivers/stats`
- `GET /api/trainers/stats`
- `GET /api/race-results`

## Filtres query params supportés
`startDate`, `endDate`, `racecourse`, `raceType`, `discipline`, `distanceMin`, `distanceMax`, `groundCondition`, `trainerId`, `jockeyOrDriverId`, `horseId`, `oddsMin`, `oddsMax`, `runnerCountMin`, `runnerCountMax`, `topPlaceThreshold`.

## Vérification API
```bash
curl "http://127.0.0.1:8000/api/dashboard/kpis"
curl "http://127.0.0.1:8000/api/race-results?startDate=2025-01-01&distanceMin=1600"
```

## Dépannage
- Erreur DB: vérifier user/password/port dans `DATABASE_URL`.
- Migration absente: `php bin/console doctrine:migrations:migrate`.
- CORS: ajuster `config/packages/nelmio_cors.yaml`.
- Erreur dépendance: relancer `composer install`.
