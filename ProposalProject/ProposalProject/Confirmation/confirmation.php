<?php
	session_start();
	require_once("../support.php");
  
  	$firstName = trim($_SESSION['firstName']);
  	$lastName = trim($_SESSION['lastName']);
  	$name = $firstName." ".$lastName;
  	$email = trim($_SESSION['email']);
  	$date = trim($_SESSION['date']);
  	$time = formatTime(trim($_SESSION['time']));
  	$company = trim($_SESSION['company']);
  	//echo($company);
  	$appointmentWith = trim($_SESSION['appointmentWith']);

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

	if (isset($_POST["toMain"])) {
		header("Location:../LoginCreateGuestCompany/loginCreateGuestCompany.php");
	} 

	$body = <<<EOBODY
	
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="confirmation.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<div class="container">

<h2>Confirmation</h2>
    <hr />		      
    <div class="agenda">
        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
			<form action="{$_SERVER['PHP_SELF']}" method="post">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
						<th>Appointment with</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr>
                        <td class="agenda-date" class="active" rowspan="12">
						 
                            <div class="dayofweek">{$date}</div>
                        </td>
                        <td class="agenda-time">
                             <p>{$time}</p><br>
                        </td>
                        <td class="agenda-events">
                            <div class="agenda-event">
								<p>{$name}</p><br>
                            </div>
                        </td>
                        <td class="agenda-events">
                            <div class="agenda-event">
                              <p>{$email}</p><br>
                            </div>
                        </td>
                        <td class="agenda-events">
                            <div class="agenda-event">
							   <p>{$company}</p><br>
                            </div>
                        </td>
						<td class="agenda-events">
                            <div class="agenda-event">
							   <p>{$appointmentWith}</p><br>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
			<p><input type="submit" name="toMain" value = "Go back to Login Page"/>
			</form>
        </div>
    </div>
</div>
		
EOBODY;

	$page = generatePage($body, "php 8");
	echo $page;
  ?>