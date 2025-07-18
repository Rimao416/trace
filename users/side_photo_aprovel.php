<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;


session_start();

$currUser = ParseUser::getCurrentUser();
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
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Photo aproval </h3> </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
                <li class="breadcrumb-item active">Photo aproval </li>
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

                    <h5 class="card-subtitle">Copy or Export CSV, Excel, PDF and Print data</h5>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Pending photos</th>
                                    <th>Verified photo/selfie</th>
                                    <th>ObjectId</th>
                                    <th>Username</th>
                                    <th>Gender</th>
                                    <th>Location</th>
                                
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();
                                    
                                    $queryPhoto = new ParseQuery("Picture");
                                    $queryPhoto->equalTo('fileStatus', 'pending');

                                    $query = new ParseQuery("_User");
                                    $query->matchesQuery("avatars", $queryPhoto);
                                    $query->descending('createdAt');
                                    
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();

                                        $name = $cObj->get('name');
                                        $username = $cObj->get('username');
                                        $email = $cObj->get('email');

                                        $gender = $cObj->get('gender');

                                        if ($gender === "male"){
                                            $UserGender = "Male";
                                        } else if ($gender === "female"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Private";
                                        }


                                        $locaton = $cObj->get('location');
                                        if ($locaton == null){
                                            $city_location = "<span class=\"badge badge-warning\">Unavailable</span>";
                                        } else{
                                            $city_location = "<span class=\"badge badge-info\">$locaton</span>";
                                        }
                                        
                                        $verifiedPhoto = $cObj->get('photo_verified_file');
                                        
                                        if($verifiedPhoto !== null){
                                            
                                            $verifiedPhotoUrl = $cObj->get('photo_verified_file')->getURL();
                                            $avatar = "<span/><a target='_blank' href=\"$verifiedPhotoUrl\" class=\"badge badge-info\">View photo</a></span>";
                                        } else {
                                            $avatar = "<span/><a class=\"badge badge-danger\">Photo Rejected</a></span>";
                                        }
                                        
                                        

                                        echo '
		            	
		            	        <tr>
		            	            <td><a href="../dashboard/review_photos.php?objectId='.$objectId.'" target="_blank" <span class="badge badge-warning">View photos</span></a></td>
                                    <td>'.$avatar.'</td>
                                    <td>'.$objectId.'</td>
                                    <td>'.$username.'</td>
                                    <td><a href="../dashboard/edit_user.php?objectId='.$objectId.'" target="_blank" <span class="badge badge-warning">'.$UserGender.'</span></a></td>
                                    <td>'.$city_location.'</td>
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
