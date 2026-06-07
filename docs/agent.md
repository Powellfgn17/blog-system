# PROMPT AGENT IA — Blog System Laravel 12

## Contexte

Tu es un expert Laravel 12. Tu vas m'aider à construire un système de blog complet de bout en bout. Tout ce qui suit est le résultat d'une conception déjà faite. Tu dois respecter scrupuleusement chaque décision architecturale décrite ci-dessous sans les modifier.

---

## État actuel du projet

Le projet est un Laravel 12 installé localement sous Windows. Voici ce qui a déjà été fait :

### Déjà installé et configuré
- Laravel 12.61.0 (PHP 8.2+)
- Laravel Breeze (authentification avec Blade) — vues login/register déjà générées
- Laravel Reverb (WebSockets) — installé via `php artisan install:broadcasting`
- Base de données MySQL configurée dans `.env`

### Fichiers déjà générés (vides pour la plupart)
**Models :** User (modifié par Breeze), Category, Post, Comment, Reaction, Report, Bookmark  
**Controllers :** CategoryController, PostController, CommentController, ReactionController, ReportController, BookmarkController, ProfileController (Breeze), NotificationController, Admin/DashboardController, Admin/ModerationController  
**Policies :** PostPolicy, CommentPolicy  
**Middleware :** IsAdmin  
**Migrations :** toutes créées et remplies (voir schéma ci-dessous)

### Migrations déjà remplies et prêtes à migrer
- `add_fields_to_users_table` — ajoute username, avatar, bio, is_admin, is_blocked
- `create_categories_table` — id, name, slug, timestamps
- `create_posts_table` — id, user_id, category_id, type ENUM(BLOG/COMMUNITY), title, body, reading_time, timestamps
- `create_comments_table` — id, user_id, post_id, parent_id (nullable, self-referencing), body, timestamps
- `create_reactions_table` — id, user_id, morphs(reactable), type ENUM(like/love/haha/sad/wow), unique(user_id, reactable_id, reactable_type)
- `create_notifications_table` — id, user_id, type, morphs(notifiable), data JSON, read_at, created_at
- `create_reports_table` — id, user_id, morphs(reportable), reason ENUM, status ENUM(pending/resolved/ignored), unique(user_id, reportable_id, reportable_type)
- `create_bookmarks_table` — id, user_id, post_id, created_at, unique(user_id, post_id)

---

## Ce qui reste à construire (ta mission complète)

Tu dois implémenter dans l'ordre :

1. **Models** — remplir tous les models avec leurs $fillable, relations Eloquent, accesseurs
2. **Middleware IsAdmin** — logique de vérification
3. **Policies** — PostPolicy et CommentPolicy
4. **Controllers** — logique complète de chaque controller
5. **Routes** — web.php complet
6. **Events & Listeners** — CommentPosted pour le temps réel
7. **Vues Blade** — toutes les vues du projet
8. **Layout principal** — navigation, notifications, cloche

---

## Cahier des charges complet

### Deux espaces distincts

**Blog principal** — publications de type `BLOG`, créées uniquement par l'administrateur (`is_admin = true`).  
**Espace communautaire** — publications de type `COMMUNITY`, créées par tout utilisateur authentifié.  
Les deux espaces ne se mélangent jamais. Le champ `type` sur la table `posts` fait la séparation.

---

### Acteurs

**Visiteur non authentifié**
- Lire les articles et publications
- Consulter les profils publics
- Rechercher par mot-clé
- Parcourir les catégories
- Ne peut pas commenter, réagir, publier, ni mettre en favori

**Utilisateur authentifié**
- Profil personnel (avatar, bio, nom affiché, mot de passe modifiables — username non modifiable)
- Créer, modifier, supprimer ses publications dans l'espace COMMUNITY
- Commenter et répondre aux commentaires (threads imbriqués via parent_id)
- Modifier et supprimer ses propres commentaires
- Réagir avec 5 types : 👍 like, ❤️ love, 😂 haha, 😢 sad, 😮 wow
- Taguer des utilisateurs via @username (suggestions en temps réel, lien cliquable, notification envoyée)
- Ajouter/retirer des publications en favoris
- Recevoir des notifications in-app
- Signaler un contenu (spam, harassment, offensive, misinformation, other)

**Administrateur (is_admin = true)**
- Tout ce que fait l'utilisateur authentifié
- Créer, modifier, supprimer des articles dans le blog principal (type BLOG)
- Modifier ou supprimer toute publication ou commentaire
- Gérer les catégories
- Accéder au tableau de bord admin
- Consulter et traiter les signalements (supprimer contenu, ignorer, bloquer l'auteur)
- Bloquer/débloquer un utilisateur

---

### Authentification (via Breeze déjà installé)

Inscription : username (unique, 3-30 chars, lettres/chiffres/underscore), email (unique), password (min 8, confirmation)  
Connexion : email + mot de passe  
Après blocage : connexion refusée avec message explicite (middleware IsAdmin doit aussi vérifier is_blocked côté auth)

---

### Profil utilisateur

Champs publics : username (@username), name, avatar, bio, date inscription, nombre publications, nombre commentaires  
Champs privés : email  
Non modifiable : username, email, date inscription  
Modifiable : name, avatar, bio, password  
Page profil affiche les publications COMMUNITY de l'utilisateur.

---

### Publications

Une seule table `posts` avec champ `type` :
- `BLOG` → blog principal, admin seulement
- `COMMUNITY` → espace communautaire, tout utilisateur authentifié

Chaque publication a un **temps de lecture estimé** calculé automatiquement : `ceil(str_word_count($body) / 200)` minutes. Stocké dans `reading_time` à la création/modification.

Pagination : 10 par page, triées de la plus récente à la plus ancienne.  
Suppression en cascade : comments, reactions, bookmarks associés supprimés automatiquement (géré en DB via migrations).

---

### Commentaires

Threads imbriqués via `parent_id` (null = commentaire racine, valeur = réponse).  
Affichage récursif dans les vues.  
La modification affiche une mention "modifié" avec horodatage.  
Suppression d'un commentaire parent supprime en cascade ses réponses (géré en DB).

---

### Réactions

Polymorphiques (Post ou Comment).  
Une seule réaction active par utilisateur par cible.  
Changer de réaction remplace l'ancienne.  
Cliquer sur la réaction active l'annule (toggle).  
Compteur affiché par type de réaction.

---

### Tags @username

Pendant la saisie, dès que `@` est tapé, une liste de suggestions filtrées apparaît via une requête AJAX.  
À l'affichage, `@username` est transformé en lien cliquable `<a href="/profile/username">@username</a>`.  
La personne taguée reçoit une notification de type `mention`.  
Plusieurs utilisateurs peuvent être tagués dans le même contenu.

---

### Favoris (Bookmarks)

Bouton toggle sur chaque publication.  
État du bouton reflète si la publication est déjà en favori.  
Liste des favoris accessible depuis le profil (visible uniquement par le propriétaire).

---

### Notifications in-app

Événements déclencheurs :
- `comment` → quelqu'un commente ta publication → notifie l'auteur de la publication
- `reply` → quelqu'un répond à ton commentaire → notifie l'auteur du commentaire
- `reaction` → quelqu'un réagit à ta publication ou ton commentaire → notifie l'auteur
- `mention` → tu es tagué dans un contenu → notifie l'utilisateur mentionné

Affichage : icône cloche dans la navbar avec compteur de non-lus. Clic ouvre un dropdown. Chaque notification est cliquable et redirige vers le contenu. Possibilité de tout marquer comme lu.

---

### Modération

**Signalements :**
- Un seul signalement par utilisateur par contenu (contrainte unique en DB)
- Invisible pour l'auteur du contenu signalé
- Motifs : spam, harassment, offensive, misinformation, other

**Tableau de bord admin :**
- Liste des contenus signalés triés par nombre de signalements
- Actions : supprimer le contenu, ignorer le signalement, bloquer l'auteur

**Blocage :**
- is_blocked = true en DB
- Sessions actives invalidées immédiatement
- Message explicite à la connexion
- Données conservées (pas de suppression)

---

### Temps réel (Laravel Reverb + Echo)

À chaque nouveau commentaire, un Event `CommentPosted` est émis.  
Il est broadcasté sur le canal public `post.{post_id}`.  
Laravel Echo côté client écoute ce canal et insère le commentaire dans le DOM sans rechargement.  
Les compteurs de réactions se mettent aussi à jour en temps réel.

---

## Relations Eloquent à implémenter

```
User       → hasMany(Post)
User       → hasMany(Comment)
User       → hasMany(Notification)
User       → hasMany(Bookmark)
User       → morphMany(Reaction)

Post       → belongsTo(User)
Post       → belongsTo(Category) [nullable]
Post       → hasMany(Comment)
Post       → hasMany(Bookmark)
Post       → morphMany(Reaction)
Post       → morphMany(Report)

Comment    → belongsTo(User)
Comment    → belongsTo(Post)
Comment    → belongsTo(Comment, 'parent_id') [parent]
Comment    → hasMany(Comment, 'parent_id')   [replies]
Comment    → morphMany(Reaction)
Comment    → morphMany(Report)

Reaction   → morphTo() [reactable]
Notification → morphTo() [notifiable]
Report     → morphTo() [reportable]
Bookmark   → belongsTo(User)
Bookmark   → belongsTo(Post)
```

---

## Structure des routes à créer (web.php)

```
// Publiques
GET  /                          → PostController@blogIndex
GET  /blog/{post}               → PostController@show
GET  /community                 → PostController@communityIndex
GET  /community/{post}          → PostController@show
GET  /categories/{slug}         → CategoryController@show
GET  /search                    → PostController@search
GET  /profile/{username}        → ProfileController@show

// Auth middleware
POST /blog                      → PostController@store         [admin only]
GET  /blog/create               → PostController@create        [admin only]
PUT  /blog/{post}               → PostController@update        [admin only]
DELETE /blog/{post}             → PostController@destroy       [admin only]

GET  /community/create          → PostController@create
POST /community                 → PostController@store
PUT  /community/{post}          → PostController@update
DELETE /community/{post}        → PostController@destroy

POST /posts/{post}/comments     → CommentController@store
PUT  /comments/{comment}        → CommentController@update
DELETE /comments/{comment}      → CommentController@destroy

POST /reactions                 → ReactionController@toggle
POST /bookmarks/{post}          → BookmarkController@toggle
POST /reports                   → ReportController@store

GET  /notifications             → NotificationController@index
POST /notifications/read-all    → NotificationController@readAll
POST /notifications/{id}/read   → NotificationController@markRead

GET  /profile/edit              → ProfileController@edit
PUT  /profile                   → ProfileController@update
GET  /profile/bookmarks         → ProfileController@bookmarks

// AJAX
GET  /users/search?q=           → UserController@search (suggestions @username)

// Admin middleware
GET  /admin                     → Admin/DashboardController@index
GET  /admin/moderation          → Admin/ModerationController@index
DELETE /admin/content/{type}/{id} → Admin/ModerationController@destroy
POST /admin/reports/{id}/ignore → Admin/ModerationController@ignore
POST /admin/users/{id}/block    → Admin/ModerationController@block
POST /admin/users/{id}/unblock  → Admin/ModerationController@unblock
GET  /admin/categories          → CategoryController@adminIndex
POST /admin/categories          → CategoryController@store
DELETE /admin/categories/{id}   → CategoryController@destroy
```

---

## Structure des vues Blade à créer

```
resources/views/
├── layouts/
│   └── app.blade.php            (navbar, notifications cloche, footer)
├── blog/
│   ├── index.blade.php          (liste articles blog)
│   └── create.blade.php         (formulaire création/édition)
├── community/
│   ├── index.blade.php          (liste publications communautaires)
│   └── create.blade.php         (formulaire création/édition)
├── posts/
│   └── show.blade.php           (détail d'une publication, commentaires, réactions)
├── comments/
│   └── _comment.blade.php       (partial récursif commentaire + réponses)
├── profile/
│   ├── show.blade.php           (profil public)
│   ├── edit.blade.php           (édition profil)
│   └── bookmarks.blade.php      (liste favoris)
├── notifications/
│   └── index.blade.php          (liste notifications)
└── admin/
    ├── dashboard.blade.php      (tableau de bord)
    ├── moderation.blade.php     (liste signalements)
    ├── users.blade.php          (liste utilisateurs)
    └── categories.blade.php     (gestion catégories)
```

---

## Contraintes techniques importantes

- **Laravel 12** — utiliser la syntaxe moderne (pas de `$this->middleware()` dans les constructeurs, utiliser les attributs de route ou le groupe middleware dans web.php)
- **Blade** uniquement pour les vues — pas de Vue.js ni React
- **Bootstrap 5** pour le CSS (CDN dans le layout)
- **Polymorphisme Eloquent** pour reactions, reports, notifications — utiliser `morphTo()`, `morphMany()`
- Le middleware `IsAdmin` vérifie `auth()->user()->is_admin === true`
- Le middleware doit aussi rejeter les utilisateurs avec `is_blocked === true` avec un message explicite
- Les **Policies** utilisent `$user->id === $post->user_id` pour update/delete, et `$user->is_admin` pour les overrides admin
- L'accesseur `reading_time` calcule `ceil(str_word_count($this->body) / 200)` ou est stocké en DB à la création
- Les réactions et bookmarks utilisent des requêtes AJAX (fetch API) pour le toggle sans rechargement
- L'Event `CommentPosted` implémente `ShouldBroadcast` et est émis sur `new PresenceChannel('post.'.$this->comment->post_id)`

---

## Instructions pour l'agent

1. Implémente les fichiers dans cet ordre exact : Models → Middleware → Policies → Controllers → Routes → Events/Listeners → Vues
2. Pour chaque fichier, génère le code complet sans raccourcis ni `// TODO`
3. Respecte strictement toutes les décisions architecturales décrites — ne propose pas d'alternatives
4. Utilise les conventions Laravel 12 (PHP 8.2, types stricts, syntaxe moderne)
5. Le layout `app.blade.php` doit inclure la navbar avec : lien Blog, lien Communauté, icône cloche notifications, lien profil, bouton déconnexion — et le bouton "Nouveau post" visible uniquement pour l'admin dans le contexte Blog, et pour tout utilisateur connecté dans le contexte Communauté
6. Le partial `_comment.blade.php` est récursif — il s'inclut lui-même pour afficher les réponses
7. La page `show.blade.php` d'une publication gère à la fois les types BLOG et COMMUNITY
8. Toutes les validations sont faites côté serveur dans les controllers ou dans des FormRequest dédiés
9. Les messages flash (succès/erreur) sont affichés dans le layout via `session('success')` et `session('error')`

---

*Prompt généré pour agent IA — Blog System Laravel 12 — Powell Fagnon / EPAC-UAC*