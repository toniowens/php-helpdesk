<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Submit A Helpdesk Ticket</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<div class="container">
			<h1>New Helpdesk Ticket</h1>

			<form class="cf" method="POST" action="EnterTicketData.php">
				<div class="form-sidebar">
					<p>Your User ID:
						<?php
							if (isset($_POST['userID']))
							{
								$userID = $_POST['userID'];
								echo "<strong>$userID</strong>";
							}
							else
							{
								$userID = -1;
								echo "<strong>You are not logged in!</strong>";
							}
						?>
					</p>
					<p>Urgency:
						<select name="urgency">
							<option value="Low" selected="selected">Low</option>
							<option value="Medium">Medium</option>
							<option value="High">High</option>
							<option value="Very High">Very High</option>
							<option value="Severe">Severe</option>
							<option value="Critical">Critical</option>
						</select>
					</p>
				</div>
				<div class="form-main">
					<p>Ticket Subject:
						<input type="text" name="subject" />
					</p>
					<p>Category:
						<select name="category">
							<option value="Defective Product">Defective Product</option>
							<option value="Performance">Performance Issue</option>
							<option value="Order Incorrect">Order Incorrect</option>
							<option value="Software">Software Issue</option>
							<option value="Other">Other</option>
						</select>
					</p>
					<p>Ticket Body:</p>
					<textarea name="ticket_body"></textarea>
					<?php
						echo "<input type='hidden' name='userID' value='$userID' />\n";
					?>
					<p>
						<input type="submit" value="Post Ticket" />
						<input type="reset" value="Clear Form" />
					</p>
				</div>
			</form>
		</div>
	</body>
</html>