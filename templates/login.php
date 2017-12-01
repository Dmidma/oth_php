<form method="post" action="login.php">
	Username:
	<input type="text" name="username" placeholder="Username" required>
	Password:
	<input type="password" name="password" placeholder="Password" required>
	<div>
	<?php
		if (isset($message))
		{
			echo htmlspecialchars($message);
		}
	?>
	</div>
	<input type="submit" value="Login">
</form>