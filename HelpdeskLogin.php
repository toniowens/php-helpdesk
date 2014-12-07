<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Helpdesk Login</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div class="container cf">
			<h1>Company Helpdesk</h1>
			<h2>Register / Log In</h2>
			<p>New users, use the registration form on the left. 
				Returning users, please log in on the right.</p>

			<div id="registration-form">
				<h3>New Helpdesk User</h3>
				<form method="POST" action="RegisterUser.php" class="cf">
					<p>Name (First and Last):
						<input type="text" name="user_name" required />
					</p>
					<p>E-mail Address:
						<input type="email" name="email" required />
					</p>
					<p>Password:
						<input type="password" name="password" required />
					</p>
					<p>Retype Password:
						<input type="password" name="password2" required />
					</p>
					<p>
						<input type="checkbox" name="is_admin" value="true" />
						Register as a helpdesk admin
					</p>
					<p>
						<input type="submit" name="submit" value="Register User" />
						<input type="reset" name="reset" value="Clear Form" />
					</p>
				</form>
			</div>

			<div id="login-form">
				<h3>Returning Helpdesk User</h3>
				<form method="POST" action="VerifyLogin.php">
					<p>E-mail Address:
						<input type="email" name="email" />
					</p>
					<p>Password:
						<input type="password" name="password" />
					</p>
					<p>
						<input type="submit" name="login" value="Log In" />
						<input type="reset" name="reset" value="Clear Form" />
					</p>
				</form>
			</div>
		</div>
	</body>
</html>