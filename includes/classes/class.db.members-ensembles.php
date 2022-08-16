<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");

class db_members_ensembles extends db
{
  protected int $member_ID;
  protected int $ensemble_ID;

  public function __construct($db_connection, $ID = 0)
  {
    parent::__construct($db_connection, $ID, "members-ensembles");
  }

}

?>