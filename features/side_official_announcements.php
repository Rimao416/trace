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
                <li class="breadcrumb-item active">All Official Announcements</li>
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

                    $query = new ParseQuery("OfficialAnnouncement");
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Official Announcements</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">URL</th>
                                    <th style="color:#242526;">Title</th>
                                    <th style="color:#242526;">Sub-title</th>
                                    <th style="color:#242526;">Preview Image</th>
                                    <th style="color:#242526;">Views</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {
                                    
                                    $query = new ParseQuery("OfficialAnnouncement");
                                    $query->descending('createdAt');
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $title = $cObj->get('title');
                                        $subtitle = $cObj->get('sub_title');
                                        $url = $cObj->get('web_view_url');
                                        
                                        if($cObj->get("viewed_by") !== null){
                                            $views = count($cObj->get("viewed_by"));
                                        }else{
                                            $views = 0;
                                        }

                                        $previewImage = $cObj->get('preview_image');
                                        if ($previewImage !== null){

                                            $previewImageUrl = $previewImage->getURL();

                                            $previewImageTag = "<span style=\"margin-right: 10px; margin-bottom: 10px;\"><a href='#' onclick='showImage(\"$previewImageUrl\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";

                                        } else{

                                            $previewImageTag = "<span class=\"text-danger font-weight-bold\">NOT AVAILABLE</span>";
                                        }

                                        $giftName = $cObj->get('name');
                                        $giftCategory = $cObj->get('categories');

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$created.'</td>
                                    <td>'.$url.'</td>
                                    <td><span>'.$title.'</span></td>
                                    <td><span>'.$subtitle.'</span></td>
                                    <td>'.$previewImageTag.'</td>
                                    <td><span>'.$views.'</span></td>
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
