<?php
	
	/**
	 * functions.php
	 *
	 * Essential and helper functions.
	 */
	
	require_once("constants.php");



	/**
	 * Function that will make a database connection, and then 
	 * executes the SQL query if valid.
	 * It will return an array of all rows in result set or 
	 * false on error.
	 */
	function query()
	{
		// SQL Statement
		$sql = func_get_arg(0);

		// Parameters, if any
		$parameters = array_slice(func_get_args(), 1);

		// try to connect to database
		static $handle;
		if (!isset($handle))
		{
			try
			{
				// connect to database
				$handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

				// ensure that PDO::prepare returns false when passed invalid SQL
				$handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch (Exception $e)
			{
				// trigger error and exit
				trigger_error($e->getMessage(), E_USER_ERROR);
				exit;
			}
		}

		// prepare SQL statement
		$statement = $handle->prepare($sql);
		if ($statement === false)
		{
			// trigger error and exit
			trigger_error($handle->errorInfo()[2], E_USER_ERROR);
			exit;
		}

		// execute SQL statement
		$results = $statement->execute($parameters);

		// return results set's rows, if any
		if ($results !== false)
		{
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Function that help debug on the JavaScript console.
	 * It will test if the data is an object or regular variable.
	 */
	function debug_to_console($data)
	{
		if (is_array($data) || is_object($data))
		{
			echo("<script>console.log('PHP console debuging:" . json_decode($data) . "');</script>");
		}
		else
		{
			echo("<script>console.log('PHP console debuging:" . $data . "');</script>");	
		}
	}

	/**
	 * Renders template, passing in values.
	 */
	function render($template, $values = [])
	{
		// if template exists, render it
		if (file_exists("./templates/$template"))
		{
			// extract variables into local scope
			extract($values);

			// render header
            require("./templates/header.php");

            // render template
            require("./templates/$template");

            // render footer
            require("./templates/footer.php");
		}
		else
		{
			trigger_error("Invalid template: $template", E_USER_ERROR);
		}
	}


	/**
	 * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
	 */
	function redirect($destination)
	{
		// handle URL
		if (preg_match("/^https?:\/\//", $destination))
		{
			header("Location: " . $destination);
		}

		// handle abolute path
		else if (preg_match("/^\//", $destination))
		{
			$protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
			$host = $_SERVER["HTTP_HOST"];
			header("Location: $protocol://$host$destination");
		}

		// handle relative path
		{
			$protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
			$host = $_SERVER["HTTP_HOST"];
			$path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
			header("Location: $protocol://$host$path/$destination");
		}

		// exit immediately since we're redirecting anyway
		exit;
	}




	function logout()
	{
		// unset any session variables
		$_SESSION = [];


		// destroy session
		session_destroy();
	}

	function same_page_error($template, $title, $message)
	{
		render($template, ["title" => $title, "message" => $message]);
		exit;
	}

	
	// This function will generate a salt text with the specified length.
	// The length will be by default 20
	function generate_salt($leng = 20)
	{
        $salt = "";
        $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        
        for ($i = 0; $i < $leng; $i++ )
			$salt = $salt . substr($possible, rand(0, strlen($possible)), 1);
		
        return $salt;
	}

	/**
     * Sends email to user, using PHPMailer.
     */
    function send_email($email, $msg)
    {	

    	require("./includes/PHPMailer-master/PHPMailerAutoload.php");

        

    	$mail = new PHPMailer;

    	$mail->isSMTP();
    	$mail->Host = 'smtp-mail.outlook.com';
    	$mail->Port = 587;	
    	$mail->SMTPAuth = true;
    	$mail->Username = 'verify-me1@outlook.com';
    	$mail->Password = 'harder1593';

    	$mail->setFrom('verify-me1@outlook.com', 'Verify');
    	$mail->addAddress($email);
    	$mail->isHTML(true);			

    	$mail->Subject = 'Email Verification';
    	$mail->Body    = '<h1>Thank you for your Registration</h1>' . '<b>' . $msg . '</b>';
    	
    	// if the email was successfuly sent we will redirect to profile
    	if($mail->send())
    	{
    	    return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    function send_verification_email($email, $salt, $id)
    {
    	$msg = "Verify your email by clicking on this link: " . "http://mi-casa/oth/verify.php?pghz=" . $id . "&eadef=" . $salt;
    	return send_email($email, $msg);
    }

    function something_went_wrong($template, $bool)
    {	
    	if ($bool === false)
    	{	
    		query("ROLLBACK;");
    		same_page_error($template, "Error", "Something Went Wrong! Plz Repeat");
    	}
    }

?>