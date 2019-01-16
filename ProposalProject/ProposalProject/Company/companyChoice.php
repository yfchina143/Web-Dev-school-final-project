<?php
	session_start();
	require_once("../dbLogin.php");

	if(isset($_POST['submitButton'])) {
     if(isset($_POST['companyChoice'])){
		$_SESSION['companyChoice'] = $_POST['companyChoice'];}
		 header("Location:../Calendar/calendar.php");
	}

	$menu = "";

	$db_connection = new mysqli($host, $user, $password, $database);
	if ($db_connection->connect_error) {
		die($db_connection->connect_error);
	} else {
	}

	$query = "select * from company";


	$result = $db_connection->query($query);
	if (!$result) {
		die("Retrieval failed: ". $db_connection->error);
	} else {

		$num_rows = $result->num_rows;
		if ($num_rows === 0) {
		} else {
			for ($row_index = 0; $row_index < $num_rows; $row_index++) {
				$result->data_seek($row_index);
				$row = $result->fetch_array(MYSQLI_ASSOC);
					$menu .= "<option value= '{$row['company_name']}'>{$row['company_name']}</option>";
			}
		}
	}

	/* Closing connection */
	$db_connection->close();

	/* Freeing memory */
	$result->close();

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
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h1 class="text-center">Please Select a Company</h1>
        </div>
         <div class="modal-body">
             <div class="form-group">
                <body onload="main()">
					<div class="container-fluid">
						<form action="{$_SERVER['PHP_SELF']}" method="post">
							<select name = "companyChoice">

EOBODY;

$page = $body.$menu."</select><br><br></div><div class='form-group'>
<input type='submit' class='btn btn-block btn-sm btn-primary' value = 'Submit' name='submitButton' />
</form>
             </div>
         </div>
    </div>
 </div>
 </body>
 </html>";
echo $page;

?>
