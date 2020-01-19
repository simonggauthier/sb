<?php

	class Book
	{
		public $id;
		public $name;
		public $ownerId;

		public function __construct ($id, $name, $ownerId)
		{
			$this->id = $id;
			$this->name = $name;
			$this->ownerId = $ownerId;
		}

		public function toCreateArray ()
		{
			return [$this->name, $this->ownerId];
		}

		public static function fromResultSet ($rs)
		{
			return new Book($rs['id'], $rs['name'], $rs['owner_id']);
		}
	}
