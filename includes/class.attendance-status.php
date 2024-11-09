<?php
class attendanceStatus
{
	public $member_ID;
	public $ensemble_ID;
	public $status;
	public $first_name;
	public $last_name;
	public $instrument;
	public $edit_datetime;

	function __construct($member_ID, $ensemble_ID, $status, $first_name, $last_name, $instrument, $edit_datetime)
	{
		$this->member_ID     = $member_ID;
		$this->ensemble_ID   = $ensemble_ID;
		$this->status        = $status;
		$this->first_name    = $first_name;
		$this->last_name     = $last_name;
		$this->instrument    = $instrument;
		$this->edit_datetime = $edit_datetime;
	}
}
