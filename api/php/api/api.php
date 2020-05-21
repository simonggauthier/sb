<?php

function randomString()
{
	return bin2hex(openssl_random_pseudo_bytes(32));
}

class ApiException extends Exception
{
	public $message;
	public $httpCode;

	public function __construct($message, $httpCode)
	{
		$this->message = $message;
		$this->httpCode = $httpCode;
	}
}

function exists($value)
{
	if (!$value) {
		throw new ApiException('Value does not exist', 400);
	}

	return $value;
}

function notExists($value)
{
	if ($value) {
		throw new ApiException('Value exists', 400);
	}

	return $value;
}

class Api
{
	private $routes;
	public $definition;

	protected function __construct()
	{
		session_start();

		$this->definition = new ApiDefinition('../definition.json');

		$this->routes = array();
	}

	protected function checkPassword($passwordHash, $password)
	{
		if ($passwordHash !== hash('sha256', $password)) {
			throw new ApiException('Invalid username or password', 400);
		}
	}

	protected function setLoggedUser($userId)
	{
		$_SESSION['userId'] = $userId;
	}

	protected function getLoggedUser()
	{
		return $_SESSION['userId'];
	}

	protected function checkLogin()
	{
		if (!isset($_SESSION['userId'])) {
			throw new ApiException('Not logged in', 400);
		}
	}

	protected function error($e)
	{
		http_response_code($e->httpCode);

		echo json_encode($e);
	}

	protected function ok($payload = null)
	{
		http_response_code(200);

		if ($payload) {
			echo json_encode($payload);
		}
	}

	protected function route()
	{
		header('Content-Type: application/json; charset=utf-8');

		$method = strtolower($_SERVER['REQUEST_METHOD']);

		if (!isset($_REQUEST['action'])) {
			throw new ApiException('No action parameter', 400);
		}

		$action = $_REQUEST['action'];

		$route = $this->definition->findRoute($method,  '/' . $action);

		if (!$route) {
			throw new ApiException('Invalid action', 400);
		}

		$params = $route->extractParams();

		$i = 0;

		foreach ($route->parameters as $param) {
			if (!isset($params[$i])) {
				throw new ApiException('Invalid parameter: ' . $param->name, 400);
			}

			$i++;
		}

		call_user_func_array(array($this, $route->controllerName), $params);
	}
}
