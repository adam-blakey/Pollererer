<?php
	/**
	 * Member datastore.
	 *
	 * Stores, loads, and saves member data to database.
	 *
	 * @author     Adam Blakey
	 * @copyright  2022 Adam Blakey
	 * @version    Release: @package_version@
	 * @since      Class available since Release 1.1.0
	 */ 
	class member
	{
		/**
	     * The data fields for a member record.
	     */
		private int     $ID;
		private ?string $first_name;
		private ?string $last_name;
		private ?string $instrument;
		private ?int    $row;
		private ?int    $seat;
		private ?int    $user_level;
		private ?int    $image;

		/**
	     * Database management variables.
	     */
		private      $db_connection;
		private bool $data_changed;

		/**
		 * Constructor.
		 *
		 * @param integer $ID            ID of this user.
		 * @param mysqli  $db_connection Database connection.
		 * @param load    $load          Should the data automatically be loaded from the database?
		 * 
		 * @author Adam Blakey
		 * @return boolean Result of load function (if called).
		 */ 
		public function __construct($ID, &$db_connection, $load = false)
		{
			$this->ID           = $ID;
			$this->data_changed = false;
			
			if ($load)
			{
				$this->load();
			}
		}

		/**
		 * Destructor.
		 *
		 * @param boolean $save Should the data be saved to the database?
		 * 
		 * @author Adam Blakey
		 * @return boolean Result of save function (if called).
		 */ 
		public function __destruct($save = false)
		{
			if ($save)
			{
				return $this->save();
			}
		}

		/**
		 * Getter.
		 *
		 * @param mixed $property Name of property to get value of.
		 * 
		 * @author Adam Blakey
		 * @return mixed Value of property, or NULL.
		 */ 
		public function __get($property)
		{
			if (property_exists($this, $property))
			{
				return $this->$property;
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Setter.
		 *
		 * @param mixed $property Name of property to set value of.
		 * 
		 * @author Adam Blakey
		 * @return boolean Successful save or not.
		 */ 
		public function __set($property, $value)
		{
			if (property_exists($this, $property))
			{
				$this->data_changed = true;
				$this->$property    = $value;

				return true;
			}
			else
			{
				return NULL;
			}
		}

		/**
		 * Loads data from database.
		 * 
		 * @author Adam Blakey
		 * @return boolean Whether load was successful or not.
		 */ 
		public function load()
		{


			$this->data_changed = false;

			return false;
		}

		/**
		 * Saves data to database.
		 * 
		 * @author Adam Blakey
		 * @return boolean Whether save was successful or not.
		 */ 
		public function save()
		{
			if ($this->data_changed)
			{

			}

			return false;
		}
	}

	/**
	 * Storage for multiple members.
	 *
	 * Stores multiple member objects.
	 *
	 * @author     Adam Blakey
	 * @copyright  2022 Adam Blakey
	 * @version    Release: @package_version@
	 * @since      Class available since Release 1.1.0
	 */
	class members
	{
		/**
	     * Storage for array of member IDs, and the member objects themselves.
	     */
		private array member_IDs;
		private array member_objects;

		/**
		 * Constructor.
		 *
		 * @param integer $ID            IDs of all users.
		 * @param mysqli  $db_connection Database connection.
		 * @param load    $load          Should the data automatically be loaded from the database?
		 * 
		 * @author Adam Blakey
		 * @return boolean Worst-case result of load function (if called).
		 */ 
		public function __construct(array $IDs, &$db_connection, $load = false)
		{
			$this->member_IDs = $IDs;

			foreach ($this->member_IDs as $member_ID)
			{
				$this->member_objects[$member_ID] = new member($member_ID, $db_connection, $load);
			}
			
			if ($load)
			{
				$this->load();
			}
		}

		/**
		 * Destructor.
		 *
		 * @param boolean $save Should the data be saved to the database?
		 * 
		 * @author Adam Blakey
		 * @return boolean Worst-case result of save function (if called).
		 */ 
		public function __destruct($save = false)
		{
			if ($save)
			{
				$save_result = $this->save_all();
			}
			else
			{
				$save_result = NULL;
			}

			foreach ($this->member_IDs as $member_ID)
			{
				unset($this->member_objects[$member_ID]);
			}

			return $save_result;
		}

		/**
		 * Getter.
		 *
		 * @param mixed $property Name of property to get value of.
		 * 
		 * @author Adam Blakey
		 * @return mixed Value of property, or NULL.
		 */ 
		public function __get($property)
		{
			if (property_exists($this, $property))
			{
				return $this->$property;
			}
		}

		/**
		 * Loads all data from database.
		 * 
		 * @author Adam Blakey
		 * @return boolean Worst-case status of load.
		 */ 
		public function load_all()
		{
			$load_status = true;
			foreach ($this->member_objects as $member)
			{
				if (!$member->load())
				{
					$load_status = false;
				}
			}
		}

		/**
		 * Saves all data to database.
		 * 
		 * @author Adam Blakey
		 * @return boolean Worst-case status of save.
		 */ 
		public function save_all()
		{
			$save_status = true;
			foreach ($this->member_objects as $member)
			{
				if (!$member->save())
				{
					$save_status = false;
				}
			}
		}
	}
?>