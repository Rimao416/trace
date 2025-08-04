<?php
require 'vendor/autoload.php';
$app_name = 'Trace';
$default_icon_color = 'text-white'; // use Bootstrap text color sintax

use Parse\ParseClient;
use Parse\ParseSessionStorage;

// ✅ SOLUTION : Démarrer la session une seule fois au début du fichier
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // ⚠️ REMPLACEZ CES CLÉS PAR VOS VRAIES CLÉS DE BACK4APP
    $APP_ID = 'T9DQHJQkPYSt9gd7PLaWTwuDqTNaNQXEaLA2xQU5';
    $REST_KEY = 'PNrDNmmqCDVzvW1KZY89mX6ABpglg8Ntuvvc1mpe';
    $MASTER_KEY = 'tIUb5rA63HVyl5d6rtGnmefRZuX5xTPfzArigsdL';

    // Affichage des clés uniquement en mode développement
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "🔑 Utilisation des clés:\n";
        echo "APP_ID: " . substr($APP_ID, 0, 8) . "...\n";
        echo "REST_KEY: " . substr($REST_KEY, 0, 8) . "...\n";
        echo "MASTER_KEY: " . substr($MASTER_KEY, 0, 8) . "...\n\n";
    }

    ParseClient::initialize($APP_ID, $REST_KEY, $MASTER_KEY);
    
    // ✅ Configuration du serveur Back4App - URL CORRIGÉE
    ParseClient::setServerURL('https://parseapi.back4app.com', 'parse');
    ParseClient::setStorage(new ParseSessionStorage());

    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "✅ Configuration Parse initialisée avec succès\n";
    }
} catch (Exception $e) {
    error_log("❌ Erreur lors de l'initialisation Parse: " . $e->getMessage());
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "❌ Erreur lors de l'initialisation Parse: " . $e->getMessage() . "\n";
    }
    exit(1);
}

// Test de connexion (remplace getServerHealth qui ne fonctionne pas avec Back4App)
try {
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "🔍 Test de connexion au serveur...\n";
    }
    $testQuery = new Parse\ParseQuery('_User');
    $testQuery->limit(1);
    $result = $testQuery->find();
    
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "✅ Serveur Parse connecté avec succès\n";
    }
} catch (Exception $e) {
    error_log("❌ Erreur de connexion: " . $e->getMessage());
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
        echo "Code d'erreur: " . $e->getCode() . "\n";
    }
    exit(1);
}

// Website root url - Corrigé pour pointer vers votre domaine
$GLOBALS['WEBSITE_PATH'] = 'http://localhost:8000';

// Configuration supplémentaire
$GLOBALS['TIMEZONE'] = 'Europe/Paris';
date_default_timezone_set($GLOBALS['TIMEZONE']);

// ✅ FONCTIONS UTILITAIRES CORRIGÉES AVEC GUARDS
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        $currUser = Parse\ParseUser::getCurrentUser();
        return $currUser !== null;
    }
}

if (!function_exists('redirectToLogin')) {
    function redirectToLogin() {
        header("Location: ../index.php");
        exit();
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isUserLoggedIn()) {
            redirectToLogin();
        }
    }
}

// ✅ NOUVELLE FONCTION : Vérifier si l'utilisateur est admin
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        $currUser = Parse\ParseUser::getCurrentUser();
        if (!$currUser) {
            header("Location: ../index.php");
            exit();
        }
        if ($currUser->get("role") !== "admin") {
            header("Location: ../auth/logout.php");
            exit();
        }
    }
}

// ✅ FONCTION POUR OBTENIR L'UTILISATEUR ACTUEL
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        return Parse\ParseUser::getCurrentUser();
    }
}
?>