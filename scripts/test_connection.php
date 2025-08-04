<?php
include 'Configs.php';
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseException;

class ConnectionTester {
   
    public function testConnection() {
        echo "🔍 Test de connexion à Back4App...\n\n";
       
        // Test 1: Santé du serveur (méthode alternative)
        $this->testServerHealthAlternative();
       
        // Test 2: Création d'un objet test
        $this->testCreateObject();
       
        // Test 3: Lecture d'objets
        $this->testReadObjects();
       
        // Test 4: Nettoyage
        $this->cleanupTestObjects();
       
        echo "\n✅ Tests de connexion terminés!\n";
    }
   
    /**
     * Test de santé alternatif car getServerHealth() a des problèmes d'authentification
     * avec Back4App et les versions récentes du SDK
     */
    private function testServerHealthAlternative() {
        echo "1️⃣ Test de santé du serveur (méthode alternative)...\n";
       
        try {
            // Test avec une requête simple sur la classe _User
            $testQuery = new ParseQuery('_User');
            $testQuery->limit(5);
            
            // Tenter de faire un count() qui nécessite moins de permissions
            $count = $testQuery->count();
            
            echo "   ✅ Serveur accessible (Utilisateurs: $count)\n";
            
        } catch (ParseException $e) {
            // Si même le count échoue, tester avec une classe custom
            try {
                echo "   ⚠️  Accès _User limité, test avec classe personnalisée...\n";
                
                $testObj = new ParseObject('HealthCheck');
                $testObj->set('ping', 'pong');
                $testObj->set('timestamp', new DateTime());
                $testObj->save();
                
                // Si on arrive ici, la connexion fonctionne
                echo "   ✅ Serveur accessible (Test création réussi)\n";
                
                // Nettoyer immédiatement
                $testObj->destroy();
                
            } catch (ParseException $e2) {
                echo "   ❌ Erreur serveur: " . $e2->getMessage() . " (Code: " . $e2->getCode() . ")\n";
                
                // Diagnostics supplémentaires
                $this->diagnoseProblem($e2);
            }
            
        } catch (Exception $e) {
            echo "   ❌ Erreur générale: " . $e->getMessage() . "\n";
        }
       
        echo "\n";
    }
    
    /**
     * Méthode de diagnostic pour identifier les problèmes courants
     */
    private function diagnoseProblem($exception) {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        
        echo "   🔍 Diagnostic:\n";
        
        switch ($code) {
            case 100:
                echo "   • Code 100: Problème de connexion réseau\n";
                echo "   • Vérifiez votre connexion internet\n";
                echo "   • Vérifiez l'URL du serveur Parse\n";
                break;
                
            case 119:
                echo "   • Code 119: Permissions insuffisantes\n";
                echo "   • Vérifiez vos clés API (App ID, REST Key)\n";
                echo "   • Assurez-vous que les ACL permettent l'accès\n";
                break;
                
            case 141:
                echo "   • Code 141: Authentication invalide\n";
                echo "   • Vérifiez vos clés Back4App\n";
                echo "   • Assurez-vous que les clés sont correctes\n";
                break;
                
            default:
                echo "   • Code $code: $message\n";
        }
    }
   
    private function testCreateObject() {
        echo "2️⃣ Test de création d'objet...\n";
       
        try {
            $testObject = new ParseObject('TestConnection');
            $testObject->set('message', 'Hello from Trace App!');
            $testObject->set('timestamp', new DateTime());
            $testObject->set('testNumber', rand(1, 1000));
            $testObject->set('phpVersion', PHP_VERSION);
            $testObject->set('userAgent', $_SERVER['HTTP_USER_AGENT'] ?? 'CLI');
           
            $testObject->save();
           
            echo "   ✅ Objet créé avec succès (ID: {$testObject->getObjectId()})\n";
           
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors de la création: " . $e->getMessage() . " (Code: " . $e->getCode() . ")\n";
            
            // Conseils spécifiques selon l'erreur
            if ($e->getCode() === 119) {
                echo "   💡 Conseil: Vérifiez les permissions d'écriture dans Back4App\n";
            }
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
                $message = $obj->get('message') ?: 'N/A';
                $timestamp = $obj->getCreatedAt() ? $obj->getCreatedAt()->format('Y-m-d H:i:s') : 'N/A';
                echo "   📄 {$obj->getObjectId()}: $message (créé: $timestamp)\n";
            }
           
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors de la lecture: " . $e->getMessage() . " (Code: " . $e->getCode() . ")\n";
            
            if ($e->getCode() === 119) {
                echo "   💡 Conseil: Vérifiez les permissions de lecture dans Back4App\n";
            }
        }
       
        echo "\n";
    }
   
    private function cleanupTestObjects() {
        echo "4️⃣ Nettoyage des objets de test...\n";
       
        try {
            $query = new ParseQuery('TestConnection');
            $objects = $query->find();
            
            $deleteCount = 0;
            foreach ($objects as $obj) {
                try {
                    $obj->destroy();
                    $deleteCount++;
                } catch (ParseException $e) {
                    echo "   ⚠️  Impossible de supprimer {$obj->getObjectId()}: " . $e->getMessage() . "\n";
                }
            }
           
            echo "   ✅ $deleteCount objet(s) de test supprimé(s)\n";
           
        } catch (ParseException $e) {
            echo "   ❌ Erreur lors du nettoyage: " . $e->getMessage() . " (Code: " . $e->getCode() . ")\n";
        }
       
        echo "\n";
    }
   
    public function showConfig() {
        echo "⚙️  Configuration actuelle:\n\n";
        echo "App Name: " . ($GLOBALS['app_name'] ?? 'Non défini') . "\n";
        echo "PHP Version: " . PHP_VERSION . "\n";
        echo "Parse SDK Version: " . (class_exists('Parse\ParseClient') ? 'Installé' : 'Non installé') . "\n";
        echo "Website Path: " . ($GLOBALS['WEBSITE_PATH'] ?? 'Non défini') . "\n";
        echo "Timezone: " . date_default_timezone_get() . "\n";
        echo "\n";
       
        echo "📝 Configuration Back4App:\n";
        echo "1. Connectez-vous à https://www.back4app.com/\n";
        echo "2. Sélectionnez votre app ou créez-en une nouvelle\n";
        echo "3. Allez dans Settings > Security & Keys\n";
        echo "4. Copiez les clés suivantes dans Configs.php:\n";
        echo "   - Application ID\n";
        echo "   - REST API Key\n";
        echo "   - Master Key (optionnel mais recommandé)\n";
        echo "\n";
        
        echo "🔧 Résolution des problèmes courants:\n";
        echo "• Si 'Unauthorized': Vérifiez vos clés API\n";
        echo "• Si 'Permission denied': Configurez les ACL dans Back4App\n";
        echo "• Si 'Connection timeout': Vérifiez votre connexion internet\n";
        echo "• Pour PHP 8+: Utilisez les dernières versions du SDK\n";
        echo "\n";
    }
    
    /**
     * Test avancé de connexion avec plus de détails
     */
    public function advancedTest() {
        echo "🔬 Test avancé de connexion...\n\n";
        
        // Test des URLs
        echo "🌐 Configuration réseau:\n";
        echo "Server URL: " . ParseClient::getServerURL() . "\n";
        echo "Mount Path: " . ParseClient::getMountPath() . "\n";
        echo "\n";
        
        // Test des permissions
        $this->testPermissions();
        
        // Test de performance
        $this->testPerformance();
    }
    
    private function testPermissions() {
        echo "🔐 Test des permissions:\n";
        
        $permissions = [
            'read_user' => function() {
                $query = new ParseQuery('_User');
                return $query->count();
            },
            'create_object' => function() {
                $obj = new ParseObject('PermissionTest');
                $obj->set('test', true);
                $obj->save();
                return $obj->getObjectId();
            },
            'read_object' => function() {
                $query = new ParseQuery('PermissionTest');
                return $query->count();
            }
        ];
        
        foreach ($permissions as $name => $test) {
            try {
                $result = $test();
                echo "   ✅ $name: OK (résultat: $result)\n";
            } catch (Exception $e) {
                echo "   ❌ $name: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    }
    
    private function testPerformance() {
        echo "⚡ Test de performance:\n";
        
        $start = microtime(true);
        
        try {
            // Test simple
            $query = new ParseQuery('_User');
            $query->limit(1);
            $query->count();
            
            $duration = (microtime(true) - $start) * 1000; // en ms
            
            if ($duration < 500) {
                echo "   ✅ Excellente performance: " . round($duration, 2) . "ms\n";
            } elseif ($duration < 1000) {
                echo "   🟡 Performance correcte: " . round($duration, 2) . "ms\n";
            } else {
                echo "   🔴 Performance lente: " . round($duration, 2) . "ms\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Impossible de tester la performance: " . $e->getMessage() . "\n";
        }
        
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
            
        case 'advanced':
            $tester->advancedTest();
            break;
           
        default:
            echo "Usage: php test_connection.php [test|config|advanced]\n";
            echo "  test     - Tester la connexion (rapide)\n";
            echo "  config   - Afficher les informations de configuration\n";
            echo "  advanced - Test complet avec diagnostics\n";
    }
} else {
    $tester = new ConnectionTester();
    $tester->testConnection();
}
?>