<?php
require 'vendor/autoload.php';
include 'Configs.php';

use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseACL;
use Parse\ParseQuery;

class UserSeeder {
   
    private $admins = [
        [
            'username' => 'admin',
            'email' => 'admin@trace.com',
            'password' => 'Admin123!',
            'firstName' => 'Super',
            'lastName' => 'Admin',
            'role' => 'super_admin'
        ],
        [
            'username' => 'moderator',
            'email' => 'moderator@trace.com',
            'password' => 'Moderator123!',
            'firstName' => 'Mod',
            'lastName' => 'Erator',
            'role' => 'moderator'
        ],
        [
            'username' => 'support',
            'email' => 'support@trace.com',
            'password' => 'Support123!',
            'firstName' => 'Support',
            'lastName' => 'Team',
            'role' => 'support'
        ]
    ];
   
    public function seedAdmins() {
        echo "🌱 Début du seed des administrateurs...\n\n";
       
        foreach ($this->admins as $adminData) {
            $this->createAdmin($adminData);
        }
       
        echo "\n✅ Seed des administrateurs terminé!\n";
    }
   
    private function createAdmin($adminData) {
        try {
            // Vérifier si l'utilisateur existe déjà
            if ($this->userExists($adminData['username'], $adminData['email'])) {
                echo "⚠️  L'utilisateur {$adminData['username']} existe déjà\n";
                return;
            }
           
            // Créer un nouveau utilisateur
            $user = new ParseUser();
            $user->set('username', $adminData['username']);
            $user->set('email', $adminData['email']);
            $user->set('password', $adminData['password']);
            
            // Ajouter les champs personnalisés APRÈS la création
            $user->set('firstName', $adminData['firstName']);
            $user->set('lastName', $adminData['lastName']);
            $user->set('role', $adminData['role']);
            $user->set('isActive', true);
            $user->set('isVerified', true);
            $user->set('createdBy', 'system');
            
            // Utiliser un timestamp au lieu de DateTime pour éviter les problèmes
            $user->set('lastLogin', new \DateTime());
           
            // Sauvegarder l'utilisateur AVANT de définir les ACL
            $user->signUp();
            
            // Définir les ACL APRÈS la création
            $acl = new ParseACL();
            $acl->setPublicReadAccess(false);
            $acl->setPublicWriteAccess(false);
            // Donner les permissions à l'utilisateur lui-même
            $acl->setReadAccess($user, true);
            $acl->setWriteAccess($user, true);
            $user->setACL($acl);
            
            // Sauvegarder à nouveau pour appliquer les ACL
            $user->save();
           
            echo "✅ Administrateur créé: {$adminData['username']} ({$adminData['email']}) - Role: {$adminData['role']}\n";
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la création de {$adminData['username']}: " . $e->getMessage() . "\n";
            echo "Code d'erreur: " . $e->getCode() . "\n";
        }
    }
   
    private function userExists($username, $email) {
        try {
            // Vérifier d'abord par username
            $usernameQuery = ParseUser::query();
            $usernameQuery->equalTo('username', $username);
            $userByUsername = $usernameQuery->first();
            
            if ($userByUsername) {
                return true;
            }
            
            // Ensuite par email
            $emailQuery = ParseUser::query();
            $emailQuery->equalTo('email', $email);
            $userByEmail = $emailQuery->first();
            
            return $userByEmail !== null;
           
        } catch (ParseException $e) {
            echo "⚠️  Erreur lors de la vérification d'existence: " . $e->getMessage() . "\n";
            return false;
        }
    }
   
    public function listAdmins() {
        echo "📋 Liste des administrateurs:\n\n";
       
        try {
            $query = ParseUser::query();
            $query->containedIn('role', ['super_admin', 'moderator', 'support']);
            $query->ascending('username');
           
            $admins = $query->find();
           
            if (empty($admins)) {
                echo "Aucun administrateur trouvé.\n";
                return;
            }
           
            foreach ($admins as $admin) {
                echo sprintf(
                    "👤 %s (%s) - %s %s - Role: %s - Actif: %s\n",
                    $admin->get('username') ?? 'N/A',
                    $admin->get('email') ?? 'N/A',
                    $admin->get('firstName') ?? 'N/A',
                    $admin->get('lastName') ?? 'N/A',
                    $admin->get('role') ?? 'N/A',
                    $admin->get('isActive') ? 'Oui' : 'Non'
                );
            }
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la récupération des administrateurs: " . $e->getMessage() . "\n";
        }
    }
   
    public function deleteAllAdmins() {
        echo "🗑️  Suppression de tous les administrateurs...\n\n";
       
        try {
            $query = ParseUser::query();
            $query->containedIn('role', ['super_admin', 'moderator', 'support']);
           
            $admins = $query->find();
           
            if (empty($admins)) {
                echo "Aucun administrateur à supprimer.\n";
                return;
            }
           
            foreach ($admins as $admin) {
                $username = $admin->get('username');
                $admin->destroy();
                echo "🗑️  Supprimé: {$username}\n";
            }
           
            echo "\n✅ Tous les administrateurs ont été supprimés!\n";
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la suppression: " . $e->getMessage() . "\n";
        }
    }
    
    // Méthode pour déboguer les problèmes
    public function debugUserCreation() {
        echo "🔍 Test de création d'utilisateur...\n\n";
        
        try {
            $testUser = new ParseUser();
            $testUser->set('username', 'test_debug');
            $testUser->set('email', 'test@debug.com');
            $testUser->set('password', 'TestDebug123!');
            
            echo "📝 Tentative de création d'un utilisateur de test...\n";
            $testUser->signUp();
            
            echo "✅ Utilisateur de test créé avec succès!\n";
            echo "🧹 Suppression de l'utilisateur de test...\n";
            
            $testUser->destroy();
            echo "✅ Utilisateur de test supprimé!\n";
            
        } catch (ParseException $e) {
            echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
            echo "Code d'erreur: " . $e->getCode() . "\n";
            
            // Codes d'erreur courants
            switch ($e->getCode()) {
                case 125:
                    echo "💡 Suggestion: Vérifiez que l'email n'est pas déjà utilisé\n";
                    break;
                case 202:
                    echo "💡 Suggestion: Vérifiez que le username n'est pas déjà utilisé\n";
                    break;
                case 200:
                    echo "💡 Suggestion: Vérifiez votre mot de passe (complexité requise)\n";
                    break;
                default:
                    echo "💡 Vérifiez votre configuration Parse dans Configs.php\n";
            }
        }
    }
}

// Utilisation du script
if (php_sapi_name() === 'cli') {
    $seeder = new UserSeeder();
   
    $action = $argv[1] ?? 'seed';
   
    switch ($action) {
        case 'seed':
            $seeder->seedAdmins();
            break;
           
        case 'list':
            $seeder->listAdmins();
            break;
           
        case 'delete':
            $seeder->deleteAllAdmins();
            break;
            
        case 'debug':
            $seeder->debugUserCreation();
            break;
           
        default:
            echo "Usage: php user_seeder.php [seed|list|delete|debug]\n";
            echo "  seed   - Créer les administrateurs\n";
            echo "  list   - Lister les administrateurs\n";
            echo "  delete - Supprimer tous les administrateurs\n";
            echo "  debug  - Tester la création d'utilisateur\n";
    }
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
}
?>

