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
			$JSON_response->status        = "success";

			foreach ($term_dates_data as $data)
			{
				$id 	      = strip_tags($data["id"]);
				$date       = strip_tags($data["date"]);
				$start_time = strip_tags($data["start-time"]);
				$end_time   = strip_tags($data["end-time"]);
				$featured   = strip_tags($data["featured"]);
				$deleted    = strip_tags($data["hidden"]);

				$start_datetime = strtotime($date."T".$start_time.":00");
				$end_datetime   = strtotime($date."T".$end_time.":00");

				// A new ID and hasn't already been just inserted.
				if (substr($id, 0, 3) == "new")
				{
					$term_dates_query = $db_connection->query("INSERT INTO `term_dates` (`term_ID`, `datetime`, `datetime_end`, `is_featured`, `deleted`) VALUES ('".$term_ID."', '".$start_datetime."', '".$end_datetime."', '".$featured."', '".$deleted."')");
				}
				// Existing record; will update.
				else
				{
					$term_dates_query = $db_connection->query("UPDATE `term_dates` SET `datetime`='".$start_datetime."', `datetime_end`='".$end_datetime."', `is_featured`='".$featured."', `deleted`='".$deleted."' WHERE `ID`='".$id."'");
				}

				if (!$term_dates_query)
				{
					$JSON_response->status        = "error";
					$JSON_response->error_message = "failed to insert into database with term_ID=".$term_ID.", start_datetime=".$start_datetime.", end_datetime=".$end_datetime.", featured=".$featured.", deleted=".$deleted."; ".$db_connection->error;

					break;
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