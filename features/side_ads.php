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
            <h3 class="text-white">Comments</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">My ads</li>
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

                        $query = new ParseQuery("Advertising");
                        $matchCounter = $query->count(true);
    
                        echo ' <h2 class="card-title">'.$matchCounter.' Ads in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Title</th>
                                    <th style="color:#242526;">Description</th>
                                    <th style="color:#242526;">Type</th>
                                    <th style="color:#242526;">Presentation duration</th>
                                    <th style="color:#242526;">Content</th>
                                    <th style="color:#242526;">link</th>
                                    <th style="color:#242526;">Clicks</th>
                                    <th style="color:#242526;">Status</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Advertising");
                                    $query->descending('createdAt');
                                    $adArray = $query->find(false);

                                    foreach ($adArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");
                                        
                                        $title = $cObj->get('title');
                                        
                                        $description = $cObj->get('description');
                                        
                                        $type = $cObj->get('type');
                                        
                                        switch($type){
                                            case "video":
                                                $type_text = "Video";
                                                break;
                                            case "banner":
                                                $type_text = "Banner";
                                                break;
                                            case "medium_image":
                                                $type_text = "Medium Image";
                                                break;
                                            case "full_screen_image":
                                                $type_text = "Full Screen Image";
                                                break;
                                        }
                                        
                                        $link = $cObj->get('link');
                                        $link = $link != null 
                                            ? "<span><a href=\"$link\" target=\"blank\" class=\"badge badge-info\" style=\"background:#5d0375;\">Website</a></span>"
                                            : "<span/><a class=\"text-warning font-weight-bold\">Undefined</a></span>";
                                
                                        $file = $cObj->get('file');
                                        
                                        if($type == "video"){
                                            $video_url = $file->getURL(); 
                                                
                                            $file = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";
                                        }else{
                                            $image_url = $file->getURL(); 
                                                
                                            $file = "<span><a href='#' onclick='showImage(\"$image_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";
                                        }
                                        
                                        $duration = $cObj->get('PresentationDuration').' seconds';
                                        
                                        $click = $cObj->get('click') ?? "<span/><a class=\"text-warning font-weight-bold\">No clicks</a></span>";
                                        
                                        $status = $cObj->get('isActive') 
                                            ? "<span/><a class=\"text-success font-weight-bold\">Active</a></span>" 
                                            : "<span/><a class=\"text-danger font-weight-bold\">Inactive</a></span>";
                                        
                                        echo '
		            	
		            	        <tr>
		            	            <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$title.'</td>
                                    <td>'.$description.'</td>
                                    <td>'.$type_text.'</td>
                                    <td>'.$duration.'</td>
                                    <td>'.$file.'</td>
                                    <td>'.$link.'</td>
                                    <td>'.$click.'</td>
                                    <td>'.$status.'</td>
                                    <td><a href="../dashboard/edit_ad.php?objectId='.$objectId.'" <span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
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
