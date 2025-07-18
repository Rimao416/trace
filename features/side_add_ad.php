<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseFile;
use Parse\ParseObject;
use Parse\ParseUser;


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

// SIGN UP ------------------------------------------------
if(isset($_POST['val-type']) && isset($_FILES['val-file'])){
    try {
        $title = $_POST['val-title'];
        $description = $_POST['val-description'];
        $type = $_POST['val-type'];
        $link = $_POST['val-link'];
        $duration = abs($_POST['val-duration']);
        $status = $_POST['val-status'];
        
        $filePath = realpath($_FILES["val-file"]["tmp_name"]);
        $fileName = $_FILES['val-file']['name'];
        
        if($_FILES['val-file']['type'] == 'image/jpeg'){
            $upload_name = 'image.jpg';
        } else if($_FILES['val-file']['type'] == 'image/png'){
            $upload_name = 'image.png';
        } else {
            $upload_name = 'video.mp4';
        }
        
        $newAd = ParseObject::create("Advertising");
    
        $newAd->set("title", $title);
        $newAd->set("description", $description);
        $newAd->set("type", $type);
        $newAd->set("link", $link);
        $newAd->set("PresentationDuration", $duration);
        $newAd->set("isActive", $status == 1 ? true : false);
        $newAd->set("file", ParseFile::createFromFile($filePath, $upload_name));

        $newAd->save(true);
        $created = true;
    } catch (ParseException $ex) {  
        $created = false;
    }
    
    $has_message = true;
}

$ad_type = [
    "Video" => "video",
    "Banner" => "banner",
    "Medium Image" => "medium_image",
    "Full Screen Image" => "full_screen_image",
];

$ad_status = [
    "Active"=>1,
    "Inactive"=>0,
];

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Category </h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Create new ad </li>
            </ol>
        </div>
    </div>
    
 
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row bg-white m-l-0 m-r-0 box-shadow ">

        </div>
        <div class="row">
            <div class="col-12 col-md-10 col-xl-6" style="margin-left:auto; margin-right:auto;padding:10px 30px">
                <?php 
                    if($has_message && $created){
                        echo '<div class="alert alert-success text-dark" role="alert">
                          The new ad was created successfuly!
                        </div>';
                    } else if($has_message && !$created) {
                        echo '<div class="alert alert-danger text-dark" role="alert">
                          Something went wrong and the Ad was not created. Try again later.
                        </div>';
                    }
                ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="needs-validation">
                        <form class="form-valide" enctype="multipart/form-data" action="" method="post" novalidate>
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad title <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-title" name="val-title" placeholder="Give a title to the ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad description <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-description" name="val-description" placeholder="Give a description to the ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="account">Ad type <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="val-type" name="val-type" required>
                                    <?php 
                                        foreach($ad_type as $key => $value){
                                           echo '<option value="'.$value.'">'.$key.'</option>';
                                        }
                                    ?>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad link <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="url" class="form-control" id="val-link" name="val-link" placeholder="Give a link to the Ad" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-name" class="col-sm-4 col-form-label">Ad presentation time (In seconds) <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="val-duration" name="val-duration" placeholder="Give a presentation time to the ad"
                                min="1" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="account">Ad Status <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="val-status" name="val-status" required>
                                    <?php 
                                        foreach($ad_status as $key => $value){
                                           echo '<option value="'.$value.'">'.$key.'</option>';
                                        }
                                    ?>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="val-file" class="col-sm-4 col-form-label">Ad image/video (jpg/png/mp4) <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input id="val-file" name="val-file" type="file" accept = "video/mp4, image/png, image/jpeg" required />
                                <div class="invalid-feedback">Please choose the image (png/jpg), or a video(mp4)</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn text-white" style="background:#5d0375;"> Save</button>
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
</div>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('form-valide');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>


