<?php
	include 'config.php';

	function createPdo ()
	{
		if($GLOBALS['MODE'] === 'prod')
		{

		}
		else
		 {
			$host = 'localhost';
			$database = 'sb';
			$username = 'root';
			$password = '';
		 }

		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];

		$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

		return new PDO($dsn, $username, $password, $options);
	}

	function isLoginOk ($id, $passwordHash)
	{
		$stmt = createPdo()->prepare('SELECT COUNT(*) AS c FROM users WHERE id = ? and password_hash = ?');
		$stmt->execute([$id, $passwordHash]);

		return $stmt->fetch()['c'] > 0;
	}

	function objectExists($id, $userId)
	{
		$stmt = createPdo()->prepare('SELECT COUNT(*) AS c FROM objects WHERE id = ? and owner_id = ?');
		$stmt->execute([$id, $userId]);

		return $stmt->fetch()['c'] > 0;
	}

	function saveObject ($id, $value, $userId)
	{
		if (!objectExists($id, $userId))
		{
			$stmt = createPdo()->prepare('INSERT INTO objects VALUES (?, ?, ?)');
			$stmt->execute([$id, $value, $userId]);
		}
		else
		{
			$stmt = createPdo()->prepare('UPDATE objects SET value = ? WHERE id = ? and owner_id = ?');
			$stmt->execute([$value, $id, $userId]);
		}
	}

	function getObject ($id, $userId)
	{
		$stmt = createPdo()->prepare('SELECT * FROM objects WHERE id = ? and owner_id = ?');
		$stmt->execute([$id, $userId]);

		$ret = $stmt->fetch();

		if (!$ret)
		{
			return null;
		}
		else
		{
			return $ret['value'];
		}
	}
