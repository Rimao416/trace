<?php
include 'Configs.php';

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseException;

class ConnectionTester {
    
    public function testConnection() {
        echo "🔍 Test de connexion à Back4App...\n\n";
        
        // Test 1: Santé du serveur
        $this->testServerHealth();
        
        // Test 2: Création d'un objet test
        $this->testCreateObject();
        
        // Test 3: Lecture d'objets
        $this->testReadObjects();
        
        // Test 4: Nettoyage
        $this->cleanupTestObjects();
        
        echo "\n✅ Tests de connexion terminés!\n";
    }
    
    private function testServerHealth() {
        echo "1️⃣ Test de santé du serveur...\n";
        
        try {
            $health = ParseClient::getServerHealth();
            
            if ($health['status'] === 200) {
                echo "   ✅ Serveur accessible (Status: {$health['status']})\n";
            } else {
                echo "   ❌ Problème serveur (Status: {$health['status']})\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Erreur: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function testCreateObject() {
        echo "2️⃣ Test de création d'objet...\n";
        
        try {
            $testObject = new ParseObject('TestConnection');
            $testObject->set('message', 'Hello from Trace App!');
            $testObject->set('timestamp', new DateTime());
            $testObject->set('testNumber', rand(1, 1000));
            
            $testObject->save();
            
            echo "   ✅ Objet créé avec succès (ID: {$testObject->getObjectId()})\n";
            
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors de la création: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function testReadObjects() {
        echo "3️⃣ Test de lecture d'objets...\n";
        
        try {
            $query = new ParseQuery('TestConnection');
            $query->limit(5);
            $query->descending('createdAt');
            
            $objects = $query->find();
            
            echo "   ✅ " . count($objects) . " objet(s) trouvé(s)\n";
            
            foreach ($objects as $obj) {
                echo "   📄 {$obj->getObjectId()}: {$obj->get('message')}\n";
            }
            
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors de la lecture: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function cleanupTestObjects() {
        echo "4️⃣ Nettoyage des objets de test...\n";
        
        try {
            $query = new ParseQuery('TestConnection');
            $objects = $query->find();
            
            foreach ($objects as $obj) {
                $obj->destroy();
            }
            
            echo "   ✅ " . count($objects) . " objet(s) de test supprimé(s)\n";
            
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors du nettoyage: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function showConfig() {
        echo "⚙️  Configuration actuelle:\n\n";
        echo "App Name: " . $GLOBALS['app_name'] . "\n";
        echo "Server URL: " . ParseClient::getServerURL() . "\n";
        echo "Website Path: " . $GLOBALS['WEBSITE_PATH'] . "\n";
        echo "\n";
        
        echo "📝 Pour configurer Back4App:\n";
        echo "1. Connectez-vous à https://www.back4app.com/\n";
        echo "2. Créez une nouvelle app ou sélectionnez une existante\n";
        echo "3. Allez dans Settings > Security & Keys\n";
        echo "4. Copiez les clés suivantes dans config.php:\n";
        echo "   - Application ID\n";
        echo "   - REST API Key\n";
        echo "   - Master Key\n";
        echo "\n";
    }
}

// Utilisation du script
if (php_sapi_name() === 'cli') {
    $tester = new ConnectionTester();
    
    $action = $argv[1] ?? 'test';
    
    switch ($action) {
        case 'test':
            $tester->testConnection();
            break;
            
        case 'config':
            $tester->showConfig();
            break;
            
        default:
            echo "Usage: php test_connection.php [test|config]\n";
            echo "  test   - Tester la connexion\n";
            echo "  config - Afficher les informations de configuration\n";
    }
} else {
    $tester = new ConnectionTester();
    $tester->testConnection();
}
?>