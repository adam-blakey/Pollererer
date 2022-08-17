<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.real.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/class.db.members.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/class.real.members.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/db_connect.php");

$db_connection = db_connect();

//$my_member = new db_members($db_connection, 1);

// echo $my_member->first_name;

// $my_member->first_name = "Kacey";

// echo ($my_member->details_changed)?"Yes":"No";

// echo ($my_member->IDExistsInDatabase())?"Yes":"No";

// $my_member->saveToDatabase();

// echo $my_member->first_name;



// echo $my_member->first_name . "<br>";

// $my_member->first_name = "Kacey";

// echo $my_member->first_name . "<br>";

// $my_member->saveToDatabase();

// echo $my_member->first_name . "<br>";

$list_of_members = new real_members($db_connection, 'getAllMembersFromEnsemble_safeName', "new-ensemble");

foreach ($list_of_members->members as $list_no => $member)
{
  echo "#".$list_no."<br />";
  echo $member->first_name." ".$member->last_name."<br />";
  echo "<br />";
}



db_disconnect($db_connection);

?>