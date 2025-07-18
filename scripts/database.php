<?php
require 'vendor/autoload.php';
include 'Configs.php';

use Parse\ParseObject;
use Parse\ParseUser;
use Parse\ParseACL;
use Parse\ParseQuery;
use Parse\ParseException;

class TraceDatabaseSeeder {
    
    private $categories = [];
    private $users = [];
    
    public function __construct() {
        echo "🗄️ INITIALISATION DE LA BASE DE DONNÉES TRACE\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
    }
    
    // 1. Créer les catégories
    public function seedCategories() {
        echo "📁 Création des catégories...\n";
        
        $categoriesData = [
            [
                'name' => 'Technologie',
                'slug' => 'technologie',
                'description' => 'Articles sur les nouvelles technologies',
                'color' => '#3498db',
                'icon' => 'laptop'
            ],
            [
                'name' => 'Développement',
                'slug' => 'developpement',
                'description' => 'Tutoriels et guides de développement',
                'color' => '#e74c3c',
                'icon' => 'code'
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Articles sur le design et l\'UX/UI',
                'color' => '#9b59b6',
                'icon' => 'palette'
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Actualités et conseils business',
                'color' => '#2ecc71',
                'icon' => 'briefcase'
            ],
            [
                'name' => 'Actualités',
                'slug' => 'actualites',
                'description' => 'Dernières actualités du secteur',
                'color' => '#f39c12',
                'icon' => 'news'
            ]
        ];
        
        foreach ($categoriesData as $catData) {
            try {
                // Vérifier si la catégorie existe déjà
                $query = new ParseQuery('Category');
                $query->equalTo('slug', $catData['slug']);
                $existing = $query->first();
                
                if ($existing) {
                    echo "⚠️  Catégorie '{$catData['name']}' existe déjà\n";
                    $this->categories[] = $existing;
                    continue;
                }
                
                $category = new ParseObject('Category');
                $category->set('name', $catData['name']);
                $category->set('slug', $catData['slug']);
                $category->set('description', $catData['description']);
                $category->set('color', $catData['color']);
                $category->set('icon', $catData['icon']);
                $category->set('isActive', true);
                $category->set('articlesCount', 0);
                
                // ACL public en lecture
                $acl = new ParseACL();
                $acl->setPublicReadAccess(true);
                $acl->setPublicWriteAccess(false);
                $category->setACL($acl);
                
                $category->save();
                $this->categories[] = $category;
                
                echo "✅ Catégorie créée: {$catData['name']} ({$catData['slug']})\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors de la création de la catégorie {$catData['name']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    // 2. Créer les tags
    public function seedTags() {
        echo "🏷️ Création des tags...\n";
        
        $tagsData = [
            'PHP', 'JavaScript', 'Python', 'React', 'Vue.js', 'Laravel', 
            'Node.js', 'MySQL', 'PostgreSQL', 'MongoDB', 'CSS', 'HTML',
            'Bootstrap', 'Tailwind', 'API', 'REST', 'GraphQL', 'Docker',
            'Git', 'GitHub', 'VSCode', 'Figma', 'Photoshop', 'SEO'
        ];
        
        foreach ($tagsData as $tagName) {
            try {
                // Vérifier si le tag existe déjà
                $query = new ParseQuery('Tag');
                $query->equalTo('name', $tagName);
                $existing = $query->first();
                
                if ($existing) {
                    echo "⚠️  Tag '{$tagName}' existe déjà\n";
                    continue;
                }
                
                $tag = new ParseObject('Tag');
                $tag->set('name', $tagName);
                $tag->set('slug', strtolower(str_replace([' ', '.'], ['-', ''], $tagName)));
                $tag->set('color', '#' . substr(md5($tagName), 0, 6));
                $tag->set('usageCount', 0);
                
                // ACL public en lecture
                $acl = new ParseACL();
                $acl->setPublicReadAccess(true);
                $acl->setPublicWriteAccess(false);
                $tag->setACL($acl);
                
                $tag->save();
                echo "✅ Tag créé: {$tagName}\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors de la création du tag {$tagName}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    // 3. Créer les paramètres de l'application
    public function seedAppSettings() {
        echo "⚙️ Création des paramètres de l'application...\n";
        
        $settingsData = [
            [
                'key' => 'site_title',
                'value' => 'Trace - Plateforme de Contenu',
                'type' => 'string',
                'description' => 'Titre principal du site'
            ],
            [
                'key' => 'site_description',
                'value' => 'Plateforme moderne de gestion de contenu et d\'articles',
                'type' => 'string',
                'description' => 'Description du site'
            ],
            [
                'key' => 'site_logo',
                'value' => '/assets/images/logo.png',
                'type' => 'string',
                'description' => 'Chemin vers le logo du site'
            ],
            [
                'key' => 'posts_per_page',
                'value' => '10',
                'type' => 'number',
                'description' => 'Nombre d\'articles par page'
            ],
            [
                'key' => 'comments_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Activer les commentaires'
            ],
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Autoriser les nouvelles inscriptions'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Mode maintenance activé'
            ],
            [
                'key' => 'default_theme',
                'value' => 'light',
                'type' => 'string',
                'description' => 'Thème par défaut (light/dark)'
            ]
        ];
        
        foreach ($settingsData as $settingData) {
            try {
                // Vérifier si le paramètre existe déjà
                $query = new ParseQuery('AppSetting');
                $query->equalTo('key', $settingData['key']);
                $existing = $query->first();
                
                if ($existing) {
                    echo "⚠️  Paramètre '{$settingData['key']}' existe déjà\n";
                    continue;
                }
                
                $setting = new ParseObject('AppSetting');
                $setting->set('key', $settingData['key']);
                $setting->set('value', $settingData['value']);
                $setting->set('type', $settingData['type']);
                $setting->set('description', $settingData['description']);
                $setting->set('isPublic', true);
                
                // ACL public en lecture seulement
                $acl = new ParseACL();
                $acl->setPublicReadAccess(true);
                $acl->setPublicWriteAccess(false);
                $setting->setACL($acl);
                
                $setting->save();
                echo "✅ Paramètre créé: {$settingData['key']} = {$settingData['value']}\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors de la création du paramètre {$settingData['key']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    // 4. Créer des articles d'exemple
    public function seedArticles() {
        echo "📝 Création d'articles d'exemple...\n";
        
        // Récupérer les utilisateurs admin
        $this->loadUsers();
        
        if (empty($this->users)) {
            echo "❌ Aucun utilisateur trouvé. Exécutez d'abord le user_seeder.php\n\n";
            return;
        }
        
        $articlesData = [
            [
                'title' => 'Bienvenue sur Trace',
                'content' => 'Ceci est votre premier article sur la plateforme Trace. Vous pouvez le modifier ou le supprimer à tout moment.',
                'excerpt' => 'Article de bienvenue sur la plateforme Trace',
                'status' => 'published',
                'featured' => true,
                'tags' => ['trace', 'bienvenue', 'premier-article']
            ],
            [
                'title' => 'Guide de démarrage rapide',
                'content' => 'Ce guide vous aidera à prendre en main rapidement la plateforme Trace et toutes ses fonctionnalités.',
                'excerpt' => 'Guide complet pour bien démarrer avec Trace',
                'status' => 'published',
                'featured' => false,
                'tags' => ['guide', 'démarrage', 'tutoriel']
            ],
            [
                'title' => 'Les nouvelles fonctionnalités',
                'content' => 'Découvrez les dernières fonctionnalités ajoutées à la plateforme Trace.',
                'excerpt' => 'Présentation des nouvelles fonctionnalités',
                'status' => 'published',
                'featured' => false,
                'tags' => ['nouveautés', 'fonctionnalités', 'mise-à-jour']
            ]
        ];
        
        foreach ($articlesData as $articleData) {
            try {
                // Vérifier si l'article existe déjà
                $query = new ParseQuery('Article');
                $query->equalTo('title', $articleData['title']);
                $existing = $query->first();
                
                if ($existing) {
                    echo "⚠️  Article '{$articleData['title']}' existe déjà\n";
                    continue;
                }
                
                $article = new ParseObject('Article');
                $article->set('title', $articleData['title']);
                $article->set('content', $articleData['content']);
                $article->set('excerpt', $articleData['excerpt']);
                $article->set('slug', $this->generateSlug($articleData['title']));
                $article->set('status', $articleData['status']);
                $article->set('featured', $articleData['featured']);
                $article->set('tags', $articleData['tags']);
                $article->set('author', $this->users[0]); // Premier utilisateur admin
                $article->set('viewCount', rand(0, 100));
                $article->set('likesCount', rand(0, 20));
                $article->set('commentsCount', 0);
                
                // Assigner une catégorie aléatoire si disponible
                if (!empty($this->categories)) {
                    $randomCategory = $this->categories[array_rand($this->categories)];
                    $article->set('category', $randomCategory);
                }
                
                // ACL public en lecture
                $acl = new ParseACL();
                $acl->setPublicReadAccess(true);
                $acl->setWriteAccess($this->users[0], true);
                $article->setACL($acl);
                
                $article->save();
                echo "✅ Article créé: {$articleData['title']}\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors de la création de l'article {$articleData['title']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    // 5. Créer la structure des pages
    public function seedPages() {
        echo "📄 Création des pages du site...\n";
        
        $pagesData = [
            [
                'title' => 'À propos',
                'content' => 'Cette page présente votre site et votre mission.',
                'slug' => 'a-propos',
                'template' => 'page',
                'isPublished' => true
            ],
            [
                'title' => 'Contact',
                'content' => 'Informations de contact et formulaire de contact.',
                'slug' => 'contact',
                'template' => 'contact',
                'isPublished' => true
            ],
            [
                'title' => 'Mentions légales',
                'content' => 'Mentions légales du site.',
                'slug' => 'mentions-legales',
                'template' => 'page',
                'isPublished' => true
            ],
            [
                'title' => 'Politique de confidentialité',
                'content' => 'Politique de confidentialité et gestion des données.',
                'slug' => 'politique-confidentialite',
                'template' => 'page',
                'isPublished' => true
            ]
        ];
        
        foreach ($pagesData as $pageData) {
            try {
                // Vérifier si la page existe déjà
                $query = new ParseQuery('Page');
                $query->equalTo('slug', $pageData['slug']);
                $existing = $query->first();
                
                if ($existing) {
                    echo "⚠️  Page '{$pageData['title']}' existe déjà\n";
                    continue;
                }
                
                $page = new ParseObject('Page');
                $page->set('title', $pageData['title']);
                $page->set('content', $pageData['content']);
                $page->set('slug', $pageData['slug']);
                $page->set('template', $pageData['template']);
                $page->set('isPublished', $pageData['isPublished']);
                $page->set('author', $this->users[0] ?? null);
                $page->set('viewCount', 0);
                
                // ACL public en lecture
                $acl = new ParseACL();
                $acl->setPublicReadAccess(true);
                $acl->setPublicWriteAccess(false);
                $page->setACL($acl);
                
                $page->save();
                echo "✅ Page créée: {$pageData['title']}\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors de la création de la page {$pageData['title']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    // Méthodes utilitaires
    private function loadUsers() {
        try {
            $query = ParseUser::query();
            $query->containedIn('role', ['super_admin', 'moderator', 'support']);
            $this->users = $query->find();
        } catch (ParseException $e) {
            echo "❌ Erreur lors du chargement des utilisateurs: " . $e->getMessage() . "\n";
        }
    }
    
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    // Méthode principale
    public function seedAll() {
        echo "🚀 Lancement du seed complet...\n\n";
        
        $this->seedCategories();
        $this->seedTags();
        $this->seedAppSettings();
        $this->seedArticles();
        $this->seedPages();
        
        echo "🎉 Seed terminé avec succès !\n";
        echo "\n📊 Résumé des classes créées :\n";
        echo "- Category (catégories d'articles)\n";
        echo "- Tag (tags pour les articles)\n";
        echo "- AppSetting (paramètres de l'application)\n";
        echo "- Article (articles de blog)\n";
        echo "- Page (pages statiques)\n";
        echo "\n🔍 Vous pouvez vérifier dans votre Dashboard Back4App > Database Browser\n";
    }
    
    // Méthode pour nettoyer la base
    public function cleanDatabase() {
        echo "🧹 Nettoyage de la base de données...\n\n";
        
        $classes = ['Category', 'Tag', 'AppSetting', 'Article', 'Page'];
        
        foreach ($classes as $className) {
            try {
                $query = new ParseQuery($className);
                $objects = $query->find();
                
                foreach ($objects as $object) {
                    $object->destroy();
                }
                
                echo "✅ Classe {$className} nettoyée (" . count($objects) . " objets supprimés)\n";
                
            } catch (ParseException $e) {
                echo "❌ Erreur lors du nettoyage de {$className}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n🎯 Nettoyage terminé !\n";
    }
}

// Utilisation du script
if (php_sapi_name() === 'cli') {
    $seeder = new TraceDatabaseSeeder();
    
    $action = $argv[1] ?? 'seed';
    
    switch ($action) {
        case 'seed':
            $seeder->seedAll();
            break;
            
        case 'categories':
            $seeder->seedCategories();
            break;
            
        case 'tags':
            $seeder->seedTags();
            break;
            
        case 'settings':
            $seeder->seedAppSettings();
            break;
            
        case 'articles':
            $seeder->seedArticles();
            break;
            
        case 'pages':
            $seeder->seedPages();
            break;
            
        case 'clean':
            $seeder->cleanDatabase();
            break;
            
        default:
            echo "Usage: php database_seeder.php [action]\n";
            echo "Actions disponibles:\n";
            echo "  seed      - Créer toutes les données (par défaut)\n";
            echo "  categories - Créer seulement les catégories\n";
            echo "  tags      - Créer seulement les tags\n";
            echo "  settings  - Créer seulement les paramètres\n";
            echo "  articles  - Créer seulement les articles\n";
            echo "  pages     - Créer seulement les pages\n";
            echo "  clean     - Nettoyer toute la base de données\n";
    }
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
}
?>