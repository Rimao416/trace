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
            <h3 class="text-white">Reports</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item active">Reports</li>
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

                    $query = new ParseQuery('Report');
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Reports in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Reporter</th>
                                    <th style="color:#242526;">Reported</th>
                                    <th style="color:#242526;">Reason</th>
                                    <th style="color:#242526;">Live Stream</th>
                                    <th style="color:#242526;">Video</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Report");
                                    $query->descending('createdAt');
                                    $query->includeKey("accuser");
                                    $query->includeKey("accused");
                                    $query->includeKey("liveStreaming");
                                    $query->includeKey("video");
                                    $catArray = $query->find(true);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $fromName = $cObj->get('accuser')->get('name');
                                        $toName = $cObj->get('accused')->get('name');
                                        
                                        $message = $cObj->get('message') !== null? $cObj->get('message') : '';
                                        
                                        if ($cObj->get('liveStreamingId') !== null){
                                            $objectIdStream = $cObj->get('liveStreamingId');
                                        } else {
                                            $objectIdStream = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        if ($cObj->get('videoId') !== null){
                                            $objectIdVideo = $cObj->get('videoId');
                                        } else {
                                            $objectIdVideo = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$fromName.'</td>
                                    <td>'.$toName.'</td>
                                    <td>'.$message.'</td>
                                    <td>'.$objectIdStream.'</td>
                                    <td>'.$objectIdVideo.'</td>
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
