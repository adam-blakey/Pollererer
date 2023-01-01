<?php
	$term_date_ID = htmlspecialchars($_POST["term_date_ID"]);
	$session_ID   = htmlspecialchars($_POST["session_ID"]);

	$JSON_response = new stdClass();

	include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
	$db_connection = db_connect();

	$session_query = $db_connection->query("SELECT `logins_sessions`.`member_ID`, `members`.`user_level` FROM `logins_sessions` INNER JOIN `members` ON `logins_sessions`.`member_ID`=`members`.`ID` WHERE `logins_sessions`.`ID`='".$session_ID."'");

	if ($session_query)
	{      
		$user_level = $session_query->fetch_assoc()["user_level"];

		if ($user_level >= 1)
		{
			$term_date_query = $db_connection->query("DELETE FROM `term_dates` WHERE `ID`='".$term_date_ID."'");

			if ($term_date_query)
			{
				$JSON_response->status = "success";
      }
			else
			{
				$JSON_response->status        = "error";
				$JSON_response->error_message = "could not delete term date with ID ".$term_date_ID.": ".$db_connection->error;
			}
		}
		else
		{
			$JSON_response->status        = "error";
			$JSON_response->error_message = "you do not have permission to edit term dates";
		}
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "invalid session_ID; either login is invalid or you do not have permission to edit term dates";
	}

	db_disconnect($db_connection);

	echo json_encode($JSON_response);
?>