<?php

function init(){
	
	$countEnd = 0;
	$countDay = 0;
	$totIncDay = 0;
	$totIncEnd = 0;
	$totSolEnd = 0;
	$totSolDay = 0;
	$prodArrE = array(); 
	$prodArrW = array(); 
	
}













function process($rows[0]){

global $debug_file;
global $oldMonth;
global $countEnd;
global $countDay;
global $totIncDay;
global $totIncEnd;
global $totSolEnd;
global $totSolDay;
global $prodArrE;
global $prodArrW; 


$debug_mode = TRUE;
if ($debug_mode)
{
	if (!isset($debug_file))
		$debug_file = fopen("/vhosts/erewards/euf/assets/temp/logNectarWeekendbyProd.txt", 'w'); // open if not set
}


if ($debug_mode){ fwrite($debug_file, "---Row being processed---\r\n"); fflush($debug_file);}



if($rows[0][2]-> val < 0){

$rows[0][2]->val = 0; // reset null to zero
$rows[0][3]->val = 0;

}

$month = $rows[0][0]->val;  // config for month
$day = $rows[0][1]->val;   // config for day
$inc = $rows[0][2]->val;   // config for inc
$sol = $rows[0][3]->val;  // config for solution time
$prId = $rows[0][4]->val; // config for prod Id format: concat lvl1-lvl2

if ($debug_mode){ fwrite($debug_file, "Current month is: ".$month." Current day is: ".$day." Current incident is: " .$inc. " Current Solution Time is: ".$sol." .\r\n"); fflush($debug_file); }

if(!$oldMonth){

$oldMonth = $month;  // sets first month

}

if($oldMonth != $month){ // if months don't match reset data

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
	
	
	if($prodArrE){
		$match = false;
		foreach ($prodArrE as $prod){
	
			if($prod[0] == $prId){
					
				$match = true;
				$prod[1] += $inc; 
				$prod[2] += ($sol * $inc); 
				
		if ($debug_mode){ fwrite($debug_file, "prodArrE is appending... Current Prod is: ".$prId." Current number of incidents is: " .$inc. " Current Solution Time is: ".$sol." .\r\n"); fflush($debug_file); }		
		if ($debug_mode){ fwrite($debug_file, "prodArrE NEW values... Current Prod is: ".$prod[0]." Current number of incidents is: " .$prod[1]. " Current Solution Time is: ".$prod[2]." .\r\n"); fflush($debug_file); }
				break;
			}
	
	
		}
		if(!$match){
	        $pisArr = array(
	                      $prId, 
	        		      $inc, 
	        			  $sol,	        			        		
	       				 );
			
	        $prodArrE.push($pisArr);

	        if ($debug_mode){ fwrite($debug_file, "Adding new Array to prodArrE... Current Prod is: ".$prId." Current number of incidents is: " .$inc. " Current Solution Time is: ".$sol." .\r\n"); fflush($debug_file); }
	        if ($debug_mode){ fwrite($debug_file, "prodArrE NEW values... Current Prod is: ".$prod[0]." Current number of incidents is: " .$prod[1]. " Current Solution Time is: ".$prod[2]." .\r\n"); fflush($debug_file); }
	
		}
		
		$temp = $totIncEnd; // set temp for totInc
		$totIncEnd =  $temp + $inc; // totIncEnd equals $rows[0][2]
		$totSolEnd += $sol * $inc;  // keeps track of total solution time
		$rows[0][2]->val = $totIncEnd; // update $totInc time
		
		if($totIncEnd > 0)
		{ // only update total solution if totIncEnd exists
	//	$rows[0][3]->val = $totSolEnd / $totIncEnd;
		
		}
		
	}
	// product has some associated data total incidents and total solution time 
	
	
	if ($countEnd == 2)
	{
		$rows[0][1]->val = "Weekend";
		
	}

	else{

  	//	unset($rows[0]);

	}



}
if (!($day == 'Saturday' || $day == 'Sunday')) {
$countDay++;

	foreach ($prodArrD as $prod){
	
			if($prod[0] == $prId){
					
				$match = true;
				$prod[1] += $inc; 
				$prod[2] += ($sol * $inc); 
				
		if ($debug_mode){ fwrite($debug_file, "prodArrD is appending... Current Prod is: ".$prId." Current number of incidents is: " .$inc. " Current Solution Time is: ".$sol." .\r\n"); fflush($debug_file); }		
		if ($debug_mode){ fwrite($debug_file, "prodArrD NEW values... Current Prod is: ".$prod[0]." Current number of incidents is: " .$prod[1]. " Current Solution Time is: ".$prod[2]." .\r\n"); fflush($debug_file); }
				break;
			}
	
	
		}
		if(!$match){
	        $pisArr = array(
	                      $prId, 
	        		      $inc, 
	        			  $sol,	        			        		
	       				 );
			
	        $prodArrD.push($pisArr);

	        if ($debug_mode){ fwrite($debug_file, "Adding new Array to prodArrD... Current Prod is: ".$prId." Current number of incidents is: " .$inc. " Current Solution Time is: ".$sol." .\r\n"); fflush($debug_file); }
	        if ($debug_mode){ fwrite($debug_file, "prodArrD NEW values... Current Prod is: ".$prod[0]." Current number of incidents is: " .$prod[1]. " Current Solution Time is: ".$prod[2]." .\r\n"); fflush($debug_file); }
	
		}

// $rows[0][2]->val = ; //   PRINT VAL
if($totIncDay > 0){
// $rows[0][3]->val = $totSolDay / $totIncDay; // move to array
}
if ($countDay == 5) {

$rows[0][1]->val = "Weekday"; // set weekday on five


}
else {

// unset($rows[0]);

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


} 