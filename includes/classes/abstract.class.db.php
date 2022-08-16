<?php
/**
 * @copyright Copyright (c) 2022, Adam Blakey
 *
 * @author Adam Blakey <adam@blakey.family>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

/**
 * Class AbstractClassDb
 * 
 * Abstract class for database-held data. Children should represent the structure
 * of each table in the database. Assumes that each table has a primary key of ID.
 *
 * @package   Pollererer
 * @author    Adam Blakey <adam@blakey.family>
 * @copyright 2022 Adam Blakey
 * @license   AGPL-3.0
 */
abstract class db
{
  /**
   * @var integer $ID We assume that every table has a primary key of ID.
   */
  protected int    $ID;
  /**
   * @var mysqli $db_connection The already connected dataase connection.
   */
  protected mysqli $db_connection;
  /**
   * @var string $table_name The name of the table in the database (defined
   * in the child class).
   */
  protected string $table_name;
  /**
   * @var bool $details_changed Flag to keep track of when the details have
   * fallen out-of-sync with those stored in the database.
   */
  protected bool   $details_changed = false;
  /**
   * @var bool $loaded_from_database Flag to keep track of when the object has
   * been loaded from the database.
   */
  protected bool   $loaded_from_database = false;
  /**
   * @var int $no_table_columns Stores the number of columns in the table
   * (equivalently, the number of properties in the child class).
   */
  protected int    $no_table_columns;
  /**
   * @var array $table_columns Stores the names of the properties in the child
   * class.
   */
  protected array  $table_columns;
  /**
   * @var array $abstract_properties Stores the names of the properties that
   * only exist in this abstract class.
   */
  protected array  $abstract_properties;

  /****************************************************************************/

  /**
   * Constructor
   * 
   * @param mysqli  $db_connection The database connection to use.
   * @param integer $ID            The ID of the object to load (0 if doesn't
   *                               exist in database yet).
   * @param string  $table_name    The name of the table to use.
   * 
   * @return void
   */
  public function __construct($db_connection, $ID, $table_name)
  {
    $this->ID            = $ID;
    $this->db_connection = $db_connection;
    $this->table_name    = $table_name;

    $this->loadChildColumnNames();
    $this->loadAbstractColumnNames();

    if ($ID > 0)
    {      
      $this->loadFromDatabase();
    }
  }

  /**
   * Get
   * 
   * @param string $property The property to get.
   * 
   * @return mixed The value of the asked property.
   */
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

  /**
   * Set
   * 
   * @param string $property The property to get.
   * @param mixed  $value    The value to set.
   * 
   * @return bool True if the property was successfully set, 
   *              False otherwise.
   */
  public function __set($property, $value)
  {
    if (property_exists($this, $property) && !in_array($property, $this->abstract_properties))
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

  /**
   * IDExistsInDatabase
   * 
   * @return bool True if a record with specified ID exists in the database,
   *              False otherwise.
   */
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

  /**
   * LoadFromDatabase
   * 
   * Load all columns from the database into the local object.
   * 
   * @return bool True if the object was successfully loaded from the database,
   *              False otherwise.
   */
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

        foreach ($this->table_columns as $column => $type)
        {
          $this->$column = $member[$column];
        }
        
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

  /**
   * SaveToDatabase
   * 
   * Save all properties to their columns in the database.
   * 
   * @return bool True if the object was successfully saved to the database,
   *              False otherwise.
   */
  public function saveToDatabase()
  {
    if(!$this->IDExistsInDatabase())
    {
      $sql = "INSERT INTO `".$this->table_name."` " .
        "(`".implode("`, `", $this->table_columns)."`) " .
        "VALUES ".
        "(".implode(", ", array_fill(0, $this->no_table_columns, "?")).")";

      $statement = $this->db_connection->prepare($sql);
      
      $bind_param_types = "";
      foreach($this->table_columns as $column => $type)
      {
        $bind_param_types .= substr($type, 0, 1);
      }

      $statement ->bind_param($bind_param_types, ...$this->getValuesArray());
      $statement ->execute();

      if ($statement->affected_rows == 1)
      {
        $this->ID = $this->db_connection->insert_id;
      }
      else
      {
        //die("Can't create record with ID=".$this->ID." in database table `".$this->table_name."`: ".$this->db_connection->error);
        return false;
      }

      $statement->close();
    }
    else
    {
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
    }

    $this->loadFromDatabase();

    return true;
  }

  /** 
   * NameOfClass
   * 
   * @return string The name of the class.
   */
  public function nameOfClass()
  {
    return get_class($this);
  }

  /**
   * LoadChildColumnNames
   * 
   * Loads all the columns of the child into the local variable
   *  `table_columns`.
   * 
   * @return bool True if the child column names were successfully loaded.
   */
  private function loadChildColumnNames()
  {
    $derived_class = new ReflectionClass($this->nameOfClass());

    $table_columns = array_filter($derived_class->getProperties(), function($p) { return !($p->class == get_parent_class($this)); });

    $this->table_columns = array();
    foreach ($table_columns as $column)
    {
      $this->table_columns[$column->name] = $column->getType()->getName();
    }
    $this->table_columns["ID"] = "int";

    $this->no_table_columns = count($this->table_columns);

    return true;
  }

  /**
   * LoadAbstractColumnNames
   * 
   * Loads all the columns of the parent into the local variable
   *  `abstract_properties`.
   * 
   * @return bool True if the parent properties names were successfully loaded.
   */
  private function loadAbstractColumnNames()
  {
    $derived_class = new ReflectionClass($this->nameOfClass());

    $abstract_properties = array_filter($derived_class->getProperties(), function($p) { return ($p->class == get_parent_class($this)); });

    $this->abstract_properties = array();
    foreach ($abstract_properties as $column)
    {
      $this->abstract_properties[$column->name] = $column->getType()->getName();
    }
    $this->abstract_properties["ID"] = "int";

    $this->no_abstract_properties = count($this->abstract_properties);

    return true;
  }

  /**
   * GetValuesArray
   * 
   * @return array An array of all the values of the object, in order 
   * with the variable `table_columns`.
   */
  private function getValuesArray()
  {
    foreach ($this->table_columns as $column => $type)
    {
      $values[] = $this->$column;
    }

    return $values;
  }
}

?>