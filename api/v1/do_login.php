<?php
	$email    = strip_tags($_POST["email"]);
	$password = strip_tags($_POST["password"]);

	if (!isset($_POST["email"]))
	{
		$JSON_response->status        = "error";	
		$JSON_response->error_message = "missing email address.";
	}
	elseif (!isset($_POST["password"]))
	{
		$JSON_response->status        = "error";	
		$JSON_response->error_message = "missing password.";
	}
	else
	{
		include("../../includes/db_connect.php");
		$db_connection = db_connect();

		$select_query = $db_connection->query("SELECT `password`, `ID` FROM `logins` WHERE `email` = '".$email."' LIMIT 1");

		if ($select_query->num_rows == 1)
		{
			$select_result = $select_query->fetch_all()[0];
			$db_password   = $select_result[0];
			$ID            = $select_result[1];
			$IP            = $_SERVER['HTTP_X_FORWARDED_FOR'];

			if(password_verify($password, $db_password))
			{
				$session_ID = $db_connection->query("SELECT UUID()")->fetch_all()[0][0];
				$created_session = $db_connection->query("INSERT INTO `logins_sessions` (`ID`, `member_ID`, `start`, `expiry`, `IP`) VALUES ('".$session_ID."', '".$ID."', CURRENT_TIMESTAMP(), DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 7 DAY), '".$IP."')");

				$expiry_date = new DateTime('+1 week');

				$JSON_response->status     = "success";
				$JSON_response->session_ID = $session_ID;
				$JSON_response->expiry     = $expiry_date->format(DateTime::COOKIE);

			}
			else
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "incorrect password.";		
			}

		}
		elseif ($select_query->num_rows == 0)
		{
			$JSON_response->status        = "error";	
			$JSON_response->error_message = "unknown email address.";
		}
		else
		{
			$JSON_response->status        = "error";	
			$JSON_response->error_message = "failed to select logins: ".$db_connection->error;
		}
	}

	echo json_encode($JSON_response);
?>