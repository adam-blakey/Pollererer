<?php
	$attendance_data = json_decode($_POST["attendance_data"], true);
	$session_ID      = htmlspecialchars($_POST["session_ID"]);

	$JSON_response = new stdClass();

	if (count($attendance_data) >= 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$JSON_response->status = "success";

		$session_query = $db_connection->query("SELECT `member_ID` FROM `logins_sessions` WHERE `ID`='".$session_ID."'");

		if ($session_query)
		{
			$edit_member_ID = $session_query->fetch_array()[0];

			foreach ($attendance_data as $data)
			{
				$member_ID     = strip_tags($data["member_ID"]);
				$ensemble_ID   = strip_tags($data["ensemble_ID"]);
				$term_dates_ID = strip_tags($data["term_dates_ID"]);
				$status        = ($data["status"])?1:0;
				$IP            = $_SERVER['REMOTE_ADDR'];

				$insert_query = $db_connection->query("INSERT INTO `attendance` (`member_ID`, `edit_datetime`, `edit_member_ID`, `term_dates_ID`, `ensemble_ID`, `IP`, `status`) VALUES ('".$member_ID."', '".time()."', '".$edit_member_ID."', '".$term_dates_ID."', '".$ensemble_ID."', '".$IP."', '".$status."')");

				if (!$insert_query)
				{
					$JSON_response->status        = "error";	
					$JSON_response->error_message = "failed to insert into database with member_ID=".$member_ID.", ensemble_ID=".$ensemble_ID.", term_dates_ID=".$term_dates_ID.", status=".$status."; ".$db_connection->error;

					break;
				}
			}
		}
		else
		{
			$JSON_response->status        = "error";
			$JSON_response->error_message = "invalid edit_member_ID";
		}

		db_disconnect($db_connection);
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "no data provided";
	}

	echo json_encode($JSON_response);
?>