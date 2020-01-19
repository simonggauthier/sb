<?php
	
	class User
	{
		public $id;
		public $passwordHash;

		public function __construct ($id, $passwordHash)
		{
			$this->id = $id;
			$this->passwordHash = $passwordHash;
		}

		public static function fromResultSet ($rs)
		{
			return new User($rs['id'], $rs['password_hash']);
		}
	}
