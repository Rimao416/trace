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
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Posts</h3> </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Posts</li>
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
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Posts in total</h2> ';

                    ?>

                    <h5 class="card-subtitle">Copy or Export CSV, Excel, PDF and Print data</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ObjectId</th>
                                    <th>Date</th>
                                    <th>Author</th>
                                    <th>Description</th>
                                    <th>Picture</th>
                                    <th>Video</th>
                                    <th>Likes</th>
                                    <th>Comments</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Posts");
                                    $query->descending('createdAt');
                                    $query->includeKey("Author");
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $author = $cObj->get('Author')->get('name');

                                        $text = $cObj->get('text');
                                        
                                        if($cObj->get('video')) {
                                            $video_url = $cObj->get('video')->getURL(); 
                                                
                                            $videoTag = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";
                                        }else{
                                            $videoTag = "<span><a class=\"badge badge-warning\">No video</a></span>";
                                        }

                                        if ($cObj->get("list_of_images") !== null) {
                                            
                                            
                                            if (count($cObj->get("list_of_images")) >= 1) {
                                                $photos = $cObj->get('list_of_images');
                                                $image = ""; // Inicializar a variável $image

                                                for ($i = 0; $i < count($photos); $i++) {
                                                    $profilePhotoUrl = $photos[$i]->getURL();
                                                    $image .= "<span style=\"margin-right: 10px; margin-bottom: 10px;\"><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";
                                                }
                                            } else {
                                                $image = "<span><a class=\"badge badge-warning\">No Image</a></span>"; // Corrigir a estrutura da tag <span>
                                            }
                                            
                                            
                                            
                                                
                                            //$image = "<span/><a target='_blank' href=\"$profilePhotoUrl\" class=\"badge badge-info\">Download</a></span>";
                                            
                                            
                                            

                                        } else {
                                            $image = "<span/><a class=\"badge badge-warning\">No Image</a></span>";
                                        }

                                        if($cObj->get("likes") !== null){
                                            $likes = count($cObj->get("likes"));
                                        }else{
                                            $likes = 0;
                                        }

                                        if($cObj->get("comments") !== null){
                                            $comments = count($cObj->get("comments"));
                                        }else{
                                            $comments = 0;
                                        }

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$author.'</td>
                                    <td>'.$text.'</td>
                                    <td><span>'.$image.'</span></td>
                                    <td><span>'.$videoTag.'</span></td>
                                    <td>'.$likes.'</td>
                                    <td>'.$comments.'</td>
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
