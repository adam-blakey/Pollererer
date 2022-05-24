<?php
	$login_data = json_decode($_POST["login_data"], true);

	if (count($ensemble_data) == 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$token    = strip_tags($login_data["token"]);
		$password = password_hash($login_data["password"], PASSWORD_DEFAULT);

		$token_query = $db_connection->query("SELECT `member_ID`, `token`, `expiry` FROM `password_reset_tokens` WHERE `token`=".$token." AND `expiry`>CURRENT_TIMESTAMP() LIMIT 1");

		if ($token_query->num_rows == 0)
		{
			$JSON_response->status        = "error";
			$JSON_response->error_message = "invalid token";
		}
		else
		{
			$member_ID = $token_query->get_assoc()["member_ID"];

			$update_query = $db_connection->query("UPDATE `logins` SET `password`='".$password."' WHERE `ID`='".$member_ID."'");

			if (!$update_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to insert into database with ID=".$member_ID."; ".$db_connection->error;
			}
			else
			{
				$JSON_response->status = "success";
			}
		}

		db_disconnect($db_connection);
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "no/too much data provided";
	}

	echo json_encode($JSON_response);
?>