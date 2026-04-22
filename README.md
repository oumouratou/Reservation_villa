# Projet Full-Stack Laravel + Vue

Le workspace contient deux applications:

- backend: API Laravel 13
- frontend: application Vue 3 + Vite

## Etat actuel

- Le scaffold est deja cree.
- Les migrations backend passent.
- Les routes backend sont chargees correctement.

## Prerequis

- PHP 8.3+
- Composer
- Node.js 20+
- npm

## Re-scaffolder si besoin (PowerShell)

Depuis la racine du workspace:

```powershell
Set-Location .
composer create-project laravel/laravel backend
npm create vite@latest frontend -- --template vue --yes
Set-Location frontend
npm install
```

## Lancer l'application

Terminal 1 (backend):

```powershell
Set-Location backend
php artisan serve
```

Terminal 2 (frontend):

```powershell
Set-Location frontend
npm run dev
```

## Commandes utiles backend

```powershell
Set-Location backend
php artisan migrate
php artisan storage:link
php artisan route:list
```

## Taches VS Code

Le fichier .vscode/tasks.json contient:

- Scaffold Laravel + Vue
- Run Laravel Backend
- Run Vue Frontend
