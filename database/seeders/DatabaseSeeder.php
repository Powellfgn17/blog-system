<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création du répertoire avatars sur le disque public
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('avatars');

        // Utilitaire pour télécharger un avatar de test avec fallback silencieux
        $downloadAvatar = function (string $filename, string $url) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url);
                if ($response->successful()) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/' . $filename, $response->body());
                    return 'avatars/' . $filename;
                }
            } catch (\Exception $e) {
                // Pas de connexion internet ou timeout : fallback silencieux
            }
            return null;
        };

        // ============================================
        // 1. CRÉATION DES UTILISATEURS
        // ============================================
        
        // Admin principal (Editor in Chief)
        $adminAvatar = $downloadAvatar('admin.jpg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDUftEROSCdZGjahv07Cp6yC-3zLmK8OnxghQjZllUxOGOzP3bXX4CfLXoyBFTf6FpiRGvtDGl1kKARoZUEiAZryREeL2-2jmugi7xfZZteLbjuOGP6kb9ayF_ecamYLmISM2EoLht7e2BftT9gCCabJKzCh9rPObK0F-VsMUV37mM8Ozos0sdEsnuJDl2DrXTCD-H2_IcB9Rszpfqs-pBHv6kUc5LOUjYCE2RSLWpXMylgKM8Iky2CL-4cZ35b-_gKamg_fWdLk7id');
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@blog-system.test'],
            [
                'username' => 'julian_cross',
                'name' => 'Julian Cross',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_blocked' => false,
                'bio' => 'Editor in Chief of Thesaurus. Exploring the intersection of design, architecture, and technology.',
                'avatar' => $adminAvatar,
            ]
        );

        // Utilisateur actif - développeur
        $devAvatar = $downloadAvatar('dev.jpg', 'https://i.pravatar.cc/150?img=33');
        $devUser = User::query()->updateOrCreate(
            ['email' => 'dev@blog-system.test'],
            [
                'username' => 'dev_master',
                'name' => 'Thomas Martin',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_blocked' => false,
                'bio' => 'Développeur Full-Stack spécialisé Laravel et Vue.js. Toujours curieux d\'apprendre de nouvelles technologies.',
                'avatar' => $devAvatar,
            ]
        );

        // Utilisateur active - designer
        $designerAvatar = $downloadAvatar('designer.jpg', 'https://i.pravatar.cc/150?img=44');
        $designerUser = User::query()->updateOrCreate(
            ['email' => 'designer@blog-system.test'],
            [
                'username' => 'sophie_design',
                'name' => 'Sophie Bernard',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_blocked' => false,
                'bio' => 'UX/UI Designer avec 5 ans d\'expérience. Passionnée par l\'accessibilité et le design system.',
                'avatar' => $designerAvatar,
            ]
        );

        // Utilisateur actif - débutant
        $beginnerAvatar = $downloadAvatar('beginner.jpg', 'https://i.pravatar.cc/150?img=68');
        $beginnerUser = User::query()->updateOrCreate(
            ['email' => 'beginner@blog-system.test'],
            [
                'username' => 'junior_dev',
                'name' => 'Lucas Petit',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_blocked' => false,
                'bio' => 'Développeur junior en formation. Apprend Laravel et découvre le développement web.',
                'avatar' => $beginnerAvatar,
            ]
        );

        // Utilisateur bloqué (pour tester la modération)
        $blockedAvatar = $downloadAvatar('blocked.jpg', 'https://i.pravatar.cc/150?img=59');
        $blockedUser = User::query()->updateOrCreate(
            ['email' => 'blocked@blog-system.test'],
            [
                'username' => 'troll_account',
                'name' => 'Alex Spam',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_blocked' => true,
                'bio' => 'Compte de test pour la modération.',
                'avatar' => $blockedAvatar,
            ]
        );

        // Utilisateurs supplémentaires pour les interactions
        $extraUsers = collect();
        for ($i = 1; $i <= 5; $i++) {
            $avatar = $downloadAvatar("user_extra_{$i}.jpg", "https://i.pravatar.cc/150?img=" . (10 + $i));
            $extraUsers->push(User::factory()->create([
                'avatar' => $avatar,
            ]));
        }

        $allUsers = collect([$admin, $devUser, $designerUser, $beginnerUser, $blockedUser])->concat($extraUsers);
        $activeUsers = collect([$admin, $devUser, $designerUser, $beginnerUser])->concat($extraUsers);

        // ============================================
        // 2. CRÉATION DES CATÉGORIES
        // ============================================
        
        $categories = collect([
            ['name' => 'Architecture & Design', 'slug' => 'architecture-design'],
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Editorial Opinion', 'slug' => 'editorial-opinion'],
            ['name' => 'Deep Dive', 'slug' => 'deep-dive'],
            ['name' => 'Discussion', 'slug' => 'discussion'],
            ['name' => 'Editorial', 'slug' => 'editorial'],
        ])->map(fn ($cat) => Category::query()->updateOrCreate(
            ['slug' => $cat['slug']],
            ['name' => $cat['name']]
        ));

        // ============================================
        // 3. CRÉATION DES POSTS BLOG (par admin)
        // ============================================
        
        $blogPosts = collect([
            [
                'title' => 'The Silent Dialogue of Modernist Geometry',
                'body' => "Exploring how harsh angles and stark contrasts in contemporary structural design foster an unexpected sense of quietude and introspection in urban environments.\n\n<p>Modernist architecture, often characterized by its use of raw concrete, geometric precision, and an absence of applied ornamentation, is frequently misunderstood as cold or unfeeling. Yet, when one stands within these monumental spaces, a different reality emerges.</p><p>The deliberate use of light and shadow creates a dynamic interplay that changes throughout the day, effectively turning the building into a sundial of human experience.</p><p>It is in this starkness that we find a 'silent dialogue'—a space that doesn't dictate how one should feel through elaborate decoration, but rather offers a blank canvas for introspection.</p>",
                'category' => 'architecture-design',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDAX9Cyd1kf7_Hqp-O6iXCWIKpZtfqD4ClkotTpUWeyrWDvM6H2WEQ5M9bVpqTj8QQh9tZrR1xN22k5Hk5WcOtpHnMTgq09Qqe6i-J_tMAEyyMehYWPY10PsrsGQMf2O6N5rY9bZGTkeEMZB4vnh2qr6PcLRXZI8Dyqp_PXS6w9rmKR1M0U55-35lfXoUIoPMnwl9qwDY_pA6O_alnnrROHkHool6nn12sOrSngAFeRxrjj696cJ7jsBF6YhmWbaodDEi8IdXyLWGZp',
            ],
            [
                'title' => 'The Ethics of Generative Algorithms in Curated Spaces',
                'body' => "A deep dive into how AI is shaping the future of digital art installations.\n\n<p>As generative algorithms become increasingly sophisticated, their integration into curated physical spaces raises profound ethical and aesthetic questions. When a machine creates an environment that reacts and adapts to human presence, who is the true artist?</p><p>This piece explores a sophisticated digital installation art piece featuring glowing, generative geometric shapes suspended in a vast, minimalist gallery space.</p><p>The room, illuminated by high-key, soft white lighting, creates a bright, modern light-mode aesthetic, challenging our preconceived notions of authorship and creative intent in the 21st century.</p>",
                'category' => 'technology',
                'cover_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuApFSzl-sqkC7qeUgwG68dmAfIZmu2iuDF6sYyWw7J7l9Bw9H0ZAp62NT9gRk5c0cdsP4fa5SAfBdeKQqcVhFGsmE0vueDi591wdVNBsSL7JkNJdEyI991BaGe5XgG_923ub1D-pYKfGT7Ak26R263NGiCwJewHQqlyrr-I82F-axKtkXcNzAbhJIBcEFKT0RqH3fc-nZXJji4_JtVIyTDvkKw-0NJzz3c9m9oLsllcepD3LW1dWyoTg7pkEyHSIVYftnJkvk5bkWSr',
            ],
            [
                'title' => 'Why Typography Remains the Invisible Foundation of Trust',
                'body' => "In an era of hyper-visual stimulation, the quiet rigor of well-set type does more to establish authority than any high-fidelity image or interactive motion ever could. We explore the psychology behind serif pairings.\n\n<p>Trust is an ephemeral quality in digital design. While many rely on bold graphics or dynamic animations to capture attention, true authority is often established in the margins—quite literally.</p><p>The subtle cues of a well-chosen typeface, the rigorous attention to line height and kerning, and the thoughtful pairing of serifs signal to the reader that the content has been curated with care.</p><p>In a world of noise, good typography is the ultimate signal.</p>",
                'category' => 'editorial-opinion',
                'cover_image_url' => null,
            ],
            [
                'title' => 'Deconstructing the \'Clean\' Aesthetic: When Minimalism Becomes Sterile',
                'body' => "A critical essay on the pervasive trend of ultra-minimalist interfaces and interior spaces. At what point does removing visual clutter strip away human warmth and narrative?\n\n<p>The pursuit of the 'clean' aesthetic has dominated design for the better part of a decade. From software interfaces to modern homes, the mandate has been clear: reduce, simplify, eliminate.</p><p>But as we strip away the idiosyncratic details that give spaces and products character, we risk creating environments that feel profoundly sterile.</p><p>This essay examines the fine line between elegant minimalism and cold emptiness, arguing for a return to purposeful complexity and human-centric imperfection.</p>",
                'category' => 'deep-dive',
                'cover_image_url' => null,
            ],
            [
                'title' => 'The Dual-Identity Framework',
                'body' => "True design silence allows the community's voice to become the focal point.\n\n<p>The core philosophy of the Dual-Identity framework is rooted in the concept of 'design silence'.</p><p>By intentionally recessing the interface—using subtle color palettes, stark geometry, and purposeful negative space—we create an environment where the content itself is elevated.</p><p>It is not about creating a beautiful container for its own sake, but rather building an invisible scaffolding that empowers the voices within the community to resonate clearly.</p>",
                'category' => 'editorial',
                'cover_image_url' => null,
            ],
        ])->map(fn ($post) => Post::query()->updateOrCreate(
            ['title' => $post['title']],
            [
                'user_id' => $admin->id,
                'category_id' => $categories->firstWhere('slug', $post['category'])->id,
                'type' => Post::TYPE_BLOG,
                'body' => $post['body'],
                'cover_image_url' => $post['cover_image_url'] ?? null,
            ]
        ));

        // ============================================
        // 4. CRÉATION DES POSTS COMMUNITY
        // ============================================
        
        $communityPosts = collect([
            [
                'title' => 'Quel framework PHP pour 2024 ? Laravel vs Symfony',
                'body' => "Je débute en développement PHP et je me demande quel framework choisir pour mes projets. J'ai entendu parler de Laravel et Symfony, mais je ne sais pas lequel correspondrait le mieux à mes besoins.\n\nJ'ai déjà des bases en HTML/CSS et un peu de JavaScript. Je veux créer des applications web modernes avec une bonne courbe d'apprentissage.\n\nQuels sont vos avis ? Quels sont les avantages et inconvénients de chacun ?",
                'author' => $beginnerUser,
                'category' => 'discussion',
            ],
            [
                'title' => 'Problème avec les relations Eloquent - Besoin d\'aide',
                'body' => "Salut la communauté !\n\nJ'ai un problème avec les relations Eloquent dans Laravel. J'ai trois tables : users, posts et comments.\n\nUn user a plusieurs posts, et un post a plusieurs comments. Je veux récupérer tous les comments d'un user avec les posts associés.\n\nJ'ai essayé ça :\n```php\n\$comments = User::with('posts.comments')->find(\$id);\n```\n\nMais ça ne me donne pas ce que je veux. Quelqu'un peut m'aider ?\n\nMerci d'avance pour votre aide !",
                'author' => $beginnerUser,
                'category' => 'technology',
            ],
            [
                'title' => 'Mon setup de développement Laravel en 2024',
                'body' => "Je partage mon setup de développement pour Laravel que j'affine depuis quelques années. Peut-être que ça aidera certains d'entre vous !\n\n**IDE**\n- VS Code avec les extensions : Laravel Extra Intellisense, PHP Intelephense, GitLens\n\n**Outils locaux**\n- Laravel Sail pour l'environnement Docker\n- Xdebug pour le debugging\n\n**Extensions Chrome**\n- Laravel Debugbar (indispensable !)\n- Vue.js devtools\n\n**Workflow**\n1. Je commence par les tests PHPUnit\n2. J'utilise les factories pour les données de test\n3. J'applique TDD quand c'est possible\n\nEt vous, quel est votre setup ?",
                'author' => $devUser,
                'category' => 'technology',
            ],
            [
                'title' => 'Comment gérer le dark mode dans une application Laravel ?',
                'body' => "Je travaille sur une application Laravel avec Vue.js et je veux implémenter un dark mode. J'hésite entre plusieurs approches :\n\n1. **Tailwind dark mode avec class**\nAvantages : Simple à mettre en place\nInconvénients : Nécessite JavaScript\n\n2. **Préférence système**\nAvantages : Respect les préférences utilisateur\nInconvénients : Moins de contrôle\n\n3. **Cookie + Middleware**\nAvantages : Persistance côté serveur\nInconvénients : Plus complexe\n\nQuelle approche recommandez-vous ? Avez-vous des retours d'expérience ?",
                'author' => $designerUser,
                'category' => 'architecture-design',
            ],
            [
                'title' => 'Déployer Laravel sur VPS - Guide complet',
                'body' => "Après plusieurs années à déployer des applications Laravel, je partage mon guide complet pour un déploiement sur VPS.\n\n**Prérequis**\n- Ubuntu 22.04 LTS\n- Nginx\n- PHP 8.2+\n- MySQL ou PostgreSQL\n- Composer\n\n**Étapes**\n\n1. **Configuration du serveur**\n```bash\nsudo apt update\nsudo apt install nginx php8.2-fpm php8.2-mysql\n```\n\n2. **Installation de Composer**\n```bash\ncurl -sS https://getcomposer.org/installer | php\nsudo mv composer.phar /usr/local/bin/composer\n```\n\n3. **Configuration de l'application**\n- Cloner le repository\n- Installer les dépendances\n- Configurer .env\n- Générer la clé d'application\n- Exécuter les migrations\n\n4. **Configuration Nginx**\nCréer un virtual host avec la configuration Laravel...\n\n5. **SSL avec Let's Encrypt**\nUtiliser certbot pour un certificat gratuit.\n\nAvez-vous des questions ou des améliorations à suggérer ?",
                'author' => $devUser,
                'category' => 'technology',
            ],
            [
                'title' => 'Les nouveautés de Laravel 12 - Ce qu\'il faut savoir',
                'body' => "Laravel 12 vient de sortir et apporte son lot de nouveautés ! Voici un résumé des changements les plus importants.\n\n**Nouvelles fonctionnalités**\n\n1. **Improved Queue System**\n- Meilleure gestion des jobs failed\n- Nouvelle interface pour monitoring\n\n2. **Enhanced Eloquent**\n- Nouveaux scopes globaux\n- Amélioration des performances\n\n3. **Better Testing**\n- Nouveaux assertions\n- Meilleure intégration avec Pest\n\n**Breaking Changes**\n- Certaines méthodes dépréciées ont été supprimées\n- La configuration par défaut a changé\n\nJe prépare un article détaillé sur chaque nouveauté. Dites-moi si vous êtes intéressés par un sujet en particulier !",
                'author' => $admin,
                'category' => 'technology',
            ],
        ])->map(fn ($post) => Post::query()->updateOrCreate(
            ['title' => $post['title']],
            [
                'user_id' => $post['author']->id,
                'category_id' => $categories->firstWhere('slug', $post['category'])->id,
                'type' => Post::TYPE_COMMUNITY,
                'body' => $post['body'],
            ]
        ));

        $allPosts = $blogPosts->concat($communityPosts);

        // ============================================
        // 5. CRÉATION DES COMMENTAIRES HIÉRARCHIQUES
        // ============================================
        
        // Commentaires pour le post "Quel framework PHP"
        $frameworkPost = $communityPosts->firstWhere('title', 'Quel framework PHP pour 2024 ? Laravel vs Symfony');
        if ($frameworkPost) {
            Comment::query()->updateOrCreate(
                ['post_id' => $frameworkPost->id, 'body' => "Je te recommande Laravel pour débuter ! La courbe d'apprentissage est plus douce et la documentation est excellente. Il y a aussi une très grande communauté.", 'user_id' => $devUser->id, 'parent_id' => null],
                ['post_id' => $frameworkPost->id, 'body' => "Je te recommande Laravel pour débuter ! La courbe d'apprentissage est plus douce et la documentation est excellente. Il y a aussi une très grande communauté.", 'user_id' => $devUser->id, 'parent_id' => null]
            );
            
            $comment1 = Comment::query()->where('post_id', $frameworkPost->id)->where('user_id', $devUser->id)->first();
            if ($comment1) {
                Comment::query()->updateOrCreate(
                    ['post_id' => $frameworkPost->id, 'body' => "Merci pour ta réponse ! Je vais commencer par Laravel alors. Tu connais des bons tutoriels ?", 'user_id' => $beginnerUser->id, 'parent_id' => $comment1->id],
                    ['post_id' => $frameworkPost->id, 'body' => "Merci pour ta réponse ! Je vais commencer par Laravel alors. Tu connais des bons tutoriels ?", 'user_id' => $beginnerUser->id, 'parent_id' => $comment1->id]
                );
                
                Comment::query()->updateOrCreate(
                    ['post_id' => $frameworkPost->id, 'body' => "Laravel官方文档就是最好的教程！另外推荐Laracasts，上面有很多高质量的视频教程。", 'user_id' => $extraUsers->get(0)->id, 'parent_id' => $comment1->id],
                    ['post_id' => $frameworkPost->id, 'body' => "Laravel官方文档就是最好的教程！另外推荐Laracasts，上面有很多高质量的视频教程。", 'user_id' => $extraUsers->get(0)->id, 'parent_id' => $comment1->id]
                );
            }
            
            Comment::query()->updateOrCreate(
                ['post_id' => $frameworkPost->id, 'body' => "Symfony est excellent pour les projets d'entreprise. Si tu vises une carrière dans les grandes entreprises, c'est un bon choix. Mais pour débuter et créer des projets rapidement, Laravel est plus adapté.", 'user_id' => $admin->id, 'parent_id' => null],
                ['post_id' => $frameworkPost->id, 'body' => "Symfony est excellent pour les projets d'entreprise. Si tu vises une carrière dans les grandes entreprises, c'est un bon choix. Mais pour débuter et créer des projets rapidement, Laravel est plus adapté.", 'user_id' => $admin->id, 'parent_id' => null]
            );
        }

        // Commentaires pour le post "Problème avec les relations Eloquent"
        $eloquentPost = $communityPosts->firstWhere('title', 'Problème avec les relations Eloquent - Besoin d\'aide');
        if ($eloquentPost) {
            Comment::query()->updateOrCreate(
                ['post_id' => $eloquentPost->id, 'body' => "Ton code est presque correct ! Le problème c'est que with('posts.comments') va charger tous les posts avec tous leurs comments. Essaie plutôt :\n\n```php\n\$comments = Comment::with('post.user')->whereHas('post', function(\$q) use (\$id) {\n    \$q->where('user_id', \$id);\n})->get();\n```", 'user_id' => $devUser->id, 'parent_id' => null],
                ['post_id' => $eloquentPost->id, 'body' => "Ton code est presque correct ! Le problème c'est que with('posts.comments') va charger tous les posts avec tous leurs comments. Essaie plutôt :\n\n```php\n\$comments = Comment::with('post.user')->whereHas('post', function(\$q) use (\$id) {\n    \$q->where('user_id', \$id);\n})->get();\n```", 'user_id' => $devUser->id, 'parent_id' => null]
            );
            
            $comment1 = Comment::query()->where('post_id', $eloquentPost->id)->where('user_id', $devUser->id)->first();
            if ($comment1) {
                Comment::query()->updateOrCreate(
                    ['post_id' => $eloquentPost->id, 'body' => "Génial ! Ça marche parfaitement. Merci beaucoup @dev_master !", 'user_id' => $beginnerUser->id, 'parent_id' => $comment1->id],
                    ['post_id' => $eloquentPost->id, 'body' => "Génial ! Ça marche parfaitement. Merci beaucoup @dev_master !", 'user_id' => $beginnerUser->id, 'parent_id' => $comment1->id]
                );
            }
        }

        // Commentaires pour le post "Mon setup de développement"
        $setupPost = $communityPosts->firstWhere('title', 'Mon setup de développement Laravel en 2024');
        if ($setupPost) {
            Comment::query()->updateOrCreate(
                ['post_id' => $setupPost->id, 'body' => "Excellent partage ! J'utilise aussi Laravel Sail et c'est vraiment pratique. Par contre j'ai du mal à configurer Xdebug avec Sail, tu as un guide ?", 'user_id' => $beginnerUser->id, 'parent_id' => null],
                ['post_id' => $setupPost->id, 'body' => "Excellent partage ! J'utilise aussi Laravel Sail et j'utilise aussi Laravel Sail et c'est vraiment pratique. Par contre j'ai du mal à configurer Xdebug avec Sail, tu as un guide ?", 'user_id' => $beginnerUser->id, 'parent_id' => null]
            );
            
            Comment::query()->updateOrCreate(
                ['post_id' => $setupPost->id, 'body' => "Pour Xdebug avec Sail, tu dois ajouter la configuration dans docker-compose.yml. Je peux te partager mon fichier si tu veux.", 'user_id' => $devUser->id, 'parent_id' => null],
                ['post_id' => $setupPost->id, 'body' => "Pour Xdebug avec Sail, tu dois ajouter la configuration dans docker-compose.yml. Je peux te partager mon fichier si tu veux.", 'user_id' => $devUser->id, 'parent_id' => null]
            );
            
            Comment::query()->updateOrCreate(
                ['post_id' => $setupPost->id, 'body' => "Moi j'utilise PHPStorm au lieu de VS Code. L'intégration Laravel est native et très puissante. Ça vaut le prix si tu fais du Laravel tous les jours.", 'user_id' => $extraUsers->get(1)->id, 'parent_id' => null],
                ['post_id' => $setupPost->id, 'body' => "Moi j'utilise PHPStorm au lieu de VS Code. L'intégration Laravel est native et très puissante. Ça vaut le prix si tu fais du Laravel tous les jours.", 'user_id' => $extraUsers->get(1)->id, 'parent_id' => null]
            );
        }

        // Commentaires pour le post "Dark mode"
        $darkModePost = $communityPosts->firstWhere('title', 'Comment gérer le dark mode dans une application Laravel ?');
        if ($darkModePost) {
            Comment::query()->updateOrCreate(
                ['post_id' => $darkModePost->id, 'body' => "J'utilise l'approche Tailwind dark mode avec class + localStorage. C'est simple et ça marche bien. Voici mon code :\n\n```javascript\n// Check localStorage or system preference\nif (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {\n  document.documentElement.classList.add('dark')\n} else {\n  document.documentElement.classList.remove('dark')\n}\n```", 'user_id' => $devUser->id, 'parent_id' => null],
                ['post_id' => $darkModePost->id, 'body' => "J'utilise l'approche Tailwind dark mode avec class + localStorage. C'est simple et ça marche bien. Voici mon code :\n\n```javascript\n// Check localStorage or system preference\nif (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {\n  document.documentElement.classList.add('dark')\n} else {\n  document.documentElement.classList.remove('dark')\n}\n```", 'user_id' => $devUser->id, 'parent_id' => null]
            );
            
            Comment::query()->updateOrCreate(
                ['post_id' => $darkModePost->id, 'body' => "Perso je préfère la préférence système. C'est plus respectueux de l'utilisateur et ça demande moins de code. Les utilisateurs qui veulent forcer peuvent le faire dans leurs paramètres système.", 'user_id' => $designerUser->id, 'parent_id' => null],
                ['post_id' => $darkModePost->id, 'body' => "Perso je préfère la préférence système. C'est plus respectueux de l'utilisateur et ça demande moins de code. Les utilisateurs qui veulent forcer peuvent le faire dans leurs paramètres système.", 'user_id' => $designerUser->id, 'parent_id' => null]
            );
        }

        // Commentaires pour les posts blog
        foreach ($blogPosts as $blogPost) {
            Comment::factory()->count(random_int(2, 5))->create([
                'post_id' => $blogPost->id,
                'user_id' => $activeUsers->random()->id,
                'parent_id' => null,
            ]);
        }

        // ============================================
        // 6. AJOUT DES RÉACTIONS
        // ============================================
        
        foreach ($allPosts as $post) {
            // Réactions sur les posts
            $reactionCount = random_int(3, 8);
            $reactingUsers = $activeUsers->shuffle()->take($reactionCount);
            
            foreach ($reactingUsers as $user) {
                Reaction::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'reactable_id' => $post->id,
                        'reactable_type' => $post->getMorphClass(),
                    ],
                    [
                        'type' => fake()->randomElement(Reaction::TYPES),
                    ]
                );
            }
            
            // Réactions sur les commentaires du post
            $comments = $post->comments;
            foreach ($comments as $comment) {
                if (random_int(0, 1) === 1) {
                    $commentReactionUsers = $activeUsers->shuffle()->take(random_int(1, 3));
                    foreach ($commentReactionUsers as $user) {
                        Reaction::query()->updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'reactable_id' => $comment->id,
                                'reactable_type' => $comment->getMorphClass(),
                            ],
                            [
                                'type' => fake()->randomElement(Reaction::TYPES),
                            ]
                        );
                    }
                }
            }
        }

        // ============================================
        // 7. AJOUT DES SIGNETS
        // ============================================
        
        // Le débutant bookmark quelques posts intéressants
        Bookmark::query()->updateOrCreate(
            ['user_id' => $beginnerUser->id, 'post_id' => $blogPosts->first()->id],
            ['user_id' => $beginnerUser->id, 'post_id' => $blogPosts->first()->id]
        );
        
        Bookmark::query()->updateOrCreate(
            ['user_id' => $beginnerUser->id, 'post_id' => $communityPosts->firstWhere('title', 'Mon setup de développement Laravel en 2024')->id],
            ['user_id' => $beginnerUser->id, 'post_id' => $communityPosts->firstWhere('title', 'Mon setup de développement Laravel en 2024')->id]
        );

        // Le designer bookmark les posts design
        Bookmark::query()->updateOrCreate(
            ['user_id' => $designerUser->id, 'post_id' => $blogPosts->firstWhere('title', 'The Silent Dialogue of Modernist Geometry')->id],
            ['user_id' => $designerUser->id, 'post_id' => $blogPosts->firstWhere('title', 'The Silent Dialogue of Modernist Geometry')->id]
        );

        // ============================================
        // 8. AJOUT DES NOTIFICATIONS
        // ============================================
        
        // Notifications pour le débutant (réponses à ses commentaires)
        $frameworkPost = $communityPosts->firstWhere('title', 'Quel framework PHP pour 2024 ? Laravel vs Symfony');
        if ($frameworkPost) {
            $comment = Comment::query()->where('post_id', $frameworkPost->id)->where('user_id', $beginnerUser->id)->whereNotNull('parent_id')->first();
            if ($comment) {
                Notification::query()->updateOrCreate(
                    ['user_id' => $beginnerUser->id, 'notifiable_id' => $comment->id, 'notifiable_type' => Comment::class],
                    [
                        'type' => Notification::TYPE_REPLY,
                        'data' => [
                            'message' => "{$devUser->name} a répondu à votre commentaire.",
                            'post_id' => $frameworkPost->id,
                            'url' => route('community.show', $frameworkPost),
                        ],
                    ]
                );
            }
        }

        // Notifications pour le dev (réactions sur ses posts)
        $setupPost = $communityPosts->firstWhere('title', 'Mon setup de développement Laravel en 2024');
        if ($setupPost) {
            Notification::query()->updateOrCreate(
                ['user_id' => $devUser->id, 'notifiable_id' => $setupPost->id, 'notifiable_type' => Post::class],
                [
                    'type' => Notification::TYPE_REACTION,
                    'data' => [
                        'message' => "{$beginnerUser->name} a réagi à votre publication.",
                        'post_id' => $setupPost->id,
                        'url' => route('community.show', $setupPost),
                    ],
                ]
            );
        }

        // ============================================
        // 9. AJOUT DES SIGNALEMENTS (pour tester la modération)
        // ============================================
        
        // Signalement d'un commentaire (par l'utilisateur bloqué)
        $eloquentPost = $communityPosts->firstWhere('title', 'Problème avec les relations Eloquent - Besoin d\'aide');
        if ($eloquentPost) {
            $comment = Comment::query()->where('post_id', $eloquentPost->id)->first();
            if ($comment) {
                Report::query()->updateOrCreate(
                    ['user_id' => $blockedUser->id, 'reportable_id' => $comment->id, 'reportable_type' => Comment::class],
                    [
                        'reason' => Report::REASON_SPAM,
                        'status' => Report::STATUS_PENDING,
                    ]
                );
            }
        }

        // Signalement d'un post (pour test)
        Report::query()->updateOrCreate(
            ['user_id' => $extraUsers->get(2)->id, 'reportable_id' => $communityPosts->first()->id, 'reportable_type' => Post::class],
            [
                'reason' => Report::REASON_OFFENSIVE,
                'status' => Report::STATUS_PENDING,
            ]
        );

        $this->command->info('✅ Données de démo scénarisées créées avec succès !');
        $this->command->info('📊 Statistiques :');
        $this->command->info("   - Utilisateurs : {$allUsers->count()}");
        $this->command->info("   - Catégories : {$categories->count()}");
        $this->command->info("   - Posts Blog : {$blogPosts->count()}");
        $this->command->info("   - Posts Community : {$communityPosts->count()}");
        $this->command->info("   - Commentaires : " . Comment::count());
        $this->command->info("   - Réactions : " . Reaction::count());
        $this->command->info("   - Signets : " . Bookmark::count());
        $this->command->info("   - Notifications : " . Notification::count());
        $this->command->info("   - Signalements : " . Report::count());
        $this->command->newLine();
        $this->command->info('🔐 Comptes de test :');
        $this->command->info("   - Admin: admin@blog-system.test / password");
        $this->command->info("   - Dev: dev@blog-system.test / password");
        $this->command->info("   - Designer: designer@blog-system.test / password");
        $this->command->info("   - Beginner: beginner@blog-system.test / password");
        $this->command->info("   - Blocked: blocked@blog-system.test / password");
    }
}
