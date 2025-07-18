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
            <h3 class="text-white">Challenge</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Challenge</li>
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

                    $query = new ParseQuery("Challenge");
                    $query->notEqualTo("Mode","LIV");
                    $streamCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$streamCounter.' Challenges in total</h2> ';

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
                                    <th style="color:#242526;">Challenger</th>
                                    <th style="color:#242526;">Mode</th>
                                    <th style="color:#242526;">Author's Video</th>
                                    <th style="color:#242526;">Challenger's Video</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Challenge");
                                    $query->descending('createdAt');
                                    $query->includeKey("author");
                                    $query->includeKey("challenger");
                                    $query->includeKey("authorVideo");
                                    $query->includeKey("challengerVideo");
                                    $query->includeKey("liveStreaming");
                                    $query->notEqualTo("Mode","LIV");
                                    
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $author_name = $cObj->get('author')->get('name');
                                        
                                        if($cObj->get('challenger') !== null){
                                            $challenger_name = $cObj->get('challenger')->get('name');    
                                        } else {
                                            $challenger_name = "<span/><a class=\"text-warning font-weight-bold\">No Challenger</a></span>";
                                        }
                        
                                        $mode_code = $cObj->get('Mode');

                                        switch ($mode_code) {
                                            case 'POL':
                                                $mode = "<span>POLL</span>";
                                                break;
                                            case '_1V1':
                                                $mode = "<span>1VS1</span>";
                                                break;
                                            case 'SIG':
                                                $mode = "<span>SINGLE</span>";
                                                break;
                                            case 'LIV':
                                                $mode = "<span>LIVESTREAMING</span>";
                                                break;
                                            case 'GRP':
                                                $mode = "<span>GROUP</span>";
                                                break;
                                            case 'TRN':
                                                $mode = "<span>TORNEO</span>";
                                                break;
                                        }

                                        if ($cObj->get('authorVideo') !== null) {

                                            $authorVideo = $cObj->get('authorVideo');

                                            if($authorVideo->get('video') !== null){

                                                $video_url = $authorVideo->get('video')->getURL(); 
                                                
                                                $authorVideoLink = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";
                                                
                                            } elseif ($authorVideo->get('url') !== null){

                                                $video_url = $authorVideo->get('url');

                                                $authorVideoLink = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";

                                            } else {
                                                $authorVideoLink = "<span/><a class=\"text-warning font-weight-bold\">No Author Video</a></span>";
                                            }

                                        } else {
                                            $authorVideoLink = "<span/><a class=\"text-warning font-weight-bold\">No Author Video</a></span>";
                                        }
                                        

                                        if ($cObj->get('challengerVideo') !== null) {

                                            $challengerVideo = $cObj->get('challengerVideo');

                                            if($challengerVideo->get('video') !== null){

                                                $video_url = $challengerVideo->get('video')->getURL(); 
                                                
                                                $challengerVideoLink = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";

                                            } elseif ($challengerVideo->get('url') !== null){

                                                $video_url = $challengerVideo->get('url');

                                                $challengerVideoLink = "<span><a href='#' onclick='playVideo(\"$video_url\")' class=\"badge badge-info\" style=\"background:#5d0375;\">Play</a></span>";

                                            } else {
                                                $challengerVideoLink = "<span/><a class=\"text-warning font-weight-bold\">No Challenger Video</a></span>";
                                            }

                                        } else {
                                            $challengerVideoLink = "<span/><a class=\"text-warning font-weight-bold\">No Challenger Video</a></span>";
                                        }
                            
                                               
                                        if($cObj->get('Reward') !== null){
                                            $reward_code = $cObj->get('Reward');

                                            switch ($reward_code) {
                                                case 'CLC':
                                                    $reward = "<span>CHALLENGECOIN</span>";
                                                    break;
                                                case 'GLC':
                                                    $reward = "<span>GOLDENCOIN</span>";
                                                    break;
                                                case 'SKN':
                                                    $reward = "<span>SKIN</span>";
                                                    break;
                                                case 'BST':
                                                    $reward = "<span>BOOSTER</span>";
                                                    break;
                                                case 'ACR':
                                                    $reward = "<span>ACCESSORIES</span>";
                                                    break;
                                                case 'CRT':
                                                    $reward = "<span>CRYPTO</span>";
                                                    break;
                                            }        
                                        } else {
                                            $reward_code = "<span/><a class=\"text-warning font-weight-bold\">No Reward</a></span>";
                                        }          
                                    
                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$author_name.'</td>
                                    <td>'.$challenger_name.'</td>
                                    <td>'.$mode.'</td>
                                    <td>'.$authorVideoLink.'</td>
                                    <td>'.$challengerVideoLink.'</td>
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
