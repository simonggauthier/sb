<?php

include 'data/database.php';
include 'api/api.php';

class Implementation extends Api
{
	private $database;

	public function __construct()
	{
		parent::__construct();

		$this->database = new Database('./data/sb.db', $this->definition);

		$this->route();
	}

	public function execute($bookJson)
	{
		$this->ok(['msg' => $bookJson]);
	}

	public function login($username, $password, $device)
	{
		$user = exists($this->database->getEntity('user', ['id' => $username]));

		$this->checkPassword($user->get('passwordHash'), $password);

		$token = new Collection(['id' => randomString(), 'ownerId' => $user->get('id'), 'device' => $device, 'lastUseDate' => '$now']);
		$this->database->createEntity('loginToken', $token);

		$this->setLoggedUser($user->get('id'));

		$this->ok(['token' => $this->getLoginTokenById($token->get('id'))]);
	}

	public function loginByToken($tokenId)
	{
		$token = exists($this->getLoginTokenById($tokenId));

		exists($this->getUserById($token->get('ownerId')));

		$token->set('lastUseDate', '$now');
		$this->database->updateEntity('loginToken', $token);

		$this->setLoggedUser($token->get('ownerId'));

		$this->ok(['token' => $this->getLoginTokenById($token->get('id'))]);
	}

	public function logout()
	{
		$this->setLoggedUser(null);

		$this->ok([]);
	}

	public function getBook($name)
	{
		$this->checkLogin();

		$book = exists($this->getBookByName($name));

		$ret = new Collection();
		$ret->set('categories', new Collection());
		$ret->set('transactions', new Collection());

		$categories = $this->database->getEntities('category', ['bookId' => $book->get('id')]);

		$categories->forEach(function ($key, $category) use (&$ret) {
			$ret->get('categories')->push($category);
		});

		$transactions = $this->database->getEntities('transaction', ['bookId' => $book->get('id')]);

		$transactions->forEach(function ($key, $transaction) use (&$ret) {
			$ret->get('transactions')->push($transaction);
		});

		$ret->set('id', $book->get('id'));

		$this->ok($ret);
	}

	public function createCategory($name, $color, $bookId)
	{
		$this->checkLogin();

		exists($this->getBookById($bookId));

		notExists($this->getCategoryByName($name, $bookId));

		$category = new Collection(['bookId' => $bookId, 'name' => $name, 'color' => $color]);

		$this->database->createEntity('category', $category);

		$this->ok($category);
	}

	public function updateCategory($name, $color, $bookId, $id)
	{
		$this->checkLogin();

		$category = exists($this->getCategoryById($id));

		exists($this->getBookById($category->get('bookId')));

		$category->set('name', $name);
		$category->set('color', $color);
		$this->database->updateEntity('category', $category);

		$this->ok($category);
	}

	public function deleteCategory($id)
	{
		$this->checkLogin();

		$category = exists($this->getCategoryById($id));

		exists($this->getBookById($category->get('bookId')));

		$transactions = $this->database->getEntities('transaction', ['categoryId' => $id]);

		if ($transactions->size() > 0) {
			throw new ApiException('This category is linked to existing transactions', 400);
		}

		$this->database->deleteEntity('category', ['id' => $id]);

		$this->ok(['action' => 'deleted']);
	}

	public function createTransaction($bookId, $direction, $title, $categoryId, $amount, $date)
	{
		$this->checkLogin();

		$book = exists($this->getBookById($bookId));

		$transaction = new Collection([
			'bookId' => $bookId,
			'direction' => $direction,
			'title' => $title,
			'categoryId' => $categoryId,
			'amount' => $amount,
			'creationDate' => $date
		]);

		$this->database->createEntity('transaction', $transaction);

		$this->ok($this->getTransactionById($transaction->get('id')));
	}

	public function updateTransaction($bookId, $direction, $title, $categoryId, $amount, $creationDate, $id)
	{
		$this->checkLogin();

		$transaction = exists($this->getTransactionById($id));

		exists($this->getBookById($transaction->get('bookId')));

		$transaction->set('direction', $direction);
		$transaction->set('title', $title);
		$transaction->set('categoryId', $categoryId);
		$transaction->set('amount', $amount);
		$transaction->set('creationDate', $creationDate);

		$this->database->updateEntity('transaction', $transaction);

		$this->ok($this->getTransactionById($transaction->get('id')));
	}

	public function deleteTransaction($id)
	{
		$this->checkLogin();

		$transaction = exists($this->getTransactionById($id));

		exists($this->getBookById($transaction->get('bookId')));

		$this->database->deleteEntity('transaction', ['id' => $id]);

		$this->ok(['action' => 'deleted']);
	}

	// Helpers
	private function getLoginTokenById($id)
	{
		return $this->database->getEntity('loginToken', ['id' => $id]);
	}

	private function getUserById($id)
	{
		return $this->database->getEntity('user', ['id' => $id]);
	}

	private function getBookById($id)
	{
		return $this->database->getEntity('book', ['id' => $id, 'ownerId' => $this->getLoggedUser()]);
	}

	private function getBookByName($name)
	{
		return $this->database->getEntity('book', ['name' => $name, 'ownerId' => $this->getLoggedUser()]);
	}

	private function getCategoryById($id)
	{
		return $this->database->getEntity('category', ['id' => $id]);
	}

	private function getCategoryByName($name, $bookId)
	{
		return $this->database->getEntity('category', ['name' => $name, 'bookId' => $bookId]);
	}

	private function getTransactionById($id)
	{
		return $this->database->getEntity('transaction', ['id' => $id]);
	}
}

new Implementation();
