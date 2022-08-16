<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_terms extends db
{
  protected string $safe_name;
  protected string $name;
  protected string $image;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "terms");
  }

}

?>