# SDCC - Gestion des Véhicules de Service

Application web interne de **réservation des véhicules de service** pour le **SDCC**.
Elle permet aux employés de soumettre des demandes de déplacement, aux administrateurs
de les valider selon les rôles, et de suivre l'ensemble du parc automobile et des demandes
depuis un tableau de bord unique.

Le projet est organisé dans le dossier [`vehicule-sdcc/`](vehicule-sdcc/) (application Laravel).

---

## Fonctionnalités

- **Authentification** — connexion / déconnexion, réinitialisation de mot de passe, sessions sécurisées
- **Gestion des rôles et autorisations** — `super_admin`, `admin`, `employee` (Spatie Laravel Permission)
- **Gestion des utilisateurs** — création, modification, activation / désactivation, affectation des services
- **Gestion des véhicules** — parc automobile, modèles, matricules, disponibilités
- **Création et soumission de demandes de réservation** — destination, dates/heures, motif, kilométrage prévu
- **Consultation des demandes** — liste filtrable, fiche détaillée de la demande (`DEM-XXXXX`)
- **Gestion des disponibilités** — véhicules disponibles par date, détection des chevauchements
- **Validation des demandes selon les rôles** — approbation / rejet par les administrateurs
- **Suivi des demandes** — historique, filtres, statistiques, statuts (en attente, approuvée, rejetée, annulée)
- **Gestion du kilométrage** — saisie et suivi des entrées de kilométrage par véhicule
- **Impression des demandes** — fiche imprimable au format A4
- **Tableau de bord** — indicateurs et statistiques pour les différents rôles
- **Planification et calendrier** — fenêtres de planification, zones, vue calendrier
- **Exports** — rapports Excel et PDF (réservations, véhicules, planification)
- **Notifications** — centre de notifications de l'application
- **Interface responsive** — desktop, tablette et mobile

---

## Technologies

| Catégorie | Technologies |
|---|---|
| Back-end | **Laravel 10**, **PHP 8.1+** |
| Templates | **Blade** |
| Base de données | **MySQL** |
| Front-end | **HTML**, **CSS**, **JavaScript** (bundler **Vite**) |
| Icônes | **Font Awesome** |
| Notifications / UI | **SweetAlert2** |
| Rôles & permissions | **spatie/laravel-permission** |
| Imports / exports | **maatwebsite/excel**, **barryvdh/laravel-dompdf** |
| API & auth | **laravel/sanctum** |
| Tests | **PHPUnit** |

---

## Installation locale

### Prérequis

- PHP >= 8.1 (extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer
- Node.js >= 18 et npm
- MySQL 5.7+ / 8.0+

### 1. Récupérer le projet

```bash
git clone https://github.com/naimyouness91-bit/sdcc-vehicule-service.git
cd sdcc-vehicule-service/vehicule-sdcc
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Puis ouvrez le fichier `.env` et renseignez vos valeurs :

```dotenv
APP_NAME="SDCC Réservation"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation-sdcc
DB_USERNAME=root
DB_PASSWORD=

SUPER_ADMIN_EMAIL=admin@votre-domaine.ma
SUPER_ADMIN_PASSWORD="ChangezCeMotDePasse"
```

> **Important** : ne commitez jamais le fichier `.env`. Il est ignoré par `.gitignore`
> et ne doit contenir aucun mot de passe réel dans un dépôt public.

### 4. Configurer la base de données

```bash
mysql -u root -p -e "CREATE DATABASE reservation-sdcc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Puis lancez les migrations et les seeders (rôles, super admin, véhicules, services, zones) :

```bash
php artisan migrate --seed
```

### 5. Lancer l'application

```bash
# en développement (rechargement automatique des assets)
npm run dev
php artisan serve
```

```bash
# build de production des assets
npm run build
```

L'application est alors accessible sur **http://localhost:8000**

### 6. Tests

```bash
php artisan test
```

---

## Structure du projet

```
vehicule-sdcc/
├── app/                # Contrôleurs, modèles, commandes, middleware
├── config/             # Configuration Laravel
├── database/
│   ├── migrations/     # Schéma de la base de données
│   └── seeders/        # Rôles, super admin, véhicules, services, zones
├── public/             # Assets publics (Font Awesome, SweetAlert2, CSS/JS)
├── resources/
│   └── views/          # Templates Blade (dashboard, demandes, admin, ...)
├── routes/             # Routes web et API
├── storage/            # Logs, cache, vues compilées (non versionnés)
└── tests/              # Tests fonctionnels PHPUnit
```

---

## Auteur

**Youness Naim**
