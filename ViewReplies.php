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
			<h2>View Ticket Replies</h2>

			<?php
				include "Settings.php";

				// Get the ticket ID.
				if (isset($_REQUEST['ticketID']))
				{
					$ticketID = $_REQUEST['ticketID'];
				}
				else
				{
					$ticketID = -1;
					echo "<p>No valid ticket selected.</p>";
				}

				// Get the user's ID.
				if (isset($_REQUEST['userID']))
				{
					$userID = $_REQUEST['userID'];
				}
				else
				{
					$userID = -1;
				}

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

				// Locate the entry for this user in the users table.
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
					if ($row['is_admin'] = "true")
					{
						$is_admin = 1;
					}
				}
				else
				{
					$name = "";
					$is_admin = 0;
				}

				// Sidebar can go here.

				echo "<p>Hello, $name!</p>\n";

				// Get the ticket info.
				$tableName = "tickets";

				if ($errors == 0)
				{
					$SQLstring = "SELECT * FROM $tableName
								  WHERE ticketID = '$ticketID'";
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
							echo "<p>Invalid ticket ID!</p>";
							++$errors;
						}
					}
				}

				// Grab the ticket's basic info.
				if ($errors == 0)
				{
					$row = mysql_fetch_assoc($queryResult);
					$ticketUserID = $row['userID'];
					$urgency = $row['urgency'];
					$datePosted = $row['date_posted'];
					$status = $row['status'];
					$subject = $row['subject'];
					$category = $row['category'];
					$ticketBody = $row['body'];
					$numReplies = $row['num_replies'];
				}
				else
				{
					$ticketUserID = "";
					$urgency = "";
					$datePosted = "";
					$status = "";
					$subject = "";
					$category = "";
					$ticketBody = "";
					$numReplies = "";
				}

				// Get the info of the user who posted the ticket.
				$tableName = "users";

				if ($errors == 0)
				{
					$SQLstring = "SELECT * FROM $tableName
								  WHERE userID = '$ticketUserID'";
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
							echo "<p>Ticket submitted by anonymous user.</p>";
							++$errors;
						}
					}
				}

				// Retrieve ticket author's name and email.
				if ($errors == 0)
				{
					$row = mysql_fetch_assoc($queryResult);
					$ticketUserName = $row['name'];
					$ticketUserEmail = $row['email'];
				}
				else
				{
					$name = "";
					$email = "";
				}

				// Display the ticket info.
				echo "<div id='ticket' class='cf'>\n";
				echo "<div id='ticket-side'>\n";
				echo "	<h4>Ticket Details</h4>\n";
				echo "	<p><strong>Ticket Number:</strong> $ticketID</p>\n";
				echo "	<p><strong>User ID:</strong> $ticketUserID</p>\n";
				echo "	<p><strong>User:</strong> $ticketUserName</p>\n";
				echo "	<p><strong>User Email:</strong> <a href='mailto:$ticketUserEmail'>$ticketUserEmail</a></p>\n";
				echo "	<p><strong>Posted:</strong> $datePosted</p>\n";
				echo "	<p><strong>Urgency:</strong> $urgency</p>\n";
				echo "	<p><strong>Status:</strong> $status</p>\n";
				echo "	<p><strong>Replies:</strong> $numReplies</p>\n";
				echo "	<hr />\n";
				echo "	<p><strong>Category:</strong> $category</p>\n";
				echo "</div>\n";

				echo "<div id='ticket-main'>\n";
				echo "	<h3>$subject</h3>\n";
				echo "	<p>$ticketBody</p>\n";
				echo "</div>\n";
				echo "</div>\n";

				// Now we can switch to the replies table.
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

				// Show the ticket's replies.
				if ($errors == 0)
				{
					$SQLstring = "SELECT * FROM $tableName
								  WHERE ticketID = '$ticketID'";
					$queryResult = @mysql_query($SQLstring, $DBconnect);

					if (mysql_num_rows($queryResult) == 0)
					{
						echo "<p>There are no replies posted for the selected ticket.</p>\n";
					}
					else
					{
						echo "<div id='replies'>\n";

						while (($row = mysql_fetch_assoc($queryResult)) != FALSE)
						{
							echo "<div class='reply'>\n";
							echo "	<h5>Reply from user ID: " . $row['userID'] .
								" on " . $row['date_posted'] . "</h5>\n";
							echo "	<p>" . stripslashes(htmlentities($row['reply_body'])) . "</p>\n";
							echo "</div>\n";
						}

						echo "</div>\n";
					}
				}

				// Show the form for posting a reply to the ticket.
				echo "<div id='reply-form'>\n";
				echo "	<form method='POST' action='PostReply.php'>\n";
				echo "		<p><strong>Post Reply:</strong></p>\n";
				echo "		<textarea name='reply_body'></textarea>\n";
				echo "		<p><input type='checkbox' name='close_ticket' value='true' /> Close this ticket</p>\n";
				echo "		<input type='hidden' name='userID' value='$userID' />\n";
				echo "		<input type='hidden' name='ticketID' value='$ticketID' />\n";
				echo "		<input type='hidden' name-'is_admin' value='$is_admin' />\n";
				echo "		<input type='submit' name='submit' value='Post Reply' />\n";
				echo "	</form>\n";

				echo "<form method='POST' action='ViewTickets.php'>\n";
				echo "	<input type='hidden' name='userID' value='$userID' />\n";
				echo "	<input type='submit' name='submit' value='View Tickets' />\n";
				echo "</form>";

				echo "<form method='POST' action='SubmitTicket.php'>\n";
				echo "	<input type='hidden' name='userID' value='$userID' />\n";
				echo "	<input type='submit' name='submit' value='Submit A New Ticket' />\n";
				echo "</form>";
				echo "</div>\n";

				mysql_free_result($queryResult);
				mysql_close($DBconnect);
			?>
		</div>
	</body>
</html>