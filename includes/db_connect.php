<?php
	function db_connect()
	{
		require($_SERVER['DOCUMENT_ROOT']."/config.php");
		return new mysqli($config["db_host"], $config["db_username"], $config["db_password"], $config["db_name"]);
	}

	function db_disconnect($db_connection)
	{
		return $db_connection->close();
	}
?>