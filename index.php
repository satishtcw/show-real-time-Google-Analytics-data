<?php
// Load the Google API PHP Client Library.
header("Refresh:60");
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/function.php';
require_once __DIR__ . '/rt_function.php';
//echo date_default_timezone_get();
session_start();
$analytics = initializeAnalytics();
	//$profile = getFirstProfileId($analytics);
	
	$profile = "9234054";
	$data['activeUsersType'] = activeUsersType($analytics, $profile);
	$data['activeUsersDevice'] = activeUsersDevice($analytics, $profile);
	$data['activeUsersPages'] = activeUsersPages($analytics, $profile);
	$data['sessionDuration'] = sessionDuration($analytics, $profile);
	$data['currentUsers'] = currentUsers($analytics, $profile);
	$data['activePagesView'] = activePagesView($analytics, $profile);
	$data['todayVisitors'] = todayVisitors($analytics, $profile,'today','today');
	$data['pages10'] = activeUsersPages($analytics, $profile);
	$data['user7day'] = getUsersByDay7($analytics, $profile);
	$data['maxDaysUser'] = maxDaysUser($analytics, $profile,'30daysAgo','today');

function initializeAnalytics()
{
  // Creates and returns the Analytics Reporting service object.

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';
  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_Analytics($client);

  return $analytics;
}

function getFirstProfileId($analytics) {
  // Get the user's first view (profile) ID.

  // Get the list of accounts for the authorized user.
  $accounts = $analytics->management_accounts->listManagementAccounts();

  if (count($accounts->getItems()) > 0) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();

    // Get the list of properties for the authorized user.
    $properties = $analytics->management_webproperties
        ->listManagementWebproperties($firstAccountId);

    if (count($properties->getItems()) > 0) {
      $items = $properties->getItems();
      $firstPropertyId = $items[0]->getId();

      // Get the list of views (profiles) for the authorized user.
      $profiles = $analytics->management_profiles
          ->listManagementProfiles($firstAccountId, $firstPropertyId);

      if (count($profiles->getItems()) > 0) {
        $items = $profiles->getItems();

        // Return the first view (profile) ID.
        return $items[0]->getId();

      } else {
        throw new Exception('No views (profiles) found for this user.');
      }
    } else {
      throw new Exception('No properties found for this user.');
    }
  } else {
    throw new Exception('No accounts found for this user.');
  }
}

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
   <style>
   .progress {
    height: 70px;
}
   </style>
</head>

<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
   
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <div class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard </a>
                          
                        </li>
                      
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </div>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Dashboard</h3> </div>
               
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
               

                <div class="row ">

            <div class="col-md-4">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-usd f-s-40 color-primary"></i></span>
                                </div>
                                <div class="media-body text-center">
								
									<h1>Right now</h1>
                                    <h2><?php echo $data['activeUsersType']['totals']['rt_activeUsers'];?></h2>
                                    <p class="m-b-0">active users on site</p>
									 <p class="m-b-0">30-Day Max <?php echo isset($data['maxDaysUser']['rows'][0]['ga_users'])?$data['maxDaysUser']['rows'][0]['ga_users']:'';?></p>
								
                                </div>
							
                            </div>
                        </div>
              </div>
			   <div class="col-md-4">
			  <div class="card">
							<div class="card-body">             
								<p>Mobile V Desktop</p>             
								<table class="table table-striped">
								<tbody> 
									<tr>
									<?php
										$users_arr=array();
										if(!empty($data['activeUsersDevice']['rows'])):
											foreach($data['activeUsersDevice']['rows'] as $key=>$value):
												if($value['rt_deviceCategory']=="MOBILE"):
													$users_arr['MOBILE'] =$value['rt_activeUsers'];
												elseif($value['rt_deviceCategory']=="DESKTOP"):
													$users_arr['DESKTOP'] =$value['rt_activeUsers'];
												endif;
											endforeach;
										endif;
										$mobileu=isset($users_arr['MOBILE'])?$users_arr['MOBILE']:'0';
										$desktopu=isset($users_arr['DESKTOP'])?$users_arr['DESKTOP']:'0';
										$traffic = percentage($mobileu,$desktopu);
									?>
										<td><img src="images/mobile.png" width="80">
												<?php echo $traffic['num1per'].'%'; ?>
										</td>
										<td><img src="images/desktop-512.png" width="80">
										<?php echo $traffic['num2per'].'%' ?>
										</td>
									</tr>
								</tbody>
								</table>
							</div>
						</div> </div>
						 <div class="col-md-4">
						<div class="card">
							<div class="card-body">             
								<p>Returning users V New users</p>            
								<table class="table table-striped">
								<tbody>
									<tr>
										<td><div class="progress">
											<?php 
											$users_arr=array();
											if(!empty($data['activeUsersType']['rows'])):
												foreach($data['activeUsersType']['rows'] as $key=>$value):
													if($value['rt_userType']=="NEW"):
														$users_arr['NEW'] =$value['rt_activeUsers'];
													elseif($value['rt_userType']=="RETURNING"):
														$users_arr['RETURNING'] =$value['rt_activeUsers'];
													endif;
												endforeach;
											endif;
											$newu=isset($users_arr['NEW'])?$users_arr['NEW']:'0';
											$returnu=isset($users_arr['RETURNING'])?$users_arr['RETURNING']:'0';
											$returnuser = percentage($returnu,$newu);
											?>
											<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $returnuser['num1per'] ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $returnuser['num1per'] ?>%">
												<span class=""><?php echo $returnu;?> Returning</span>
											</div>
											<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $returnuser['num2per'] ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $returnuser['num2per'] ?>%">
											<span class=""><?php echo $newu;?> New</span>
											</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
						</div>
						</div>
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Page views per minute</h4>
                                <div id="colomnchart"></div>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">LIVE: Concurent Visits(Since Midnight)</h4>
                                <div id="colomnchart2"></div>
                            </div>
                        </div>
                    </div>
						 <div class="col-md-6">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-usd f-s-40 color-primary"></i></span>
                                </div>
                                <div class="media-body text-center">
									<h2>Live:Engagement Today</h2>
									<?php $timeSeconds = isset($data['sessionDuration']['totals']['ga_sessionDuration'])?$data['sessionDuration']['totals']['ga_sessionDuration']:'';
									$daytime = getdayandtime($timeSeconds); ?>
                                    <h3><?php echo isset($daytime['day']) && !empty($daytime['day']) ?$daytime['day'].'days':''; ?></h3>
									 <h4><?php echo isset($daytime['hours']) && !empty($daytime['hours']) ?$daytime['hours'].'hours':''; ?> </h4>
									<h5><?php echo isset($daytime['minutes']) && !empty($daytime['minutes']) ?$daytime['minutes'].'minutes':''; ?></h5>
						
		
                            
                                </div>
							
                            </div>
                        </div>
					</div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">3D Chart</h4>
                                <div id="piechart_3d" ></div>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">3D Chart</h4>
                                <div id="piechart"  ></div>
                            </div>
                        </div>
                    </div>
                     
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Top Pages</h4>
                                <div id="table_div" ></div>
                            </div>
                        </div>
                    </div>
                    <!-- column -->
					
					
                </div>
  	        


                <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
            <!-- footer -->
       
            <!-- End footer -->
        </div>
        <!-- End Page wrapper  -->
    </div>
   

</body>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
     google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
	var data = new google.visualization.DataTable();
			  data.addColumn('string', 'year');
			  data.addColumn('number', 'Percent');
			  data.addRows(<?php echo json_encode($data['currentUsers']['chart']) ?>);   
                    var options_pie_3d = {
                        fontName: 'Roboto',
                        is3D: true,
                        height: 300,
                        width: 540,
                        chartArea: {
                            left: '5%',
                            width: '40%',
                            height: '95%'
                        }
                    };
        var options = {
          title: 'concurrent visitors',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
	  
    </script>
	<script language = "JavaScript">
	  google.charts.load('current', {packages: ['corechart']});   
         function drawChartColumn() {
			var data = new google.visualization.DataTable();
			  data.addColumn('string', 'year');
			  data.addColumn('number', 'Users');
			  data.addRows(<?php echo json_encode($data['todayVisitors']['chart']) ?>);   
                   
				 var options = {
                        fontName: 'Roboto',
                        height: 400,
						is3D: true,
                        fontSize: 12,
                        chartArea: {
                            left: '10%',
                            width: '90%',
                            height: '50%'
                        },
                        tooltip: {
                            textStyle: {
                                fontName: 'Roboto',
                                fontSize: 13
                            }
                        },
						hAxis: {
							title: 'Time',
							 titleTextStyle: {
                                fontSize: 13,
                                italic: false
                            },
                            gridlines: {
                                color: '#e5e5e5',
                                count: 10
                            },
                            minValue: 0
						},
                        vAxis: {
                            title: '',
                            titleTextStyle: {
                                fontSize: 13,
                                italic: false
                            },
                            gridlines: {
                                color: '#e5e5e5',
                                count: 10
                            },
                            minValue: 0
                        },
                        legend: {
                            position: 'top',
                            alignment: 'center',
                            textStyle: {
                                fontSize: 12
                            }
                        }
                    };

            // Instantiate and draw the chart.
            var chart = new google.visualization.ColumnChart(document.getElementById('colomnchart2'));
            chart.draw(data, options);
         }
         google.charts.setOnLoadCallback(drawChartColumn);
   </script>
	<script language = "JavaScript">
	  google.charts.load('current', {packages: ['corechart']});   
         function drawChartColumn() {
			var data = new google.visualization.DataTable();
			  data.addColumn('string', 'year');
			  data.addColumn('number', 'Page views');
			  data.addRows(<?php echo json_encode($data['activePagesView']['chart']) ?>);   
                   
				 var options = {
                        fontName: 'Roboto',
                        height: 400,
						is3D: true,
                        fontSize: 12,
                        chartArea: {
                            left: '10%',
                            width: '90%',
                            height: '50%'
                        },
                        tooltip: {
                            textStyle: {
                                fontName: 'Roboto',
                                fontSize: 13
                            }
                        },
						hAxis: {
							title: 'Time in minutes',
							 titleTextStyle: {
                                fontSize: 13,
                                italic: false
                            },
                            gridlines: {
                                color: '#e5e5e5',
                                count: 10
                            },
                            minValue: 0
						},
                        vAxis: {
                            title: '',
                            titleTextStyle: {
                                fontSize: 13,
                                italic: false
                            },
                            gridlines: {
                                color: '#e5e5e5',
                                count: 10
                            },
                            minValue: 0
                        },
                        legend: {
                            position: 'top',
                            alignment: 'center',
                            textStyle: {
                                fontSize: 12
                            }
                        }
                    };

            // Instantiate and draw the chart.
            var chart = new google.visualization.ColumnChart(document.getElementById('colomnchart'));
            chart.draw(data, options);
         }
         google.charts.setOnLoadCallback(drawChartColumn);
   </script>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['table']});
      google.charts.setOnLoadCallback(drawTable);

      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Pages');
        data.addColumn('number', 'Active Users');
		data.addColumn('string', 'Percent(%)');
      data.addRows(<?php echo json_encode($data['pages10']['chart']) ?>);     
        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
      }
    </script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		
		function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'year');
			data.addColumn('number', 'Percent');
			data.addRows(<?php echo json_encode($data['user7day']['chart']) ?>);  
			var options = {
				title: 'My Daily Activities',
						height: 300,
						is3D: true,
                        fontSize: 12,
			};
			 
			var chart = new google.visualization.PieChart(document.getElementById('piechart'));
			chart.draw(data, options);
		}
	</script>
</html>