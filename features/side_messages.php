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
            <h3 class="text-white">Messages</h3> </div>  -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Features</a></li>
                <li class="breadcrumb-item active">Messages</li>
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

                    $query = new ParseQuery('Message');
                    $query->doesNotExist('call');
                    $messagesCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$messagesCounter.' Messages in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">ObjectId</th>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">From</th>
                                    <th style="color:#242526;">To</th>
                                    <th style="color:#242526;">Message</th>
                                    <th style="color:#242526;">File</th>
                                    <th style="color:#242526;">Seen</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Message");
                                    $query->doesNotExist('call');
                                    $query->descending('createdAt');
                                    $query->includeKey("Author");
                                    $query->includeKey("Receiver");
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");

                                        $fromName = $cObj->get('Author')->get('name');
                                        
                                        if($cObj->get('Receiver') !== null){
                                           $toName = $cObj->get('Receiver')->get('name'); 
                                        }else{
                                            $toName = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        $seen_status = $cObj->get('read');
                                        if ($seen_status === true){
                                            $status_seen = "<span class=\"text-success font-weight-bold\">YES</span>";
                                        } else{
                                            $status_seen = "<span class=\"text-danger font-weight-bold\">NO</span>";
                                        }

                                        $typeFile = $cObj->get('messageType');
                                        if ($typeFile === 'picture'){

                                            $profilePhoto = $cObj->get("pictureMessage");
                                            $profilePhotoUrl = $profilePhoto->getURL();

                                            $fileFile = "<span class=\"text-success font-weight-bold\">YES</span>";
                                            $message = "<span><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";

                                        } else{

                                            $fileFile = "<span class=\"text-danger font-weight-bold\">NO</span>";
                                            $message = $cObj->get('text');
                                        }

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td>'.$created.'</td>
                                    <td>'.$fromName.'</td>
                                    <td>'.$toName.'</td>
                                    <td>'.$message.'</td>
                                    <td>'.$fileFile.'</td>
                                    <td>'.$status_seen.'</td>
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
