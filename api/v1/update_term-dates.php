<?php
	$term_dates_data = json_decode($_POST["term_dates_data"], true);
	$term_ID         = htmlspecialchars($_POST["term_ID"]);
	$session_ID      = htmlspecialchars($_POST["session_ID"]);

	$JSON_response = new stdClass();

	if (count($term_dates_data) >= 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$session_query = $db_connection->query("SELECT `logins_sessions`.`member_ID`, `members`.`user_level` FROM `logins_sessions` INNER JOIN `members` ON `logins_sessions`.`member_ID`=`members`.`ID` WHERE `logins_sessions`.`ID`='".$session_ID."'");

		if ($session_query)
		{      
			// $edit_member_ID = $session_query->fetch_array()[0];

			// foreach ($attendance_data as $data)
			// {
			// 	$member_ID     = strip_tags($data["member_ID"]);
			// 	$ensemble_ID   = strip_tags($data["ensemble_ID"]);
			// 	$term_dates_ID = strip_tags($data["term_dates_ID"]);
			// 	$status        = ($data["status"])?1:0;
			// 	$IP            = $_SERVER['REMOTE_ADDR'];

			// 	$insert_query = $db_connection->query("INSERT INTO `attendance` (`member_ID`, `edit_datetime`, `edit_member_ID`, `term_dates_ID`, `ensemble_ID`, `IP`, `status`) VALUES ('".$member_ID."', '".time()."', '".$edit_member_ID."', '".$term_dates_ID."', '".$ensemble_ID."', '".$IP."', '".$status."')");

			// 	if (!$insert_query)
			// 	{
			// 		$JSON_response->status        = "error";	
			// 		$JSON_response->error_message = "failed to insert into database with member_ID=".$member_ID.", ensemble_ID=".$ensemble_ID.", term_dates_ID=".$term_dates_ID.", status=".$status."; ".$db_connection->error;

			// 		break;
			// 	}
			// }

			$JSON_response->status        = "error";
			$JSON_response->error_message = "";

			// Maps the IDs numbered from the term-dates.php page to the globally inserted IDs, once they've been inserted.
			$inserted_new_ids = array();

			foreach ($term_dates_data as $data)
			{
				$id 	 = strip_tags($data["id"]);
				$type  = strip_tags($data["type"]);
				$value = strip_tags($data["value"]);

				// A new ID and hasn't already been just inserted.
				if (substr($id, 0, 3) == "new" && !array_key_exists(substr($id, 3), $inserted_new_ids))
				{
					$new_id = substr($id, 3);

					$JSON_response->error_message .= $new_id . " ";

					// TODO: INSERT NEW RECORDS BELOW.
					if ($type == "date")
					{
						// $date_query = $db_connection->query("INSERT INTO `term_dates` (`term_ID`, `date`) VALUES ('".$term_ID."', '".$value."')");
					}
					else if ($type == "start-time")
					{
						
					}
					else if ($type == "end-time")
					{
						
					}
					else if ($type == "featured")
					{
						
					}
					else if ($type == "deleted")
					{
						
					}

					$inserted_new_ids[$new_id] = "some brilliant new id";
				}
				// Existing record; will update.
				else
				{
					// TODO: UPDATE EXISTING RECORDS BELOW.
					if ($type == "date")
					{
						
					}
					else if ($type == "start-time")
					{
						
					}
					else if ($type == "end-time")
					{
						
					}
					else if ($type == "featured")
					{
						
					}
					else if ($type == "deleted")
					{
						
					}
				}
			}
		}
		else
		{
			$JSON_response->status        = "error";
			$JSON_response->error_message = "invalid session_ID; either login is invalid or you do not have permission to edit term dates";
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