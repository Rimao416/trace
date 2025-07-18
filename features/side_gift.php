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

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Gifts</h3> </div>  -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">All gifts</li>
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

                    <?php

                    $query = new ParseQuery("Gifts");
                    $categoriesToExclude = ["avatar_frame", "party_theme", "entrance_effect", "promotional_image"];
                    $query->notContainedIn("categories", $categoriesToExclude);
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Gift in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Name</th>
                                    <th style="color:#242526;">Category</th>
                                    <th style="color:#242526;">Credits</th>
                                    <th style="color:#242526;">File</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {
                                    
                                    $query = new ParseQuery("Gifts");
                                    $categoriesToExclude = ["avatar_frame", "party_theme", "entrance_effect", "promotional_image"];
                                    $query->notContainedIn("categories", $categoriesToExclude);
                                    $query->descending('createdAt');
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $credits = $cObj->get('coins');

                                        $typeFile = $cObj->get('file');
                                        if ($typeFile !== null){

                                            $profilePhotoUrl = $typeFile->getURL();

                                            $file = "<span/><a target='_blank' href=\"$profilePhotoUrl\" class=\"badge badge-info\" style=\"background:#5d0375;\">Download</a></span>";

                                        } else{

                                            $file = "<span class=\"text-danger font-weight-bold\">NOT AVAILABLE</span>";
                                        }

                                        $giftName = $cObj->get('name');
                                        $giftCategory = $cObj->get('categories');

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td><span>'.$giftName.'</span></td>
                                    <td><span>'.$giftCategory.'</span></td>
                                    <td>'.$credits.'</td>
                                    <td><span>'.$file.'</span></td>
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
