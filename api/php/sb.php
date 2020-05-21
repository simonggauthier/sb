<?php

include 'data/database.php';

include 'api/api.php';
include 'api/definition.php';

class Sb extends Api
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

	/**
	 * Login user to api
	 */
	public function login($username, $password, $device)
	{
		$user = exists($this->database->getEntity('user', ['id'], [$username]));

		$this->checkPassword($user['passwordHash'], $password);

		print_r($user);

		$token = new LoginToken(randomString(), $user['id'], $device, $this->database->now());
		$this->database->createLoginToken($token);

		$this->setLoggedUser($user['id']);

		$this->ok(['token' => $token]);
	}

	/**
	 * Login user to api by token
	 */
	public function loginByToken($tokenId)
	{
		$token = exists($this->database->getEntity('loginToken', ['id'], [$tokenId]));

		print_r($token);

		exists($this->database->getEntity('user', ['id'], [$token['ownerId']]));

		$token['lastUseDate'] = $this->database->now();

		On est rendu là
		$this->database->updateLoginToken($token);

		$this->setLoggedUser($token['ownerId']);

		$this->ok(['token' => $token]);
	}

	/**
	 * Logout user to api
	 */
	public function logout()
	{
		$this->setLoggedUser(null);

		$this->ok([]);
	}

	/**
	 * Get book
	 */
	public function getBook($name)
	{
		$this->checkLogin();

		$book = exists($this->database->getEntity('book', ['name', 'ownerId'], [$name, $this->getLoggedUser()]));

		$ret = [];
		$ret['categories'] = [];
		$ret['transactions'] = [];

		$categories = $this->database->getEntities('category', ['bookId'], [$book['id']]);

		foreach ($categories as $category) {
			array_push($ret['categories'], $category);
		}

		$transactions = $this->database->getEntities('transaction', ['bookId'], [$book['id']]);

		foreach ($transactions as $transaction) {
			array_push($ret['transactions'], $transaction);
		}

		$ret['id'] = $book['id'];

		$this->ok($ret);
	}

	/**
	 * Create category
	 */
	public function createCategory($name, $color, $bookId)
	{
		$this->checkLogin();

		exists($this->database->getBook($bookId, $this->getLoggedUser()));

		notExists($this->database->findCategory($name, $bookId));

		$category = new Category(0, $bookId, $name, $color);
		$this->database->createCategory($category);

		$this->ok($category->toMap());
	}

	/**
	 * Update category
	 */
	public function updateCategory($name, $color, $bookId, $id)
	{
		$this->checkLogin();

		$category = exists($this->database->getCategory($id));

		exists($this->database->getBook($category->bookId, $this->getLoggedUser()));

		$category->name = $name;
		$category->color = $color;
		$this->database->updateCategory($category);

		$this->ok($category->toMap());
	}

	/**
	 * Delete category
	 */
	public function deleteCategory($id)
	{
		$this->checkLogin();

		$category = exists($this->database->getCategory($id));

		exists($this->database->getBook($category->bookId, $this->getLoggedUser()));

		$transactions = $this->database->getTransactionsByCategory($category->id);

		if (count($transactions) > 0) {
			throw new ApiException('This category is linked to existing transactions', 400);
		}

		$this->database->deleteCategory($category->id);

		$this->ok(['action' => 'deleted']);
	}

	/**
	 * Create transaction
	 */
	public function createTransaction($bookId, $direction, $title, $categoryId, $amount, $date)
	{
		$this->checkLogin();

		$book = exists($this->database->getBook($bookId, $this->getLoggedUser()));

		$transaction = new Transaction(0, $bookId, $direction, $title, $categoryId, $this->database->formatMoney($amount), $this->database->dateFromEpoch($date));
		$this->database->createTransaction($transaction);

		$this->ok($transaction->toMap());
	}

	/**
	 * Update transaction
	 */
	public function updateTransaction($id, $direction, $title, $categoryId, $amount, $date)
	{
		$this->checkLogin();

		$transaction = exists($this->database->getTransaction($id));

		exists($this->database->getBook($transaction->bookId, $this->getLoggedUser()));

		$transaction->direction = $direction;
		$transaction->title = $title;
		$transaction->categoryId = $categoryId;
		$transaction->amount = $this->database->formatMoney($amount);
		$transaction->creationDate = $this->database->dateFromEpoch($date);
		$this->database->updateTransaction($transaction);

		$this->ok($transaction->toMap());
	}

	/**
	 * Delete transaction
	 */
	public function deleteTransaction($id)
	{
		$this->checkLogin();

		$transaction = exists($this->database->getTransaction($id));

		exists($this->database->getBook($transaction->bookId, $this->getLoggedUser()));

		$this->database->deleteTransaction($id);

		$this->ok(['action' => 'deleted']);
	}
}

new Sb();
