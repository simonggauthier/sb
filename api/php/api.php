<?php

	include 'data/database.php';

	function randomString ()
	{
		return bin2hex(openssl_random_pseudo_bytes(32));
	}
	
	class ApiError extends Exception
	{
		public $error;
		public $httpCode;

		public function __construct($error, $httpCode)
		{
			$this->error = $error;
			$this->httpCode = $httpCode;
		}
	}

	class GenericError
	{
		public $message;

		public function __construct($message)
		{
			$this->message = $message;
		}
	}

	class Api
	{
		private $database;

		public function __construct () 
		{
			$this->database = new Database();

			session_start();

			header('Content-Type: application/json; charset=utf-8');
		}

		/**
		* Login user to api
		*/
		public function login ()
		{
			if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['device']))
			{
				throw new ApiError(new GenericError('Invalid login request'), 400);
			}

			if (!Api::isDeviceValid($_POST['device']))
			{
				throw new ApiError(new GenericError('Invalid device'), 400);
			}

			$user = $this->database->getUser($_POST['username']);

			if ($user->passwordHash !== hash('sha256', $_POST['password']))
			{
				throw new ApiError(new GenericError('Invalid username or password'), 400);
			}

			$token = new LoginToken(randomString(), $user->id, $_POST['device'], $this->database->now());

			$this->database->createLoginToken($token);

			$_SESSION['userId'] = $user->id;

			$this->ok(['token' => $token]);
		}

		/**
		* Login user to api by token
		*/
		public function loginByToken ()
		{
			if (!isset($_POST['tokenId']))
			{
				throw new ApiError(new GenericError('Invalid login request'), 400);
			}

			$token = $this->database->getLoginToken($_POST['tokenId']);

			if (!$token)
			{
				throw new ApiError(new GenericError('Invalid login token'), 400);
			}

			$token->lastUseDate = $this->database->now();
			$this->database->updateLoginToken($token);

			$_SESSION['userId'] = $token->ownerId;

			$this->ok(['token' => $token]);
		}

		/**
		* Get book
		*/
		public function getBook ()
		{
			if (!isset($_GET['name']))
			{
				throw new ApiError(new GenericError('Invalid get book request'), 400);
			}

			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			$book = $this->database->findBook($_GET['name']);

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('No book'), 400);
			}

			$ret = [];
			$ret['categories'] = [];
			$ret['transactions'] = [];

			$categories = $this->database->getAllCategories($book->id);

			foreach ($categories as $category) 
			{
				array_push($ret['categories'], $category->toMap());
			}

			$transactions = $this->database->getAllTransactions($book->id);

			foreach ($transactions as $transaction) 
			{
				array_push($ret['transactions'], $transaction->toMap());
			}

			$ret['id'] = $book->id;

			$this->ok($ret);
		}

		/**
		* Create category
		*/
		public function createCategory ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['name']) || !isset($_POST['color']) || !isset($_POST['bookId']))
			{
				throw new ApiError(new GenericError('Invalid create category request'), 400);
			}

			$book = $this->database->getBook($_POST['bookId']);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid create category request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid create category request'), 400);
			}

			$dupCheck = $this->database->findCategory($_POST['name'], $_POST['bookId']);

			if ($dupCheck)
			{
				throw new ApiError(new GenericError('Category ' . $_POST['name'] . ' already exists'), 400);
			}

			$category = new Category(0, $_POST['bookId'], $_POST['name'], $_POST['color']);
			$this->database->createCategory($category);

			$this->ok($category->toMap());
		}

		/**
		* Update category
		*/
		public function updateCategory ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['name']) || !isset($_POST['color']))
			{
				throw new ApiError(new GenericError('Invalid update category request'), 400);
			}

			$category = $this->database->getCategory($_POST['id']);

			if (!$category)
			{
				throw new ApiError(new GenericError('Invalid category id'), 400);
			}

			$book = $this->database->getBook($category->bookId);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid update category request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid category id'), 400);
			}

			$category->name = $_POST['name'];
			$category->color = $_POST['color'];
			$this->database->updateCategory($category);

			$this->ok($category->toMap());
		}

		/**
		* Update category
		*/
		public function deleteCategory ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['id']))
			{
				throw new ApiError(new GenericError('Invalid delete category request'), 400);
			}

			$category = $this->database->getCategory($_POST['id']);

			if (!$category)
			{
				throw new ApiError(new GenericError('Invalid category id'), 400);
			}

			$book = $this->database->getBook($category->bookId);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid update category request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid category id'), 400);
			}

			$transactions = $this->database->getTransactionsByCategory($category->id);

			if (count($transactions) > 0) 
			{
				throw new ApiError(new GenericError('This category is linked to transactions'), 400);
			}

			$this->database->deleteCategory($category->id);

			$this->ok(['action' => 'deleted']);
		}

		/**
		* Create transaction
		*/
		public function createTransaction ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['bookId']) || !isset($_POST['direction']) || !isset($_POST['title']) || !isset($_POST['categoryId']) || !isset($_POST['amount']) || !isset($_POST['date']))
			{
				throw new ApiError(new GenericError('Invalid create transaction request'), 400);
			}

			$book = $this->database->getBook($_POST['bookId']);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid create transaction request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid book id'), 400);
			}

			$transaction = new Transaction(0, $_POST['bookId'], $_POST['direction'], $_POST['title'], $_POST['categoryId'], str_replace('.', '', '' . $_POST['amount']), $this->database->dateFromEpoch($_POST['date']));
			$this->database->createTransaction($transaction);

			$this->ok($transaction->toMap());
		}

		/**
		* Update transaction
		*/
		public function updateTransaction ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['direction']) || !isset($_POST['title']) || !isset($_POST['categoryId']) || !isset($_POST['amount']) || !isset($_POST['date']))
			{
				throw new ApiError(new GenericError('Invalid update transaction request'), 400);
			}

			$transaction = $this->database->getTransaction($_POST['id']);

			if (!$transaction)
			{
				throw new ApiError(new GenericError('Invalid transaction id'), 400);
			}

			$book = $this->database->getBook($transaction->bookId);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid update transaction request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid book id'), 400);
			}

			$transaction->direction = $_POST['direction'];
			$transaction->title = $_POST['title'];
			$transaction->categoryId = $_POST['categoryId'];
			$transaction->amount = str_replace('.', '', '' . $_POST['amount']);
			$transaction->creationDate = $this->database->dateFromEpoch($_POST['date']);
			$this->database->updateTransaction($transaction);

			$this->ok($transaction->toMap());
		}


		/**
		* Update transaction
		*/
		public function deleteTransaction ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			if (!isset($_POST['id']))
			{
				throw new ApiError(new GenericError('Invalid delete transaction request'), 400);
			}

			$transaction = $this->database->getTransaction($_POST['id']);

			if (!$transaction)
			{
				throw new ApiError(new GenericError('Invalid transaction id'), 400);
			}

			$book = $this->database->getBook($transaction->bookId);

			if (!$book)
			{
				throw new ApiError(new GenericError('Invalid delete transaction request'), 400);
			}

			if ($book->ownerId !== $_SESSION['userId'])
			{
				throw new ApiError(new GenericError('Invalid book id'), 400);
			}

			$this->database->deleteTransaction($_POST['id']);

			$this->ok(['action' => 'deleted']);
		}	

		/**
		* Convert json book object to model
		*/
		public function conversionScript ()
		{
			if (!isset($_SESSION['userId']))
			{
				throw new ApiError(new GenericError('Not logged in'), 400);
			}

			$actions = [];

			array_push($actions, 'Starting conversion');

			array_push($actions, 'Checking existing book');

			$book = $this->database->getBookByOwnerId($_SESSION['userId']);

			if (!$book)
			{
				array_push($actions, 'No existing book, creating one');

				$book = new Book(0, 'Budget', $_SESSION['userId']);
				$this->database->createBook($book);

				array_push($actions, 'Created book ' . $book->id);
			}
			else
			{
				array_push($actions, 'Book exists ' . $book->id);
			}

			array_push($actions, 'Loading old json book');

			$stmt = $this->database->createStatement('select value as v from objects where id = "book"');
			$stmt->execute();

			$json = $stmt->fetch()['v'];

			$obj = json_decode($json, true);

			array_push($actions, 'Creating categories');

			$catCache = [];

			foreach ($obj['categories'] as $cat)
			{
				$category = new Category(0, $book->id, $cat['name'], $cat['color']);
				$this->database->createCategory($category);

				$catCache[$cat['key']] = $category->id;

				array_push($actions, 'Created category ' . $category->name . ', ' . $category->id . ' [' . $cat['key'] . ']');
			}

			array_push($actions, 'Creating transactions');

			foreach ($obj['transactions'] as $tra)
			{
				$categoryId = $catCache[$tra['category']];

				if (!$categoryId)
				{
					array_push($actions, 'Invalid category');

					continue;
				}

				$amount = str_replace('.', '', '' . $tra['amount']);

				$transaction = new Transaction(0, $book->id, $tra['direction'], $tra['title'], $categoryId, $amount, $this->database->dateFromEpoch($tra['date']));
				$this->database->createTransaction($transaction);
			}

			$this->ok(['log' => $actions]);
		}

		public static function isDeviceValid ($device)
		{
			return $device === 'desktop' || $device === 'mobile';
		}

		public function error ($e)
		{
			http_response_code($e->httpCode);
			
			echo json_encode($e->error);
		}

		public function ok ($payload = null)
		{
			http_response_code(200);

			if ($payload)
			{
				echo json_encode($payload);
			}
		}
	}

	class RouterError
	{
		public $message;

		public function __construct($message)
		{
			$this->message = $message;
		}
	}

	class Router
	{
		private $api;

		private $method;
		private $action;

		public function __construct ($api) {
			$this->api = $api;

			$this->method = $_SERVER['REQUEST_METHOD'];

			if (!isset($_REQUEST['action'])) {
				throw new ApiError(new RouterError('Paramater action not set'), 400);
			}

			$this->action = $_REQUEST['action'];

			$this->route();
		}

		private function route () {
			$routes = array();

			// Routes
			$routes['POST']['login'] = function () {
				$this->api->login();
			};

			$routes['POST']['login-by-token'] = function () {
				$this->api->loginByToken();
			};

			$routes['POST']['category'] = function () {
				if (isset($_POST['id']))
				{
					if (isset($_POST['delete']))
					{
						$this->api->deleteCategory();
					}
					else
					{
						$this->api->updateCategory();
					}
				}
				else
				{
					$this->api->createCategory();
				}
			};

			$routes['POST']['transaction'] = function () {
				if (isset($_POST['id']))
				{
					if (isset($_POST['delete']))
					{
						$this->api->deleteTransaction();
					}
					else
					{
						$this->api->updateTransaction();
					}
				}
				else
				{
					$this->api->createTransaction();
				}
			};

			$routes['GET']['book'] = function () {
				$this->api->getBook();
			};

			$routes['GET']['convert'] = function () {
				$this->api->conversionScript();
			};

			// Logic
			if (isset($routes[$this->method][$this->action])) {
				$routes[$this->method][$this->action]();
			} else {
				throw new ApiError(new RouterError('Invalid action (' . $this->method . ' ' . $this->action . ')'), 400);
			}
		}
	}

	$api = new Api();

	try
	{
		$router = new Router($api);
	}
	catch (ApiError $e)
	{
		$api->error($e);
	}
