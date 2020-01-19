<?php

    class Transaction
    {
        public $id;
        public $bookId;
        public $direction;
        public $title;
        public $categoryId;
        public $amount;
        public $creationDate;

        public function __construct ($id, $bookId, $direction, $title, $categoryId, $amount, $creationDate)
        {
            $this->id = $id;
            $this->bookId = $bookId;
            $this->direction = $direction;
            $this->title = $title;
            $this->categoryId = $categoryId;
            $this->amount = $amount;
            $this->creationDate = $creationDate;
        }

		public function toCreateArray ()
		{
			return [$this->bookId, $this->direction, $this->title, $this->categoryId, $this->amount, $this->creationDate];
        }
        
        public function toUpdateArray ()
		{
			return [$this->direction, $this->title, $this->categoryId, $this->amount, $this->creationDate, $this->id];
		}

        public function toMap ()
        {
            return ['id' => $this->id, 'title' => $this->title, 'categoryId' => $this->categoryId, 'amount' => $this->formatAmount(), 'date' => $this->creationDate, 'direction' => $this->direction];
        }

        public function formatAmount () 
        {
            $a = '' . $this->amount;

            return substr($a, 0, strlen($a) - 2) . '.' . substr($a, strlen($a) - 2);
        }

        public static function fromResultSet ($rs)
        {
            return new Transaction($rs['id'], $rs['book_id'], $rs['direction'], $rs['title'], $rs['category_id'], $rs['amount'], strtotime($rs['creation_date']) * 1000);
        }
    }
