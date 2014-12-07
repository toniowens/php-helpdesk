<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Post Ticket Reply</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<div class="container">
			<h1>Company Helpdesk</h1>
			<h2>Post Ticket Reply</h2>

			<?php
				include "Settings.php";
				$errors = 0;

				// Make sure something was entered.
				if (empty($_POST['reply_body']))
				{
					echo "<p>No reply was entered.</p>";
					$errors++;
				}
				else
				{
					$replyBody = stripslashes($_POST['reply_body']);
				}

				// Retrieve userID
				if (isset($_POST['userID']))
				{
					$userID = $_POST['userID'];
				}
				else
				{
					$userID = -1;
					echo "<p>You are not logged in!</p>";
					$errors++;
				}

				// Retrieve ticketID
				if (isset($_POST['ticketID']))
				{
					$ticketID = $_POST['ticketID'];
				}
				else
				{
					$ticketID = -1;
					echo "<p>Invalid ticket ID!</p>";
					$errors++;
				}

				// Close ticket?
				if ($_POST['close_ticket'] == "true")
				{
					$close_ticket = 1;
				}
				else
				{
					$close_ticket = 0;
				}

				// Retrieve admin
				if (isset($_POST['is_admin']))
				{
					$is_admin = $_POST['is_admin'];
				}
				else
				{
					$is_admin = 0;
				}

				if ($errors > 0)
				{
					echo "<p>Please use the BACK button on your browser to 
						correct the errors indicated.</p>";
				}

				if ($errors == 0)
				{
					// Use the variables from Settings.php to setup $DBconnect.
					$DBconnect = @mysql_connect($DBhost, $DBuser, $DBpassword);

					// Make sure we can connect.
					if ($DBconnect === FALSE)
					{
						echo "<p>Unable to connect to the database server.</p>" .
							 "<p>Error code " . mysql_errno() . ": " .
							 mysql_error() . "</p>";
					}
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

						// We'll be able to select it now.
						mysql_select_db($DBname, $DBconnect);

						$tableName = "replies";
						$SQLstring = "SHOW TABLES LIKE '$tableName'";
						$queryResult = @mysql_query($SQLstring, $DBconnect);

						// If it doesn't exist, create it.
						if (mysql_num_rows($queryResult) == 0)
						{
							$SQLstring = "CREATE TABLE $tableName (
										  replyID SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
										  ticketID SMALLINT,
										  userID SMALLINT,
										  date_posted DATE,
										  reply_body VARCHAR(500))";
							$queryResult = @mysql_query($SQLstring, $DBconnect);

							if ($queryResult === FALSE)
							{
								echo "<p>Unable to create the table. Error code " .
									mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
									"</p>";
								++$errors;
							}
						}

						// Insert the variables we grabbed from $_POST earlier.
						$SQLstring = "INSERT INTO $tableName VALUES (
									  NULL, '$ticketID', '$userID', NOW(), '" . addslashes($replyBody) . "')";
						$queryResult = @mysql_query($SQLstring, $DBconnect);

						if ($queryResult === FALSE)
						{
							echo "<p>Unable to execute the query. Error code " .
								mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
								"</p>";
						}
						else
						{
							if ($close_ticket == 1)
							{
								$status = "Resolved";
							}
							else if ($is_admin == 1)
							{
								$status = "Awaiting User Response";
							}
							else
							{
								$status = "Awaiting Staff Response";
							}

							// Now we need to update some things in the tickets table.
							$tableName = "tickets";
							$SQLstring = "SELECT status, num_replies FROM $tableName WHERE ticketID = '$ticketID'";

							$queryResult = @mysql_query($SQLstring, $DBconnect);

							if (mysql_num_rows($queryResult) == 0)
							{
								echo "<p>Invalid ticket ID!</p>\n";
							}
							else
							{
								$row = mysql_fetch_assoc($queryResult);
								$num_replies = $row['num_replies'];
								$num_replies++;
								$SQLstring = "UPDATE $tableName
											  SET status = '$status', num_replies = '$num_replies'
											  WHERE ticketID = '$ticketID'";
								$queryResult = @mysql_query($SQLstring, $DBconnect);

								if ($queryResult === FALSE)
								{
									echo "<p>Unable to update ticket record. Error code " .
										mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
										"</p>";
								}
								else
								{
									echo "<p>Thank you, your reply has been posted.</p>";

									echo "<form method='POST' action='ViewTickets.php'>\n";
									echo "	<input type='hidden' name='userID' value='$userID' />\n";
									echo "	<input type='submit' name='submit' value='View Tickets' />\n";
									echo "</form>";

									echo "<form method='POST' action='SubmitTicket.php'>\n";
									echo "	<input type='hidden' name='userID' value='$userID' />\n";
									echo "	<input type='submit' name='submit' value='Submit A New Ticket' />\n";
									echo "</form>";

									echo "<form method='POST' action='ViewReplies.php?userID=$userID&ticketID=$ticketID'>\n";
									echo "	<input type='hidden' name='ticketID' value='$ticketID' />\n";
									echo "	<input type='submit' name='submit' value='Back to Ticket' />\n";
									echo "</form>";
								}
							}
						}
					}
				}
				mysql_close($DBconnect);
			?>
		</div>
	</body>
</html>