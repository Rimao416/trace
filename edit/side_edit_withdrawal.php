<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseException;

session_start();

$currUser = ParseUser::getCurrentUser();
if ($currUser){

    // Store current user session token, to restore in case we create new user
    $_SESSION['token'] = $currUser -> getSessionToken();
} else {

    header("Refresh:0; url=../index.php");
}

// Update data ------------------------------------------------
if(isset($_POST['status'])){
    // $username = $_POST['username'];
    // $fullName = $_POST['fullname'];
    $status = $_POST['status'];


    // $birthdayDate = new DateTime($birthday);

    // 4 days ago
    //$date = DateTime::createFromFormat('m/d/Y H:i:s', date("m/d/Y H:i:s"));

    $adObjID = $_GET['objectId'];

    $query = new ParseQuery("Withdrawal");
    try {
        $withdrawal = $query->get($adObjID, true);
        // The object was retrieved successfully.
    } catch (ParseException $ex) {
        // The object was not retrieved successfully.
        // error is a ParseException with an error code and message.
    }

    try {
        $withdrawal->set("status", $status);
        $withdrawal->save(true);

    } catch (Exception $e) {
    }

}

?>

<?php
// Get current User
$adObjID = $_GET['objectId'];

$query = new ParseQuery("Withdrawal");
$query->includeKey("author");
try {
    $currWithdrawal = $query->get($adObjID, true);
    // The object was retrieved successfully.
} catch (ParseException $ex) {
    // The object was not retrieved successfully.
    // error is a ParseException with an error code and message.
}

//$currUser = ParseUser::getCurrentUser();
$cuObjectID = $currWithdrawal->getObjectId();
$date= $currWithdrawal->getCreatedAt();

// Nome do autor
$name = $currWithdrawal->get('author')->get('name');
$amount = $currWithdrawal->get('amount');
$method = $currWithdrawal->get('method');

if($method != 'iban'){
    $destination = $currWithdrawal->get('email');
}else{
    $destination = $currWithdrawal->get('bank_name').' '.$currWithdrawal->get('IBAN').' '.$currWithdrawal->get('account_name');
}

// Data de publicação
$created = date_format($date,"Y-m-d");

// Todos os estados
$all_status = [
    "pending"=>"Pending",
    "processing"=>"Processing",
    "completed"=>"Completed",
];

// Status
$status = $currWithdrawal->get('status');
$currentStatus = $all_status[$status];

$_SESSION['author']   = $name;
$_SESSION['date']     = $created;
$_SESSION['status'] = $currentStatus;

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Edit Payout Status</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Videos</a></li>
                <li class="breadcrumb-item active">Edit payout status</li>
            </ol>
        </div>
    </div>

    <?php
    
    $indexes = [];
    $values = [];
    $selected = [];
    $selectedValue = '';
    
    foreach ($all_status as $i=>$v ) {
        if($status == $i){
            array_unshift($indexes,$i);
            array_unshift($values,$v);
        }else{
            array_push($indexes,$i);
            array_push($values,$v);
        }
    }

    echo '
    
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row bg-white m-l-0 m-r-0 box-shadow ">

        </div>
        <div class="row">
            <div class="col-12 col-md-10 col-xl-6" style="margin-left:auto; margin-right:auto;padding:10px 30px">
                <div class="card">
                    <div class="card-body">
                        <div class="form-validation">
                        <form class="form-valide" action="" method="post">
                        <div class="form-group row">
                            <label for="username" class="col-sm-4 col-form-label">Author <span class="text-danger">*</span> </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="author" name="author" value="'.$name.'" placeholder="Por favor coloque o nome de utilizador" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-sm-4 col-form-label">Requested in <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="date" name="date" value="'.$created.'" placeholder="dd/mm/yyyy" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="method" class="col-sm-4 col-form-label">Method <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="method" name="method" value="'.$method.'" placeholder="dd/mm/yyyy" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount" class="col-sm-4 col-form-label">Amount <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="amount" name="amount" value="'.$amount.' USD" placeholder="dd/mm/yyyy" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination" class="col-sm-4 col-form-label">Transfer to <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="destination" name="destination" value="'.$destination.'" placeholder="dd/mm/yyyy" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="status">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status">
                                    <option value="'.$indexes[0].'">'.$values[0].'</option>
                                    <option value="'.$indexes[1].'">'.$values[1].'</option>
                                    <option value="'.$indexes[2].'">'.$values[2].'</option>
                                </select>
                            </div>
                        </div>
                            
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn text-white" style="background:#5d0375;"> Save </button>
                                <a class="btn btn-inverse" href="../dashboard/withdrawals.php"> Back </a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
                </div>
        </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
    
    ';
    ?>


</div>