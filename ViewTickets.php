<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>View Helpdesk Tickets</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<div class="container">
			<h1>Company Helpdesk</h1>
			<h2>View Your Tickets</h2>

			<?php
				include "Settings.php";

				// Retrieve userID
				if (isset($_POST['userID']))
				{
					$userID = $_POST['userID'];
				}
				else
				{
					$userID = -1;
				}

				// Connect to server, select helpdesk database
				$errors = 0;
				$DBconnect = @mysql_connect($DBhost, $DBuser, $DBpassword);

				if ($DBconnect === FALSE)
				{
					echo "<p>Unable to connect to the database server. Error code " .
						mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
						"</p>\n";
					++$errors;
				}
				else
				{
					$result = @mysql_select_db($DBname, $DBconnect);

					if ($result === FALSE)
					{
						echo "<p>Unable to select the database. Error code " .
							mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
							"</p>\n";
						++$errors;
					}
				}

				$tableName = "users";

				// Locate the corresponding entry in the users table.
				if ($errors == 0)
				{
					$SQLstring = "SELECT * FROM $tableName
								  WHERE userID = '$userID'";
					$queryResult = @mysql_query($SQLstring, $DBconnect);

					if ($queryResult === FALSE)
					{
						echo "<p>Unable to execute the query. Error code " .
							mysql_errno($DBconnect) . ": " . mysql_error($DBconnect) .
							"</p>\n";
						++$errors;
					}
					else
					{
						if (mysql_num_rows($queryResult) == 0)
						{
							echo "<p>Invalid user ID!</p>";
							++$errors;
						}
					}
				}

				// Retrieve user's name and admin status.
				if ($errors == 0)
				{
					$row = mysql_fetch_assoc($queryResult);
					$name = $row['name'];
					$is_admin = $row['is_admin'];
				}
				else
				{
					$name = "";
					$is_admin = 0;
				}

				// Sidebar can go here.

				echo "<p>Hello, $name!</p>";

				// Check which tickets this user can view.
				$tableName = "tickets";
				$approvedTickets = 0;

				if ($is_admin == 1)
				{
					$SQLstring = "SELECT COUNT(ticketID) FROM $tableName 
								  WHERE date_posted IS NOT NULL";
					$queryResult = @mysql_query($SQLstring, $DBconnect);
				}
				else
				{
					$SQLstring = "SELECT COUNT(ticketID) FROM $tableName 
								  WHERE userID = '$userID' 
								  AND date_posted IS NOT NULL";
					$queryResult = @mysql_query($SQLstring, $DBconnect);
				}

				if (mysql_num_rows($queryResult) > 0)
				{
					$row = mysql_fetch_row($queryResult);
					$approvedTickets = $row[0];
					mysql_free_result($queryResult);
				}

				// Get the tickets from the tickets table.
				$tickets = array();
				if ($is_admin == 1)
				{
					$SQLstring = "SELECT ticketID, userID, urgency, date_posted, status, subject, category, num_replies FROM $tableName";
				}
				else
				{
					$SQLstring = "SELECT ticketID, userID, urgency, date_posted, status, subject, category, num_replies FROM $tableName WHERE userID = '$userID'";
				}

				$queryResult = @mysql_query($SQLstring, $DBconnect);
				
				if (mysql_num_rows($queryResult) == 0)
				{
					echo "<p>There are no tickets available for you to view!</p>\n";	
				}
				else
				{
					// Table head.
					echo "<div id='tickets'>\n";
					echo "	<table id='tickets-table'>\n";
					echo "		<tr>\n";
					echo "			<th>Ticket</th>\n";
					echo "			<th>Name</th>\n";
					echo "			<th>Started By</th>\n";
					echo "			<th>Replies</th>\n";
					echo "			<th>Urgency</th>\n";
					echo "			<th>Status</th>\n";
					echo "			<th>Date Posted</th>\n";
					echo "		</tr>\n";

					// Generate the table of tickets this user can view.
					while (($row = mysql_fetch_assoc($queryResult)) != FALSE)
					{
						echo "		<tr>\n";
						echo "			<td><a href='ViewReplies.php?userID=$userID&ticketID=" . 
											htmlentities($row['ticketID']) . "'>" . htmlentities($row['ticketID']) . 
											" [view]</a></td>\n";
						echo "			<td>[" . htmlentities($row['category']) . "] " .
											htmlentities($row['subject']) . "</td>\n";
						echo "			<td>" . htmlentities($row['userID']) . "</td>\n";
						echo "			<td>" . htmlentities($row['num_replies']) . "</td>\n";
						echo "			<td>" . htmlentities($row['urgency']) . "</td>\n";
						echo "			<td>" . htmlentities($row['status']) . "</td>\n";
						echo "			<td>" . htmlentities($row['date_posted']) . "</td>\n";
						echo "		</tr>\n";
					}
				}
				mysql_free_result($queryResult);
				mysql_close($DBconnect);

				// Close out the table.
				echo "	</table>\n";
				echo "</div>\n";

				echo "<form method='POST' action='SubmitTicket.php'>\n";
				echo "	<input type='hidden' name='userID' value='$userID' />\n";
				echo "	<input type='submit' name='submit' value='Submit A New Ticket' />\n";
				echo "</form>";
			?>
		</div>
	</body>
</html>