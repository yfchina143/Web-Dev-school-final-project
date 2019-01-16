<?php
	session_start();
	require_once("..\Sanitize\Sanitize.php");
	use Sanitize\Sanitize;
	require_once("../dbLogin.php");

	$bottomPart = "";

	$db_connection = new mysqli($host, $user, $password, $database);
			if ($db_connection->connect_error) {
				die($db_connection->connect_error);
			}
	// BACK GROUND COMPANY CREATE
	     if($db_connection->query("DESCRIBE company")) {
	      // table exists
	    } else {
	    // create table.
	      $create_table = <<<CREATE
	            CREATE TABLE company (company_name VARCHAR(100), company_email VARCHAR(100) PRIMARY KEY, company_pw VARCHAR(100), company_logo longblob);
CREATE;
	      $db_connection->query($create_table);

	      // MANUAL inserting LOGO.
		$googleLogo = addslashes(file_get_contents("../googleLogo.png"));
		$facebookLogo = addslashes(file_get_contents("../facebookLogo.png"));
		$snapchatLogo = addslashes(file_get_contents("../snapchatLogo.jpg"));

		$sqlQuery = "insert into company (company_name, company_email, company_pw, company_logo) values ";
		$sqlQuery .= "('Google', 'google@gmail.com', 'google', '{$googleLogo}')";

		$result = mysqli_query($db_connection, $sqlQuery);
		if (!$result) {
			die("Insertion failed: " . $db_connection->error);
		}

		$sqlQuery = "insert into company (company_name, company_email, company_pw, company_logo) values ";
		$sqlQuery .= "('Facebook', 'facebook@gmail.com', 'facebook', '{$facebookLogo}')";
		
		$result = mysqli_query($db_connection, $sqlQuery);
		if (!$result) {
			die("Insertion failed: " . $db_connection->error);
		}

		$sqlQuery = "insert into company (company_name, company_email, company_pw, company_logo) values ";
		$sqlQuery .= "('Snapchat', 'snapchat@gmail.com', 'snapchat', '{$snapchatLogo}')";
		
		$result = mysqli_query($db_connection, $sqlQuery);
		if (!$result) {
			die("Insertion failed: " . $db_connection->error);
		}
		////////////////////////////////////////////////////////////
	    }
		
	    
	/*
	function sanitize_string($db_connection, $string) {
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		return htmlentities($db_connection->real_escape_string($string));
	}*/

		if(isset($_POST['backButton'])) {
			header("Location:../LoginCreateGuestCompany/loginCreateGuestCompany.php");
		}

		if(isset($_POST['submitButton'])) {
			/* Connecting to the database */
			
      	$companyName = $_SESSION['company'];
      		// oop
      		$sanitize = new Sanitize();
		  $name = $sanitize->sanitize_string($db_connection, trim($_POST['name']));

		

		  	 // check if table exists.
	    if($db_connection->query("DESCRIBE employees")) {
	      // table exists
	    } else {
	    // create table.
	      $create_table = <<<CREATE
	            CREATE TABLE employees (employee_name VARCHAR(100), company_name VARCHAR(100));
CREATE;
	      $db_connection->query($create_table);
	    }

	    
			/* Query */
			$query = "insert into employees values('{$name}','{$companyName}')";

			/* Executing query */
			$result = $db_connection->query($query);
			if (!$result) {
				die("Insertion failed: " . $db_connection->error);
			}
     $bottomPart = "<br><strong>'{$name}' added.</strong></p>";
			$db_connection->close();
		}

	$body = <<<EOBODY
	<!DOCTYPE html>
	<html lang="en">

<!DOCTYPE html>
	<html lang="en">

<head>
	<title>Bootstrap Example</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="addCompanyEmployees.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body onload="main()">
	<div class="container-fluid">
	<form action="{$_SERVER['PHP_SELF']}" method="post">
		<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h1 class="text-center">Welcome</h1>
        </div>
         <div class="modal-body">
             <div class="form-group">
				 <strong>Employee Name: </strong><input type="text" class="form-control input-lg" name="name" /><br>
             </div>
             <div class="form-group">
				 <input type="submit" class="btn btn-block btn-sm btn-primary" value = "Submit" name="submitButton" /><br>
				 <input type="submit" class="btn btn-block btn-sm btn-primary" value = "Back" name="backButton" />
             </div>
         </div>
    </div>
 </div>
	</form>
EOBODY;

$body .="</div></body></html>";
$page = $body.$bottomPart;
echo $page;
?>
