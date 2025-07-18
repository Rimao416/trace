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
            <h3 class="text-white">Live Streams</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Live Streams</li>
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

                    $query = new ParseQuery("Streaming");
                    $streamCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$streamCounter.' Streams in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Streamer</th>
                                    <th style="color:#242526;">Gender</th>
                                    <th style="color:#242526;">Views</th>
                                    <th style="color:#242526;">Status</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Streaming");
                                    $query->descending('createdAt');
                                    $query->includeKey("Author");
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $name = $cObj->get('Author')->get('name');

                                        $gender = $cObj->get('Author')->get('gender');

                                        if ($gender === "MAL"){
                                            $UserGender = "Male";
                                        } else if ($gender === "FML"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Other";
                                        }

                                        $views = $cObj->get('viewersCountLive');

                                        // $credits = $cObj->get('streaming_tokens');
                                        // $duration = $cObj->get('streaming_time');

                                        // pegar status
                                        $status = $cObj->get('streaming');
                                        if ($status == true){
                                            $status_mine = "<span class=\"text-danger font-weight-bold\">LIVE NOW</span>";
                                        } else{
                                            $status_mine = "<span class=\"text-success font-weight-bold\">FINISHED</span>";
                                        }
                                        
                                        /*href="../dashboard/edit_video.php?objectId='.$objectId.'"*/

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$name.'</td>
                                    <td><span>'.$UserGender.'</span></td>
                                    <td><span>'.$views.'</span></td>
                                    <td>'.$status_mine.'</td>
                                    <td><a href="../dashboard/edit_streaming.php?objectId='.$objectId.'" <span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
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
