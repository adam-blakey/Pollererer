<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_pre_rehearsal_email extends db
{
  protected bool   $been_sent;
  protected int    $ensemble_ID;
  protected int    $term_date_ID;
  protected string $message_content;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "pre-rehearsal-email");
  }

}

?>