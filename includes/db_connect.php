<?php
	function db_connect()
	{
		//return new mysqli("192.168.1.241:3306", "attendance", "NCkf@ud1@V(a[E9A", "attendance");
		return new mysqli("localhost", "eefbadcc_attendance", "!J1AH33IKykb", "eefbadcc_attendance");
	}

	function db_disconnect($db_connection)
	{
		return $db_connection->close();
	}
?>