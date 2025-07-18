<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseFile;
use Parse\ParseObject;
use Parse\ParseUser;


session_start();

$categories = ['love','moods','artists','collectibles','games','family','classic','3d','vip'];

$currUser = ParseUser::getCurrentUser();
if ($currUser){
    // Store current user session token, to restore in case we create new user
    $_SESSION['token'] = $currUser -> getSessionToken();
} else {

    header("Refresh:0; url=../index.php");
}

// SIGN UP ------------------------------------------------
if(isset($_POST['val-name']) && isset($_POST['val-credits']) && isset($_FILES['val-file'])){
    
    if($currUser->getObjectId() == "HwvtVkUbZp") {
        echo "<script>
        window.alert('You need administrator authorization to perform this action.');
        </script>";
    }else{
    
        $name = $_POST['val-name'];
        $category = $_POST['val-category'];
        $credits = $_POST['val-credits'];
        $filePath = realpath($_FILES["val-file"]["tmp_name"]);
        $fileName = $_FILES['val-file']['name'];

        $filePreviewPath = realpath($_FILES["val-preview-file"]["tmp_name"]);
        $filePreviewName = $_FILES['val-preview-file']['name'];
    
        $newGift = ParseObject::create("Gifts");
    
        $newGift->set("name", $name);
        $newGift->set("categories",'svga_gifts');
        $newGift->set("coins", (int)$credits);
        $newGift->set("file", ParseFile::createFromFile($filePath, $fileName));
        $newGift->set("preview", ParseFile::createFromFile($filePreviewPath, $filePreviewName));
    
        $newGift->save(true);
        
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
                <li class="breadcrumb-item active">Add new gift </li>
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
                            <label for="val-name" class="col-sm-4 col-form-label">Gift Name<span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="val-name" name="val-name" placeholder="Give a name to the gift" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="val-category" class="col-sm-4 col-form-label">Category<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="val-category" name="val-category">
                                    <?php foreach($categories as $c){
                                        echo '<option value="'.$c.'">'.$c.'</option>';
                                    }?>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="val-credits" class="col-sm-4 col-form-label">Credits<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="val-credits" name="val-credits" placeholder="Credits needed to send the gift" required>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                            <div class="form-group row">
                                <label for="val-file" class="col-sm-4 col-form-label">SVGA (.sgva) file<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input id="val-file" name="val-file" type="file" accept="application/svga" required />
                                    <div class="invalid-feedback">Please choose your Gift file, in .sgva format</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="val-preview-file" class="col-sm-4 col-form-label">PNG file<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input id="val-preview-file" name="val-preview-file" type="file" accept="application/png" required />
                                    <div class="invalid-feedback">Please choose your Gift Preview file, in .png format</div>
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


