<?php
// Inclure une seule fois Configs.php avec require_once
require_once '../Configs.php';
// Utiliser les fonctions du fichier Configs.php
// requireAdmin(); // Cette fonction gère déjà la vérification de l'utilisateur et du rôle admin

require '../vendor/autoload.php';
include_once '../Configs.php';
use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

session_start();
$currUser = ParseUser::getCurrentUser();
// if ($currUser){
//     // Store current user session token, to restore in case we create new user
//     $_SESSION['token'] = $currUser -> getSessionToken();
// } else {
//     header("Refresh:0; url=../index.php");
// }
?>
<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Admin Users </h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
                <li class="breadcrumb-item active">Admin Users </li>
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
                    <!--<h5 class="card-subtitle">Copy or Export CSV, Excel, PDF and Print data</h5> -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Name</th>
                                    <th style="color:#242526;">Username</th>
                                    <th style="color:#242526;">Avatar</th>
                                    <th style="color:#242526;">Gender</th>
                                    <th style="color:#242526;">Bithday</th>
                                    <!-- <th style="color:#242526;">Age</th>  -->
                                    <th style="color:#242526;">Mode</th>
                                    <!-- <th style="color:#242526;">Activation</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                try {
                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();
                                    $query = new ParseQuery("_User");
                                    $query->descending('createdAt');
                                    $query->equalTo('role', 'admin');
                                    // Utiliser le master key pour accéder aux données sensibles des administrateurs
                                    $catArray = $query->find(true); // true pour utiliser le master key
                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;
                                        $objectId = $cObj->getObjectId();
                                        $name = $cObj->get('name');
                                        $username = $cObj->get('username');
                                        $email = $cObj->get('email');
                                        if ($cObj->get("avatar") !== null) {
                                            $photos = $cObj->get('avatar');
                                            $profilePhotoUrl = $photos->getURL();
                                               
                                            $avatar = "<span><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";
                                        } else {
                                            $avatar = "<span/><a class=\"text-warning font-weight-bold\">No Avatar</a></span>";
                                        }
                                        $gender = $cObj->get('gender');
                                        if ($gender === "MAL"){
                                            $UserGender = "Male";
                                        } else if ($gender === "FML"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Other";
                                        }
                                        $birthday= $cObj->get('birthday');
                                        if($birthday == null || $birthday == ""){
                                            $birthDate = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }else{
                                            $birthDate = date_format($birthday,"d/m/Y");
                                        }
                                        // $age = $cObj->get('age');
                                        $verified = $cObj->get('emailVerified');
                                        if ($verified == false){
                                            $verification = "<span class=\"text-warning font-weight-bold\">UNVERIFIED</span>";
                                        } else {
                                            $verification = "<span class=\"text-success font-weight-bold\">VERIFED</span>";
                                        }
                                        $locaton = $cObj->get('location');
                                        if ($locaton == null){
                                            $city_location = "<span class=\"text-warning font-weight-bold\">Unavailable</span>";
                                        } else{
                                            $city_location = "<span class=\"text-info font-weight-bold\">$locaton</span>";
                                        }
                                       
                                        $mode = $cObj->get('isViewer') == false? 'Challenger' : 'Viewer';
                                       
                                        echo '
                       
                                <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$name.'</td>
                                    <td>'.$username.'</td>
                                    <td>'.$avatar.'</td>
                                    <td><span>'.$UserGender.'</span></td>
                                    <td><span>'.$birthDate.'</span></td>
                                    <td>'.$mode.'</td>
                                </tr>
                               
                                ';
                                    }
                                    // error in query
                                } catch (ParseException $e){ echo $e->getMessage(); }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
    <!-- footer -->
    <!-- End footer -->
</div>