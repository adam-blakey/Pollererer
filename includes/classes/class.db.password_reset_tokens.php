<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_password_reset_tokens extends db
{
  protected string $token;
  protected int    $expiry;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "password_reset_tokens");
  }

}

?>