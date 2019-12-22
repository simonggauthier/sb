<?php
	include 'data/db.php';

	session_start();

	function error ($id, $msg)
	{
		return "{\"error\": {\"msg\": \"$msg\", \"errorId\": $id}}";
	}

	function ok ($id)
	{
		return "{\"id\": \"$id\"}";
	}

	function isValidJson ($json)
	{
		json_decode($json);

		return (json_last_error() == JSON_ERROR_NONE);
	}

	function login ($id, $password)
	{
		if (isLoginOk($id, hash('sha256', $password)))
		{
			$_SESSION['userId'] = $id;

			http_response_code(200);
		}
		else
		{
			http_response_code(401);
		}
	}

	function get ($id) 
	{
		header('Content-Type: application/json; charset=utf-8');

		if (!isset($_SESSION['userId']))
		{
			http_response_code(401);

			echo error(4, 'not logged in');

			return;
		}

		$ret = getObject($id, $_SESSION['userId']);

		if (!$ret)
		{
			http_response_code(404);

			echo error(1, 'no object');

			return;
		}

		http_response_code(200);

		echo $ret;
	}

	function post ($id)
	{
		header('Content-Type: application/json; charset=utf-8');

		if (!isset($_SESSION['userId']))
		{
			http_response_code(401);
			
			echo error(4, 'not logged in');

			return;
		}

		if (!$_POST['value'])
		{
			http_response_code(400);

			echo error(2, 'no parameter value');

			return;
		}

		$json = $_POST['value'];

		if (!isValidJson($json))
		{
			http_response_code(422);

			echo error(3, 'invalid parameter value');

			return;
		}

		saveObject($id, $json, $_SESSION['userId']);

		http_response_code(200);

		echo ok($id);
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$page = $_GET['id'];

		if ($page === 'login')
		{
			login($_POST['username'], $_POST['password']);
		}
		else
		{
			post($_GET['id']);
		}
	}
	else
	{
		get($_GET['id']);
	}
