<?php
require '../vendor/autoload.php';
include '../Configs.php';

use Parse\ParseUser;
use Parse\ParseException;

$spinner_color = '#000';

$success = false;
$currUser = ParseUser::getCurrentUser();
// if ($currUser) {

//     try {
//         if ($currUser->get("role") === 'admin') {
//             header('Refresh:0; url=../dashboard/panel.php');
//         } else {
//             header('Refresh:0; url=../auth/logout.php');
//         }
//     } catch (Exception $e) {
//     }

// }


// LOGIN ------------------------------------------
if(isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];


    try {
        $user = ParseUser::logIn($username, $password);

        $currUser = ParseUser::getCurrentUser();
        if ($currUser->get("role") === 'admin'){
            { header('Refresh:0; url=../dashboard/panel.php'); }
        } else {
            { header('Refresh:0; url=../auth/logout.php'); }
        }
        
        $success = true;

        /*echo '
            <div class="text-center">
                <div class="alert alert-success">
                    You have successfully logged in. <br>
                    Please wait...
                </div>
            </div>
        ';*/
        

        // error
    } catch (ParseException $error) {

        $e = $error->getMessage();

        showSweetAlert("Error: ", $e, "error");

    } catch (Exception $e) {
    }
}

?>
<!-- header -->

<head>
    <title><?php echo $app_name;?> Dashboard - Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/dashboard/images/favicon.png">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="../assets/login/images/icons/favicon.png"/>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../assets/login/css/util.css">
    <link rel="stylesheet" type="text/css" href="../assets/login/css/main.css">
    <!--===============================================================================================-->

    <link href="../assets/dashboard/css/sweetalert2.css" rel="stylesheet">
    <link href="../assets/dashboard/css/sweetalert.css" rel="stylesheet">


</head>

<body>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            
            <form method="post" class="login100-form validate-form">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="../media/logo.png" alt="IMG">
                </div>
                
				<span class="login100-form-title">Welcome to dashboard</span>

                <div class="wrap-input100 validate-input" data-validate = "Email empty">
                    <input class="input100" type="text" name="username" placeholder="Admin username">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Password empty">
                    <input class="input100" type="password" name="password" placeholder="Admin Password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                </div>

                <div class="container-login100-form-btn">
                    <?php if($success){
                        echo '<button disabled class="login100-form-btn"><i class="fa fa-spin fa-spinner" style="color:".$spinner_color.";"></i></button>';
                    }else {
                        echo '<button class="login100-form-btn">Login</button>';
                    }?>    
                </div>

                <div class="text-center p-t-12">
						<span class="txt1">Forgot</span>
                    <a class="txt2" href="recover.php">Username or password?</a>
                </div>

                <div class="text-center p-t-136">

                </div>
            </form>
        </div>
    </div>
</div>

<?php

function showSweetAlert($title, $explain, $type)
{
    echo '<script type="text/javascript">
    setTimeout(function () { swal("'.$title.'","'.$explain.'","'.$type.'");
    }, 1000);</script>';
}
?>

<!--===============================================================================================-->
<script src="../vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="../vendor/bootstrap/js/popper.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="../vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="../vendor/tilt/tilt.jquery.min.js"></script>
<script >
    $('.js-tilt').tilt({
        scale: 1.1
    })
</script>
<!--===============================================================================================-->
<script src="../assets/login/js/main.js"></script>


<script src="../assets/dashboard/js/sweetalert2.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</body>
