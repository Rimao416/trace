<?php
/**
 * Created by PhpStorm.
 * User: maravilhosinga
 * Date: 13/06/18
 * Time: 01:26
 */

use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

?>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <!-- <div class="col-md-5 align-self-center">
            <h3 class="text-white">Control panel</h3> </div>  -->
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
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-md-6">
                    <div class="card p-30">
                        <div class="media">
                            <?php
                            try {
                            $query = new ParseQuery('_Installation');
                            $query->greaterThanOrEqualToRelativeTime('createdAt', '24 hrs ago');
                            $registedToday = $query->count(true);
    
    
                                echo '<div class="media-body media-text-left">
                                <h2>'.$registedToday.'</h2>
                                <p class="m-b-0">Installations today</p>
                            </div>';
                                // error in query
                            } catch (ParseException $e){ echo $e->getMessage(); } catch (Exception $e) {
                            }
                            ?>
                            <div class="media-left meida media-middle">
                                <span><i class="fa fa-tablet f-s-60 <?php echo $default_icon_color;?>"></i></span>
                            </div>
    
                        </div>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="card p-30">
                            <div class="media">
                                <?php
        
                                $query = new ParseQuery('_Installation');
                                $count = $query->count(true);
        
                                echo '
                                
                                <div class="media-body media-text-left">
                                    <h2>'.$count.'</h2>
                                    <p class="m-b-0">Total Installations</p>
                                </div>
                                
                                ';
        
                                ?>
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-tablet f-s-60 <?php echo $default_icon_color;?>"></i></span>
                                </div>
                            </div>
                        </div>
                </div>
                    <div class="col-md-6">
                        <div class="card p-30">
                            <div class="media">
                                <?php
                                
                                $queryTotal = new ParseQuery('_Installation');
                                $countTotal = $queryTotal->count(true);
        
                                $query = new ParseQuery('_Installation');
                                $query->equalTo('deviceType', 'android');
                                $count = $query->count(true);
                                // The count request succeeded. Show the count
                                //echo "Sean has played " . $count . " games";
        
                                echo '
                                
                                <div class="media-body media-text-left">
                                    <h2><span style="font-size:18pt;" id="android">'.$count.'</span> ('.round($count*100/$countTotal,2).'%)</h2>
                                    <p class="m-b-0">Android</p>
                                </div>
                                
                                ';
        
                                ?>
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-android f-s-60 text-warning"></i></span>
                                </div>
                            </div>
                        </div>
                </div>
                    <div class="col-md-6">
                        <div class="card p-30">
                            <div class="media">
                                <?php
                                
                                $queryTotal = new ParseQuery('_Installation');
                                $countTotal = $queryTotal->count(true);
        
                                $query = new ParseQuery('_Installation');
                                $query->equalTo('deviceType', 'ios');
                                $count = $query->count(true);
        
                                echo '
                                
                                <div class="media-body media-text-left">
                                    <h2><span style="font-size:18pt;" id="ios">'.$count.'</span> ('.round($count*100/$countTotal,2).'%)</h2>
                                    <p class="m-b-0">IOS</p>
                                </div>
                                
                                ';
        
                                ?>
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-apple f-s-60 text-danger"></i></span>
                                </div>
                            </div>
                        </div>
                </div>
                </div>       
            </div>
            <div class="col-12 col-md-6">
                <div class="card p-0">
                    <div class="card-body">
                        <div class="row" style="margin:10px 0px;text-align:center;">
                            <canvas id="myChart" style="width:100%;max-width:600px;margin-left:auto;margin-right:auto;margin-bottom:5;"></canvas>
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
                        <h4>Latest Installations</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-dark">
                                <thead class="thead-light">
                                <tr>
                                    <th>ObjectId</th>
                                    <th>Created</th>
                                    <th>Device</th>
                                    <th>Local</th>
                                    <th>Time Zone</th>
                                    <th>Push Type</th>
                                    <th>App Version</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                try {


                                    $query = new ParseQuery('_Installation');
                                    $query->descending('createdAt');
                                    $query->includeKey('user');
                                    $query->limit(20);
                                    $catArray = $query->find(true);

                                    foreach ($catArray as $iValue) {
                                        // Get Parse Object

                                        $objectId = $iValue->getObjectId();

                                        $createdDate= $iValue->getCreatedAt();

                                        $createdAt = date_format($createdDate,"d/m/Y");

                                        $device = $iValue->get('deviceType');
                                        if ($device === 'android'){
                                            $deviceType = "<span class=\"text-warning font-weight-bold\">Android</span>";
                                        } else if ($device === 'ios'){
                                            $deviceType = "<span class=\"text-danger font-weight-bold\">iOS</span>";
                                        } else {
                                            $deviceType = "<span class=\"text-info font-weight-bold\">$device</span>";
                                        }

                                        $local = $iValue->get('localeIdentifier');
                                        $timeZone = $iValue->get('timeZone');

                                        $pushType = $iValue->get('pushType');
                                        $appVersion = $iValue->get('appVersion');

                                        echo '
		            	
		            	        <tr>
                                    <td>'.$objectId.'</td>
                                    <td><span>'.$createdAt.'</span></td>
                                    <td><span>'.$deviceType.'</span></td>
                                    <td><span>'.$local.'</span></td>
                                    <td>'.$timeZone.'</td>
                                    <td>'.$pushType.'</td>
                                    <td>'.$appVersion.'</td>
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

<script>
    var android = Number(document.getElementById("android").innerText);
    var ios = Number(document.getElementById("ios").innerText);

    var xValues = ["Android", "IOS"];
    var yValues = [android, ios,];
    var barColors = ["rgb(255, 178, 41)", "rgb(239, 83, 79)",];
    
    /*var barColors = [
      "rgba(0,0,255,1.0)",
      "rgba(0,0,255,0.8)",
      "rgba(0,0,255,0.6)",
      "rgba(0,0,255,0.4)",
      "rgba(0,0,255,0.2)",
    ];*/
    
    new Chart("myChart", {
      type: "doughnut", //"pie", // type: "horizontalBar",
      data: {
        labels: xValues,
        datasets: [{
          backgroundColor: barColors,
          data: yValues
        }]
      },
      options: {
        title: {
            display: true,
            text: "Installation graph illustration"
        }
      }
    });
    
    
</script>
