<?php

use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles" class="row-top">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Control panel</h3> </div> -->
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Control panel</li>
            </ol>
        </div>
    </div>


    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->


        <div class="row">
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        try {
                        $query = new ParseQuery('_User');
                        $query->greaterThanOrEqualToRelativeTime('createdAt', '24 hrs ago');
                        $registedToday = $query->count();


                            echo '<div class="media-body media-text-left">
                            <h2>'.$registedToday.'</h2>
                            <p class="m-b-0">Registered today</p>
                        </div>';
                            // error in query
                        } catch (ParseException $e){ echo $e->getMessage(); } catch (Exception $e) {
                        }
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-user-plus f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("_User");
                        $count = $query->count();

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Total Users</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-users f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Message");
                        $count = $query->count();

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Messages</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-comments-o f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Video");
                        //$query->equalTo("tipo", "aluno");
                        $count = $query->count();
                        // The count request succeeded. Show the count
                        //echo "Sean has played " . $count . " games";

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Videos</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-play f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Streaming");
                        //$query->equalTo("tipo", "aluno");
                        $count = $query->count();
                        // The count request succeeded. Show the count
                        //echo "Sean has played " . $count . " games";

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Streamings</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-video-camera f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Challenge");
                        //$query->equalTo("tipo", "aluno");
                        $count = $query->count();
                        // The count request succeeded. Show the count
                        //echo "Sean has played " . $count . " games";

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">All challenges</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-hashtag f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Category");
                        //$query->equalTo("tipo", "aluno");
                        $count = $query->count();
                        // The count request succeeded. Show the count
                        //echo "Sean has played " . $count . " games";

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Categories</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-hashtag f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php

                        $query = new ParseQuery("Stories");
                        #$query->greaterThan("expiration_date", date("Y-m-d h:i:sa"));
                        $count = $query->count();
                        // The count request succeeded. Show the count
                        //echo "Sean has played " . $count . " games";

                        echo '
                        
                        <div class="media-body media-text-left">
                            <h2>'.$count.'</h2>
                            <p class="m-b-0">Stories</p>
                        </div>
                        
                        ';

                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-history f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row bg-white m-l-0 m-r-0 box-shadow ">

        </div>
        <div class="row">
            <div class="col-lg">
                <div class="card">
                    <div class="card-title">
                        <h4>Latest Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-dark">
                                <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Avatar</th>
                                    <th>Gender</th>
                                    <th>Bithday</th>
                                    <th>Location</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {

                                    $currUser = ParseUser::getCurrentUser();
                                    $cuObjectID = $currUser->getObjectId();

                                    $query = new ParseQuery("_User");
                                    $query->descending('createdAt');
                                    $query->limit(10);
                                    $query->notEqualTo('objectId', $cuObjectID);
                                    $catArray = $query->find(false);

                                    foreach ($catArray as $iValue) {

                                        $cObj = $iValue;

                                        $name = $cObj->get('name');
                                        $username = $cObj->get('username');

                                        if ($cObj->get("avatar") !== null) {

                                            $photos = $cObj->get('avatar');

                                            $profilePhotoUrl = $photos->getURL();
                                                
                                            $avatar = "<span><a href='#' onclick='showImage(\"$profilePhotoUrl\")' class=\"badge badge-info\" style=\"background:#5d0375;\">View</a></span>";

                                        } else {
                                            $avatar = "<span/><a class=\"text-warning font-weight-bold\">No Avatar</a></span>";
                                        }

                                        $gender = $cObj->get('gender');

                                        if ($gender === "MAL"){
                                            $UserGender = "Male";
                                        } else if ($gender === "FML"){
                                            $UserGender = "Female";
                                        } else {
                                            $UserGender = "Other";
                                        }

                                        $birthday= $cObj->get('birthday');
                                        if($birthday == null || $birthday == ""){
                                            $birthDate = '<span class="text-warning font-weight-bold p-5">Undefined</span>';
                                        }else{
                                            $birthDate = date_format($birthday,"d/m/Y");
                                        }

                                        $age = $cObj->get('age');

                                        $verified = $cObj->get('emailVerified');
                                        if ($verified == false){
                                            $verification = "<span class=\"text-warning font-weight-bold\">UNVERIFIED</span>";
                                        } else {
                                            $verification = "<span class=\"text-success font-weight-bold\">VERIFED</span>";
                                        }

                                        $locaton = $cObj->get('location');
                                        if ($locaton == null){
                                            $city_location = "<span class=\"text-warning font-weight-bold\">Unavailable</span>";
                                        } else{
                                            $city_location = "<span class=\"text-info font-weight-bold\">$locaton</span>";
                                        }

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$name.'</td>
                                    <td><span>'.$username.'</span></td>
                                    <td>'.$avatar.'</td>
                                    <td><span>'.$UserGender.'</span></td>
                                    <td><span>'.$birthDate.'</span></td>
                                    <td>'.$city_location.'</td>
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
