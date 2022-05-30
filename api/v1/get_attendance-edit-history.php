<?php
	$member_ID    = htmlspecialchars($_POST["member_ID"]);
	$ensemble_ID  = htmlspecialchars($_POST["ensemble_ID"]);
	$term_ID      = htmlspecialchars($_POST["term_ID"]);

	$JSON_response = new stdClass();

	if ($member_ID != '0')
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$member_query = $db_connection->query("SELECT `first_name`, `last_name` FROM `members` WHERE `ID`='".$member_ID."' LIMIT 1");

		$member_details = $member_query->fetch_assoc();
		$member_name    = $member_details["first_name"]." ".$member_details["last_name"];

		if ($member_query->num_rows == 1)
		{
			$edit_query = $db_connection->query("SELECT `status`, `edit_datetime`, CONCAT(`members`.`first_name`, ' ', `members`.`last_name`) AS `edit_member_name`, `term_dates`.`datetime` AS `rehearsal_datetime` FROM `attendance` LEFT JOIN `members` ON `attendance`.`edit_member_ID`=`members`.`ID` LEFT JOIN `term_dates` ON `attendance`.`term_dates_ID`=`term_dates`.`ID` WHERE `member_ID`='".$member_ID."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_ID`='".$term_ID."' ORDER BY `edit_datetime` DESC");

			if ($edit_query->num_rows >= 1)
			{
				$edit_history = [];

				for ($i=0; $i < $edit_query->num_rows; $i++)
				{ 
					$edit = $edit_query->fetch_assoc();

					$rehearsal_date = new DateTime();
					$rehearsal_date ->setTimestamp($edit["rehearsal_datetime"]);
                    $rehearsal_date ->setTimeZone(new DateTimeZone('Europe/London'));

					$edit_history[$i] = array
					(
						findTimeAgo($edit["edit_datetime"]),
						$rehearsal_date->format('jS F'),
						($edit["status"]==1)?"Attending":"Not attending",
						$edit["edit_member_name"]
					);
				}

				$JSON_response->status       = "success";
				$JSON_response->edit_history = $edit_history;
				$JSON_response->member_name  = $member_name;
			}
			else if ($edit_query->num_rows == 0) 
			{
				$JSON_response->status       = "no_results";
				$JSON_response->edit_history = "Never updated.";
				$JSON_response->member_name  = $member_name;
			}
			else
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to select edits: "."SELECT `status`, `edit_datetime`, CONCAT(`members`.`first_name`, ' ', `members`.`last_name`) AS `edit_member_name`, `term_dates`.`datetime` AS `rehearsal_datetime` FROM `attendance` LEFT JOIN `members` ON `attendance`.`edit_member_ID`=`members`.`ID` LEFT JOIN `term_dates` ON `attendance`.`term_dates_ID`=`term_dates`.`ID` WHERE `member_ID`='".$member['ID']."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_ID`='".$term_ID."' ORDER BY `edit_datetime` DESC";
			}
		}
		else
		{
			$JSON_response->status        = "error";
			$JSON_response->error_message = "no such member with ID=".$member_ID;
		}		
	}
	else
	{
		$JSON_response->status        = "error";
		$JSON_response->error_message = "no data provided";
	}

	echo json_encode($JSON_response);
?>