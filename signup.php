<?php
	

	// configuration
	// require("./includes/functions.php");
	require("./includes/config.php");

	// if user reached page via GET	
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{	
		render("signup.php", ["title" => "Sign Up"]);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{	

		// Nothing to check since we can have different users
		// with the same name and/or last_name and/or birthday
		$name = $_POST["name"];
		$last_name = $_POST["last_name"];
	
		// $birthday = DateTime::createFromFormat("Y-M-D", $_POST["birthday"]);
		// debug_to_console($birthday->format("d-m-Y"));
		

		// check the user name
		$username = $_POST["username"];
		$check_username = query("SELECT COUNT(*) as nbr_users FROM user_registery WHERE username=?;", $username);
		if (isset($check_username["nbr_users"]) && $check_username["nbr_users"] != 0)
		{
			// invalid username
			same_page_error("signup.php", "Sign UP", "Invalid User Name!");
		}


		$password = $_POST["password"];
		$confirmation = $_POST["confirmation"];
		// check if the password and confirmation are identical
		if ($password != $confirmation)
		{	
			// password does not confirm
			same_page_error("signup.php", "Sign UP", "Password and Confrimation does not match!");
		}

		// check the length of the password
		if (!$password || mb_strlen($password) < 8)
		{
			// invalid password
			same_page_error("signup.php", "Sign UP", "Password must contain 8+ characters!");
		}
		
		// generate a salt for the password
		$salt = generate_salt();

		// PASSWORD_DEFAULT specifies the bcrypt algorithm
		// cost array key specifies the work factor
		// Hash the password with the salt
		$password_and_salt = trim($password) . $salt;
		$hashed_password = password_hash($password_and_salt, PASSWORD_DEFAULT, ['cost' => 12]);
		

		// Retrieve domain, top level domain, and local part of the email
		$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
		if ($email === false)
		{
			// the email was intered in a wrong manner
			same_page_error("signup.php", "Sign UP", "Invalid Email!");
		}
		$two_parts = explode("@", $email);
		$local = $two_parts[0];
		$domains = $two_parts[1];

		$two_parts = explode(".", $domains);
		$domain = $two_parts[0];
		$tld = $two_parts[1];


		// check if the email is not used
		$email_query = 'SELECT COUNT(*) AS nbr_emails FROM email e INNER JOIN email_domains d ON e.domain_id=d.id where e.local_part=? AND d.domain_name=? AND d.domain_tld=?;';
		$check_email = query($email_query, $local, $domain, $tld)[0];

		if ($check_email["nbr_emails"] != 0)
		{
			// invalid email
			same_page_error("signup.php", "Sign UP", "Invalid Email!");
		}

		// start a transaction here, since we are going to insert a block
		query("START TRANSACTION;");

		// check the email domains
		$query_email_domains = "SELECT id FROM email_domains WHERE domain_name=? AND domain_tld=?;";
		$check_email_domains = query($query_email_domains, $domain, $tld);

		something_went_wrong("signup.php", $check_email_domains);

		// found a match
		if (isset($check_email_domains))
		{	
			// get email domain id
			$email_domain_id = $check_email_domains[0]["id"];
		}
		else
		{	
			// insert the new email domain
			$query_insert_email_domains = "INSERT INTO email_domains (domain_name, domain_tld) VALUES (?, ?);";
			query($query_insert_email_domains, $domain, $tld);
			// get email domain id
			$email_domain_id = query("SELECT LAST_INSERT_ID() AS id")[0]["id"];
			something_went_wrong("signup.php", $email_domain_id);
		}

		// insert the local part
		$query_insert_email = "INSERT INTO email (local_part, domain_id) VALUES(?, ?);";
		$inserted_email = query($query_insert_email, $local, $email_domain_id);
		something_went_wrong("signup.php", $inserted_email);

		// get email id
		$email_id = query("SELECT LAST_INSERT_ID() AS id")[0]["id"];
		something_went_wrong("signup.php", $email_id);

		// insert the new user to the database		
		$insert_query = "INSERT INTO user_registery (name, last_name, username, password, melh, email) VALUES (?, ?, ?, ?, ?, ?);";

		$inserted_user = query($insert_query, $name, $last_name, $username, $hashed_password, $salt, $email_id);
		something_went_wrong("signup.php", $inserted_user);

		// send email confirmation
		$sent_mail = send_verification_email($email, $salt, query("SELECT LAST_INSERT_ID() AS id")[0]["id"]);
		something_went_wrong("signup.php", $sent_mail);


		// commit the transaction
		$after_insertion = query("COMMIT;");
		something_went_wrong("signup.php", $after_insertion);

		// should add something to the user!
		render("after_signup.php", ["title" => "Welcome", "username" => $username]);
		exit;
	}
?>