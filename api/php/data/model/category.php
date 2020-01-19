<?php

	class Category
	{
		public $id;
		public $bookId;
		public $name;
		public $color;

		public function __construct ($id, $bookId, $name, $color)
		{
			$this->id = $id;
			$this->bookId = $bookId;
			$this->name = $name;
			$this->color = $color;
		}

		public function toCreateArray ()
		{
			return [$this->bookId, $this->name, $this->color];
		}

		public function toUpdateArray ()
		{
			return [$this->name, $this->color, $this->id];
		}

		public function toMap ()
		{
			return ['id' => $this->id, 'name' => $this->name, 'color' => $this->color];
		}

		public static function fromResultSet ($rs)
		{
			return new Category($rs['id'], $rs['book_id'], $rs['name'], $rs['color']);
		}
	}
