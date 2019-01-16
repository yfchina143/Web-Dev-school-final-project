<?php
session_start();
require_once("../support.php");
require_once("../dbLogin.php");

$db_connection = new mysqli($host, $user, $password, $database);

if ($db_connection->connect_error) {
	    die($db_connection->connect_error);
}
$info = $_POST["nextPageValue"];
$info = explode(",",$info);
$_SESSION["info"] = $info;

$guest = $_SESSION["isGuest"];

if($guest == "true") {
	$_SESSION['firstName'] = $_SESSION["guestFirstName"];
	  $_SESSION['lastName'] =  $_SESSION["guestLastName"];
	  $_SESSION['email'] =  $_SESSION["guestEmail"];
	  $_SESSION['date'] = $info[0];
	  $_SESSION['time'] = $info[2];
	  $_SESSION['company'] = $info[1];
	  $_SESSION['appointmentWith'] = $info[3];
} else { //get from database

			//$db_connection = new mysqli($host, $user, $password, $database);
			$email = $_SESSION["userEmail"];
			$sqlQuery3 = "select * from user_info where email = \"{$email}\"";
			$result = $db_connection->query($sqlQuery3);
			
			$num_rows = $result->num_rows;
			
			if ($num_rows === 0) {	
				echo "Empty Table";
			} else {
				for ($row_index = 0; $row_index < $num_rows; $row_index++) {
					$result->data_seek($row_index);
					$row = $result->fetch_array(MYSQLI_ASSOC);
				}		
			}
			
			$result->close();
			$db_connection->close();
			
			$_SESSION['firstName'] = $row["firstName"];
		  	$_SESSION['lastName'] = $row["lastName"];
		  	$_SESSION['email'] =  $email;
		  	$_SESSION['date'] = $info[0];
		  	$_SESSION['time'] = $info[2];
		  	$_SESSION['company'] = $info[1];
		  	$_SESSION['appointmentWith'] = $info[3];
	}
	 
	
	$_SESSION["guestFirstName"] = "";
	$_SESSION["guestLastName"] = "";
	$_SESSION["guestPhoneFirst"] = "";
	$_SESSION["guestPhoneSecond"] = "";  
	$_SESSION["guestPhoneThird"] = "";
	$_SESSION["guestDOB"] = "";
	$_SESSION["guestEmail"] = "";
	$_SESSION["isGuest"] = "";

	header("Location:previewCommitt.php");
?>