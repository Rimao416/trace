<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;


session_start();

$photoObjectId = '';

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

// Approve the photo ------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['approveAction'])){
    
    $query = new ParseQuery("Picture");
    
    $adObjID = $_POST['objectId'];
    $userObjectId = $_POST['userId'];
    
    try {
        $photo = $query->get($adObjID, true);
    
        // The object was retrieved successfully.
    } catch (ParseException $ex) {
        // The object was not retrieved successfully.
        // error is a ParseException with an error code and message.
    }
    
    try {
        $photo->set("fileStatus", 'approved');
    } catch (Exception $e) {
    }
    
    
     $photo->save(true);
     
     $query = new ParseQuery("_User");
    try {
    $currUser = $query->get($userObjectId, true);
    // The object was retrieved successfully.
    } catch (ParseException $ex) {
    // The object was not retrieved successfully.
    // error is a ParseException with an error code and message.
    }
    
    try {
        $currUser->set("photo_verified", true);

    } catch (Exception $e) {
    }


    $currUser->save(true);
    
}

// Approve and make default photo ------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['defaultAction'])){
    
    $query = new ParseQuery("Picture");
    
    $adObjID = $_POST['objectId'];
    $userObjectId = $_POST['userId'];
    
    try {
        $photo = $query->get($adObjID, true);
    
        // The object was retrieved successfully.
    } catch (ParseException $ex) {
        // The object was not retrieved successfully.
        // error is a ParseException with an error code and message.
    }
    
    try {
        $photo->set("fileStatus", 'approved');
    } catch (Exception $e) {
    }
    
    
     $photo->save(true);
     
     $query = new ParseQuery("_User");
    try {
    $currUser = $query->get($userObjectId, true);
    // The object was retrieved successfully.
    } catch (ParseException $ex) {
    // The object was not retrieved successfully.
    // error is a ParseException with an error code and message.
    }
    
    try {
        //$currUser->set("photo_verified", true);
        //$currUser->set("photo_verified_file", $photo->get('file'));
        $currUser->set("avatar", $photo->get('file'));

    } catch (Exception $e) {
    }


    $currUser->save(true);
    
}

// Delete the photo ------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['rejectAction'])){
    
    $query = new ParseQuery("Picture");
    
    $adObjID = $_POST['objectId'];
    $userObjectId = $_POST['userId'];
    $role = $_POST['photoRole'];
    
    try {
        $photo = $query->get($adObjID, true);
    
        // The object was retrieved successfully.
    } catch (ParseException $ex) {
        // The object was not retrieved successfully.
        // error is a ParseException with an error code and message.
    }
    
    //$photo->destroy(true);
    $photo->destroy();
    //$photo->save(true);
    
    if ($role === "selfie"){

        $query = new ParseQuery("_User");
            try {
                $currUser = $query->get($userObjectId, true);
                // The object was retrieved successfully.
            } catch (ParseException $ex) {
                // The object was not retrieved successfully.
            // error is a ParseException with an error code and message.
            }
    
            try {
                $currUser->set("photo_verified", false);
                $currUser->delete("avatar");
                $currUser->delete("photo_verified_file");

            } catch (Exception $e) {
            }

            $currUser->save(true);
    }
    
}

// Get current User
$adObjID = $_GET['objectId'];

$query = new ParseQuery("_User");
try {
    $currUser = $query->get($adObjID, true);
    // The object was retrieved successfully.
} catch (ParseException $ex) {
    // The object was not retrieved successfully.
    // error is a ParseException with an error code and message.
}

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Photos Review </h3> </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Photo Approval</a></li>
                <li class="breadcrumb-item active">Photos Review </li>
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
                                    <th>ObjectId</th>
                                    <th>Type</th>
                                    <th>Photo</th>
                                    <th>Acttion</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    //$currUser = ParseUser::getCurrentUser();
                                    $userObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Picture");
                                    $query->equalTo('author', $currUser);
                                    $query->equalTo('fileStatus', 'pending');
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;
                                        $photoObjectId = $cObj->getObjectId();

                                        $objectId = $cObj->getObjectId();
                                        $profilePhotoUrl = $cObj->get('file')->getURL();
                                        
                                        $role = $cObj->get('fileRole');
                                        
                                         if ($role === "selfie"){
                                            $type = "Selfie verification";
                                        } else if ($role === "normal"){
                                            $type = "Normal photo";
                                        } else {
                                            $type = "Profile picture";
                                        }
                                        
                                        $avatar = "<span/><a target='_blank' href=\"$profilePhotoUrl\" class=\"badge badge-info\">View</a></span>";
                                        

                                        echo '
		            	
		            	        <tr>
		            	        
		            	            <td>'.$objectId.'</td>
		            	            <td><span>'.$type.'</span></td>
		            	            <td>'.$avatar.'</td>
                                    <td>
                                    <form class="form-valide" action="" method="post">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="submit" name="defaultAction" class="btn btn-success"> Default </button>
                                            <button type="submit" name="approveAction" class="btn btn-info"> Approve </button>
                                            <button type="submit" name="rejectAction" class="btn btn-danger"> Reject </button>
                                            <input type="hidden" name="objectId" value="'.$photoObjectId.'"/>
                                            <input type="hidden" name="userId" value="'.$userObjectID.'"/>
                                            <input type="hidden" name="photoRole" value="'.$role.'"/>
                                        </div>
                                    </div>
                                    </form>
                                    </td>
                            
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
