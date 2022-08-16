<?php

abstract class db
{
  protected int    $ID;

  protected mysqli $db_connection;
  protected string $table_name;
  protected bool   $details_changed = false;
  protected bool   $loaded_from_database = false;
  protected int    $no_table_columns;
  protected array  $table_columns;

  public function __construct($db_connection, $ID = 0)
  {
    if ($ID > 0)
    {
      $this->ID            = $ID;
      $this->db_connection = $db_connection;
      
      $this->calculate_numberOfColumns();
      $this->loadFromDatabase();
    }
  }

  public function __get($property)
  {
    if (property_exists($this, $property))
    {
      return $this->$property;

      return true;
    }
    else
    {
      return false;
    }
  }

  public function __set($property, $value)
  {

    if (property_exists($this, $property) &&
      ($property != 'db_connection' &&
       $property != 'table_name' &&
       $property != 'ID' &&
       $property != 'details_changed' &&
       $property != 'loaded_from_database')
    )
    {
      $this->$property       = $value;
      $this->details_changed = true;

      return true;
    }
    else
    {
      return false;
    }
  }

  public function IDExistsInDatabase()
  {
    $sql = "SELECT `ID` FROM `".$this->table_name."` WHERE `ID` = ? LIMIT 1";
    
    $statement = $this->db_connection->prepare($sql);
    $statement ->bind_param("i", $this->ID);
    $statement ->execute();
    
    if($statement->get_result())
    {
      return true;
    }
    else
    {
      return false;
    }

    $statement->close();
  }

  public function loadFromDatabase()
  {
    if ($this->ID > 0 && $this->db_connection->ping())
    {
      $sql = "SELECT * FROM `".$this->table_name."` WHERE `ID` = ? LIMIT 1";
    
      $statement = $this->db_connection->prepare($sql);
      if ($statement)
      {
        $statement ->bind_param("i", $this->ID);
        $statement ->execute();
        
        $result   = $statement->get_result();
        $member   = $result->fetch_assoc();
        $statement->close();

        $this->ID         = $member["ID"];
        $this->first_name = $member["first_name"];
        $this->last_name  = $member["last_name"];
        $this->instrument = $member["instrument"];
        $this->row        = $member["row"];
        $this->seat       = $member["seat"];
        $this->user_level = $member["user_level"];
        $this->image      = $member["image"];
        
        $this->loaded_from_database = true;
        $this->details_changed      = false;

        return true;
      }
      else
      {
        die("Error: " . $this->db_connection->error);
      }
    }
    else
    {
      return false;
    }
  }

  public function saveToDatabase()
  {
    if(!$this->IDExistsInDatabase())
    {
      $sql = "INSERT INTO `".$this->table_name."` " .
        "(`first_name`, `last_name`, `instrument`, `row`, `seat`, `user_level`, `image`) " .
        "VALUES ".
        "(? , ? , ? , ? , ? , ? , ?)";

      $statement = $this->db_connection->prepare($sql);
      $statement ->bind_param("sssiiis", $this->first_name, $this->last_name, $this->instrument, $this->row, $this->seat, $this->user_level, $this->image);
      $statement ->execute();

      if ($statement->affected_rows == 1)
      {
        $this->ID = $this->db_connection->insert_id;
      }
      else
      {
        die("Can't create record with ID=".$this->ID." in database table `".$this->table_name."`: ".$this->db_connection->error);
      }

      $statement->close();
    }

    $sql = "UPDATE `".$this->table_name."` SET " .
      "`first_name` = ? , " .
      "`last_name` = ? , " .
      "`instrument` = ? , " .
      "`row` = ? , " .
      "`seat` = ? , " .
      "`user_level` = ? , " .
      "`image` = ? " .
      "WHERE `ID` = ? LIMIT 1";
    
    $statement = $this->db_connection->prepare($sql);
    $statement ->bind_param("sssiiisi", $this->first_name, $this->last_name, $this->instrument, $this->row, $this->seat, $this->user_level, $this->image, $this->ID);
    $statement ->execute();
    $statement ->close();
    
    $this->loadFromDatabase();
  }

  public function nameOfClass()
  {
    return get_class($this);
  }

  private function calculate_numberOfColumns()
  {
    $derived_class = new ReflectionClass($this->nameOfClass());

    $table_columns = array_filter($derived_class->getProperties(), function($p) { return !($p->class == get_parent_class($this)); });

    $this->no_table_columns = count($table_columns);

    $this->table_columns = array();
    foreach ($table_columns as $column)
    {
      $this->table_columns[] = $column->name;
    }

    return true;
  }
}

?>