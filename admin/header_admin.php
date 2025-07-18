<?php

require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

session_start();

$currUser = ParseUser::getCurrentUser();
$cuObjectID = $currUser->getObjectId();

// Get avatar

try {
    
    $queryWidrawal = new ParseQuery('Withdrawal');
    $queryWidrawal->equalTo("status", "pending");
    $counter = $queryWidrawal->count(true);
    
    if($counter > 0){
        $msg = $counter > 1 ? 'There are new pedding payouts':'There is a new pedding payout'; 
        $content = '<li nav-item><a class="nav-link p-r-15 p-l-15" href="../dashboard/pending_withdrawals.php" style="color:#fff;margin-top:4px;">
            '.$msg.'</a></li>
            
            <li><a href="../dashboard/pending_withdrawals.php" class="p-r-15 p-l-15" style="text-align:center;"> View all </a></li>
            ';
    }else{
        $content = '<li><a href="#" class="p-t-25 p-b-25 p-r-15 p-l-15" style="text-align:center;"> No notifications for now</a></li>';
    }
    
    if($counter > 0){
        $badge = '<sup><span class="badge " style="font-size:12px;background:#5d0375;color:#fff;">'.$counter.'</span></sup>';
    }else{
        $badge = '';
    }
    
    if (sizeof($currUser->get("photos") > 0)) {

        $photos = $currUser->get('avatar');
        $name = $currUser->get('name');

        if ($photos !== null){
            $avatarURL = $photos->getURL();
        } else {
            $avatarURL = "../assets/dashboard/images/avatar_blank.png";
        }

    } else {
        $avatarURL = "../assets/dashboard/images/avatar_blank.png";
    }
} catch (Exception $e) {
}

?>

<?php

echo '
<div class="header" style="top:-3px">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- Logo 
        <div class="navbar-header" style="z-index:999;">
            &nbsp;
             <a class="navbar-brand" href="../dashboard/panel.php">
                <!-- Logo icon
                <b><img src="../assets/dashboard/images/logo.png" alt="homepage" class="dark-logo" width="40" /></b>
                <!--End Logo icon 
                <!-- Logo text 
                <!--<span><img src="../assets/dashboard/images/logo_text.png" alt="homepage" class="dark-logo" width="140" /></span>
            </a>
        </div> 
         End Logo -->
        <div class="navbar-collapse">
            <!-- toggle and nav items -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted " href="javascript:void(0)"><i class="mdi mdi-menu" style="color:#fff;"></i></a> </li>
                <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted" href="javascript:void(0)"><i class="ti-menu" style="color:#fff;"></i></a> </li>
            </ul>
            <!-- User profile and search -->
            <ul class="navbar-nav my-lg-0">

                <!-- Settings 
                <li nav-item><a class="nav-link" href="../dashboard/ads_settings.php" style="color:#fff;margin-top:4px;"><i class="fa fa-cog"></i> Settings</a></li>

                 Log out 
                <li nav-item><a class="nav-link" href="#" style="color:#fff;margin-top:4px;" onclick="logOut()"><i class="fa fa-sign-out"></i> Logout</a></li>-->
                
                <!-- Notifications -->
                
                <li class="nav-item dropdown p-t-8">
                <a class="nav-link dropdown-toggle text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span><i class="fa fa-bell h1"></i>'.$badge.'</span></a> 
                
                    <div class="dropdown-menu dropdown-menu-right p-t-0" style="background:#242526;color:#fff;box-shadow:0px 0px 1px #cecece">
                        <ul class="dropdown-user">
                        
                            '.$content.'
                        
                        </ul>
                    </div>
                </li>
                
                
                <!-- Profile  -->
                
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="' .$avatarURL.'" alt="user" class="profile-pic" /></a> 
                
                    <div class="dropdown-menu dropdown-menu-right  p-t-0" style="background:#242526;color:#fff;box-shadow:0px 0px 1px #cecece">
                        <ul class="dropdown-user">
                        
                            <!-- <li><a href="#" style="color:#fff;" onclick="logOut()"><i class="fa fa-sign-out"></i> Exit</a></li> -->
                            
                            <!-- Payouts 
                            <li nav-item><a class="nav-link" href="../dashboard/pending_withdrawals.php" style="color:#fff;margin-top:4px;">
                                <i class="fa fa-bell"></i>'.$badge.' &nbsp;&nbsp;&nbsp;Pending payouts</a></li> -->
                            
                            <!-- Settings 
                            <li nav-item><a class="nav-link" href="../dashboard/ads_settings.php" style="color:#fff;margin-top:4px;"><i class="fa fa-cog"></i> &nbsp;&nbsp;&nbsp;&nbsp;Settings</a></li>
                            -->
            
                            <!-- Profile -->
                            <li nav-item><a class="nav-link" href="../dashboard/edit_user.php?objectId='.$cuObjectID.'" style="color:#fff;margin-top:4px;"><i class="fa fa-user"></i> &nbsp;&nbsp;&nbsp;&nbsp;Edit profile</a></li>
            
                            <!-- Log out -->
                            <li nav-item><a class="nav-link" href="#" style="color:#fff;margin-top:4px;" onclick="logOut()"><i class="fa fa-sign-out"></i> &nbsp;&nbsp;&nbsp;&nbsp;Logout</a></li>
                    
                        </ul>
                    </div>
                </li>
                
                <!-- Profile Name 
                <li nav-item><a class="nav-link" href="#" style="color:#fff;margin-top:4px;" > '.$name.'</a></li> -->
                
            </ul>
        </div>
    </nav>
</div>

';

?>
