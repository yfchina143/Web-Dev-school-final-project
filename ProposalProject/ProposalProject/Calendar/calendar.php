<?php
	session_start();
	

function getInfo ($date, $company) {
	require_once("../dbLogin.php");
	$db_connection = new mysqli($host, $user, $password, $database);
	$d = $date;
	$c = $company;
	$people = array();	
	$time = array();
	$sqlQuery1 = "select employee_name from employees where company_name = \"{$c}\"";
	$result = $db_connection->query($sqlQuery1);

	$num_rows = $result->num_rows;
	
	if ($num_rows === 0) {	
		echo "Empty table";
	} else {
		for ($row_index = 0; $row_index < $num_rows; $row_index++) {
			$result->data_seek($row_index);
			$row = $result->fetch_array(MYSQLI_ASSOC);

			foreach($row as $r) {
				array_push($people, $r);
				$time[$r] = array();
			}
		}		
	}

	$db_connection = new mysqli($host, $user, $password, $database);
	$d = $date;
	$c = $company;

	$sqlQuery2 = "select appointmentWith, time from appointments where company = \"{$c}\" and date = \"{$d}\" order by time";
	$result = $db_connection->query($sqlQuery2);

	$num_rows = $result->num_rows;

	if ($num_rows === 0) {	

	} else {			
		for ($row_index = 0; $row_index < $num_rows; $row_index++) {
			$result->data_seek($row_index);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$count = 0;
			$name = "";

			foreach($row as $info) {
				if($count == 0) {
					$name = $info;
					$count++;
				} else {
					array_push($time[$name], intval($info));
				}
			}
		}
	}
	
	$result->close();
	$db_connection->close();		

	return $time;
}


function formatTime($time){
	$returnVal=intval($time/100);
	$amOrPm="";
	if($returnVal-12<0){
		$amOrPm="am";
	}
	else{
		$returnVal=$returnVal-12;
		if($returnVal==0){
			$returnVal=12;
		}
		$amOrPm="pm";
	}
	$returnVal.=":";
	$temp=$time%100;
	if($temp==0){
		return $returnVal.="00".$amOrPm;
	}
	else{
		$temp=($time%100);
		if($temp<10){
			$temp="0".$temp;
		}
		return $returnVal.=$temp.$amOrPm;
	}
	
}

function createTable($start,$end,$day,$interval,$people,$info){
	$returnVal ="<br/>";
	$emptyValues="";
		//table title
	$returnVal.= " <th><input type=text class=form-control placeholder=Time disabled></th>";
	for($i=0;$i<sizeof($people);$i++){
		$returnVal.= "<th>{$people[$i]}</th>";
	}
	$returnVal.= "</tr>";
	$currentHour=$start;
	$currentHour-=($start%100);
	//echo $currentHour."<br/>";
	$nextHour=$currentHour+100;
	$currentTime=$start;
	$rowNumber=($end-$start)/($interval/(0.6));
	$checkPoint=false;
	$thefixValue=0;
	//$currentTime-=$interval;
	for($i=0;$i<$rowNumber;$i++){
		$returnVal.= "<tr>";
		//advance to next hour
		if($currentTime>=$nextHour-40){
			//echo "advance to next before:".$currentTime."<br/>";
			$currentTime=$currentTime-$currentHour-60+$nextHour;

			//echo "advance to next:".$currentTime."<br/>";
			$currentHour=$nextHour;
			$nextHour+=100;
			$thefixValue=$currentTime;
			$formatedTime=formatTime($currentTime);
			$returnVal.= "<td>$formatedTime</td>";
			$checkPoint=true;
			
		}
		else{
			//echo $currentTime;
			
			if($checkPoint==true){
				$checkPoint=false;
				//echo "in hour to before next:".$currentTime."<br/>";
				$currentTime+=$interval;
				$thefixValue=$currentTime;
				$formatedTime=formatTime($currentTime);
				$returnVal.= "<td>$formatedTime</td>";
				//echo "in hour to next:".$currentTime."<br/>";
				$currentTime+=$interval;
				
				
			}
			else{
				//echo "in hour to before next:".$currentTime."<br/>";
				$thefixValue=$currentTime;
				$formatedTime=formatTime($currentTime);
				$returnVal.= "<td>$formatedTime</td>";
				$currentTime+=$interval;
				//echo "in hour to next:".$currentTime."<br/>";

			}
			
			
		}
		// //past code
		// if(($i)%4==0){
		// 	$currentTime+=55;
		// 	$formatedTime=formatTime($currentTime);
		// 	$returnVal.= "<td>$formatedTime</td>";
			
		// }
		// else{//next 15mins
		// 	$currentTime+=$interval;
		// 	$formatedTime=formatTime($currentTime);
		// 
	

		//generate appointments datas
		for($k=0;$k<sizeof($people);$k++){
			if(sizeof($info[$people[$k]])==0){//book me{$people[$k]}{$currentTime}
			$returnVal.= "<td><input type=\"submit\"  class=\"submitButton\" value=\"book\" name=\"{$people[$k]}{$thefixValue}\" id=\"{$people[$k]}{$thefixValue}\"></td>";
			$emptyValues.=$people[$k].$thefixValue.",";
		}else{
			$value=$info[$people[$k]][0];
			if($thefixValue!=$value){
				$returnVal.= "<td> <input type=\"submit\"  class=\"submitButton\" name=\"{$people[$k]}{$thefixValue}\" value=\"book\" id=\"{$people[$k]}{$thefixValue}\"></td>";
				$emptyValues.=$people[$k].$thefixValue.",";
			}
			else{
				$returnVal.= "<td> booked </td>";
				array_shift($info[$people[$k]]);
			}
		}

	}
		//end this row

		$returnVal.= "</tr>";
	}
	//end the table
	
	$returnVal.= "</table>";
	$returnVal.="<input type=\"hidden\" id=\"allButtons\" value=\"$emptyValues\" />";


	return $returnVal;	
}

if(isset($_POST["db"])) {
	$date = $_POST["db"];
} else {
	$date = date("m/d/Y");
}


$company = $_SESSION["companyChoice"];

$t = getInfo($date, $company);
$key=array_keys($t);
//print_r($key);
$hiddenInfo=$date.",".$company.",";

$table =  createTable(900,1900,$date,30,$key,$t);



$topPart = <<<EOBODY
<!doctype html>
<html>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>appointmentPage</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="calender.css" />
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

</head>
	<script>
		$( function() {
			$( "#datepicker" ).datepicker({
				changeMonth: true,
				changeYear: true,
				altField: "#alternate",
				altFormat: "mm/dd/yy"
			});
		} );
		
	</script>
    
<body onload="getDate()">
	<div class="container">
		<h3>Appointment System</h3>
		<hr>
	    <div class="col-md-10 col-md-offset-5">
		<form method="post" onsubmit="setTime()" action="{$_SERVER["PHP_SELF"]}">
			<div id="datepicker" name="datepicker"></div>
				<br>
				<input type = "hidden" id="newDate" value="">
			</div>
			<div class="col-md-10 col-md-offset-4">
				Date: <input type="text"  id="alternate" name="db" size="30">
				<input type="submit" id="refreshPage" value="Change Date"></div> <br>
			</div>
		</form>
			<div class="container">
				<div class="row">
					<div class="panel panel-primary filterable">
						<div class="panel-heading">
							<h3 class="panel-title">Please Select a Time</h3>
						</div>
						<table class="table">
							<tr class="filters">
						<form method="post" onsubmit="validateForm()" action="../Preview/preview.php">
							$table
						<input type="hidden" name="nextPageValue" id="nextPageValue" value="$hiddenInfo" >
						</form>
					</div>
				</div>
			</div>
			<input type="hidden" name="nextPageValue" id="nextPageValue" value="$hiddenInfo" >
		</div>
	</div>
</body>
	
	<script>
		

		let info=document.getElementById("allButtons").value;
		let res = info.split(",");
		res.pop();
		var p="";
		let tempName="";
		let tempTime="";
		let tempTimeAMPM="";
		let numberCheck = "";
		function formatTime(time){
			let returnVal=Math.floor(time/100);
			let amOrPm="";
			if(returnVal-12<0){
				amOrPm="am";
			}
			else{
				amOrPm="pm";
				returnVal=returnVal-12;
				if(returnVal==0){
					returnVal=12;
				}
				
			}
			returnVal+=":";
			let temp=time%100;
			if(temp==0){
				return returnVal+="00"+amOrPm;
			}
			else{
				return returnVal+=(time%100)+amOrPm;
			}

		}

		for(let i=0;i<res.length;i++){
			console.log("setting up button: "+res[i]);
			document.getElementById(res[i]).onclick=function(){
				p=""+res[i];
				tempName=/^[a-zA-Z\s]*/.exec(p);
				tempTime=/\d+/.exec(p);
				if (tempTime < 1000) {
					numberCheck = "0" + tempTime.toString();
					tempTime = numberCheck;
				}
				tempTimeAMPM=formatTime(tempTime);
			};
		}
		
		
		function validateForm() {
			let temp5=document.getElementById("nextPageValue").value;
			temp5=temp5+""+tempTime+","+tempName;
			document.getElementById("nextPageValue").value=temp5;
			return true;
		}
		
		function getDate(){
			let temp= sessionStorage.getItem('user');
			if(temp!=""){
				document.getElementById("alternate").value=temp;
				sessionStorage.setItem('user', "");

			}
		}
		function setTime(){
			let tempTime=document.getElementById("alternate").value;
			sessionStorage.setItem('user', tempTime);
		}
		
	</script>
</html>
EOBODY;
echo $topPart;

?>