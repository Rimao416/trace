<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseConfig;

session_start();

// get existing application-wide config
$config = new ParseConfig();

$accountAdsEnable = $config->get('googleAdsEnabled');

$currUser = ParseUser::getCurrentUser();
if ($currUser){

    // Store current user session token, to restore in case we create new user
    $_SESSION['token'] = $currUser -> getSessionToken();
} else {

    header("Refresh:0; url=../index.php");
}

// Update data ------------------------------------------------
if(isset($_POST['account'])){
    $adsStatus = $_POST['account'];

    //$config = new ParseConfig();

    if($adsStatus == "true"){
        $accountAdsEnable = true;
    } else {
        $accountAdsEnable = false;
    }
    

    // check a config value of yours
    //$adsEnabled = $config->get('adsEnabled');

    // add a simple config value
    $config->set('googleAdsEnabled', $accountAdsEnable);

    // save this global config
    $config->save();

}

?>

<?php
// Ads
$isAdsActive = $config->get('googleAdsEnabled');

if ($isAdsActive == true){

    $status1 = "Enabled";
    $statusDisabled1 = "true";

    $status2 = "Disabled";
    $statusDisabled2 = "false";

} else {

    $status1 = "Disabled";
    $statusDisabled1 = "false";

    $status2 = "Enabled";
    $statusDisabled2 = "true";

}

$_SESSION['account']  = $account;

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <!-- <h3 class="text-white">Advertising </h3> --></div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Advertising</a></li>
                <li class="breadcrumb-item active">Google Admob </li>
            </ol>
        </div>
    </div>

    <?php

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
                                <label class="col-lg-4 col-form-label" for="account">Google Ads Enable/Disable <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="account" name="account" disabled>
                                         <option value="'.$statusDisabled1.'">'.$status1.'</option>
                                         <option value="'.$statusDisabled2.'">'.$status2.'</option>
                                    </select>
                                </div>
                            </div>
                      
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-info"> Save </button>
                                <a class="btn btn-inverse" href="../dashboard/all_users.php"> Back </a>

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