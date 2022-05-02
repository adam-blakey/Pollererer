<?php
	$member_data = json_decode($_POST["member_data"], true);

	if (count($member_data) >= 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$JSON_response->status = "success";

		foreach ($member_data as $data)
		{
			$first_name  = strip_tags($data["first_name"]);
			$last_name   = strip_tags($data["last_name"]);
			$instrument  = strip_tags($data["instrument"]);
			$user_level  = strip_tags($data["user_level"]);
			$ensemble_ID = strip_tags($data["ensemble_ID"]);

			$insert_query = $db_connection->query("INSERT INTO `members` (`first_name`, `last_name`, `instrument`, `user_level`, `ensemble_ID`) VALUES ('".$first_name."', '".$last_name."', '".$instrument."', '".$user_level."', '".$ensemble_ID."')");

			if (!$insert_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to insert into database with first_name=".$first_name.", last_name=".$last_name.", instrument=".$instrument.", ensemble_ID=".$ensemble_ID."; ".$db_connection->error;

				break;
			}
		}
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "no data provided";
	}

	echo json_encode($JSON_response);
?>