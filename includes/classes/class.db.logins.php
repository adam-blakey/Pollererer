<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_logins extends db
{
  protected string $email;
  protected string $password;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "logins");
  }

}

?>