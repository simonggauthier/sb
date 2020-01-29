<?php

include 'config.php';

include 'model/user.php';
include 'model/login-token.php';
include 'model/book.php';
include 'model/category.php';
include 'model/transaction.php';
include 'model/objective.php';

class Database
{
	private $pdo;

	public function __construct()
	{
		$host = $GLOBALS['DB_HOST'];
		$database = $GLOBALS['DB_DB'];
		$username = $GLOBALS['DB_USERNAME'];
		$password = $GLOBALS['DB_PASSWORD'];

		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];

		$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

		$this->pdo = new PDO($dsn, $username, $password, $options);
	}

	public function createStatement ($query)
	{
		return $this->pdo->prepare($query);
	}

	public function get ($query, $params)
	{
		$stmt = $this->createStatement($query);
		$stmt->execute($params);

		$ret = $stmt->fetch();

		if (!$ret)
		{
			return null;
		}
		
		return $ret;
	}

	public function getList ($query, $params)
	{
		$stmt = $this->createStatement($query);
		$stmt->execute($params);

		return $stmt;
	}

	public function now ()
	{
		$d = new DateTime('now', new DateTimeZone('America/Toronto'));

		return $d->format('Y-m-d H:i:s');
	}

	public function dateFromEpoch ($epoch)
	{
		$d = new DateTime(date("Y-m-d H:i:s", substr($epoch, 0, 10)), new DateTimeZone('America/Toronto'));

		return $d->format('Y-m-d H:i:s');
	}

	/* User */
	public function getUser ($id)
	{
		$rs = $this->get('SELECT * FROM users WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return User::fromResultSet($rs);
	}

	/* Login Token */
	public function getLoginToken ($id)
	{
		$rs = $this->get('SELECT * FROM login_tokens WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return LoginToken::fromResultSet($rs);
	}

	public function createLoginToken ($loginToken)
	{
		$stmt = $this->createStatement('INSERT INTO login_tokens VALUES (?, ?, ?, ?)');
		$stmt->execute($loginToken->toCreateArray());
	}

	public function updateLoginToken ($loginToken)
	{
		$stmt = $this->createStatement('UPDATE login_tokens SET use_date = ? WHERE id = ?');
		$stmt->execute($loginToken->toUpdateArray());
	}

	/* Book */
	public function getBook ($id)
	{
		$rs = $this->get('SELECT * FROM books WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return Book::fromResultSet($rs);
	}

	public function findBook ($name, $ownerId)
	{
		$rs = $this->get('SELECT * FROM books WHERE name like ? and owner_id = ?', [$name, $ownerId]);

		if (!$rs)
		{
			return null;
		}

		return Book::fromResultSet($rs);
	}

	public function getBooksByOwnerId ($ownerId)
	{
		$rs = $this->get('SELECT * FROM books WHERE owner_id = ?', [$ownerId]);

		$ret = [];

		while ($row = $rs->fetch())
		{
			array_push($ret, Book::fromResultSet($row));
		}

		return $ret;		
	}

	public function createBook ($book)
	{
		$stmt = $this->createStatement('INSERT INTO books (name, owner_id) values (?, ?)');
		$stmt->execute($book->toCreateArray());

		$book->id = $this->pdo->lastInsertId();

		return $book->id;
	}

	/* Category */
	public function getAllCategories ($bookId)
	{
		$rs = $this->getList('SELECT * FROM categories where book_id = ?', [$bookId]);
		$ret = [];

		while ($row = $rs->fetch())
		{
			array_push($ret, Category::fromResultSet($row));
		}

		return $ret;
	}

	public function getCategory ($id)
	{
		$rs = $this->get('SELECT * FROM categories WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return Category::fromResultSet($rs);
	}

	public function findCategory ($name, $bookId)
	{
		$rs = $this->get('SELECT * FROM categories WHERE name LIKE ? AND book_id = ?', [$name, $bookId]);

		if (!$rs)
		{
			return null;
		}

		return Category::fromResultSet($rs);
	}		

	public function createCategory ($category)
	{
		$stmt = $this->createStatement('INSERT INTO categories (book_id, name, color) values (?, ?, ?)');
		$stmt->execute($category->toCreateArray());

		$category->id = $this->pdo->lastInsertId();

		return $this->pdo->lastInsertId();
	}

	public function updateCategory ($category)
	{
		$stmt = $this->createStatement('UPDATE categories SET name = ?, color = ? WHERE id = ?');
		$stmt->execute($category->toUpdateArray());
	}

	public function deleteCategory ($id)
	{
		$stmt = $this->createStatement('DELETE FROM categories WHERE id = ?');
		$stmt->execute([$id]);
	}

	/* Transaction */
	public function getAllTransactions ($bookId)
	{
		$rs = $this->getList('SELECT * FROM transactions where book_id = ?', [$bookId]);
		$ret = [];

		while ($row = $rs->fetch())
		{
			array_push($ret, Transaction::fromResultSet($row));
		}

		return $ret;	
	}

	public function getTransaction ($id)
	{
		$rs = $this->get('SELECT * FROM transactions WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return Transaction::fromResultSet($rs);	
	}

	public function getTransactionsByCategory($categoryId)
	{
		$rs = $this->getList('SELECT * FROM transactions where category_id = ?', [$categoryId]);
		$ret = [];

		while ($row = $rs->fetch())
		{
			array_push($ret, Transaction::fromResultSet($row));
		}

		return $ret;
	}

	public function createTransaction ($transaction)
	{
		$stmt = $this->createStatement('INSERT INTO transactions (book_id, direction, title, category_id, amount, creation_date) values (?, ?, ?, ?, ?, ?)');
		$stmt->execute($transaction->toCreateArray());

		$transaction->id = $this->pdo->lastInsertId();

		return $transaction->id;
	}

	public function updateTransaction ($transaction)
	{
		$stmt = $this->createStatement('UPDATE transactions SET direction = ?, title = ?, category_id = ?, amount = ?, creation_date = ? WHERE id = ?');
		$stmt->execute($transaction->toUpdateArray());
	}

	public function deleteTransaction ($id)
	{
		$stmt = $this->createStatement('DELETE FROM transactions WHERE id = ?');
		$stmt->execute([$id]);
	}

	/* Objectives */
	public function getAllObjectives ($bookId)
	{
		$rs = $this->getList('SELECT * FROM objectives where book_id = ?', [$bookId]);
		$ret = [];

		while ($row = $rs->fetch())
		{
			array_push($ret, Objective::fromResultSet($row));
		}

		return $ret;
	}

	public function getObjective ($id)
	{
		$rs = $this->get('SELECT * FROM objectives WHERE id = ?', [$id]);

		if (!$rs)
		{
			return null;
		}

		return Objective::fromResultSet($rs);	
	}

	public function createObjective ($objective)
	{
		$stmt = $this->createStatement('INSERT INTO objectives (book_id, category_id, amount) values (?, ?, ?)');
		$stmt->execute($objective->toCreateArray());

		$objective->id = $this->pdo->lastInsertId();

		return $objective->id;
	}

	public function updateObjective ($objective)
	{
		$stmt = $this->createStatement('UPDATE objectives SET amount = ? WHERE id = ?');
		$stmt->execute($objective->toUpdateArray());
	}

	public function deleteObjectiveForCategory ($categoryId)
	{
		$stmt = $this->createStatement('DELETE FROM objectives WHERE category_id = ?');
		$stmt->execute([$categoryId]);
	}
}
