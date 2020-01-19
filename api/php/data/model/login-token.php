<?php
	
	class LoginToken
	{
		public $id;
		public $ownerId;
		public $device;
		public $lastUseDate;

		public function __construct ($id, $ownerId, $device, $lastUseDate)
		{
			$this->id = $id;
			$this->ownerId = $ownerId;
			$this->device = $device;
			$this->lastUseDate = $lastUseDate;
		}

		public function toCreateArray ()
		{
			return [$this->id, $this->ownerId, $this->device, $this->lastUseDate];
		}

		public function toUpdateArray ()
		{
			return [$this->lastUseDate, $this->id];
		}

		public static function fromResultSet ($rs)
		{
			return new LoginToken($rs['id'], $rs['owner_id'], $rs['device'], $rs['use_date']);
		}
	}
