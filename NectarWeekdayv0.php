<?php

function init(){
	
	$countEnd = 0;
	$countDay = 0;
	$totIncDay = 0;
	$totIncEnd = 0;
	$totSolEnd = 0;
	$totSolDay = 0;
	
}


function process($rows){

global $oldMonth;
global $countEnd;
global $countDay;
global $totIncDay;
global $totIncEnd;
global $totSolEnd;
global $totSolDay;

if($rows[0][2]-> val < 0){

	$rows[0][2]->val = 0;
	$rows[0][3]->val = 0;
}

$month = $rows[0][0]->val;  // config for month
$day = $rows[0][1]->val;   // config for day
$inc = $rows[0][2]->val;   // config for inc
$sol = $rows[0][3]->val;  // config for solution time



if(!$oldMonth){

	$oldMonth = $month;  // sets first month

}

if($oldMonth != $month){

	$oldMonth = $month;
	$countEnd = 0;
	$totIncEnd = 0;
	$countDay = 0;
	$totIncDay = 0;
	$totSolDay = 0;
	$totSolEnd = 0;

}

if($oldMonth == $month) { // if months equal check days

	if ($day == 'Saturday' || $day == 'Sunday') { // if days are sat or sunday store inc in variable
		$countEnd++; // move count forward
		$temp = $totIncEnd; // set temp for totInc
		$totIncEnd =  $temp + $inc; // totIncEnd equals $rows[0][2]
		$totSolEnd += $sol * $inc;
		$rows[0][2]->val = $totIncEnd;
		if($totIncEnd > 0){
			$rows[0][3]->val = $totSolEnd / $totIncEnd;
		}
		if ($countEnd == 2) {
			$rows[0][1]->val = "Weekend";
		}
		else{

			unset($rows[0]);

		}
	}
	if (!($day == 'Saturday' || $day == 'Sunday')) {
		$countDay++;
		$temp = $totIncDay;
		$totIncDay = $temp + $inc;
		$totSolDay += $sol * $inc;
		$rows[0][2]->val = (integer)$totIncDay;
		if($totIncDay > 0){
			$rows[0][3]->val = $totSolDay / $totIncDay;
		}
		if ($countDay == 5) {

			$rows[0][1]->val = "Weekday";


		}
		else {

			unset($rows[0]);

		}



	}

}




}










/**for($i = 3; $i < 4 ; $i++){



if($rows[0][$i]->val <= 0){

$rows[0][$i+1]->val = '00:00:00';

}

else {
$seconds = $rows[0][$i]->val;
$hours = floor($seconds / 3600);
$mins = floor($seconds / 60 % 60);
$secs = floor($seconds % 60);

if($hours < 10){

$hrs = '0'.strval($hours);

}
else{

$hrs = strval($hours);

}

if($mins < 10){

$minutes = '0'.strval($mins);

}
else{

$minutes = strval($mins);

}

if($secs < 10){

$seconds = '0'.strval($secs);

}
else{

$seconds = strval($secs);

}

$rows[0][$i+1]->val = $hrs.':'.$minutes.':'.$seconds;
}


} **/