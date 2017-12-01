<?php
	
	require("./includes/config.php");
	
	if (isset($_SESSION["connected_user"]))
	{
		// verify the id of the connected user
		$user_id = $_SESSION["connected_user"];
		$verify_user = "SELECT COUNT(id) AS nbr FROM user_registery WHERE id=?";
		$result = query($verify_user, $user_id);
		if (isset($result[0]["nbr"]) && $result[0]["nbr"] == 1)
		{
			render("protected_resource.php", ["title" => "Welcome"]);
			exit;
		}
	}
	
	redirect("login.php");
?>