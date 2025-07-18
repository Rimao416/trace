<?php
require '../vendor/autoload.php';
include '../Configs.php';
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
use Parse\ParseClient;
use Parse\ParseSessionStorage;
use Parse\ParseGeoPoint;

// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$currUser = ParseUser::getCurrentUser();
if (!$currUser) {
    header("Refresh:0; url=../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/dashboard/images/favicon.png">
    <title><?php echo $app_name;?> - Dashboard</title>
    <!-- Bootstrap Core CSS -->
    <link href="../assets/dashboard/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/dashboard/css/lib/calendar2/semantic.ui.min.css" rel="stylesheet">
    <link href="../assets/dashboard/css/lib/calendar2/pignose.calendar.min.css" rel="stylesheet">
    <link href="../assets/dashboard/css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="../assets/dashboard/css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="../assets/dashboard/css/helper.css" rel="stylesheet">
    <link href="../assets/dashboard/css/style.css" rel="stylesheet">
    <link href="../assets/dashboard/css/aliki.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
    <!-- <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" ></circle>
        </svg>
    </div> -->
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <?php
        // Get current User
        $currUser = ParseUser::getCurrentUser();
        $cuObjectID = $currUser->getObjectId();
        include '../admin/header_admin.php';
        include '../admin/left_sidebar_admin.php';
        include '../admin/control_panel_admin.php';
        ?>
        <!-- footer -->
        <?php include 'footer.php' ?>
        <!-- End footer -->
    </div>
    <!-- End Wrapper -->
    
    <!-- All Jquery -->
    <script src="../assets/dashboard/js/lib/jquery/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/dashboard/js/lib/bootstrap/js/popper.min.js"></script>
    <script src="../assets/dashboard/js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../assets/dashboard/js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="../assets/dashboard/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="../assets/dashboard/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <!--Custom JavaScript -->
    <script src="../assets/dashboard/js/custom.min.js"></script>
    
    <script>
        function logOut() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        }
    </script>
</body>
</html>