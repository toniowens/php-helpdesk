<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verify Helpdesk Login</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<div class="container">
			<h1>Company Helpdesk</h1>
			<h2>Verify User Login</h2>

			<?php
				include "Settings.php";

				$errors = 0;
				$DBconnect = @mysql_connect($DBhost, $DBuser, $DBpassword);

				// Print an error if we can't connect.
				if ($DBconnect === FALSE)
				{
					echo "<p>Unable to connect to the database server. " .
						"Error code " . mysql_errno($DBconnect) .
						": " . mysql_error($DBconnect) . "</p>\n";
					++$errors;
				}
				else
				{
					$result = @mysql_select_db($DBname, $DBconnect);

					// Print an error if we can't select the database.
					if ($result === FALSE)
					{
						echo "<p>Unable to select the database. Error code " .
							mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
							"</p>\n";
						++$errors;
					}
				}
				$tableName = "users";

				if ($errors == 0)
				{
					// Look for the entered credentials in the users table.
					$SQLstring = "SELECT userID, name FROM $tableName
								  WHERE email = '" . stripslashes($_POST['email']) .
								  "' AND password_md5 = '" . md5(stripslashes($_POST['password'])) . "'";
					$queryResult = @mysql_query($SQLstring, $DBconnect);

					// If no records match, the login fails.
					if (mysql_num_rows($queryResult) == 0)
					{
						echo "<p>The e-mail address/password combination entered is not valid.</p>\n";
						++$errors;
					}
					// Otherwise, they can login!
					else
					{
						$row = mysql_fetch_assoc($queryResult);
						$userID = $row['userID'];
						$userName = $row['name'];

						echo "<p>Welcome back, " . $userName . "!</p>\n";
					}
				}

				// Message about all the errors thrown.
				if ($errors > 0)
				{
					echo "<p>Please use your browser's BACK button to return " .
						"to the form and fix the errors indicated.</p>\n";
				}

				// Pass the user ID.
				if ($errors == 0)
				{
					echo "<form method='POST' action='ViewTickets.php'>\n";
					echo "	<input type='hidden' name='userID' value='$userID' />\n";
					echo "	<input type='submit' name='submit' value='View Tickets' />\n";
					echo "</form>";

					echo "<form method='POST' action='SubmitTicket.php'>\n";
					echo "	<input type='hidden' name='userID' value='$userID' />\n";
					echo "	<input type='submit' name='submit' value='Submit A New Ticket' />\n";
					echo "</form>";
				}
			?>
		</div>
	</body>
</html>