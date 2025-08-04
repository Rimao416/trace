<?php
require '../vendor/autoload.php';
include '../Configs.php';
use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

$currUser = ParseUser::getCurrentUser();

// Vérifier si l'utilisateur est connecté
if (!$currUser) {
    header("Location: ../index.php");
    exit();
}

if ($currUser){
    // Store current user session token, to restore in case we create new user
    $_SESSION['token'] = $currUser -> getSessionToken();
} else {
    header("Refresh:0; url=../index.php");
}

function array_get_by_index($index, $array) {
    $i=0;
    foreach ($array as $value) {
        if($i==$index) {
            return $value;
        }
        $i++;
    }
    // may be $index exceedes size of $array. In this case NULL is returned.
    return NULL;
}

// ✅ FONCTION AJOUTÉE : Récupérer tous les utilisateurs avec Master Key
function getAllUsersWithMasterKey($limit = 1500) {
    try {
        // Méthode 1: Utiliser ParseUser::query() avec Master Key
        $query = ParseUser::query();
        $query->descending('createdAt');
        $query->limit($limit);
        
        // ✅ CORRIGÉ : Utiliser find(true) pour activer useMasterKey
        $users = $query->find(true); // true = useMasterKey
        
        return $users;
        
    } catch (ParseException $e) {
        error_log("Erreur getAllUsersWithMasterKey méthode 1: " . $e->getMessage());
        
        // Méthode 2: Fallback avec ParseQuery
        try {
            $query = new ParseQuery("_User");
            $query->descending('createdAt');
            $query->limit($limit);
            
            // ✅ CORRIGÉ : Utiliser find(true) pour activer useMasterKey
            $users = $query->find(true); // true = useMasterKey
            
            return $users;
            
        } catch (ParseException $e2) {
            error_log("Erreur getAllUsersWithMasterKey méthode 2: " . $e2->getMessage());
            return [];
        }
    }
}

// ✅ FONCTION AJOUTÉE : Récupérer les utilisateurs par batch si nécessaire
function getAllUsersByBatch($batchSize = 100) {
    try {
        $allUsers = [];
        $skip = 0;
        
        do {
            $query = ParseUser::query();
            $query->descending('createdAt');
            $query->limit($batchSize);
            $query->skip($skip);
            
            // ✅ CORRIGÉ : Utiliser find(true) pour activer useMasterKey
            $batch = $query->find(true); // true = useMasterKey
            
            $allUsers = array_merge($allUsers, $batch);
            $skip += $batchSize;
            
        } while (count($batch) == $batchSize);
        
        return $allUsers;
        
    } catch (ParseException $e) {
        error_log("Erreur getAllUsersByBatch: " . $e->getMessage());
        return [];
    }
}
?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
                <li class="breadcrumb-item active">All Users </li>
            </ol>
        </div>
    </div>
    
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row bg-white m-l-0 m-r-0 box-shadow ">
        </div>
        <div class="row">
            <div class="col-lg">
               <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Name</th>
                                    <th style="color:#242526;">Username</th>
                                    <th style="color:#242526;">Avatar</th>
                                    <th style="color:#242526;">Verified</th>
                                    <th style="color:#242526;">Gender</th>
                                    <th style="color:#242526;">Birthday</th>
                                    <th style="color:#242526;">Mode</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                try {
                                    // ✅ CORRIGÉ : Utiliser la nouvelle fonction avec Master Key
                                    $catArray = getAllUsersWithMasterKey(1500);
                                    
                                    // Debug info
                                    if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                                        echo '<tr><td colspan="9"><small style="color: blue;">Debug: ' . count($catArray) . ' utilisateurs récupérés avec Master Key</small></td></tr>';
                                    }
                                    
                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;
                                        $objectId = $cObj->getObjectId();
                                        
                                        // ✅ SÉCURISATION : Protéger contre les valeurs nulles
                                        $name = $cObj->get('name');
                                        $name = is_string($name) ? htmlspecialchars($name) : 'N/A';
                                        
                                        $username = $cObj->get('username');
                                        $username = is_string($username) ? htmlspecialchars($username) : 'N/A';
                                        
                                        $email = $cObj->get('email');
                                        
                                        // Avatar handling
                                        if ($cObj->get("avatar") !== null) {
                                            $photos = $cObj->get('avatar');
                                            $profilePhotoUrl = $photos->getURL();
                                            $avatar = "<span><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\"  style=\"background:#5d0375;\">View</a></span>";
                                        } else {
                                            $avatar = "<span><a class=\"text-warning font-weight-bold\">No Avatar</a></span>";
                                        }
                                       
                                        // Verification photo handling
                                        if ($cObj->get("photo_verified_file") !== null){
                                            $profileVerifiedPhotoUrl = $cObj->get("photo_verified_file")->getURL();
                                            $avatarVerificaton = "<span><a href='#' onclick='showImage(\"$profileVerifiedPhotoUrl\")' class=\"badge badge-info\"  style=\"background:#5d0375;\">View</a></span>";
                                        } else {
                                            $avatarVerificaton = "<span><a class=\"text-warning font-weight-bold\">Not verified</a></span>";
                                        }
                                        
                                        // Gender handling
                                        $gender = $cObj->get('gender');
                                        if ($gender === "MAL"){
                                            $UserGender = "Male";
                                        } else if ($gender === "FML"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Other";
                                        }
                                        
                                        // Birthday handling
                                        $birthday = $cObj->get('birthday');
                                        if($birthday == null || $birthday == ""){
                                            $birthDate = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        } else {
                                            if ($birthday instanceof DateTime) {
                                                $birthDate = $birthday->format("d/m/Y");
                                            } else {
                                                $birthDate = '<span class="text-warning font-weight-bold p-5">Invalid Date</span>';
                                            }
                                        }
                                        
                                        // Email verification
                                        $verified = $cObj->get('emailVerified');
                                        if ($verified == false){
                                            $verification = "<span class=\"text-warning font-weight-bold\">UNVERIFIED</span>";
                                        } else {
                                            $verification = "<span class=\"text-success font-weight-bold\">VERIFIED</span>";
                                        }
                                        
                                        // Location
                                        $location = $cObj->get('location');
                                        if ($location == null){
                                            $city_location = "<span class=\"text-warning font-weight-bold\">Unavailable</span>";
                                        } else{
                                            $city_location = "<span class=\"text-info font-weight-bold\">" . htmlspecialchars($location) . "</span>";
                                        }
                                        
                                        // Activation status
                                        $activation = $cObj->get('activationStatus');
                                        if ($activation == true){
                                            $active = "<span class=\"text-warning font-weight-bold\">SUSPENDED</span>";
                                        } else {
                                            $active = "<span class=\"text-success font-weight-bold\">ENABLED</span>";
                                        }
                                       
                                        // User mode
                                        $mode = $cObj->get('isViewer') == false? 'Challenger' : 'Viewer';
                                       
                                        echo '
                                        <tr>
                                            <td>' . htmlspecialchars($objectId) . '</td>
                                            <td>' . $name . '</td>
                                            <td>' . $username . '</td>
                                            <td>' . $avatar . '</td>
                                            <td>' . $avatarVerificaton . '</td>
                                            <td><span>' . htmlspecialchars($UserGender) . '</span></td>
                                            <td><span>' . $birthDate . '</span></td>
                                            <td>' . htmlspecialchars($mode) . '</td>
                                            <td><a href="../dashboard/edit_user.php?objectId=' . htmlspecialchars($objectId) . '"><span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
                                        </tr>';
                                    }
                                    
                                } catch (ParseException $e) { 
                                    echo '<tr><td colspan="9" class="text-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    error_log("Erreur dans l'affichage des utilisateurs: " . $e->getMessage());
                                    
                                    // Tentative avec la méthode batch
                                    try {
                                        echo '<tr><td colspan="9" class="text-info">Tentative avec la méthode batch...</td></tr>';
                                        $batchUsers = getAllUsersByBatch(50);
                                        
                                        foreach ($batchUsers as $iValue) {
                                            // Même logique que ci-dessus mais simplifiée pour éviter la duplication
                                            $cObj = $iValue;
                                            $objectId = $cObj->getObjectId();
                                            $name = is_string($cObj->get('name')) ? htmlspecialchars($cObj->get('name')) : 'N/A';
                                            $username = is_string($cObj->get('username')) ? htmlspecialchars($cObj->get('username')) : 'N/A';
                                            
                                            echo '<tr>
                                                <td>' . htmlspecialchars($objectId) . '</td>
                                                <td>' . $name . '</td>
                                                <td>' . $username . '</td>
                                                <td colspan="6">Données limitées (mode batch)</td>
                                            </tr>';
                                        }
                                        
                                    } catch (ParseException $e2) {
                                        echo '<tr><td colspan="9" class="text-danger">Erreur batch: ' . htmlspecialchars($e2->getMessage()) . '</td></tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <!-- End Container fluid  -->
</div>