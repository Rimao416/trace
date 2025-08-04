<?php
require 'vendor/autoload.php';
include 'Configs.php';
use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseACL;
use Parse\ParseQuery;

class UserSeeder {
   
    private $users = [
        [
            'username' => 'admin',
            'email' => 'admin@trace.com',
            'password' => 'Admin123!',
            'name' => 'Super Admin',
            'gender' => 'other',
            'role' => 'admin'
        ],
        [
            'username' => 'moderator',
            'email' => 'moderator@trace.com',
            'password' => 'Moderator123!',
            'name' => 'Mod Erator',
            'gender' => 'male',
            'role' => 'admin'
        ],
        [
            'username' => 'support',
            'email' => 'support@trace.com',
            'password' => 'Support123!',
            'name' => 'Support Team',
            'gender' => 'female',
            'role' => 'admin'
        ],
        [
            'username' => 'john_doe',
            'email' => 'john.doe@example.com',
            'password' => 'JohnDoe123!',
            'name' => 'John Doe',
            'gender' => 'male',
            'role' => 'admin'
        ],
        [
            'username' => 'jane_smith',
            'email' => 'jane.smith@example.com',
            'password' => 'JaneSmith123!',
            'name' => 'Jane Smith',
            'gender' => 'female',
            'role' => 'admin'
        ],
        [
            'username' => 'alex_wilson',
            'email' => 'alex.wilson@example.com',
            'password' => 'AlexWilson123!',
            'name' => 'Alex Wilson',
            'gender' => 'other',
            'role' => 'admin'
        ]
    ];
   
    public function seedUsers() {
        echo "🌱 Début du seed des utilisateurs...\n\n";
       
        foreach ($this->users as $userData) {
            $this->createUser($userData);
        }
       
        echo "\n✅ Seed des utilisateurs terminé!\n";
    }
   
    private function createUser($userData) {
        try {
            // Vérifier si l'utilisateur existe déjà
            if ($this->userExists($userData['username'], $userData['email'])) {
                echo "⚠️  L'utilisateur {$userData['username']} existe déjà\n";
                return;
            }
           
            // Créer un nouveau utilisateur
            $user = new ParseUser();
            $user->set('username', $userData['username']);
            $user->set('email', $userData['email']);
            $user->set('password', $userData['password']);
            $user->set('name', $userData['name']);
            $user->set('gender', $userData['gender']);
            $user->set('role', $userData['role']);
           
            // Sauvegarder l'utilisateur
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
           
            echo "✅ Utilisateur créé: {$userData['username']} ({$userData['email']}) - {$userData['name']} - Genre: {$userData['gender']} - Rôle: {$userData['role']}\n";
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la création de {$userData['username']}: " . $e->getMessage() . "\n";
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
   
    public function listUsers() {
        echo "📋 Liste des utilisateurs:\n\n";
       
        try {
            $query = ParseUser::query();
            $query->ascending('username');
           
            $users = $query->find();
           
            if (empty($users)) {
                echo "Aucun utilisateur trouvé.\n";
                return;
            }
           
            foreach ($users as $user) {
                echo sprintf(
                    "👤 %s (%s) - %s - Genre: %s - Rôle: %s\n",
                    $user->get('username') ?? 'N/A',
                    $user->get('email') ?? 'N/A',
                    $user->get('name') ?? 'N/A',
                    $user->get('gender') ?? 'N/A',
                    $user->get('role') ?? 'N/A'
                );
            }
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la récupération des utilisateurs: " . $e->getMessage() . "\n";
        }
    }
   
    public function deleteAllUsers() {
        echo "🗑️  Suppression de tous les utilisateurs...\n\n";
       
        try {
            $query = ParseUser::query();
            $users = $query->find();
           
            if (empty($users)) {
                echo "Aucun utilisateur à supprimer.\n";
                return;
            }
           
            foreach ($users as $user) {
                $username = $user->get('username');
                $user->destroy();
                echo "🗑️  Supprimé: {$username}\n";
            }
           
            echo "\n✅ Tous les utilisateurs ont été supprimés!\n";
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la suppression: " . $e->getMessage() . "\n";
        }
    }
   
    // Méthode pour filtrer par genre
    public function listUsersByGender($gender) {
        echo "📋 Liste des utilisateurs - Genre: {$gender}\n\n";
       
        try {
            $query = ParseUser::query();
            $query->equalTo('gender', $gender);
            $query->ascending('username');
           
            $users = $query->find();
           
            if (empty($users)) {
                echo "Aucun utilisateur trouvé pour le genre '{$gender}'.\n";
                return;
            }
           
            foreach ($users as $user) {
                echo sprintf(
                    "👤 %s (%s) - %s\n",
                    $user->get('username') ?? 'N/A',
                    $user->get('email') ?? 'N/A',
                    $user->get('name') ?? 'N/A'
                );
            }
           
        } catch (ParseException $e) {
            echo "❌ Erreur lors de la récupération des utilisateurs: " . $e->getMessage() . "\n";
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
            $testUser->set('name', 'Test Debug User');
            $testUser->set('gender', 'other');
            $testUser->set('role', 'admin');
           
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
    $parameter = $argv[2] ?? null;
   
    switch ($action) {
        case 'seed':
            $seeder->seedUsers();
            break;
           
        case 'list':
            $seeder->listUsers();
            break;
           
        case 'gender':
            if ($parameter) {
                $seeder->listUsersByGender($parameter);
            } else {
                echo "Usage: php user_seeder.php gender [male|female|other]\n";
            }
            break;
           
        case 'delete':
            $seeder->deleteAllUsers();
            break;
           
        case 'debug':
            $seeder->debugUserCreation();
            break;
           
        default:
            echo "Usage: php user_seeder.php [seed|list|gender|delete|debug]\n";
            echo "  seed           - Créer les utilisateurs\n";
            echo "  list           - Lister tous les utilisateurs\n";
            echo "  gender [type]  - Lister les utilisateurs par genre (male|female|other)\n";
            echo "  delete         - Supprimer tous les utilisateurs\n";
            echo "  debug          - Tester la création d'utilisateur\n";
    }
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
}
?>