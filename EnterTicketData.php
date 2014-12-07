<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Enter Ticket</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div class="container">
			<?php
				// Get the database settings file.
				include "Settings.php";
				$errors = 0;

				// Check to make sure the required fields have been entered.
				if (empty($_POST['subject']))
				{
					echo "<p>Please enter a subject for your helpdesk 
						ticket.</p>";
					++$errors;
				}
				if (empty($_POST['ticket_body']))
				{
					echo "<p>Please provide information about the nature of 
						your problem in the ticket body.</p>";
					++$errors;
				}

				// If we have subject and body, continue.
				if ($errors > 0)
				{
					echo "<p>Please use the BACK button on your browser to 
						correct the errors indicated.</p>";
				}
				
				if ($errors == 0)
				{
					// Use the variables from Settings.php to setup $DBconnect.
					$DBconnect = @mysql_connect($DBhost, $DBuser, $DBpassword);

					// Make sure we can connect to the database.
					if ($DBconnect === FALSE)
					{
						echo "<p>Unable to connect to the database server.</p>" .
							 "<p>Error code " . mysql_errno() . ": " .
							 mysql_error() . "</p>";
					}
					// If we can connect, keep going.
					else
					{
						// We defined $DBname in Settings.php.
						// See if we can select it - if it's not there, create it.
						if (!@mysql_select_db($DBname, $DBconnect))
						{
							$SQLstring = "CREATE DATABASE $DBname";
							$queryResult = @mysql_query($SQLstring, $DBconnect);

							// If we can't execute the query, print an error message.
							if ($queryResult === FALSE)
							{
								echo "<p>Unable to execute the query. Error code " .
									mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
									"</p>";
							}
						}

						// We should be able to select the database now.
						mysql_select_db($DBname, $DBconnect);

						$tableName = "tickets";
						$SQLstring = "SHOW TABLES LIKE '$tableName'";
						$queryResult = @mysql_query($SQLstring, $DBconnect);

						// If the table's not there, create it.
						if (mysql_num_rows($queryResult) == 0)
						{
							$SQLstring = "CREATE TABLE $tableName (
										  ticketID SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
										  userID SMALLINT,
										  urgency VARCHAR(10),
										  date_posted DATE,
										  status VARCHAR(25),
										  subject VARCHAR(40),
										  category VARCHAR(25),
										  body VARCHAR(500),
										  num_replies SMALLINT)";
							$queryResult = @mysql_query($SQLstring, $DBconnect);

							if ($queryResult === FALSE)
							{
								echo "<p>Unable to create the table. Error code " .
									mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
									"</p>";
							}
						}

						// Set the variables to enter into the table.
						if (isset($_POST['userID']))
						{
							$userID = $_POST['userID'];
						}
						else
						{
							$userID = -1;
						}
						$urgency = stripslashes($_POST['urgency']);
						$status = "New";
						$subject = stripslashes($_POST['subject']);
						$category = stripslashes($_POST['category']);
						$ticketBody = stripslashes($_POST['ticket_body']);
						$numReplies = 0;

						$SQLstring = "INSERT INTO $tableName VALUES (
									  NULL, '$userID', '$urgency', NOW(), '$status', 
									  '$subject', '$category', '$ticketBody', $numReplies)";
						$queryResult = @mysql_query($SQLstring, $DBconnect);

						if ($queryResult === FALSE)
						{
							echo "<p>Unable to execute the query. Error code " .
								mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
								"</p>";
						}
						else
						{
							echo "<h2>Thank you!</h2>\n";
							echo "<p>A member of our staff will respond to your helpdesk ticket shortly.</p>";
							echo "<form method='POST' action='ViewTickets.php'>\n";
							echo "	<input type='hidden' name='userID' value='$userID' />\n";
							echo "	<input type='submit' name='submit' value='View Tickets' />\n";
							echo "</form>";

							echo "<form method='POST' action='SubmitTicket.php'>\n";
							echo "	<input type='hidden' name='userID' value='$userID' />\n";
							echo "	<input type='submit' name='submit' value='Submit Another Ticket' />\n";
							echo "</form>";
							echo "</div>\n";
						}
					}
					mysql_close($DBconnect);
				}
			?>
		</div>
	</body>
</html>