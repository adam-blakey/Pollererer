<?php
	function db_connect()
	{
		//return new mysqli("192.168.1.241:3306", "attendance", "NCkf@ud1@V(a[E9A", "attendance");
		return new mysqli("localhost", "les99cou_pollererer", "NCkf@ud1@V(a[E9A", "les99cou_pollererer");
	}

	function db_disconnect($db_connection)
	{
		return $db_connection->close();
	}
?>