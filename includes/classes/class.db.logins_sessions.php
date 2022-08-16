<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_logins_sessions extends db
{
  protected int    $member_ID;
  protected string $start;
  protected string $expiry;
  protected string $IP;
  protected int    $ended;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "logins_sessions");
  }

}

?>