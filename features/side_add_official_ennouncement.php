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

// SIGN UP ------------------------------------------------
if(isset($_POST['val-title']) && isset($_POST['val-subtitle']) && isset($_POST['val-url']) && isset($_FILES['val-file'])){
    
    $title = $_POST['val-title'];
    $subTitle = $_POST['val-subtitle'];
    $url = $_POST['val-url'];
    
    $filePath = realpath($_FILES["val-file"]["tmp_name"]);
    $fileName = $_FILES['val-file']['name'];

    $newAnnouncement = ParseObject::create("OfficialAnnouncement");
    
    if($currUser->getObjectId() == "HwvtVkUbZp") {
        echo "<script>
        window.alert('You need administrator authorization to perform this action.');
        </script>";
    }else{
    
        $newAnnouncement->set("title", $title);
        $newAnnouncement->set("sub_title", $subTitle);
        $newAnnouncement->set("web_view_url", $url);
        
        $newAnnouncement->set("preview_image", ParseFile::createFromFile($filePath, $fileName));
    
        $newAnnouncement->save(true);
        
    }
}

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Gift </h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Add new Official Announcement </li>
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
                <div class="card">
                    <div class="card-body">
                        <div class="needs-validation">
                        <form class="form-valide" enctype="multipart/form-data" action="" method="post" novalidate>
                            
                        <div class="form-group row">
                            <label for="val-title" class="col-sm-4 col-form-label">Title<span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-name" name="val-title" placeholder="Enter a title" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-subtitle" class="col-sm-4 col-form-label">Sub-title<span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-subtitle" name="val-subtitle" placeholder="Enter the sub title" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-url" class="col-sm-4 col-form-label">URL<span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-url" name="val-url" placeholder="Enter the URL" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                            <div class="form-group row">
                                <label for="val-file" class="col-sm-4 col-form-label">PNG file<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input id="val-file" name="val-file" type="file" accept=".png" required />
                                    <div class="invalid-feedback">Please choose an image preview file, in PNG format</div>
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


