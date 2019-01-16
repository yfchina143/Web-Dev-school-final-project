
<?php

session_start();
require_once("../support.php");
require_once("../dbLogin.php");

//print_r($_SESSION["info"]);
$firstName = trim($_SESSION['firstName']);
	  	$lastName = trim($_SESSION['lastName']);
	  	$email = trim($_SESSION['email']);
	  	$date = trim($_SESSION['date']);
	  	$time = formatTime(trim($_SESSION['time']));
	  	$company = trim($_SESSION['company']);
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
	  	
if (isset($_POST["return"])) {
		header("Location:../Calendar/calendar.php");
} elseif (isset($_POST["submit"])) {

	$db_connection = new mysqli($host, $user, $password, $database);
	if ($db_connection->connect_error) {
		    die($db_connection->connect_error);
	}

// check if table exists.
	    if($db_connection->query("DESCRIBE appointments")) {
	      // table exists
	    } else {
	    // create table.
	      $create_table = <<<CREATE
	            CREATE TABLE appointments (firstName VARCHAR(100), lastName VARCHAR(100), email VARCHAR(100), date VARCHAR(10), time VARCHAR(10), company VARCHAR(100), appointmentWith VARCHAR(100));
CREATE;
		}


		// connect.
      	$db_connection->query($create_table);

      	// insert.
      	$query = "insert into appointments values('{$firstName}', '{$lastName}', '{$email}', '{$date}', '{$time}', '{$company}', '{$appointmentWith}')";
	    $result = $db_connection->query($query);

	    // error.
	    if (!$result) {
	       die("Insertion failed: " . $db_connection->error);
	    }
	    // close.
    	$db_connection->close();

		header("Location:../Confirmation/confirmation.php");
}



		$body = <<<EOBODY
		<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="preview.css" />
</head>
<body>
 <div class="form-group">
    <div class="container">
        <div class="col-md-12 col-lg-offset-2">
            <div class="login-signup">
                <div class="row">
                    <div class="col-md-8 mobile-pull">
                        <article role="login">
                            <form action="{$_SERVER['PHP_SELF']}" method="post">
                             <fieldset>
                                <div class="form-group">
                                    <p class="form-title"><strong>Date: </strong>{$date}</p><br>
                                </div>
                                <div class="form-group">
                                    <p class="form-title"><strong>Time: </strong>{$time}</p><br>
                                </div>
                                <div class="form-group">
                                    <p class="form-title"><strong>Company Name:</strong>{$company}</p><br>
                                </div>
                                <div class="form-group">
                                    <p class="form-title"><strong>Appointment with:</strong>{$appointmentWith}</p><br>
                                </div>
                                <div class="col-md-12 col-lg-offset-2">
                                    <input type="submit" name="submit" value = "Submit" class="submitButton" />
                                    <input type="submit" name="return" value = "Cancel" class="submitButton"/>
                                </div>
                               </fieldset>
                            </form>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
</body> 
EOBODY;

	$page = generatePage($body, "page 7");
	echo $page;
		?>