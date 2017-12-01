<form method="post" action="signup.php">

	Name:
	<input type="text" name="name" placeholder="Name" required>
	Last Name:
	<input type="text" name="last_name" placeholder="Last Name" required>
	Birthday:
	<input type="date" name="birthday" required>
	User Name:
	<input type="text" name="username" placeholder="Username" required>
	Password:
	<input type="password" name="password" placeholder="Password" required>
	Confirmation:
	<input type="password" name="confirmation" placeholder="Confirmation" required>
	Email:
	<input type="email" name="email" placeholder="Email" required>

	<div>
	<?php
		if (isset($message))
		{
			echo htmlspecialchars($message);
		}
	?>
	</div>

	<input type="submit" value="Signup">
	
</form>