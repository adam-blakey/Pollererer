<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_attendance extends db
{
  protected int    $member_ID;
  protected string $edit_datetime;
  protected int    $edit_member_ID;
  protected int    $term_dates_ID;
  protected int    $ensemble_ID;
  protected string $IP;
  protected int    $status;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "attendance");
  }

}

?>