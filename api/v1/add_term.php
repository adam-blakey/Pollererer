<?php
	$term_data = json_decode($_POST["term_data"], true);

	if (count($term_data) >= 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$JSON_response->status = "success";

		foreach ($term_data as $data)
		{
			$term_name = strip_tags($data["term_name"]);

			$insert_query = $db_connection->query("INSERT INTO `terms` (`name`) VALUES ('".$term_name."')");

			if (!$insert_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to insert into database with term_name=".$term_name."; ".$db_connection->error;

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