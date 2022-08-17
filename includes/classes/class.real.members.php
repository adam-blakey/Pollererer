<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.real.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/abstract.class.db.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/classes/class.db.members.php");

class real_members extends real
{
  protected array $members;

  protected mysqli $db_connection;

  public function __construct($db_connection, $control, ...$variables)
  {
    parent::__construct($db_connection);

    switch ($control) {
      case 'getAllMembers':
        $this->getAllMembers();
        break;

      case 'getAllMembersFromEnsemble_ID':
        $this->getAllMembersFromEnsemble_ID($variables[0]);
        break;

      case 'getAllMembersFromEnsemble_safeName':
        $this->getAllMembersFromEnsemble_safeName($variables[0]);
        break;
      
      default:
        die('Invalid control string: '.$control);
        break;
    }

    $this->executeQuery();
    $this->storeResult();
  }

  private function storeResult()
  {
    if ($this->result)
    {
      $this->members = [];
      foreach ($this->result_assoc as $member)
      {
        $this->members[] = new db_members($this->db_connection, $member["ID"]);
      }

      foreach ($this->members as $member)
      {
        $member->loadFromDatabase();
      }

      return true;
    }
    else
    {
      return false;
    }
  }

  private function getAllMembers()
  {
    $this->sql        = "SELECT ID FROM `members`";
    $this->parameters = [];
  }

  private function getAllMembersFromEnsemble_ID($ensemble_ID)
  {
    $this->sql        = "SELECT `members`.* FROM `members` INNER JOIN `members-ensembles` ON `members`.`ID`=`members-ensembles`.`member_ID` WHERE `members-ensembles`.`ensemble_ID` = ?";
    $this->parameters = [$ensemble_ID];
  }

  private function getAllMembersFromEnsemble_safeName($ensemble_safe_name)
  {
    $this->sql        = "SELECT ID FROM `ensembles` WHERE `safe_name` = ? LIMIT 1";
    $this->parameters = [$ensemble_safe_name];

    $this->executeQuery();

    $this->getAllMembersFromEnsemble_ID($this->result_assoc[0]["ID"]);
  }

  private function getAllMembersFromEnsemble_name($ensemble_name)
  {

  }

}

?>