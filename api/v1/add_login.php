<?php
	$login_data = json_decode($_POST["login_data"], true);
	//$login_data = array("user_ID" => 1, "email" => "adam@blakey.family", "password" => "password123");

	if (count($login_data) >= 1)
	{
		include("../../includes/db_connect.php");
		$db_connection = db_connect();

		$JSON_response->status = "success";

		$user_ID  = strip_tags($login_data["user_ID"]);
		$email    = strip_tags($login_data["email"]);
		$password = password_hash($login_data["password"], PASSWORD_DEFAULT);

		$select_query = $db_connection->query("SELECT * FROM `logins` WHERE `ID` = ".$user_ID." LIMIT 1");

		if ($select_query->num_rows == 1)
		{
			$update_query = $db_connection->query("UPDATE `logins` SET `email`='".$email."', `password`='".$password."' WHERE `ID` = '".$user_ID."'");

			if (!$update_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to update the database with user_ID=".$user_ID."; ".$db_connection->error;
			}
		}
		elseif ($select_query->num_rows == 0)
		{
			$insert_query = $db_connection->query("INSERT INTO `logins` (`ID`, `email`, `password`) VALUES ('".$user_ID."', '".$email."', '".$password."')");

			if (!$insert_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to insert to the database with user_ID=".$user_ID."; ".$db_connection->error;
			}
		}
		else
		{
			$JSON_response->status        = "error";	
			$JSON_response->error_message = "failed to select logins: ".$db_connection->error;
		}
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "no data provided";
	}

	echo json_encode($JSON_response);
?>