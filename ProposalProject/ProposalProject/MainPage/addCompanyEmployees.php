<?php
	session_start();
	//require_once("../dbLogin.php");

	$bottomPart = "";

	function sanitize_string($db_connection, $string) {
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		return htmlentities($db_connection->real_escape_string($string));
	}

		if(isset($_POST['backButton'])) {
			header("Location:loginCreateGuestCompany.php");
		}

		if(isset($_POST['submitButton'])) {
			/* Connecting to the database */
			$db_connection = new mysqli($host, $user, $password, $database);
			if ($db_connection->connect_error) {
				die($db_connection->connect_error);
			}
      $companyName = $_SESSION['company'];
		  $name = sanitize_string($db_connection, trim($_POST['name']));
			/* Query */
			$query = "insert into company values('{$name}','{$companyName}')";

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
