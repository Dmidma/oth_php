<?php

	// configuration
	// require("./includes/functions.php");
	require("./includes/config.php");


	// if user reached page via GET
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{	
		render("login.php", ["title" => "Log In"]);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{	
		// get user name and password
		$username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
		$password = filter_var(trim($_POST["password"]), FILTER_SANITIZE_SPECIAL_CHARS);

		// check if the user exist in the database
		$check_username = "SELECT id, password, melh, verified FROM user_registery WHERE username=?";
		$result_check_username = query($check_username, $username);
		if (!isset($result_check_username[0]["id"]))
		{	
			same_page_error("login.php", "Log In", "Something went wrong!\nPlease repeat, or contact ADMIN!");
		}
		// get user id
		$user_id = $result_check_username[0]["id"];

		// get the salt and password of the username
		$db_password = $result_check_username[0]["password"];
		$db_salt = $result_check_username[0]["melh"];

		$verified = $result_check_username[0]["verified"];

		// concatinate the password with the saved salt
		$password_and_salt = $password . $db_salt;
		

		// verify the password with the hashed password
		if (password_verify($password_and_salt, $db_password) === true)
		{	
			// save redirection to the wanted resource 
			// check if the user has verified his email
			if ($verified == 0)
			{
				render("after_signup.php", ["title" => "Welcome", "username" => $username]);
				exit;
			}

			$_SESSION["connected_user"] = $user_id;
			redirect("./welcome.php");

		}
		else
		{
			same_page_error("login.php", "Log In", "Something went wrong!\nPlease repeat, or contact ADMIN!");	
		}
	}
?>