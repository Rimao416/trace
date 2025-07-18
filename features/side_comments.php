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
                <li class="breadcrumb-item active">Comments</li>
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

                    $query = new ParseQuery("VideoComments");
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Comments in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Video</th>
                                    <th style="color:#242526;">Author</th>
                                    <th style="color:#242526;">Comment</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("VideoComments");
                                    $query->descending('createdAt');
                                    $query->includeKey("video");
                                    $query->includeKey("author");
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $video = $cObj->get('video');
                                        
                                        if ($video->get('video') !== null) {
                                            
                                            $video_url = $video->get('video')->getURL(); 
                                                
                                            $link = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";

                                        } else if ($video->get('url') !== null) {
                                            $video_url = $video->get('url');

                                            $link = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";
                                        }else{
                                            $link = "<span/><a class=\"text-warning font-weight-bold\">No Video</a></span>";
                                        }

                                        $author = $cObj->get('author')->get('name');

                                        $comment = $cObj->get('text');

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$link.'</td>
                                    <td>'.$author.'</td>
                                    <td>'.$comment.'</td>
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
