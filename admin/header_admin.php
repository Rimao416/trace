<?php
require_once '../vendor/autoload.php';
include_once '../Configs.php';
use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$currUser = ParseUser::getCurrentUser();
if (!$currUser) {
    header("Location: ../index.php");
    exit();
}

$cuObjectID = $currUser->getObjectId();

// Initialiser les variables par défaut
$badge = '';
$content = '<li><a href="#" class="p-t-25 p-b-25 p-r-15 p-l-15" style="text-align:center;"> No notifications for now</a></li>';
$avatarURL = "../assets/dashboard/images/avatar_blank.png";
$name = $currUser->get('name') ?? 'User';

// Get notifications and avatar
try {
    // Récupérer les notifications de retrait en attente
    $queryWithdrawal = new ParseQuery('Withdrawal');
    $queryWithdrawal->equalTo("status", "pending");
    $counter = $queryWithdrawal->count(true);
    
    if($counter > 0){
        $msg = $counter > 1 ? 'There are new pending payouts' : 'There is a new pending payout';
        $content = '<li nav-item><a class="nav-link p-r-15 p-l-15" href="../dashboard/pending_withdrawals.php" style="color:#fff;margin-top:4px;">
            '.$msg.'</a></li>
            <li><a href="../dashboard/pending_withdrawals.php" class="p-r-15 p-l-15" style="text-align:center;"> View all </a></li>';
            
        $badge = '<sup><span class="badge " style="font-size:12px;background:#5d0375;color:#fff;">'.$counter.'</span></sup>';
    }
    
    // Récupérer l'avatar de l'utilisateur
    $photos = $currUser->get("photos");
    $avatar = $currUser->get('avatar');
    
    // Vérifier si l'utilisateur a des photos
    if ($photos !== null && is_array($photos) && count($photos) > 0) {
        // L'utilisateur a des photos dans le tableau
        if ($avatar !== null && method_exists($avatar, 'getURL')){
            $avatarURL = $avatar->getURL();
        }
    } else if ($avatar !== null && method_exists($avatar, 'getURL')) {
        // Pas de tableau photos mais avatar direct
        $avatarURL = $avatar->getURL();
    }
    // Sinon, garder l'avatar par défaut défini plus haut
    
} catch (Exception $e) {
    // En cas d'erreur, utiliser les valeurs par défaut
    error_log("Erreur dans header_admin.php: " . $e->getMessage());
    // Les variables sont déjà initialisées avec des valeurs par défaut
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .profile-pic {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .dropdown-menu {
            min-width: 200px;
        }
        
        .dropdown-user li a {
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        
        .dropdown-user li a:hover {
            background-color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand img {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
<?php
echo '
<div class="header" style="top:-3px">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- Logo -->
        <div class="navbar-header" style="z-index:999;">
            <a class="navbar-brand" href="../dashboard/panel.php">
                <!-- Logo icon -->
                <b><img src="../assets/dashboard/images/logo.png" alt="homepage" class="dark-logo" width="40" /></b>
                <!-- Logo text -->
                <span style="color: #fff; font-weight: bold; margin-left: 10px;">'.$app_name.'</span>
            </a>
        </div>
        <!-- End Logo -->
        
        <div class="navbar-collapse">
            <!-- toggle and nav items -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- Mobile menu toggle -->
                <li class="nav-item"> 
                    <a class="nav-link nav-toggler hidden-md-up text-muted " href="javascript:void(0)">
                        <i class="mdi mdi-menu" style="color:#fff;"></i>
                    </a> 
                </li>
                <!-- Desktop sidebar toggle -->
                <li class="nav-item m-l-10"> 
                    <a class="nav-link sidebartoggler hidden-sm-down text-muted" href="javascript:void(0)">
                        <i class="ti-menu" style="color:#fff;"></i>
                    </a> 
                </li>
            </ul>
            
            <!-- User profile and search -->
            <ul class="navbar-nav my-lg-0">
                <!-- Notifications -->
                <li class="nav-item dropdown p-t-8">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span><i class="fa fa-bell h1"></i>'.$badge.'</span>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right p-t-0" style="background:#242526;color:#fff;box-shadow:0px 0px 10px rgba(0,0,0,0.3);">
                        <div style="padding: 10px 15px; border-bottom: 1px solid #444; font-weight: bold;">
                            Notifications
                        </div>
                        <ul class="dropdown-user">
                            '.$content.'
                        </ul>
                    </div>
                </li>
                
                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="' .$avatarURL.'" alt="user" class="profile-pic" />
                        <span style="color: #fff; margin-left: 8px;">'.$name.'</span>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right p-t-0" style="background:#242526;color:#fff;box-shadow:0px 0px 10px rgba(0,0,0,0.3);">
                        <div style="padding: 10px 15px; border-bottom: 1px solid #444; font-weight: bold;">
                            Welcome, '.$name.'
                        </div>
                        <ul class="dropdown-user">
                            <!-- Profile -->
                            <li>
                                <a href="../dashboard/edit_user.php?objectId='.$cuObjectID.'" style="color:#fff;">
                                    <i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp;&nbsp;Edit Profile
                                </a>
                            </li>
                            
                            <!-- Settings -->
                            <li>
                                <a href="../dashboard/settings.php" style="color:#fff;">
                                    <i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp;&nbsp;Settings
                                </a>
                            </li>
                            
                            <!-- Pending Withdrawals -->
                            <li>
                                <a href="../dashboard/pending_withdrawals.php" style="color:#fff;">
                                    <i class="fa fa-money"></i> &nbsp;&nbsp;&nbsp;&nbsp;Pending Payouts '.$badge.'
                                </a>
                            </li>
                            
                            <!-- Divider -->
                            <li style="border-top: 1px solid #444; margin: 5px 0;"></li>
                            
                            <!-- Log out -->
                            <li>
                                <a href="#" style="color:#fff;" onclick="logOut()">
                                    <i class="fa fa-sign-out"></i> &nbsp;&nbsp;&nbsp;&nbsp;Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>

<script>
function logOut() {
    if (confirm("Are you sure you want to logout?")) {
        // Rediriger vers la page de déconnexion
        window.location.href = "../logout.php";
    }
}

// Fermer les dropdowns quand on clique ailleurs
document.addEventListener("click", function(event) {
    var dropdowns = document.querySelectorAll(".dropdown-menu");
    dropdowns.forEach(function(dropdown) {
        if (!dropdown.parentElement.contains(event.target)) {
            dropdown.classList.remove("show");
        }
    });
});
</script>
';
?>
</body>
</html>