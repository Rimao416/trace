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

// Approve the hangout ------------------------------------------------
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
        $currUser->set("video_verified", true);

    } catch (Exception $e) {
    }


    $currUser->save(true);
    
}

// Delete the hangout ------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['rejectAction'])){
    
    $query = new ParseQuery("Picture");
    
    $adObjID = $_POST['objectId'];
    $userObjectId = $_POST['userId'];
    
    try {
        $photo = $query->get($adObjID, true);
        $photo->set("fileStatus", 'rejected');
    
        // The object was retrieved successfully.
    } catch (ParseException $ex) {
        // The object was not retrieved successfully.
        // error is a ParseException with an error code and message.
    }
    
    //$photo->destroy(true);
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
                $currUser->set("video_verified", false);
                //$currUser->set("video_verified_file", null);

            } catch (Exception $e) {
            }

            $currUser->save(true);
    
}

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Video approval </h3> </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
                <li class="breadcrumb-item active">Video approval </li>
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
                                    <th>Fullname</th>
                                    <th>Gender</th>
                                    <th>Video</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $query = new ParseQuery("Picture");
                                    $query->descending('updatedAt');
                                    $query->includeKey("author");
                                    $query->equalTo('fileType', "video");
                                    $query->equalTo('fileStatus', "pending");
                                    $query->exists('file');
                                    $query->limit(1000);
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $userObjectID = $cObj->get('author')->getObjectId();
                                        
                                        $fromName = $cObj->get('author')->get('name');
                                        
                                        if ($cObj->get("file") !== null) {

                                            $videoUrl = $cObj->get('file')->getURL();
                                                
                                            $video = "<span/><a target='_blank' href=\"$videoUrl\" class=\"badge badge-info\">Watch video</a></span>";

                                        } else {
                                            $video = "<span/><a class=\"badge badge-warning\">No Video found</a></span>";
                                        }

                                        $gender = $cObj->get('author')->get('gender');

                                        if ($gender === "male"){
                                            $UserGender = "Male";
                                        } else if ($gender === "female"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Private";
                                        }
                                        

                                        echo '
		            	
		            	        <tr>
		            	        
		            	            <td>'.$objectId.'</td>
		            	            <td><span>'.$fromName.'</span></td>
		            	            <td>'.$UserGender.'</td>
                                    <td>'.$video.'</td>
                                    <td>
                                    <form class="form-valide" action="" method="post">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="submit" name="approveAction" class="btn btn-info"> Approve </button>
                                            <button type="submit" name="rejectAction" class="btn btn-danger"> Reject </button>
                                            <input type="hidden" name="objectId" value="'.$objectId.'"/>
                                            <input type="hidden" name="userId" value="'.$userObjectID.'"/>
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
