<?php 
function getdayandtime($time){
	$time = round($time);
	$time = preg_replace('/[.,]/', '', $time);
	$timeday=array();
	$days=$hours=$minutes=$seconds='';
	if(!empty($time)):
		$days = floor($time / (60 * 60 * 24));
		$time -= $days * (60 * 60 * 24);
		$hours = floor($time / (60 * 60));
		$time -= $hours * (60 * 60);
		$minutes = floor($time / 60);
		$time -= $minutes * 60;
		$seconds = floor($time);
		$time -= $seconds;
	endif;
	$timeday['day']=$days;
	$timeday['hours']=$hours;
	$timeday['minutes']=$minutes;
	$timeday['seconds']=$seconds;
	return $timeday;
}

function rt_resultBuilder($results){
	
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
	function activeUsersType($analytics,$profileId){
		
		$results=array();
		$optParams = array('dimensions' => 'rt:userType');
		try {
			$results = $analytics->data_realtime->get(
			'ga:' . $profileId,
			'rt:activeUsers',
			$optParams);
		} catch (apiServiceException $e) {
			$error = $e->getMessage();
		}
			
		return rt_resultBuilder($results);
	
	}
	function activePagesView($analytics,$profileId){
		
		$results=array();
		$chart=$final=array();
		$optParams = array('dimensions' => 'rt:minutesAgo');
		try {
			$results = $analytics->data_realtime->get(
			'ga:' . $profileId,
			'rt:pageviews',
			$optParams);
		} catch (apiServiceException $e) {
			$error = $e->getMessage();
		}
		 $response = resultBuilder($results);
		if(!empty($response['rows'])):
		foreach($response['rows'] as $key=>$value){
			settype($value['rt_pageviews'],"integer");
			$time=$value['rt_minutesAgo'].'m';
			$chart[]=array($time,$value['rt_pageviews']);		
		}			
		endif;
		$final['chart']= $chart;
		$final['result']= $response;
		return $final;
	
	}
	function activeUsersDevice($analytics,$profileId){
		
		$results=array();
		$optParams = array('dimensions' => 'rt:deviceCategory');
		try {
			$results = $analytics->data_realtime->get(
			'ga:' . $profileId,
			'rt:activeUsers',
			$optParams);
		} catch (apiServiceException $e) {
			$error = $e->getMessage();
		}
		return rt_resultBuilder($results);
	
	}
	
	function activeUsersPages($analytics,$profileId){
		$chart=$final=array();
		$results=array();
		$optParams = array('dimensions' => 'rt:pagePath');
		try {
			$results = $analytics->data_realtime->get(
			'ga:' . $profileId,
			'rt:activeUsers',
			$optParams);
		} catch (apiServiceException $e) {
			$error = $e->getMessage();
		}
	
		$response = resultBuilder($results);
		if(!empty($response['rows'])):
			foreach($response['rows'] as $key=>$value){
				$percentage = percentagedecimal($response['totals']['rt_activeUsers'],$value['rt_activeUsers']);
				settype($value['rt_activeUsers'],"integer");
				$chart[]=array($value['rt_pagePath'],$value['rt_activeUsers'],$percentage['num1per'].'%');		
			}			
		endif;
		$final['chart']= $chart;
		$final['result']= $response;
		//echo '<pre>'; print_r($final); die;
		return $final;
	
	}

	
	
?>