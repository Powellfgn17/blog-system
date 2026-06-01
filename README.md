# Thesaurus

Thesaurus est une application Laravel qui combine un blog éditorial avec un espace communautaire.

## Présentation

L’application propose :

- Une page d’accueil `Official Blog` pour les articles éditoriaux
- Un espace `Community Space` pour les publications communautaires
- Une navigation utilisateur avec inscription, connexion, profil et favoris
- Un système de commentaires, réactions et signalisations
- Une administration avec modération, gestion des catégories et blocage d’utilisateurs

## Fonctionnalités

- Publication et modification d’articles pour les administrateurs
- Création, édition et suppression de posts communautaires pour les utilisateurs authentifiés
- Recherche d’articles et de contenu par catégorie
- Profil utilisateur avec historique de publications et signets
- Notifications pour les actions importantes et le suivi des contenus

## Technologie

- Laravel 12
- Tailwind CSS
- PHP 8.2+
- Blade pour les vues
- Base de données SQLite / MySQL selon configuration

## Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Powellfgn17/blog-system.git
   cd blog-system
   ```
2. Installez les dépendances PHP :
   ```bash
   composer install
   ```
3. Installez les dépendances JavaScript :
   ```bash
   npm install
   ```
4. Configurez l’environnement :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Configurez la base de données dans `.env` puis migrez :
   ```bash
   php artisan migrate
   ```
6. Compilez les assets :
   ```bash
   npm run dev
   ```
7. Lancez le serveur de développement :
   ```bash
   php artisan serve
   ```

## Accès

- Page d’accueil : `/`
- Espace communautaire : `/community`
- Articles du blog : `/blog/{id}`
- Pages de catégories : `/categories/{slug}`
- Profil utilisateur : `/profile/{username}`
- Administration : `/admin`

## Routes importantes

- `GET /` — blog public
- `GET /community` — fil communautaire
- `GET /categories/{slug}` — affichage par catégorie
- `GET /profile/{username}` — page profil utilisateur
- `GET /notifications` — notifications pour les utilisateurs connectés
- `GET /admin` — tableau de bord admin

## Bonnes pratiques

- Utilisez `npm run dev` pendant le développement
- Exécutez `php artisan migrate --seed` si vous utilisez des jeux de données de test
- Vérifiez les variables d’environnement dans `.env`

## Contribution

Les contributions sont les bienvenues : corrections de bugs, évolution des interfaces, amélioration de l’expérience éditoriale et communautaire.
