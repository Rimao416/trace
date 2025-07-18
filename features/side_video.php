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
            <h3 class="text-white">Videos</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Videos</li>
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

                    $query = new ParseQuery("Posts");
                    $streamCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$streamCounter.' Videos in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Author</th>
                                    <th style="color:#242526;">Thumbnail</th>
                                    <th style="color:#242526;">Video</th>
                                    <th style="color:#242526;">Views</th>
                                    <th style="color:#242526;">Status</th>
                                    <th style="color:#242526;">Comments</th>
                                    <th style="color:#242526;">Likes</th>
                                    <th style="color:#242526;">Duration</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Posts");
                                    $query->exists("video");
                                    $query->descending('createdAt');
                                    $query->includeKey("author");
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;
                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $name = $cObj->get('author')->get('name');

                                        if ($cObj->get('thumbnail') !== null) {
                                            
                                            $image_url = $cObj->get('thumbnail')->getURL(); 
                                                
                                            $tumbnail = "<span><a href='#' onclick='showImage(\"$image_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";

                                        }else{
                                            $tumbnail = "<span/><a class=\"text-warning font-weight-bold\">No Tumbnail</a></span>";
                                        }

                                        if ($cObj->get('video') !== null) {
                                            
                                            $video_url = $cObj->get('video')->getURL(); 
                                                
                                            $link = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";

                                        } else if ($cObj->get('url') !== null) {
                                            $video_url = $cObj->get('url');

                                            $link = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";
                                        }else{
                                            $link = "<span/><a class=\"text-warning font-weight-bold\">No Video</a></span>";
                                        }
                                        
                                        $views = $cObj->get('views');
                                    
                                        $comments = count($cObj->get('comments'));

                                        $likes = count($cObj->get('likes'));


                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$name.'</td>
                                    <td>'.$tumbnail.'</td>
                                    <td>'.$link.'</td>
                                    <td><span>'.$views.'</span></td>
                                    <td>available</td>
                                    <td><span>'.$comments.'</span></td>
                                    <td><span>'.$likes.'</span></td>
                                    <td>00:35</td>
                                    <td><a href="../dashboard/edit_video.php?objectId='.$objectId.'" <span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
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
