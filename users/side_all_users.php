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

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">All Users </h3> </div> -->
        <div class="col">

                <!-- Search 
                <li class="nav-item hidden-sm-down search-box"> <a class="nav-link hidden-sm-down text-muted  " href="javascript:void(0)"><i class="ti-search"></i></a>
                    <form class="app-search">
                        <input type="text" class="form-control" placeholder="Search here"> <a class="srh-btn"><i class="ti-close"></i></a> </form>
                </li> -->

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
                                    <th style="color:#242526;">Verified</th>
                                    <th style="color:#242526;">Gender</th>
                                    <th style="color:#242526;">Bithday</th>
                                    <th style="color:#242526;">Mode</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    //$currUser = ParseUser::getCurrentUser();
                                    //$cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("_User");
                                    $query->descending('createdAt');
                                    $query->limit(1500);
                                    $catArray = $query->find(false);

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
                                                
                                            $avatar = "<span><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\"  style=\"background:#5d0375;\">View</a></span>";

                                        } else {
                                            $avatar = "<span><a class=\"text-warning font-weight-bold\">No Avatar</a></span>";
                                        }
                                        
                                        if ($cObj->get("photo_verified_file") !== null){
                                            
                                            $profileVerifiedPhotoUrl = $cObj->get("photo_verified_file")->getURL();
                                            $avatarVerificaton = "<span><a href='#' onclick='showImage(\"$profileVerifiedPhotoUrl\")' class=\"badge badge-info\"  style=\"background:#5d0375;\">View</a></span>";
                                            
                                        } else {
                                            $avatarVerificaton = "<span><a class=\"text-warning font-weight-bold\">Not verified</a></span>";
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

                                        $activation = $cObj->get('activationStatus');
                                        if ($activation == true){
                                            $active = "<span class=\"text-warning font-weight-bold\">SUSPENDED</span>";
                                        } else {
                                            $active = "<span class=\"text-success font-weight-bold\">ENABLED</span>";
                                        }
                                        
                                        $mode = $cObj->get('isViewer') == false? 'Challenger' : 'Viewer';
                                        
                                        echo '
		            	
		            	          
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$name.'</td>
                                    <td>'.$username.'</td>
                                    <td>'.$avatar.'</td>
                                    <td>'.$avatarVerificaton.'</td>
                                    <td><span>'.$UserGender.'</span></td>
                                    <td><span>'.$birthDate.'</span></td>
                                    <td>'.$mode.'</td>
                                    <td><a href="../dashboard/edit_user.php?objectId='.$objectId.'" <span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
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
