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
    
    
    $userObjectId = $_POST['userId'];
    
    $query = new ParseQuery("_User");
        try {
                $currUser = $query->get($userObjectId, true);
                // The object was retrieved successfully.
        } catch (ParseException $ex) {
                // The object was not retrieved successfully.
            // error is a ParseException with an error code and message.
        }
    
        try {
           $currUser->set("hangouts_approved", true);

          } catch (Exception $e) {
         }

        $currUser->save(true);
    
}

// Delete the hangout ------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['rejectAction'])){
    
    $userObjectId = $_POST['userId'];
    
    $query = new ParseQuery("_User");
        try {
                $currUser = $query->get($userObjectId, true);
                // The object was retrieved successfully.
        } catch (ParseException $ex) {
                // The object was not retrieved successfully.
            // error is a ParseException with an error code and message.
        }
    
        try {
           $currUser->set("hangouts_approved", false);
           $currUser->delete("hangouts_expectations");

          } catch (Exception $e) {
         }

        $currUser->save(true);
    
}

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Hangout approval </h3> </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
                <li class="breadcrumb-item active">Hangout approval </li>
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
                                    <th>Hangout</th>
                                    <th>Profile Photo</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {
                                    

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("_User");
                                    $query->descending('updatedAt');
                                    $query->notEqualTo('hangouts_approved', true);
                                    $query->exists('hangouts_expectations');
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;
                                        $photoObjectId = $cObj->getObjectId();

                                        $objectId = $cObj->getObjectId();
                                        
                                        $hangoutText = $cObj->get('hangouts_expectations');
                                        
                                        if ($cObj->get("avatar") !== null) {

                                            $profilePhotoUrl = $cObj->get('avatar')->getURL();
                                                
                                            $avatar = "<span/><a target='_blank' href=\"$profilePhotoUrl\" class=\"badge badge-info\">View</a></span>";

                                        } else {
                                            $avatar = "<span/><a class=\"badge badge-warning\">No Avatar</a></span>";
                                        }
                                        

                                        echo '
		            	
		            	        <tr>
		            	        
		            	            <td>'.$objectId.'</td>
		            	            <td><span>'.$hangoutText.'</span></td>
		            	            <td>'.$avatar.'</td>
                                    <td>
                                    <form class="form-valide" action="" method="post">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="submit" name="approveAction" class="btn btn-info"> Approve </button>
                                            <button type="submit" name="rejectAction" class="btn btn-danger"> Reject </button>
                                            <input type="hidden" name="userId" value="'.$objectId.'"/>
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
