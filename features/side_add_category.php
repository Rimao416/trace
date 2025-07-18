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
if(isset($_POST['val-name']) && isset($_FILES['val-file'])){
    $name = $_POST['val-name'];
    $filePath = realpath($_FILES["val-file"]["tmp_name"]);
    $fileName = $_FILES['val-file']['name'];

    $newCategory = ParseObject::create("Category");

    $newCategory->set("name", $name);
    $newCategory->set("code", " ");
    $newCategory->set("image", ParseFile::createFromFile($filePath, $fileName));

    $newCategory->save(true);
}

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Category </h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Add new category </li>
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
                            <label for="val-name" class="col-sm-4 col-form-label">Category Name<span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-name" name="val-name" placeholder="Give a name to the category" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="val-file" class="col-sm-4 col-form-label">Category image (SVG file)<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input id="val-file" name="val-file" type="file" accept="image/svg+xml" required />
                                <div class="invalid-feedback">Please choose the image, in SVG format</div>
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


