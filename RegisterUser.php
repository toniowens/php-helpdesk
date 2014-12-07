<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Helpdesk User Registration</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<div class="container">
			<h1>Company Helpdesk</h1>
			<h2>User Registration</h2>

			<?php
				include "Settings.php";
				$errors = 0;
				$email = "";

				// Check to see if anything was entered in the e-mail field.
				if (empty($_POST['email']))
				{
					echo "<p>You need to enter an e-mail address.</p>\n";
					$errors++;
				}
				// Otherwise, compare it with a regex.
				else
				{
					$email = stripslashes($_POST['email']);
					if (!preg_match("/^[\w-]+(\.[\w-]+)*@" .
						"[\w-]+(\.[\w-]+)*(\.[a-zA-Z]{2, })$/i", $email) == 0)
					{
						echo "<p>You need to enter a valid e-mail address.</p>\n";
						$email = "";
						$errors++;
					}
				}

				// Check that the user entered a password.
				if (empty($_POST['password']))
				{
					echo "<p>You need to enter a password.</p>\n";
					$password = "";
					$errors++;
				}
				else
				{
					$password = stripslashes($_POST['password']);
				}

				// Check that the user retyped their password.
				if (empty($_POST['password2']))
				{
					echo "<p>You need to retype your password.</p>\n";
					$password2 = "";
					$errors++;
				}
				else
				{
					$password2 = stripslashes($_POST['password2']);
				}

				if ((!(empty($password))) && (!(empty($password2))))
				{
					// Make sure the password is at least 6 characters.
					if (strlen($password) < 6)
					{
						echo "<p>The password is too short.</p>\n";
						$password = "";
						$password2 = "";
						$errors++;
					}

					// Make sure password and password2 are the same.
					if ($password <> $password2)
					{
						echo "<p>The passwords do not match.</p>\n";
						$password = "";
						$password2 = "";
						$errors++;
					}
				}

				if ($errors == 0)
				{
					// Use variables from Settings.php to setup $DBconnect.
					$DBconnect = @mysql_connect($DBhost, $DBuser, $DBpassword);

					// If we can't connect, print an error message.
					if ($DBconnect === FALSE)
					{
						echo "<p>Unable to connect to the database server. " .
							"Error code " . mysql_errno() . ": " . mysql_error() . "</p>\n";
						$errors++;
					}
					// Otherwise, try and select the database.
					else
					{
						if(!@mysql_select_db($DBname, $DBconnect))
						{
							$SQLstring = "CREATE DATABASE $DBname";
							$queryResult = @mysql_query($SQLstring, $DBconnect);
						}

						if ($queryResult === FALSE)
						{
							echo "<p>Unable to create the database. " .
								"Error code " . mysql_errno($DBconnect) .
								": " . mysql_error($DBconnect) . "</p>\n";
							$errors++;
						}
						mysql_select_db($DBname, $DBconnect);

						// Check to see if the user table already exists.
						$tableName = "users";
						$SQLstring = "SHOW TABLES LIKE '$tableName'";
						$queryResult = @mysql_query($SQLstring, $DBconnect);

						// If not, create it.
						if (mysql_num_rows($queryResult) == 0)
						{
							$SQLstring = "CREATE TABLE $tableName (
										  userID SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
										  email VARCHAR(40),
										  password_md5 VARCHAR(32),
										  name VARCHAR(80),
										  is_admin TINYINT(1))";
							$queryResult = @mysql_query($SQLstring, $DBconnect);

							// Print an error message if the table can't be created.
							if ($queryResult === FALSE)
							{
								echo "<p>Unable to create the table. " . "Error code " .
									mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) . "</p>\n";
								$errors++;
							}
						}
					}
				}

				// Make sure the email isn't already being used.
				if ($errors == 0)
				{
					$SQLstring = "SELECT COUNT(*) FROM $tableName
								  WHERE email = '$email'";
					$queryResult = @mysql_query($SQLstring, $DBconnect);

					if ($queryResult !== FALSE)
					{
						$row = mysql_fetch_row($queryResult);
						if ($row[0] > 0)
						{
							echo "<p>The email address entered (" . htmlentities($email) .
								") is already registered.</p>\n";
							$errors++;
						}
					}
				}

				// Message for errors.
				if ($errors > 0)
				{
					echo "<p>Please use your browser's BACK button to return" .
						" to the form and fix the errors indicated.</p>\n";
				}

				// Make an entry in the users table.
				if ($errors == 0)
				{
					$name = stripslashes($_POST['user_name']);
					if ($_POST['is_admin'] == 'true')
					{
						$is_admin = 1;
					}
					else
					{
						$is_admin = 0;
					}
					$SQLstring = "INSERT INTO $tableName VALUES(
								  NULL, '$email', '" . md5($password) . "', '$name', $is_admin)";
					$queryResult = @mysql_query($SQLstring, $DBconnect);

					// If there's a problem, print an error message.
					if ($queryResult === FALSE)
					{
						echo "<p>Unable to save your registration information. Error code " .
							mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) . "</p>\n";
						$errors++;
					}
					else
					{
						// Get the user ID that was just generated.
						$userID = mysql_insert_id($DBconnect);
					}
					mysql_close($DBconnect);
				}

				// Print a success message!
				if ($errors == 0)
				{
					echo "<p>Thank you, $name. ";
					echo "Your new user ID is <strong>" . $userID . "</strong>.</p>\n";
				}

				// Include the forms with the hidden field if there were no errors.
				if ($errors == 0)
				{
					echo "<form method='POST' action='ViewTickets.php'>\n";
					echo "	<input type='hidden' name='userID' value='$userID' />\n";
					echo "	<input type='submit' name='submit' value='View Tickets' />\n";
					echo "</form>";

					echo "<form method='POST' action='SubmitTicket.php'>\n";
					echo "	<input type='hidden' name='userID' value='$userID' />\n";
					echo "	<input type='submit' name='submit' value='Submit A Ticket' />\n";
					echo "</form>";
				}
			?>
		</div>
	</body>
</html>