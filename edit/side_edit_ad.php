<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseFile;

session_start();

$currUser = ParseUser::getCurrentUser();
if ($currUser){

    // Store current user session token, to restore in case we create new user
    $_SESSION['token'] = $currUser -> getSessionToken();
} else {

    header("Refresh:0; url=../index.php");
}

$has_message = false;
$created = false;

// Update data ------------------------------------------------
if(isset($_POST['val-type']) && isset($_FILES['val-file'])){
    $title = $_POST['val-title'];
    $description = $_POST['val-description'];
    $type = $_POST['val-type'];
    $link = $_POST['val-link'];
    $duration = $_POST['val-duration'];
    $filePath = realpath($_FILES["val-file"]["tmp_name"]);
    $fileName = $_FILES['val-file']['name'];
    $status = $_POST['val-status'];
    
    $adObjID = $_GET['objectId'];
    $query = new ParseQuery("Advertising");
    
    try {
        $ad = $query->get($adObjID, true);
    
        $ad->set("title", $title);
        $ad->set("description", $description);
        $ad->set("type", $type);
        $ad->set("link", $link);
        $ad->set("isActive", $status == 1 ? true : false);
        $ad->set("PresentationDuration", (int) $duration);
        
        if($_FILES['val-file']['type'] == 'image/jpeg'){
            $upload_name = 'image.jpg';
        } else if($_FILES['val-file']['type'] == 'image/png'){
            $upload_name = 'image.png';
        } else {
            $upload_name = 'video.mp4';
        }
        
        if($fileName != null){
            $ad->set("file", ParseFile::createFromFile($filePath, $upload_name));   
        }
        
        $ad->save(true);
        
        $created = true;
    } catch (ParseException $ex) {  
        $created = false;
    }
    
    $has_message = true;
}

?>

<?php
// Get current User
$adObjID = $_GET['objectId'];

$query = new ParseQuery("Advertising");
$query->includeKey("author");
try {
    $currAd = $query->get($adObjID, true);
    // The object was retrieved successfully.
} catch (ParseException $ex) {
    // The object was not retrieved successfully.
    // error is a ParseException with an error code and message.
}

//$currUser = ParseUser::getCurrentUser();
$cuObjectID = $currAd->getObjectId();
$date= $currAd->getCreatedAt();

// Ad's title
$title = $currAd->get('title');

// Ad's description
$description = $currAd->get('description');

// Ad's type
$type = $currAd->get('type');

$ad_type_list = [
    "Video" => "video",
    "Banner" => "banner",
    "Medium Image" => "medium_image",
    "Full Screen Image" => "full_screen_image",
];

// Ad's link
$link = $currAd->get('link');

// Ad's duration
$duration = $currAd->get('PresentationDuration');

// Ad's type
$status = $currAd->get('isActive');

$ad_status = [
    "Active"=>1,
    "Inactive"=>0,
];

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Edit Video Status</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Advertising</a></li>
                <li class="breadcrumb-item active">Edit ad</li>
            </ol>
        </div>
    </div>

    <?php

    echo '
    
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row bg-white m-l-0 m-r-0 box-shadow ">

        </div>
        <div class="row">
            <div class="col-12 col-md-10 col-xl-6" style="margin-left:auto; margin-right:auto;padding:10px 30px">
            
                ';?> 
                
                    <?php 
                        if($has_message && $created){
                            echo '<div class="alert alert-success text-dark" role="alert">
                              The new ad was updated successfuly!
                            </div>';
                        } else if($has_message && !$created) {
                            echo '<div class="alert alert-danger text-dark" role="alert">
                              Something went wrong and the Ad was not updated. Try again later.
                            </div>';
                        }
                    
                echo '
            
                <div class="card">
                    <div class="card-body">
                    <div class="form-validation">
                    <form class="form-valide" enctype="multipart/form-data" action="" method="post" novalidate>
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad title <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-title" name="val-title" value="'.$title.'" placeholder="Give a title to the ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad description <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-description" name="val-description" value="'.$description.'" 
                                                placeholder="Give a description to the ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="account">Ad type <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="val-type" name="val-type" required>
                                    ';?>
                                    
                                    <?php 
                                        foreach($ad_type_list as $key => $value){
                                            if($value == $type){
                                               echo '<option selected value="'.$value.'">'.$key.'</option>'; 
                                            }else{
                                                echo '<option value="'.$value.'">'.$key.'</option>';
                                            }
                                        }
                                    echo '
                                    
                                    ?>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad link <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="url" class="form-control" id="val-link" name="val-link" value="'.$link.'" placeholder="Give a link to the Ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad presentation time (In seconds) <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="val-duration" name="val-duration" value="'.$duration.'" placeholder="Give a presentation time to the ad"
                                min="1" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="account">Ad status <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="val-status" name="val-status" required>
                                    ';?>
                                    
                                    <?php 
                                        foreach($ad_status as $key => $value){
                                            if($value == $status){
                                                echo '<option selected value="'.$value.'">'.$key.'</option>'; 
                                            }else{
                                                echo '<option value="'.$value.'">'.$key.'</option>';
                                            }
                                        }
                                    echo '
                                    
                                    ?>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="val-file" class="col-sm-4 col-form-label">New ad image/video (jpg/png/mp4)</label>
                            <div class="col-sm-8">
                                <input id="val-file" name="val-file" type="file" accept = "video/mp4, image/png, image/jpeg"/>
                                <div class="invalid-feedback">Please choose the image (png/jpg), or a video(mp4)</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                            
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn text-white" style="background:#5d0375;"> Save </button>
                                <a class="btn btn-inverse" href="../dashboard/all_ads.php"> Back </a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
    
    ';
    ?>


</div>