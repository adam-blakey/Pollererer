<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_term_dates extends db
{
  protected string $datetime;
  protected string $datetime_end;
  protected bool   $is_featured;
  protected int    $term_ID;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "term_dates");
  }

}

?>