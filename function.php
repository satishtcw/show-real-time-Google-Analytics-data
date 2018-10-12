<?php 
function percentage($num1,$num2){
	$num1per =$num2per=0;
	$per=array();
	$total = $num1+$num2;
	
	if(!empty($num1)):
		$num1per =  round($num1/$total,2)*100;
	endif;	
	if(!empty($num2)):
		$num2per =  round($num2/$total,2)*100;
	endif;
		$per['num1per']=$num1per;
		$per['num2per']=$num2per;
	return $per;
}

function percentagedecimal($total,$num){
	$per=array();
	$num1per=0;
	if(!empty($num)):
		$num1per =  number_format((float)(($num/$total)*100), 2, '.', ''); 
//		number_format(($num/$total),2)*100;
	endif;	
	$per['num1per']=$num1per;
	return $per;
}

function sessionDuration($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'today';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:sessionDuration"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,'ga:sessionDuration');
	  return resultBuilder($results);
}

function resultBuilder($results){
	
	if(count($results->getRows()) > 0) {
     $totals = $results->getTotalsForAllResults();
    $sanitizedTotals = array();
    foreach ($totals as $key => $value) {
        $replacedKey = str_replace(':', '_', $key);
        $sanitizedTotals[$replacedKey] = $value;
    }
    $columnHeaders = $results->getColumnHeaders();
    foreach ($columnHeaders as &$columnHeader) {
        $columnHeader['name'] = str_replace(':', '_', $columnHeader['name']);
    }
    $rows = $results->getRows();
    if (!is_array($rows)) {
        $rows = array();
    }
    $sanitizedRows = array();
    foreach ($rows as $rowIndex => $row) {
        foreach ($row as $columnIndex => $columnValue) {
            $columnName = $columnHeaders[$columnIndex]['name'];
            $sanitizedRows[$rowIndex][$columnName] = $columnValue;
        }
       
    }
	return array('totals' => $sanitizedTotals, 'rows' => $sanitizedRows);
  } else {
	 return array('rows'=>'');
  }
}
function maxDaysUser($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'30daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"dimensions"=>"ga:dateHourMinute",
			"sort"=>"-ga:users,-ga:dateHourMinute",
			"metrics"=>"ga:users",
			"max-results"=>"1"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}

function currentUsers($analytics,$profileId,$endday=NULL,$startday=NULL){
	$pieArry=$final=array();
	$endday = !empty($endday)?$endday:'today';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:newUsers,ga:users"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	 $response = resultBuilder($results);
		if(!empty($response['rows'])):
			settype($response['rows'][0]['ga_newUsers'],"integer");
			settype($response['rows'][0]['ga_users'],"integer");
			$pieArry[]=array('New Users['.$response['rows'][0]['ga_newUsers'].']',$response['rows'][0]['ga_newUsers']);
			$pieArry[]=array('Users['.$response['rows'][0]['ga_users'].']',$response['rows'][0]['ga_users']);			
		endif;
		$final['chart']= $pieArry;
		$final['result']= $response;
		return $final;
}

function todayVisitors($analytics,$profileId,$endday=NULL,$startday=NULL){
	$chart=$final=array();
	$endday = !empty($endday)?$endday:'today';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users",
			"dimensions"=>"ga:hour,ga:dateHour"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  $response = resultBuilder($results);
		if(!empty($response['rows'])):
		foreach($response['rows'] as $key=>$value){
			settype($value['ga_users'],"integer");
			$date2=substr($value['ga_dateHour'],0,4).'-'.substr($value['ga_dateHour'],4,2).'-'.substr($value['ga_dateHour'],6,2).' '.substr($value['ga_dateHour'],8,2).':00:00';
			$time= date('h A',strtotime($date2));
			$columnchart[]=array($time,$value['ga_users']);		
		}			
		endif;
		$final['chart']= $columnchart;
		$final['result']= $response;
		return $final;
}

function getPages10($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$chart=$final=array();
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"dimensions"=>"ga:pagePath",
			"metrics"=>"ga:pageValue",
			"sort"=>"-ga:pageValue",
			"max-results"=>'10');
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	   $response = resultBuilder($results);
	 if(!empty($response['rows'])):
		foreach($response['rows'] as $key=>$value){
			settype($value['ga_pageValue'],"integer");
			$columnchart[]=array($value['ga_pagePath'],$value['ga_pageValue']);		
		}			
		endif;
		$final['chart']= $columnchart;
		$final['result']= $response;
		return $final;
}

function getUsersByDay7($analytics,$profileId,$endday=NULL,$startday=NULL){
	$chart=$final=array();
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users",
			"dimensions"=>"ga:date"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	$response = resultBuilder($results);
		if(!empty($response['rows'])):
			foreach($response['rows'] as $key=>$value){
				settype($value['ga_users'],"integer");
				$date=substr($value['ga_date'],0,4).'-'.substr($value['ga_date'],4,2).'-'.substr($value['ga_date'],6,2);
				$time= date('M d, Y',strtotime($date));
				$chart[]=array($time.'['.$value['ga_users'].']',$value['ga_users']);
			}			
		endif;
		$final['chart']= $chart;
		$final['result']= $response;
		return $final;
}

 function getUsersByDay($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}
function getReturningUserByDay($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users",
			"segment"=>"gaid::-3"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}
function getNewUsersByDay($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:newUsers"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}
function getDaysActiveUsers($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"dimensions"=>"ga:date",
			"metrics"=>"ga:30dayUsers"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}
function getPages($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"dimensions"=>"ga:pagePath",
			"metrics"=>"ga:pageValue",
			"sort"=>"-ga:pageValue"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}

function getTabletTraffic($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users",
			"segment"=>"gaid::-13"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}
function getMobileTraffic($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'7daysAgo';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:users",
			"segment"=>"gaid::-14"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}

function todayEngagement($analytics,$profileId,$endday=NULL,$startday=NULL){
	
	$endday = !empty($endday)?$endday:'today';
	$startday = !empty($startday)?$startday:'today';
	$optParams = array(
			"metrics"=>"ga:newUsers,ga:users"
    );
  $results =  $analytics->data_ga->get(
      'ga:' . $profileId,
      $endday,
      $startday,
      'ga:sessions',$optParams);
	  return resultBuilder($results);
}

?>