# Horse Race Dashboard (Symfony + Angular Material)

Socle complet d'application dashboard de statistiques hippiques, avec backend Symfony REST et frontend Angular Material.

## Arborescence
```text
.
├── backend/
│   ├── config/
│   ├── migrations/
│   ├── src/
│   │   ├── Controller/
│   │   ├── DataFixtures/
│   │   ├── Dto/
│   │   ├── Entity/
│   │   ├── Repository/
│   │   └── Service/
│   └── README.md
├── frontend/
│   ├── src/app/
│   │   ├── core/services/
│   │   ├── features/dashboard/pages/
│   │   └── models/
│   └── README.md
└── README.md
```

## Choix d'architecture (court)
- **Backend**: architecture Symfony classique Controller/Service/Repository/Entity, filtres unifiés via DTO `FilterParams`, calculs métier centralisés dans `StatisticsService`.
- **Frontend**: Angular standalone + Angular Material, page dashboard unique orientée métier, service API central, composants réactifs via RxJS/HttpClient.
- **Charts**: `ng2-charts` + `chart.js` pour courbes, barres et scatter plot.

## Prérequis globaux
- PHP 8.2+
- Composer 2+
- MySQL/MariaDB
- Node.js 20+
- npm 10+
- Angular CLI 17+
- Symfony CLI (optionnel)

## Installation backend détaillée
```bash
git clone <repo>
cd horse-race-dashboard/backend
composer install
cp .env .env.local
# éditer DATABASE_URL dans .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
symfony server:start --port=8000
# ou php -S 127.0.0.1:8000 -t public
```

## Installation frontend détaillée
```bash
cd ../frontend
npm install
# optionnel si vous repartez d'un projet vierge
ng add @angular/material
npm run start
```

## Lancement complet
1. Démarrer MySQL/MariaDB et créer un utilisateur DB.
2. Démarrer backend sur `http://127.0.0.1:8000`.
3. Démarrer frontend sur `http://localhost:4200`.
4. Ouvrir `http://localhost:4200`.
5. Vérifier API:
```bash
curl http://127.0.0.1:8000/api/dashboard/kpis
curl http://127.0.0.1:8000/api/race-results
```
6. Vérifier dans l'UI: KPI remplis, tableaux paginés, graphiques visibles.

## Troubleshooting
- **Connexion DB refusée**: vérifier `DATABASE_URL`, host et port.
- **Migrations non exécutées**: relancer `doctrine:migrations:migrate`.
- **Fixtures absentes**: exécuter `doctrine:fixtures:load -n`.
- **CORS**: adapter `backend/config/packages/nelmio_cors.yaml`.
- **Port 4200/8000 occupé**: changer le port Angular/Symfony.
- **Angular Material non stylé**: vérifier thème dans `frontend/src/styles.scss`.
- **Erreur Composer**: `composer clear-cache && composer install`.
- **Erreur npm build**: supprimer `node_modules` puis `npm install`.
