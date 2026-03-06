# Frontend Angular - Horse Race Dashboard

## Prérequis
- Node.js 20+
- npm 10+
- Angular CLI 17+

## Installation
```bash
cd frontend
npm install
# Si Angular Material n'est pas présent dans package.json
ng add @angular/material
```

## Configuration Angular Material
- Angular Material est déclaré dans `package.json`.
- Thème global configuré dans `src/styles.scss` via API theming Angular Material.
- Les composants Material sont importés au niveau des composants standalone.
- La police d'icônes Material est chargée dans `src/index.html`.

## Lancer l'application
```bash
npm run start
```
Application disponible sur `http://localhost:4200`.

## Vérifications
- API backend attendue sur `http://127.0.0.1:8000/api`.
- Vérifier dans DevTools que `GET /api/dashboard/kpis` et `GET /api/race-results` renvoient 200.

## Dépannage
- Port déjà utilisé: `ng serve --port 4300`.
- Angular Material non chargé: vérifier `src/styles.scss`.
- Erreur build: supprimer `node_modules` puis `npm install`.
- CORS API: vérifier config backend `nelmio_cors.yaml`.
