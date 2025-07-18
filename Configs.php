<?php
require 'vendor/autoload.php';
$app_name = 'Trace';
$default_icon_color = 'text-white'; // use Bootstrap text color sintax
use Parse\ParseClient;
use Parse\ParseSessionStorage;
session_start();

try {
    // ⚠️ REMPLACEZ CES CLÉS PAR VOS VRAIES CLÉS DE BACK4APP
    $APP_ID = 'yL1nJF8JS7Rk3jPGsNgTDZQHsirdzUIDqS0m50kZ';
    $REST_KEY = '270PdwN5NX85fATOx6nFOo1Yq3CkEI7IJrd2ikJo';
    $MASTER_KEY = 'JB5A3o6BzG9auaHz0G3Qbp71E4rOq1HYHr9NIwZc';
   
    echo "🔑 Utilisation des clés:\n";
    echo "APP_ID: " . substr($APP_ID, 0, 8) . "...\n";
    echo "REST_KEY: " . substr($REST_KEY, 0, 8) . "...\n";
    echo "MASTER_KEY: " . substr($MASTER_KEY, 0, 8) . "...\n\n";
   
    ParseClient::initialize($APP_ID, $REST_KEY, $MASTER_KEY);
   
    // ✅ Configuration du serveur Back4App - URL CORRIGÉE
    ParseClient::setServerURL('https://parseapi.back4app.com', 'parse');
    // Alternative si la première ne fonctionne pas :
    // ParseClient::setServerURL('https://parseapi.back4app.com/parse', '');
    
    ParseClient::setStorage(new ParseSessionStorage());
   
    echo "✅ Configuration Parse initialisée avec succès\n";
   
} catch (Exception $e) {
    echo "❌ Erreur lors de l'initialisation Parse: " . $e->getMessage() . "\n";
    exit(1);
}

// Test de connexion (remplace getServerHealth qui ne fonctionne pas avec Back4App)
try {
    echo "🔍 Test de connexion au serveur...\n";
    
    $testQuery = new Parse\ParseQuery('_User');
    $testQuery->limit(1);
    $result = $testQuery->find();
    
    echo "✅ Serveur Parse connecté avec succès\n";
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "Code d'erreur: " . $e->getCode() . "\n";
    exit(1);
}

// Website root url - Corrigé pour pointer vers votre domaine
$GLOBALS['WEBSITE_PATH'] = 'http://localhost:8000';
?>