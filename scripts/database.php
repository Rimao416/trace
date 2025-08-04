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