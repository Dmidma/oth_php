<?php
	
	// configuration
	require("./includes/config.php");

	// Only by GET
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{	
		// check if the 
		if(!empty($_GET) && isset($_GET["pghz"]) && isset($_GET["eadef"]))
		{	
			// get user id
			$user_id = filter_var($_GET["pghz"], FILTER_SANITIZE_SPECIAL_CHARS) ;
			// get salt
			$key = filter_var($_GET["eadef"], FILTER_SANITIZE_SPECIAL_CHARS);
				
			$verif_query = "SELECT COUNT(id) AS nbr FROM user_registery WHERE id=? AND melh=?;";
			$result_verif_query = query($verif_query, $user_id, $key);

			if (isset($result_verif_query[0]["nbr"]) && $result_verif_query[0]["nbr"] == 1)
			{
				$update_verif_query = "UPDATE user_registery SET verified=1 WHERE id=?;";
				$result_uvq = query($update_verif_query, $user_id);

				render("after_verification.php", ["title" => "Verification"]);
				exit;
			}
		}
	}

	redirect("index.php");
	exit;
?>