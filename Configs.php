<?php
require 'vendor/autoload.php';
$app_name = 'Trace';
$default_icon_color = 'text-white'; // use Bootstrap text color sintax
use Parse\ParseClient;
use Parse\ParseSessionStorage;
use Parse\ParseUser;
use Parse\ParseQuery;
use Parse\ParseException;

// ✅ SOLUTION : Démarrer la session une seule fois au début du fichier
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// FONCTION DE NETTOYAGE DE SESSION PARSE
// =====================================
if (!function_exists('clearParseSession')) {
    function clearParseSession() {
        try {
            // Supprimer l'utilisateur actuel du cache
            if (class_exists('Parse\ParseUser')) {
                ParseUser::logOut();
            }
           
            // Nettoyer le stockage de session Parse
            if (class_exists('Parse\ParseClient')) {
                $storage = ParseClient::getStorage();
                if ($storage) {
                    $storage->clear(); // Efface tout le stockage Parse
                }
            }
           
            // Nettoyer les sessions PHP traditionnelles
            if (session_status() == PHP_SESSION_ACTIVE) {
                $_SESSION = array();
            }
           
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "✅ Session Parse nettoyée avec succès\n";
            }
        } catch (Exception $e) {
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "⚠️ Erreur lors du nettoyage: " . $e->getMessage() . "\n";
            }
        }
    }
}

// =====================================
// NETTOYAGE FORCÉ SI DEMANDÉ
// =====================================
if (isset($_GET['clear_session']) && $_GET['clear_session'] == 'true') {
    clearParseSession();
    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
        echo "🧹 Nettoyage forcé effectué\n";
    }
}

try {
    // ⚠️ VOS CLÉS BACK4APP
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

// =====================================
// TEST DE CONNEXION AVEC GESTION DES TOKENS INVALIDES
// =====================================
if (!function_exists('testConnectionWithErrorHandling')) {
    function testConnectionWithErrorHandling() {
        try {
            // Essayer d'abord de récupérer l'utilisateur actuel
            $currentUser = ParseUser::getCurrentUser();
           
            if ($currentUser) {
                // Tester si le token est toujours valide
                try {
                    $currentUser->fetch(); // Force une requête au serveur
                    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                        echo "✅ Token de session valide pour: " . $currentUser->getUsername() . "\n";
                    }
                    return true;
                } catch (ParseException $e) {
                    if ($e->getCode() == 209) { // Code d'erreur "Invalid session token"
                        if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                            echo "⚠️ Token de session invalide détecté. Nettoyage...\n";
                        }
                       
                        // Forcer la déconnexion et nettoyer
                        clearParseSession();
                       
                        if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                            echo "✅ Session nettoyée. Token invalide résolu.\n";
                        }
                        return false;
                    }
                    throw $e; // Re-lancer si c'est une autre erreur
                }
            }
           
            // Test de connexion basique
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "🔍 Test de connexion au serveur...\n";
            }
           
            $testQuery = new ParseQuery('_User');
            $testQuery->limit(1);
            $result = $testQuery->find();
           
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "✅ Serveur Parse connecté avec succès\n";
            }
            return true;
           
        } catch (ParseException $e) {
            if ($e->getCode() == 209) {
                // Gestion spécifique des tokens invalides
                if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                    echo "⚠️ Token invalide détecté lors du test de connexion\n";
                }
                clearParseSession();
                return false;
            }
           
            error_log("❌ Erreur de connexion Parse: " . $e->getMessage());
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
                echo "Code d'erreur: " . $e->getCode() . "\n";
            }
            return false;
        } catch (Exception $e) {
            error_log("❌ Erreur de connexion: " . $e->getMessage());
            if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
                echo "Code d'erreur: " . $e->getCode() . "\n";
            }
            return false;
        }
    }
}

// Exécuter le test de connexion
if (!testConnectionWithErrorHandling()) {
    // Si le test échoue à cause d'un token invalide, rediriger vers la page de login
    if (!isset($_GET['debug'])) {
        header("Location: ../index.php?session_expired=1");
        exit();
    } else {
        echo "⚠️ Test de connexion échoué mais en mode debug\n";
    }
}

// Website root url - Corrigé pour pointer vers votre domaine
$GLOBALS['WEBSITE_PATH'] = 'http://localhost:8000';

// Configuration supplémentaire
$GLOBALS['TIMEZONE'] = 'Europe/Paris';
date_default_timezone_set($GLOBALS['TIMEZONE']);

// =====================================
// FONCTIONS UTILITAIRES AMÉLIORÉES AVEC GESTION DES TOKENS
// =====================================
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        try {
            $currUser = ParseUser::getCurrentUser();
            if (!$currUser) {
                return false;
            }
           
            // Vérifier que le token est valide en faisant une requête
            $currUser->fetch();
            return true;
           
        } catch (ParseException $e) {
            if ($e->getCode() == 209) {
                // Token invalide, nettoyer la session
                clearParseSession();
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
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

// ✅ FONCTION AMÉLIORÉE : Vérifier si l'utilisateur est admin
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        try {
            $currUser = ParseUser::getCurrentUser();
            if (!$currUser) {
                header("Location: ../index.php?admin_required=1");
                exit();
            }
           
            // Vérifier le token et le rôle
            $currUser->fetch();
            if ($currUser->get("role") !== "admin") {
                header("Location: ../auth/logout.php?insufficient_privileges=1");
                exit();
            }
           
        } catch (ParseException $e) {
            if ($e->getCode() == 209) {
                clearParseSession();
                header("Location: ../index.php?session_expired=1");
            } else {
                header("Location: ../index.php?error=1");
            }
            exit();
        } catch (Exception $e) {
            header("Location: ../index.php?error=1");
            exit();
        }
    }
}

// ✅ FONCTION AMÉLIORÉE POUR OBTENIR L'UTILISATEUR ACTUEL
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        try {
            $user = ParseUser::getCurrentUser();
            if ($user) {
                // Vérifier que le token est toujours valide
                $user->fetch();
            }
            return $user;
        } catch (ParseException $e) {
            if ($e->getCode() == 209) {
                // Token invalide, nettoyer et retourner null
                clearParseSession();
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
}

// =====================================
// FONCTION DE DÉBOGAGE POUR VÉRIFIER L'ÉTAT DE LA SESSION
// =====================================
if (!function_exists('debugSessionState')) {
    function debugSessionState() {
        if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
            echo "\n=== DEBUG SESSION STATE ===\n";
           
            $currentUser = ParseUser::getCurrentUser();
            if ($currentUser) {
                echo "👤 Utilisateur connecté: " . $currentUser->getUsername() . "\n";
                echo "🔑 Session Token: " . substr($currentUser->getSessionToken(), 0, 10) . "...\n";
                try {
                    $role = $currentUser->get("role");
                    echo "👑 Rôle: " . ($role ? $role : "non défini") . "\n";
                } catch (Exception $e) {
                    echo "⚠️ Erreur lors de la récupération du rôle: " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ Aucun utilisateur connecté\n";
            }
            echo "============================\n\n";
        }
    }
}

// Appeler la fonction de debug si demandée
if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
    debugSessionState();
}
?>