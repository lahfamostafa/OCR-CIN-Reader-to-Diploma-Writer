# OCR CIN Reader to Diploma Writer

## 1. Présentation du projet

**OCR CIN Reader to Diploma Writer** est une application web développée avec Laravel qui automatise une partie du processus de gestion des étudiants et de génération des diplômes.

Elle permet de :

- Téléverser une carte d'identité nationale (CIN) marocaine ou un passeport.
- Extraire automatiquement les informations du document grâce à une API d'intelligence artificielle.
- Vérifier et modifier les informations de l'étudiant avant leur validation.
- Générer des diplômes au format PDF à partir de modèles prédéfinis.
- Gérer les utilisateurs et les accès selon leurs rôles.

L'objectif est de réduire la saisie manuelle des données tout en conservant une validation humaine des informations extraites.

## 2. Technologies utilisées

| Catégorie        | Technologies                   |
| ---------------- | ------------------------------ |
| Backend          | PHP, Laravel 10                |
| Frontend         | Blade, Tailwind CSS, Alpine.js |
| Assets           | Vite, Node.js, npm             |
| Base de données  | MySQL / MariaDB                |
| OCR              | API Scaleway AI                |
| Génération PDF   | Laravel DomPDF                 |
| Authentification | Laravel Breeze                 |
| Dépendances PHP  | Composer                       |

## 3. Fonctionnalités principales

### 3.1. Extraction des données d'identité

L'utilisateur peut téléverser le recto d'une CIN ou d'un passeport. Pour une CIN, le verso est également requis.

L'application transmet les images au service OCR afin d'extraire les informations disponibles, notamment :

- Nom et prénom.
- Date et lieu de naissance.
- Numéro de CIN ou de passeport.
- Adresse.

Les données extraites sont ensuite proposées à l'utilisateur pour vérification et correction.

### 3.2. Gestion des étudiants

L'application permet d'enregistrer les informations des étudiants et de les modifier avant validation.

Les principales données comprennent l'identité, les informations de naissance, l'adresse et les informations relatives au document d'identité.

### 3.3. Génération des diplômes

L'application permet de générer un diplôme associé à un étudiant et à un modèle prédéfini.

Les diplômes sont produits au format PDF, enregistrés dans le stockage de l'application et peuvent être consultés ou téléchargés.

### 3.4. Gestion des modèles de diplômes

Les modèles de diplômes sont enregistrés dans la base de données et associés à des vues Blade qui définissent leur présentation.

### 3.5. Gestion des utilisateurs

L'application utilise Laravel Breeze pour l'authentification et prévoit des rôles tels que `admin` et `secretaire`, avec des accès adaptés aux fonctionnalités autorisées.

## 4. Architecture du projet

Le projet suit l'organisation conventionnelle d'une application Laravel.

| Répertoire              | Responsabilité                                |
| ----------------------- | --------------------------------------------- |
| `app/Http/Controllers/` | Gestion des requêtes HTTP                     |
| `app/Models/`           | Modèles et interactions avec les données      |
| `app/Services/`         | Logique de traitement OCR                     |
| `database/migrations/`  | Structure et évolution de la base de données  |
| `resources/views/`      | Interfaces utilisateur et modèles de diplômes |
| `resources/js/`         | Code JavaScript                               |
| `resources/css/`        | Styles de l'application                       |
| `routes/web.php`        | Définition des routes web                     |
| `storage/`              | Stockage des fichiers et des journaux         |
| `tests/`                | Tests automatisés                             |

## 5. Prérequis

Avant d'installer le projet, assure-toi de disposer de :

- PHP 8.1 ou version compatible avec les dépendances.
- Composer.
- Node.js et npm.
- MySQL ou MariaDB.
- Une clé API Scaleway AI.

## 6. Installation et configuration

### Étape 1 : Installer les dépendances

À la racine du projet, exécuter :

```bash
composer install
npm install
```

### Étape 2 : Configurer l'environnement

Copier le fichier d'exemple :

```bash
cp .env.example .env
```

Générer la clé de l'application :

```bash
php artisan key:generate
```

Configurer ensuite dans `.env` les paramètres de connexion à la base de données et la clé API OCR :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_la_base
DB_USERNAME=ton_utilisateur
DB_PASSWORD=ton_mot_de_passe

SCALEWAY_API_KEY=ta_cle_api
```

Utiliser des valeurs adaptées à ton environnement et ne jamais publier de véritables secrets dans le dépôt Git.

### Étape 3 : Préparer la base de données

Créer la base de données, puis exécuter les migrations :

```bash
php artisan migrate
```

### Étape 4 : Préparer le stockage

Si l'application utilise le disque public de Laravel, créer le lien symbolique :

```bash
php artisan storage:link
```

### Étape 5 : Lancer l'application

Dans un premier terminal :

```bash
php artisan serve
```

Dans un deuxième terminal :

```bash
npm run dev
```

L'application est généralement accessible à l'adresse :

`http://127.0.0.1:8000`

## 7. Tests

Pour exécuter les tests automatisés :

```bash
php artisan test
```

Les résultats dépendent de l'état actuel du code, de la configuration et de l'environnement de test.

## 8. Licence

Le fichier `composer.json` indique une licence MIT. Vérifier également le fichier `LICENSE` du projet pour confirmer les conditions de distribution.

