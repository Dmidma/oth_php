<h1>Welcome home</h1>
<p>
	You can do what ever you want here 
	<b>
		<?php
			echo query("SELECT username FROM user_registery WHERE id=?", $_SESSION["connected_user"])[0]["username"];
		?>
	</b>
	or just <a href="logout.php">Log Out</a>
</p>