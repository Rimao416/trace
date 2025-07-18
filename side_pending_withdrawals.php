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
            <h3 class="text-white">Payouts</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Accounting</a></li>
                <li class="breadcrumb-item active">Pending</li>
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

                    $query = new ParseQuery('Withdrawal');
                    $query->equalTo('status','pending');
                    $matchCounter = $query->count(true);

                    echo ' <h2 class="card-title">'.$matchCounter.' Pending payouts in total</h2> ';

                    ?>

                    <div class="card-body">
                        <div class="table-responsive">
                            <!--<table class="table">-->
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-light">
                                <tr>
                                    <th style="color:#242526;">Date</th>
                                    <th style="color:#242526;">Author Id</th>
                                    <th style="color:#242526;">Author</th>
                                    <th style="color:#242526;">Method</th>
                                    <th style="color:#242526;">Amount</th>
                                    <th style="color:#242526;">Currency</th>
                                    <th style="color:#242526;">Payoneer email</th>
                                    <th style="color:#242526;">IBAN</th>
                                    <th style="color:#242526;">Bank name</th>
                                    <th style="color:#242526;">Account name</th>
                                    <th style="color:#242526;">Status</th>
                                    <th style="color:#242526;">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("Withdrawal");
                                    $query->equalTo('status','pending');
                                    $query->descending('createdAt');
                                    $query->includeKey("author");
                                    $query->limit(1000000);
                                    $catArray = $query->find(true);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object
                                        $cObj = $iValue;

                                        $objectId = $cObj->getObjectId();
                                        $date= $cObj->getCreatedAt();
                                        $created = date_format($date,"d/m/Y");
                                        
                                        $authorId = $cObj->get('author')->getObjectId();
                                        $authorName = $cObj->get('author')->get('name');
                                        
                                        $paymentMethod = $cObj->get('method');
                                        
                                        $amount = $cObj->get('amount');
                                        $currency = $cObj->get('currency');

                                        if ($cObj->get('email') !== null){
                                            $email = $cObj->get('email');
                                        } else {
                                            $email = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        if ($cObj->get('IBAN') !== null){
                                            $iban = $cObj->get('IBAN');
                                        } else {
                                            $iban = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        if ($cObj->get('account_name') !== null){
                                            $account_name = $cObj->get('account_name');
                                        } else {
                                            $account_name = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        if ($cObj->get('bank_name') !== null){
                                            $bank_name = $cObj->get('bank_name');
                                        } else {
                                            $bank_name = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }
                                        
                                        $actual_status = $cObj->get('status');
                                        if($actual_status == "processing"){
                                            $status = '<span class="text-primary font-weight-bold p-5">'.$cObj->get('status').'</span>';
                                        }elseif($actual_status == "pending"){
                                            $status = '<span class="text-warning font-weight-bold p-5">'.$cObj->get('status').'</span>';
                                        }elseif($actual_status == "completed"){
                                            $status = '<span class="text-success font-weight-bold p-5 text-danger">'.$cObj->get('status').'</span>';
                                        }else{
                                            $status = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$created.'</td>
                                    <td>'.$authorId.'</td>
                                    <td>'.$authorName.'</td>
                                    <td>'.$paymentMethod.'</td>
                                    <td>'.$amount.'</td>
                                    <td>'.$currency.'</td>
                                    <td>'.$email.'</td>
                                    <td>'.$iban.'</td>
                                    <td>'.$bank_name.'</td>
                                    <td>'.$account_name.'</td>
                                    <td>'.$status.'</td>
		            	            <td><a href="../dashboard/edit_withdrawal.php?objectId='.$objectId.'" <span class="badge badge-info" style="background:#5d0375;padding:8;"><i class="fa fa-edit"></i></span></a></td>
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
