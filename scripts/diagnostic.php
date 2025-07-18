<?php
require 'vendor/autoload.php';
use Parse\ParseClient;
use Parse\ParseQuery;
use Parse\ParseUser;

// Vos clés
$APP_ID = 'yL1nJF8JS7Rk3jPGsNgTDZQHsirdzUIDqS0m50kZ';
$REST_KEY = '270PdwN5NX85fATOx6nFOo1Yq3CkEI7IJrd2ikJo';
$MASTER_KEY = 'JB5A3o6BzG9auaHz0G3Qbp71E4rOq1HYHr9NIwZc';

echo "🔍 DIAGNOSTIC DE CONNEXION BACK4APP\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Test 1: Initialisation
echo "📋 Test 1: Initialisation Parse\n";
try {
    ParseClient::initialize($APP_ID, $REST_KEY, $MASTER_KEY);
    echo "✅ Initialisation réussie\n\n";
} catch (Exception $e) {
    echo "❌ Erreur d'initialisation: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Configuration serveur - Version 1
echo "📋 Test 2a: Configuration serveur (version 1)\n";
try {
    ParseClient::setServerURL('https://parseapi.back4app.com', 'parse');
    echo "✅ Configuration serveur version 1 OK\n\n";
} catch (Exception $e) {
    echo "❌ Erreur config serveur v1: " . $e->getMessage() . "\n\n";
}

// Test 3: Requête simple
echo "📋 Test 3: Requête de test\n";
try {
    $query = new ParseQuery('_User');
    $query->limit(1);
    $users = $query->find();
    echo "✅ Requête réussie - " . count($users) . " utilisateur(s) trouvé(s)\n\n";
} catch (Exception $e) {
    echo "❌ Erreur requête: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n\n";
    
    // Test avec l'autre configuration
    echo "📋 Test 3b: Essai avec configuration alternative\n";
    try {
        ParseClient::setServerURL('https://parseapi.back4app.com/parse', '');
        $query = new ParseQuery('_User');
        $query->limit(1);
        $users = $query->find();
        echo "✅ Requête réussie avec config alternative - " . count($users) . " utilisateur(s)\n\n";
    } catch (Exception $e2) {
        echo "❌ Erreur avec config alternative: " . $e2->getMessage() . "\n\n";
    }
}

// Test 4: Informations de connexion
echo "📋 Test 4: Informations de connexion\n";
echo "APP_ID: " . substr($APP_ID, 0, 8) . "...\n";
echo "REST_KEY: " . substr($REST_KEY, 0, 8) . "...\n";
echo "MASTER_KEY: " . substr($MASTER_KEY, 0, 8) . "...\n\n";

echo "🔧 Si tous les tests échouent, vérifiez :\n";
echo "1. Vos clés dans le dashboard Back4App\n";
echo "2. Que votre app Back4App est ACTIVE\n";
echo "3. Les paramètres de sécurité de votre app\n";
echo "4. Votre connexion internet\n";
?>