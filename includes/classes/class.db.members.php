<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_members extends db
{
  protected string $first_name;
  protected string $last_name;
  protected string $instrument;
  protected int    $row;
  protected int    $seat;
  protected int    $user_level;
  protected string $image;

  public function __construct($db_connection, $ID = 0)
  {
    $this->table_name = "members";
    $this->no_table_columns = 7;

    parent::__construct($db_connection, $ID);
  }

}

?>