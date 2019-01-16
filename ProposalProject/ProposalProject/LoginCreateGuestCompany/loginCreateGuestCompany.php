<?php
	session_start();
	//declare(strict_types=1);
	require_once("..\Sanitize\Sanitize.php");
	use Sanitize\Sanitize;
	
	require_once("../dbLogin.php");

/*
	function sanitize_string($db_connection, $string) {
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		return htmlentities($db_connection->real_escape_string($string));
	}
*/
	$beginning = <<<BEGIN
		<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="loginCreateGuestCompany.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body onload="main()">
    <h1></h1>
    <div class="container-fluid">
BEGIN;

	if(isset($_POST['userLogin'])) {
		// connect.
		$db_connection = new mysqli($host, $user, $password, $database);
		if ($db_connection->connect_error) {
			die($db_connection->connect_error);
		} 

		// oop.
		$sanitize = new Sanitize();

		$email = $sanitize->sanitize_string($db_connection, trim($_POST['loginEmail']));
		$pw = $sanitize->sanitize_string($db_connection, $_POST['loginPW']);

		$body = <<<BODY
			<div class="grow">
            <div class="login">
                <p class="form-title">Login</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="login">
                    <input type="email" placeholder="Email" name="loginEmail" value="{$email}" required/>
                    <br>
                    <br/>
                    <input type="password" name="loginPW" placeholder="Password" value="{$pw} required/>
                    <br><br>
                    <input type="submit" value="Sign In" name="userLogin" class="submitButton" />
                </form>
BODY;
		if($db_connection->query("DESCRIBE user_info")) {
      		// table exists
      		/* Query */
			$query = "SELECT hashedPW FROM user_info WHERE email = '{$email}'";
					
			/* Executing query */
			$result = $db_connection->query($query);
			if (!$result) {
				die("Retrieval failed: ". $db_connection->error);
			} else {
				/* Number of rows found */
				$num_rows = $result->num_rows;
				if ($num_rows === 0) {
					$body .= "<p><strong>Invalid ID or PW</strong></p>";
				} else {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$hashedPW = $row['hashedPW'];
				}

				// then, password does not match.
				if(!password_verify($pw,$hashedPW)) {
					$body .= "<p><strong>Invalid ID or PW</strong></p>";
				} else {
					$_SESSION["userEmail"] = $_POST["loginEmail"];
					$_SESSION["isGuest"] = "false";
					header("Location: ../Company/companyChoice.php");
				}
			} 
    	} else {
    		// table doesn't exist.
    		$body .= "<p><strong>Invalid ID or PW</strong></p>";
		}
		$body .= "</div></div>";
		$beginning .= $body;
	} else {
		
		$initial_user_login = <<<INITIALUSER
		 <div class="grow">
            <div class="login">
                <p class="form-title">Login</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="login">
                    <input type="email" placeholder="Email" name="loginEmail" required/>
                    <br>
                    <br/>
                    <input type="password" name="loginPW" placeholder="Password" required/>
                    <br><br>
                    <input type="submit" value="Sign In" name="userLogin" class="submitButton" />
                </form>
            </div>
        </div>
INITIALUSER;
		$beginning .= $initial_user_login;
	}

	if(isset($_POST["createAccount"])) {
		$pass = trim($_POST["createPW"]);
	    // hash
	    $hashed = password_hash($pass, PASSWORD_DEFAULT);
	    $db_connection = new mysqli($host, $user, $password, $database);
	    
	    if ($db_connection->connect_error) {
	        die($db_connection->connect_error);
	    }

	    // check if table exists.
	    if($db_connection->query("DESCRIBE user_info")) {
	      // table exists
	    } else {
	    // create table.
	      $create_table = <<<CREATE
	            CREATE TABLE user_info (email VARCHAR(100) PRIMARY KEY, firstName VARCHAR(50), lastName VARCHAR(50), 
	            hashedPW VARCHAR(255), phoneFirst CHAR(3), phoneSecond CHAR(3), phoneThird CHAR(4), birthday VARCHAR(10));
CREATE;
	      $db_connection->query($create_table);
	    }

	    $firstName = trim($_POST['createFirstName']);
	    $lastName = trim($_POST['createLastName']);
	    $email = trim($_POST['createEmail']);
	    $phoneFirst = trim($_POST['createPhoneFirst']);
	    $phoneSecond = trim($_POST['createPhoneSecond']);
	    $phoneThird = trim($_POST['createPhoneThird']);
	    $birthday = $_POST['createBirthday'];
		$_SESSION["userEmail"] = $email;

	    $query = "insert into user_info values('{$email}', '{$firstName}', '{$lastName}', '{$hashed}', '{$phoneFirst}', '{$phoneSecond}', '{$phoneThird}','{$birthday}')";
	    $result = $db_connection->query($query);
	      if (!$result) {
	        die("Insertion failed: " . $db_connection->error);
	      }
	    $db_connection->close();

	    # move on to page 6 (calendar)
	    header("Location: ../Company/companyChoice.php");
	} else {
		$initial_create_account = <<<INITIALCREATE
			<!--CREATE AN ACCOUNT-->
        <div class="growCrtAct">
            <div class="create">
                <p class="form-title">Create an Account</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="create">
                    <p class="form-text">First Name</p>
                    <input name="createFirstName" type="text" required/>
                    <br>
                    <br/>
                    <p class="form-text">Last Name</p>
                    <input name="createLastName" type="text" required/>
                    <br>
                    <br/>
                    <p class="form-text">Email</p>
                    <input type="email" name="createEmail" required/>
                    <br>
                    <br/>
                    <p class="form-text">Password</p>
                    <input type="password" id="createPW" name="createPW" required/>
                    <br>
                    <br/>
                    <p class="form-text">Verify Password</p>
                    <input type="password" id="createVerifyPW" name="createVerifyPW" required/>
                    <br>
                    <br/>
                    <p class="form-text">Phone</p>
                    <input type="text" id="createPhoneFirst" name="createPhoneFirst" maxlength="3" size="3" required/>-
                    <input type="text" id="createPhoneSecond" name="createPhoneSecond" maxlength="3" size="3" required/>-
                    <input type="text" id="createPhoneThird" name="createPhoneThird" maxlength="4" size="4" required/>
                    <br>
                    <br>
                    <p class="form-text">Date of Birth</p>
                    <input type="text" name="createBirthday" maxlength="10" size="10" value="MM/DD/YYYY" required/>
                    <br>
                    <br>
                    <input type="submit" name="createAccount" value="Submit" class="submitButton" id="createSubmit" />
                </form>
                <script>
                	function main() {
    					document.getElementById("createSubmit").onclick = validateForm;
					}
					function checkPhone(){
					    var first = document.getElementById("createPhoneFirst").value;
					    var sec = document.getElementById("createPhoneSecond").value;
					    var third = document.getElementById("createPhoneThird").value;

					  if (isNaN(first) || isNaN(sec) || isNaN(third)){
					      return "Invalid phone number";
					    }
					    else{
					      return "";
					    }
					}

					function checkPass() {
					  var password = document.getElementById("createPW").value;
					  var confirmPassword = document.getElementById("createVerifyPW").value;

					  if(password !== confirmPassword){
					    return "Password does not match input for confirm password";
					  }
					  else{
					    return "";
					  }
					}

					function validateForm() {
					  var message = ""
					  message += checkPhone();
					  message += checkPass();
					    if(message == ""){
					      if(window.confirm("Do you want to submit the form data?")){
					        return true;
					      }
					      else{
					        return false;
					      }
					    }
					    else{
					      alert(message);
					      return false;
					    }
					}

                </script>
            </div>
        </div>
INITIALCREATE;
		$beginning .= $initial_create_account;
	}

	if(isset($_POST["guestSubmit"])) {
		$_SESSION["guestFirstName"] = $_POST["guestFirstName"];
        $_SESSION["guestLastName"] = $_POST["guestLastName"];
        $_SESSION["guestPhoneFirst"] = $_POST["guestPhoneFirst"];
        $_SESSION["guestPhoneSecond"] = $_POST["guestPhoneSecond"];  
        $_SESSION["guestPhoneThird"] = $_POST["guestPhoneThird"];
        $_SESSION["guestEmail"] = $_POST["guestEmail"];
        $_SESSION["isGuest"] = "true";

        # move on to the calendar.
        header("Location: ../Company/companyChoice.php");
	} else {
		$initial_guest = <<<INITIALGUEST
			<div class="growGuest">
            <div class="guest">
                <p class="form-title">Continue as Guest</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="guest">
                    <p class="form-text">First Name</p>
                    <input name="guestFirstName" type="text" required/>
                    <br>
                    <br/>
                    <p class="form-text">Last Name</p>
                    <input name="guestLastName" type="text" required/>
                    <br>
                    <br/>
                    <p class="form-text">Email</p>
                    <input type="email" name="guestEmail" required/>
                    <br>
                    <br/>
                    <p class="form-text">Phone</p>
                    <input type="text" name="guestPhoneFirst" maxlength="3" size="3" required/>-
                    <input type="text" name="guestPhoneSecond" maxlength="3" size="3" required/>-
                    <input type="text" name="guestPhoneThird" maxlength="4" size="4" required/>
                    <br>
                    <br>
                    <input type="submit" name="guestSubmit" value="Submit" class="submitButton" />
                </form>
            </div>
        </div>
INITIALGUEST;

		$beginning .= $initial_guest;
	}

	if(isset($_POST["companySubmit"])) {
		// connect.
		$db_connection = new mysqli($host, $user, $password, $database);
		if ($db_connection->connect_error) {
			die($db_connection->connect_error);
		} 

		$sanitize = new Sanitize();
		$email = $sanitize->sanitize_string($db_connection, trim($_POST['companyEmail']));
		$pw = $sanitize->sanitize_string($db_connection, $_POST['companyPW']);

		$body = <<<BODY
			<div class="grow">
            <div class="company">
                <p class="form-title">Company Login</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="company">
                    <input type="email" placeholder="Email" name="companyEmail" value="{$email}" required/>
                    <br>
                    <br/>
                    <input type="password" placeholder="Password" name="companyPW" value="{$pw}" required/>
                    <br><br>
                    <input type="submit" value="Sign In" class="submitButton" name="companySubmit" />
                </form>
BODY;
		if($db_connection->query("DESCRIBE company")) {
      		// table exists
      		/* Query */
      		// no hash
			$query = "SELECT company_pw FROM company WHERE company_email = '{$email}'";
			$dbPW = "";
			/* Executing query */
			$result = $db_connection->query($query);
			if (!$result) {
				die("Retrieval failed: ". $db_connection->error);
			} else {
				/* Number of rows found */
				$num_rows = $result->num_rows;
				if ($num_rows === 0) {
					$body .= "<p><strong>Invalid ID or PW</strong></p>";
				} else {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$dbPW = $row['company_pw'];
				}

				// then, password does not match.
				if(!($pw == $dbPW)) {
					$body .= "<p><strong>Invalid ID or PW</strong></p>";
				} else {
					# move onto company DB.
					$_SESSION["companyEmail"] = $email;
					header("Location: ../Company/companyDB.php");
				}
			} 
    	} else {
    		// table doesn't exist.
    		$body .= "<p><strong>Invalid ID or PW</strong></p>";
		}
		$body .= "</div></div>";
		$beginning .= $body;
	} else {
		$initial_company = <<<INITIALCOMPANY
			<div class="grow">
            <div class="company">
                <p class="form-title">Company Login</p>
                <form action="{$_SERVER['PHP_SELF']}" method="post" class="company">
                    <input type="email" placeholder="Email" name="companyEmail" required/>
                    <br>
                    <br/>
                    <input type="password" placeholder="Password" name="companyPW" required/>
                    <br><br>
                    <input type="submit" value="Sign In" class="submitButton" name="companySubmit" />
                </form>
            </div>
        </div>
INITIALCOMPANY;

		$beginning .= $initial_company;
	}

	$beginning .="</div></body></html>";
	echo($beginning);

?>