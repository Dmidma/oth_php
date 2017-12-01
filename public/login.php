<?php

	// configuration
	require("includes/functions.php");


	// if user reached page via GET
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{	
		render("login.php", ["title" => "Log In"]);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{

	}
?>