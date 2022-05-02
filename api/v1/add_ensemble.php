<?php
	$ensemble_data = json_decode($_POST["ensemble_data"], true);

	if (count($ensemble_data) >= 1)
	{
		include($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php");
		$db_connection = db_connect();

		$JSON_response->status = "success";

		foreach ($ensemble_data as $data)
		{
			$ensemble_name = strip_tags($data["ensemble_name"]);

			$insert_query = $db_connection->query("INSERT INTO `ensemble` (`name`) VALUES ('".$ensemble_name."')");

			if (!$insert_query)
			{
				$JSON_response->status        = "error";	
				$JSON_response->error_message = "failed to insert into database with ensemble_name=".$ensemble_name."; ".$db_connection->error;

				break;
			}
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