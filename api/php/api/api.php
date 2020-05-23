<?php

include_once 'collection.php';
include_once 'definition.php';

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
	public $definition;

	protected function __construct()
	{
		session_start();

		$this->definition = new ApiDefinition('../definition.json');
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

		if (!$_SESSION['userId']) {
			throw new ApiException('Not logged in', 400);
		}
	}

	protected function error($e)
	{
		if ($e instanceof ApiException) {
			http_response_code($e->httpCode);

			echo json_encode($e);
		} else {
			http_response_code(500);

			echo json_encode(['error' => $e]);
		}
	}

	protected function ok($payload = null)
	{
		http_response_code(200);

		if ($payload) {
			if (!($payload instanceof Collection)) {
				$payload = new Collection($payload);
			}

			echo json_encode($payload->toArray());
		}
	}

	protected function route()
	{
		try {
			$server = new Collection($_SERVER);
			$request = new Collection($_REQUEST);

			header('Content-Type: application/json; charset=utf-8');

			$method = strtolower($server->get('REQUEST_METHOD'));

			if (!$request->hasKey('action')) {
				throw new ApiException('No action parameter', 400);
			}

			$action = $request->get('action');

			$route = $this->definition->findRoute($method,  '/' . $action);

			if (!$route) {
				throw new ApiException('Invalid action', 400);
			}

			$params = $route->extractParams();

			$route->parameters->forEach(function ($key, $param, $i) use ($params) {
				if (!$params->hasKey($i)) {
					throw new ApiException('Invalid parameter: ' . $param->name, 400);
				}
			});

			call_user_func_array(array($this, $route->controllerName), $params->toArray());
		} catch (Exception $e) {
			$this->error($e);
		}
	}
}
