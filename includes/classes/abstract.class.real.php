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

use function PHPSTORM_META\type;

/**
 * Class AbstractClassReal
 * 
 * Abstract class for "real" data, i.e. things that exist. Children should represent
 * a different "real" thing, such as a member.
 *
 * @package   Pollererer
 * @author    Adam Blakey <adam@blakey.family>
 * @copyright 2022 Adam Blakey
 * @license   AGPL-3.0
 */
abstract class real
{
  /**
   * @var mysqli $db_connection The already connected dataase connection.
   */
  protected mysqli $db_connection;
  /**
   * @var string $sql SQL statement to execute.
   */
  protected string $sql;
  /**
   * @var array $parameters Parameters for corresponding SQL statement.
   */
  protected array $parameters;
  /**
   * @var mysqli_result $result Stores result of last performed query.
   */
  protected mysqli_result $result;
  /**
   * @var array $result_assoc Associative array of last performed query.
   */
  protected array $result_assoc;

  /****************************************************************************/

  /**
   * Constructor
   * 
   * @param mysqli  $db_connection The database connection to use.
   * 
   * @return void
   */
  public function __construct($db_connection)
  {
    $this->db_connection = $db_connection;
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
    }
    else
    {
      return false;
    }
  }

  /**
   * ExecuteQuery
   * 
   * @return ??
   */
  public function executeQuery()
  {
    if ($this->db_connection->ping())
    {    
      $statement = $this->db_connection->prepare($this->sql);
      if ($statement)
      {
        if (count($this->parameters) > 0)
        {
          $bind_param_types = "";
          foreach($this->parameters as $parameter)
          {
            $type = (is_int($parameter))?"i":"s";

            $bind_param_types .= $type;
          }

          $statement->bind_param($bind_param_types, ...$this->parameters);
        }

        $statement ->execute();
        
        $this->result = $statement->get_result();

        $this->result_assoc = [];
        while($row = $this->result->fetch_assoc())
        {
          $this->result_assoc[] = $row; 
        }

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
   * NameOfClass
   * 
   * @return string The name of the class.
   */
  public function nameOfClass()
  {
    return get_class($this);
  }
}

?>