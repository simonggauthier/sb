<?php

class Objective
{
	public $id;
	public $bookId;
	public $categoryId;
	public $amount;

	public function __construct ($id, $bookId, $categoryId, $amount)
	{
		$this->id = $id;
		$this->bookId = $bookId;
		$this->categoryId = $categoryId;
		$this->amount = $amount;
	}

	public function toCreateArray ()
	{
		return [$this->bookId, $this->categoryId, $this->amount];
	}

	public function toUpdateArray ()
	{
		return [$this->amount, $this->id];
	}

	public function toMap ()
	{
		return ['id' => $this->id, 'categoryId' => $this->categoryId, 'amount' => $this->formatAmount()];
	}

	public static function fromResultSet ($rs)
	{
		return new Objective($rs['id'], $rs['book_id'], $rs['category_id'], $rs['amount']);
	}

    public function formatAmount () 
    {
        $a = '' . $this->amount;

        return substr($a, 0, strlen($a) - 2) . '.' . substr($a, strlen($a) - 2);
    }
}
