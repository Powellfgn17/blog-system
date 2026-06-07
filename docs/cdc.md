# CAHIER DES CHARGES DÉTAILLÉ

## Système de Blog Personnel avec Espace Communautaire

**Version :** 1.1  
**Statut :** Final — Conception détaillée  
**Auteur :** Powell Fagnon — EPAC/UAC, GIT  
**Basé sur :** v1.0 (document final provisoire)

---

# 1. Présentation du projet

## 1.1 Contexte

Développement d'une plateforme de blog personnel avec un espace communautaire séparé, permettant à un propriétaire de publier des contenus officiels tout en offrant aux utilisateurs authentifiés un espace de publication indépendant. Les deux espaces coexistent sur la même plateforme sans jamais se mélanger visuellement.

## 1.2 Objectifs détaillés

| Objectif | Description |
|---|---|
| Publication d'articles | Le propriétaire publie des articles structurés dans le blog principal |
| Consultation libre | Tout visiteur, sans compte, peut lire les contenus et consulter les profils |
| Gestion des profils | Chaque utilisateur dispose d'une page de profil publique personnalisable |
| Espace communautaire | Les utilisateurs publient leurs propres contenus dans un espace dédié |
| Interactions sociales | Commentaires imbriqués, réactions multi-types, tags @username, favoris |
| Notifications | Alertes in-app pour toute interaction concernant un utilisateur |
| Modération | Signalements par les utilisateurs, actions administratives, blocage |
| Évolutivité | Architecture pensée pour accueillir l'IA et l'OAuth sans refactorisation majeure |

---

# 2. Espaces du système

## 2.1 Blog principal

- Réservé exclusivement au propriétaire/administrateur.
- Contient des articles structurés avec titre, contenu riche, catégorie et temps de lecture estimé.
- Les visiteurs et utilisateurs peuvent lire, commenter, réagir et mettre en favoris.
- Les publications de type `BLOG` ne peuvent être créées que par l'administrateur.

## 2.2 Espace communautaire

- Ouvert à tous les utilisateurs authentifiés.
- Le propriétaire peut également y publier.
- Les publications de type `COMMUNITY` appartiennent à leurs auteurs respectifs.
- Séparation visuelle stricte : l'espace communautaire possède sa propre section dans la navigation.

## 2.3 Règle de séparation

Une publication ne peut appartenir qu'à un seul espace. Le champ `type` dans la table `posts` détermine l'espace d'appartenance. Aucune publication de type `COMMUNITY` n'apparaît dans le blog principal, et inversement.

---

# 3. Acteurs du système

## 3.1 Visiteur non authentifié

**Fonctions autorisées :**
- Consulter tous les articles du blog principal.
- Consulter toutes les publications de l'espace communautaire.
- Consulter le profil public d'un utilisateur (sans les informations privées).
- Rechercher du contenu par mot-clé dans les titres et corps des publications.
- Parcourir les catégories et filtrer les publications par catégorie.

**Restrictions strictes :**
- Aucune publication, commentaire, réaction, tag ou mise en favori.
- Aucun accès aux notifications.
- Toute tentative d'interaction redirige vers la page de connexion.

---

## 3.2 Utilisateur authentifié

**Fonctions autorisées :**
- Gérer son profil personnel (photo, bio, nom affiché, mot de passe).
- Créer, modifier et supprimer ses publications dans l'espace communautaire.
- Ajouter, modifier et supprimer ses propres commentaires.
- Réagir aux publications et commentaires avec une réaction parmi 5 types.
- Taguer un ou plusieurs utilisateurs via `@username`.
- Ajouter ou retirer des publications de ses favoris.
- Recevoir et consulter ses notifications in-app.
- Signaler un contenu inapproprié.

**Restrictions :**
- Ne peut pas publier dans le blog principal.
- Ne peut pas modifier ou supprimer les publications/commentaires d'autres utilisateurs.
- Ne peut pas accéder au tableau de bord d'administration.
- Un compte bloqué perd l'accès à toutes ces fonctions.

---

## 3.3 Administrateur / Propriétaire

**Fonctions spécifiques en plus de celles de l'utilisateur authentifié :**
- Créer, modifier et supprimer des articles dans le blog principal (type `BLOG`).
- Modifier ou supprimer toute publication communautaire de n'importe quel utilisateur.
- Supprimer tout commentaire, quelle qu'en soit l'auteur.
- Gérer les catégories (création, modification, suppression).
- Consulter et traiter les signalements dans le tableau de bord.
- Bloquer ou débloquer n'importe quel utilisateur.
- Consulter la liste complète des utilisateurs.
- Accéder aux statistiques du système.

---

# 4. Authentification

## 4.1 Inscription

| Champ | Règle de validation |
|---|---|
| Username | Obligatoire — unique — entre 3 et 30 caractères — lettres, chiffres, underscores uniquement — pas d'espace |
| Email | Obligatoire — unique — format email valide |
| Mot de passe | Obligatoire — minimum 8 caractères — confirmation requise |

- Après inscription, l'utilisateur est connecté automatiquement ou redirigé vers la page de connexion selon la configuration.
- La vérification d'email est optionnelle en v1 mais prévue dans l'architecture (colonne `email_verified_at`).

## 4.2 Connexion

- Méthode : **email + mot de passe**.
- Le champ email est insensible à la casse lors de la vérification.
- En cas d'échec, un message générique est affiché (ne précise pas si c'est l'email ou le mot de passe qui est incorrect — sécurité).
- Après connexion réussie, redirection vers la dernière page visitée ou l'accueil.

## 4.3 Déconnexion

- La session est détruite côté serveur.
- Redirection vers la page d'accueil.

## 4.4 Fonctionnalités futures

- Connexion via Google OAuth.
- Connexion via GitHub.
- Connexion via Facebook.
- Authentification à deux facteurs (2FA) via application TOTP ou SMS.

---

# 5. Profil utilisateur

## 5.1 Champs du profil

| Champ | Obligatoire | Modifiable | Visible publiquement |
|---|---|---|---|
| Username (`@username`) | Oui | Non (après création) | Oui |
| Nom affiché | Oui | Oui | Oui |
| Photo de profil | Non | Oui | Oui |
| Biographie | Non | Oui | Oui |
| Date d'inscription | Auto | Non | Oui |
| Nombre de publications | Auto | Non (calculé) | Oui |
| Nombre de commentaires | Auto | Non (calculé) | Oui |
| Adresse email | Oui | Non | Non (privé) |

## 5.2 Règles sur le profil

- Le username ne peut pas être modifié après création du compte.
- La photo de profil est stockée sur le serveur ; une photo par défaut (avatar généré) est utilisée si aucune n'est uploadée.
- Le nombre de publications et de commentaires est calculé dynamiquement via les relations Eloquent, sans colonne dédiée en base.

## 5.3 Exemple d'affichage

```
[Photo]  Treasure
         @treasure_dev

         Passionné d'IA et de télécommunications.

         Publications : 12    Commentaires : 45
         Membre depuis : Mai 2026
```

## 5.4 Page profil publique

La page profil d'un utilisateur affiche :
- ses informations publiques (section 5.1) ;
- la liste de ses publications communautaires ;
- un bouton pour le taguer directement (raccourci).

---

# 6. Fonctionnalités

## 6.1 Publications

### Architecture : une seule table `posts`

Toutes les publications — blog principal et espace communautaire — sont stockées dans la même table `posts`. Un champ `type` distingue les deux espaces.

| Valeur `type` | Description | Qui peut créer |
|---|---|---|
| `BLOG` | Article du blog principal | Administrateur uniquement |
| `COMMUNITY` | Publication de l'espace communautaire | Tout utilisateur authentifié |

### Champs d'une publication

| Champ | Description |
|---|---|
| `id` | Identifiant unique |
| `user_id` | Auteur de la publication |
| `category_id` | Catégorie associée (optionnel) |
| `type` | `BLOG` ou `COMMUNITY` |
| `title` | Titre de la publication |
| `body` | Contenu principal |
| `reading_time` | Temps de lecture estimé en minutes (calculé automatiquement) |
| `created_at` | Date de création |
| `updated_at` | Date de dernière modification |

### Temps de lecture estimé

Chaque publication affiche automatiquement une estimation du temps de lecture dans son en-tête.

**Formule :** `nombre_de_mots ÷ 200`, arrondi à la minute supérieure.  
**Exemple :** un article de 850 mots affiche **"Lecture : 5 min"**.

Ce calcul est effectué dans le modèle `Post` via un accesseur Eloquent. Aucune colonne supplémentaire n'est nécessaire si le calcul est dynamique, ou la valeur peut être persistée à la création pour la performance.

### Règles de gestion

- Un utilisateur ne peut modifier ou supprimer que ses propres publications.
- L'administrateur peut modifier ou supprimer toute publication.
- La suppression d'une publication entraîne la suppression en cascade de ses commentaires, réactions et favoris associés.
- La liste des publications est affichée du plus récent au plus ancien avec pagination (10 par page par défaut).

---

## 6.2 Catégories

Les publications peuvent être associées à une catégorie pour faciliter la navigation.

- Les catégories sont gérées uniquement par l'administrateur.
- Un visiteur peut filtrer les publications par catégorie.
- Une catégorie peut contenir des publications de type `BLOG` et `COMMUNITY`.
- Exemples de catégories : Technologie, Actualité, Science, Tutoriel, Opinion.

---

## 6.3 Commentaires

### Fonctionnement

- Tout utilisateur authentifié peut commenter une publication.
- Un commentaire peut être une réponse à un autre commentaire (threads imbriqués).
- La profondeur des threads n'est pas limitée techniquement, mais l'affichage indente les réponses avec un décalage visuel.

### Structure hiérarchique

La colonne `parent_id` dans la table `comments` gère l'imbrication :
- `parent_id = null` → commentaire racine.
- `parent_id = X` → réponse au commentaire dont l'id est X.

### Règles

| Action | Qui peut l'effectuer |
|---|---|
| Ajouter un commentaire | Utilisateur authentifié |
| Modifier un commentaire | Auteur du commentaire uniquement |
| Supprimer un commentaire | Auteur du commentaire ou Administrateur |
| Voir les commentaires | Tout visiteur |

- La modification d'un commentaire affiche une mention "modifié" avec l'horodatage.
- La suppression d'un commentaire parent supprime en cascade toutes ses réponses.

---

## 6.4 Réactions

### Types disponibles

| Emoji | Label |
|---|---|
| 👍 | J'aime |
| ❤️ | J'adore |
| 😂 | Haha |
| 😢 | Triste |
| 😮 | Wow |

### Comportement

- Les réactions sont disponibles sur les publications et les commentaires.
- Un utilisateur ne peut attribuer **qu'une seule réaction** par publication ou commentaire.
- Changer de réaction remplace l'ancienne automatiquement.
- Cliquer sur la réaction déjà active l'annule (toggle).
- Le compteur de chaque type de réaction est affiché séparément.
- La réaction active de l'utilisateur connecté est mise en évidence visuellement.
- La table `reactions` est polymorphique : elle peut s'attacher à un `Post` ou à un `Comment`.

---

## 6.5 Tags (@username)

### Fonctionnement

Un utilisateur peut mentionner un autre utilisateur dans un commentaire ou une publication en tapant `@` suivi du username.

### Comportement détaillé

1. Dès que le caractère `@` est saisi, une liste de suggestions d'utilisateurs apparaît.
2. Les suggestions se filtrent en temps réel selon les caractères suivants.
3. La sélection d'un utilisateur insère le tag complet `@username` dans le texte.
4. À l'affichage, chaque `@username` est transformé en lien cliquable vers le profil de l'utilisateur tagué.
5. La personne taguée reçoit une notification de mention.
6. Plusieurs utilisateurs peuvent être tagués dans le même contenu.

---

## 6.6 Favoris (Bookmarks)

### Fonctionnement

Tout utilisateur authentifié peut sauvegarder des publications dans ses favoris pour les retrouver facilement.

### Comportement

- Un bouton "Ajouter aux favoris / Retirer des favoris" est présent sur chaque publication.
- L'état du bouton reflète si la publication est déjà dans les favoris de l'utilisateur connecté.
- La liste des favoris est accessible depuis le profil de l'utilisateur (section privée, visible uniquement par lui).
- La suppression d'une publication retire automatiquement celle-ci des favoris de tous les utilisateurs.
- Un utilisateur peut avoir un nombre illimité de favoris.

---

## 6.7 Notifications

### Événements déclencheurs

| Événement | Destinataire de la notification |
|---|---|
| Quelqu'un commente votre publication | Auteur de la publication |
| Quelqu'un répond à votre commentaire | Auteur du commentaire |
| Quelqu'un réagit à votre publication | Auteur de la publication |
| Quelqu'un réagit à votre commentaire | Auteur du commentaire |
| Vous êtes tagué dans une publication ou un commentaire | Utilisateur mentionné |

### Affichage

- Une icône cloche dans la barre de navigation affiche le nombre de notifications non lues.
- Un clic sur l'icône ouvre un panneau déroulant avec les notifications récentes.
- Chaque notification est cliquable et redirige vers le contenu concerné.
- Les notifications peuvent être marquées comme lues individuellement ou toutes à la fois.
- Les notifications lues restent visibles dans un historique.

### Version actuelle vs future

- **v1 :** notifications in-app uniquement.
- **Future :** notifications par email.

---

## 6.8 Fonctionnalités temps réel

Grâce à **Laravel Reverb** (serveur WebSocket) et **Laravel Echo** (client JavaScript), certains événements sont propagés en temps réel à tous les visiteurs d'une même page.

### Événements temps réel

| Événement | Effet |
|---|---|
| Nouveau commentaire ou réponse | Apparaît instantanément sur la page sans rechargement |
| Nouvelle réaction | Le compteur se met à jour pour tous les visiteurs actifs |

### Architecture événementielle

- Un `Event` Laravel est émis à chaque nouveau commentaire ou réaction.
- Cet événement est diffusé sur un **canal public** spécifique à la publication (`post.{id}`).
- Laravel Echo écoute ce canal côté client et met à jour le DOM dynamiquement.

---

# 7. Modération

## 7.1 Signalement par les utilisateurs

Tout utilisateur authentifié peut signaler un contenu inapproprié (publication ou commentaire).

### Processus de signalement

1. L'utilisateur clique sur "Signaler" sur la publication ou le commentaire.
2. Une fenêtre modale s'ouvre avec la liste des motifs.
3. L'utilisateur sélectionne un motif et soumet.
4. Le signalement est enregistré de manière invisible pour l'auteur du contenu.

### Motifs disponibles

- Spam
- Harcèlement
- Contenu offensant
- Contenu faux / désinformation
- Autre

### Règles

- Un utilisateur ne peut signaler le même contenu qu'une seule fois.
- Un même contenu peut être signalé par plusieurs utilisateurs différents.
- La table `reports` est polymorphique : elle peut pointer vers un `Post` ou un `Comment`.

---

## 7.2 Tableau de bord de modération (Administrateur)

L'administrateur dispose d'un espace dédié pour traiter les signalements.

### Fonctions disponibles

- Consulter la liste des contenus signalés, triés par nombre de signalements décroissant.
- Voir le détail du contenu signalé et tous les motifs soumis.
- **Supprimer** le contenu signalé.
- **Ignorer** le signalement (le marquer comme traité sans action).
- **Bloquer** l'auteur du contenu signalé directement depuis la vue.

---

## 7.3 Blocage d'un utilisateur

L'administrateur peut bloquer un compte depuis le tableau de bord ou depuis la page de modération.

### Effets du blocage

- L'utilisateur bloqué ne peut plus se connecter.
- Lors d'une tentative de connexion, un message explicite l'informe que son compte est suspendu.
- Ses publications et commentaires restent visibles (sauf suppression manuelle par l'admin).
- Toutes ses sessions actives sont invalidées immédiatement.
- Le déblocage est possible à tout moment ; le compte retrouve un fonctionnement normal.

---

# 8. Fonctionnalités IA (Version future)

## 8.1 Résumé intelligent des discussions

Un bouton **"Résumer le débat"** sur chaque publication analyse l'ensemble des commentaires via un LLM et affiche une synthèse.

### Comportement prévu

- Visible uniquement si la publication compte au moins 3 commentaires.
- La requête LLM est effectuée côté serveur (le controller appelle l'API Groq).
- Le résumé est affiché dans une carte dédiée sous le fil de commentaires.
- Un système de **cache** stocke le résumé pour éviter des appels API redondants. Le cache est invalidé si un nouveau commentaire est ajouté après la génération.
- Le résumé peut être régénéré manuellement.

**Technologie :** API Groq — modèle LLaMA 3.3 70B — client HTTP Guzzle.

---

## 8.2 Agent IA de modération automatique

Première couche de modération automatique avant intervention humaine.

### Fonctions prévues

- Détection automatique de spam dans les commentaires et publications.
- Détection de contenu potentiellement offensant ou haineux.
- Signalement automatique des contenus suspects dans le tableau de bord admin.
- La décision finale de suppression ou de blocage reste sous le contrôle de l'administrateur.

---

# 9. Sécurité

| Mesure | Description |
|---|---|
| Hash mot de passe | Algorithme **bcrypt** via `Hash::make()` de Laravel. Le mot de passe en clair n'est jamais stocké. |
| Protection CSRF | Token CSRF obligatoire sur tous les formulaires `POST`, `PUT`, `DELETE`. Géré automatiquement par Laravel avec la directive `@csrf`. |
| Protection XSS | Échappement automatique des données affichées dans les vues Blade via `{{ }}`. Toute donnée utilisateur est traitée comme non fiable. |
| Validation des entrées | Toutes les données soumises via formulaire sont validées côté serveur avec les `FormRequest` Laravel avant tout traitement. |
| Limitation des tentatives de connexion | Le `RateLimiter` de Laravel bloque temporairement un IP après 5 tentatives de connexion échouées consécutives. |
| Vérification email | Colonne `email_verified_at` prévue dans la table `users`. Activation optionnelle en v1, activée en production. |
| Middleware d'authentification | Les routes sensibles sont protégées par le middleware `auth`. Les routes admin sont protégées par un middleware `isAdmin` supplémentaire. |
| Autorisation (Policies) | Les actions sensibles (modifier/supprimer une publication) utilisent des **Laravel Policies** pour vérifier que l'utilisateur est bien propriétaire de la ressource. |

---

# 10. Base de données

## 10.1 Schéma des tables

### Table `users`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| username | VARCHAR(30) UNIQUE | Identifiant public |
| name | VARCHAR(100) | Nom affiché |
| email | VARCHAR(255) UNIQUE | Email de connexion |
| password | VARCHAR(255) | Mot de passe hashé (bcrypt) |
| avatar | VARCHAR(255) NULL | Chemin vers la photo de profil |
| bio | TEXT NULL | Biographie courte |
| is_blocked | BOOLEAN | Statut de blocage (défaut : false) |
| email_verified_at | TIMESTAMP NULL | Date de vérification email |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

---

### Table `posts`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Auteur |
| category_id | BIGINT FK → categories NULL | Catégorie associée |
| type | ENUM('BLOG', 'COMMUNITY') | Espace d'appartenance |
| title | VARCHAR(255) | Titre |
| body | TEXT | Contenu |
| reading_time | TINYINT NULL | Temps de lecture en minutes |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

---

### Table `categories`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| name | VARCHAR(100) UNIQUE | Nom de la catégorie |
| slug | VARCHAR(100) UNIQUE | Version URL-friendly |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

---

### Table `comments`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Auteur |
| post_id | BIGINT FK → posts | Publication concernée |
| parent_id | BIGINT FK → comments NULL | Commentaire parent (null = racine) |
| body | TEXT | Contenu du commentaire |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

---

### Table `reactions`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Utilisateur qui réagit |
| reactable_id | BIGINT | ID de la cible (post ou commentaire) |
| reactable_type | VARCHAR | Classe de la cible (polymorphisme) |
| type | ENUM('like','love','haha','sad','wow') | Type de réaction |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

**Contrainte d'unicité :** `(user_id, reactable_id, reactable_type)` — une seule réaction par utilisateur par cible.

---

### Table `notifications`
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Destinataire |
| type | VARCHAR(100) | Type d'événement (comment, reply, reaction, mention) |
| notifiable_id | BIGINT | ID de l'objet source |
| notifiable_type | VARCHAR | Classe de l'objet source (polymorphisme) |
| data | JSON | Données supplémentaires (texte, lien) |
| read_at | TIMESTAMP NULL | Date de lecture (null = non lu) |
| created_at | TIMESTAMP | Date de création |

---

### Table `reports` (signalements)
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Utilisateur signalant |
| reportable_id | BIGINT | ID du contenu signalé |
| reportable_type | VARCHAR | Classe du contenu signalé (polymorphisme) |
| reason | ENUM('spam','harassment','offensive','misinformation','other') | Motif |
| status | ENUM('pending','resolved','ignored') | Statut de traitement |
| created_at / updated_at | TIMESTAMP | Horodatages automatiques |

**Contrainte d'unicité :** `(user_id, reportable_id, reportable_type)` — un seul signalement par utilisateur par contenu.

---

### Table `bookmarks` (favoris)
| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant unique |
| user_id | BIGINT FK → users | Utilisateur |
| post_id | BIGINT FK → posts | Publication sauvegardée |
| created_at | TIMESTAMP | Date d'ajout aux favoris |

**Contrainte d'unicité :** `(user_id, post_id)` — une publication ne peut être en favori qu'une seule fois par utilisateur.

---

## 10.2 Relations Eloquent

```
User         → hasMany Posts
User         → hasMany Comments
User         → hasMany Notifications
User         → hasMany Bookmarks
User         → morphMany Reactions

Post         → belongsTo User
Post         → belongsTo Category
Post         → hasMany Comments
Post         → morphMany Reactions
Post         → hasMany Bookmarks

Comment      → belongsTo User
Comment      → belongsTo Post
Comment      → belongsTo Comment (parent)
Comment      → hasMany Comments (replies)
Comment      → morphMany Reactions

Reaction     → morphTo (Post ou Comment)
Notification → morphTo (source)
Report       → morphTo (Post ou Comment)
```

---

# 11. Technologies

## 11.1 Backend

| Technologie | Rôle |
|---|---|
| Laravel 11 | Framework PHP MVC principal |
| Eloquent ORM | Gestion des modèles et relations base de données |
| Laravel Blade | Moteur de templates pour les vues |
| Laravel Policies | Gestion des autorisations par ressource |
| Laravel Notifications | Système de notifications in-app |
| Laravel Queue | Traitement asynchrone (notifications, emails futurs) |
| Guzzle HTTP | Appels API externes (Groq pour l'IA future) |

## 11.2 Temps réel

| Technologie | Rôle |
|---|---|
| Laravel Reverb | Serveur WebSocket natif Laravel |
| Laravel Broadcasting | Émission d'événements côté serveur |
| Laravel Echo | Écoute des événements côté client (JavaScript) |

## 11.3 Base de données

| Technologie | Rôle |
|---|---|
| MySQL | Système de gestion de base de données relationnelle |
| Laravel Migrations | Versioning du schéma de base de données |
| Laravel Seeders | Données de test et données initiales |

## 11.4 Frontend

| Technologie | Rôle |
|---|---|
| Bootstrap 5 | Framework CSS pour l'interface utilisateur |
| JavaScript vanilla | Interactions dynamiques (toggle réactions, suggestions @tag) |
| Laravel Echo + Reverb | Mises à jour temps réel sans rechargement |

## 11.5 IA (version future)

| Technologie | Rôle |
|---|---|
| API Groq | Appels LLM pour le résumé de discussions |
| LLaMA 3.3 70B | Modèle de langage utilisé via Groq |

---

# 12. Architecture logique

```
BLOG PERSONNEL
│
├── Blog principal
│   ├── Articles (type: BLOG)
│   ├── Catégories
│   ├── Commentaires & Threads
│   ├── Réactions
│   ├── Tags @username
│   └── Temps de lecture estimé
│
├── Espace communautaire
│   ├── Publications (type: COMMUNITY)
│   ├── Catégories
│   ├── Commentaires & Threads
│   ├── Réactions
│   └── Tags @username
│
├── Profils utilisateurs
│   ├── Informations publiques
│   ├── Publications de l'utilisateur
│   └── Favoris (privé)
│
├── Notifications (in-app)
│   ├── Commentaires reçus
│   ├── Réponses reçues
│   ├── Réactions reçues
│   └── Mentions (@tag)
│
├── Temps réel (WebSocket)
│   ├── Nouveaux commentaires
│   └── Mises à jour des réactions
│
├── Gestion des comptes
│   ├── Inscription / Connexion
│   ├── Profil & Paramètres
│   └── Favoris
│
└── Administration
    ├── Tableau de bord
    ├── Modération des signalements
    ├── Gestion des utilisateurs (blocage)
    ├── Gestion des catégories
    └── Paramètres système
```

---

# 13. Structure du projet Laravel (MVC)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── PostController.php
│   │   ├── CommentController.php
│   │   ├── ReactionController.php
│   │   ├── NotificationController.php
│   │   ├── ReportController.php
│   │   ├── BookmarkController.php
│   │   ├── ProfileController.php
│   │   ├── CategoryController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       └── ModerationController.php
│   ├── Middleware/
│   │   └── IsAdmin.php
│   └── Requests/
│       ├── StorePostRequest.php
│       └── StoreCommentRequest.php
│
├── Models/
│   ├── User.php
│   ├── Post.php
│   ├── Category.php
│   ├── Comment.php
│   ├── Reaction.php
│   ├── Notification.php
│   ├── Report.php
│   └── Bookmark.php
│
├── Events/
│   └── CommentPosted.php
│
├── Listeners/
│   └── BroadcastComment.php
│
└── Policies/
    ├── PostPolicy.php
    └── CommentPolicy.php

database/
├── migrations/
└── seeders/

resources/views/
├── layouts/app.blade.php
├── posts/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── create.blade.php
├── community/
│   ├── index.blade.php
│   └── create.blade.php
├── comments/_comment.blade.php
├── profile/show.blade.php
├── notifications/index.blade.php
├── bookmarks/index.blade.php
└── admin/
    ├── dashboard.blade.php
    └── moderation.blade.php

routes/web.php
```

---

# 14. Récapitulatif des fonctionnalités

| Fonctionnalité | Catégorie | Statut |
|---|---|---|
| Inscription / Connexion | Fondamentale | v1 |
| Profil utilisateur | Fondamentale | v1 |
| CRUD publications (BLOG) | Fondamentale | v1 |
| CRUD publications (COMMUNITY) | Fondamentale | v1 |
| Catégories | Fondamentale | v1 |
| Commentaires imbriqués | Fondamentale | v1 |
| Modification / suppression commentaires | Fondamentale | v1 |
| Pagination | Fondamentale | v1 |
| Temps de lecture estimé | UX | v1 |
| Système de réactions (5 types) | Avancée | v1 |
| Tags @username avec suggestions | Avancée | v1 |
| Favoris (bookmarks) | Avancée | v1 |
| Notifications in-app | Avancée | v1 |
| Signalement de contenu | Avancée | v1 |
| Blocage utilisateur | Avancée | v1 |
| Tableau de bord modération | Avancée | v1 |
| Temps réel (WebSockets) | Très avancée | v1 |
| Résumé IA des discussions | Future | v2 |
| Agent IA de modération | Future | v2 |
| Connexion OAuth (Google/GitHub) | Future | v2 |
| Authentification 2FA | Future | v2 |
| Notifications par email | Future | v2 |

---

*Document de conception v1.1 — Blog Laravel MVC*  
*Prochaines étapes : diagrammes UML (cas d'utilisation, classes, séquences) puis modèle entité-relation.*