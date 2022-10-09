<?php
	$ensemble_ID  = (isset($_GET["ensemble_ID"]))?$_GET["ensemble_ID"]:0;
	$term_date_ID = (isset($_GET["term_date_ID"]))?$_GET["term_date_ID"]:0;

	require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php");

	if ($ensemble_ID == 0 or $term_date_ID == 0)
	{
		output_missing_page();
	}
	else
	{

		if (login_valid())
		{
			$db_connection = db_connect();

			$email_query = $db_connection->query("SELECT `message_content` FROM `pre-rehearsal-email` WHERE `ensemble_ID`='".$ensemble_ID."' AND `term_date_ID`='".$term_date_ID."'");

			if ($email_query->num_rows == 0)
			{
				echo "No email found.";
			}
			else
			{
				echo base64_decode($email_query->fetch_assoc()["message_content"]);
			}

			db_disconnect($db_connection);
		}
		else
		{
			header("Location: ".$config['base_url']."/login.php?redirect_page=".urlencode($_SERVER['REQUEST_URI'])); 
		}
	}