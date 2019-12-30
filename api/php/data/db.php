<?php
	include 'config.php';

	function randomString ()
	{
		return bin2hex(openssl_random_pseudo_bytes(32));
	}

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

	function createLoginToken ($ownerId, $device)
	{
		$ret = randomString();
		$stmt = createPdo()->prepare('INSERT INTO login_tokens VALUES (?, ?, ?, CURRENT_TIMESTAMP())');
		$stmt->execute([$ret, $ownerId, $device]);

		return $ret;
	}

	function getLoginTokenOwner ($id)
	{
		$stmt = createPdo()->prepare('SELECT owner_id as o FROM login_tokens WHERE id = ?');
		$stmt->execute([$id]);

		$ret = $stmt->fetch();

		if (!$ret) 
		{
			return null;
		}

		return $ret['o'];
	}

	function updateLoginToken ($id)
	{
		$stmt = createPdo()->prepare('UPDATE login_tokens SET use_date = CURRENT_TIMESTAMP() WHERE id = ?');
		$stmt->execute([$id]);
	}

	function objectExists ($id, $ownerId)
	{
		$stmt = createPdo()->prepare('SELECT COUNT(*) AS c FROM objects WHERE id = ? and owner_id = ?');
		$stmt->execute([$id, $ownerId]);

		return $stmt->fetch()['c'] > 0;
	}

	function saveObject ($id, $value, $ownerId)
	{
		if (!objectExists($id, $ownerId))
		{
			$stmt = createPdo()->prepare('INSERT INTO objects VALUES (?, ?, ?)');
			$stmt->execute([$id, $value, $ownerId]);
		}
		else
		{
			$stmt = createPdo()->prepare('UPDATE objects SET value = ? WHERE id = ? and owner_id = ?');
			$stmt->execute([$value, $id, $ownerId]);
		}
	}

	function getObject ($id, $ownerId)
	{
		$stmt = createPdo()->prepare('SELECT * FROM objects WHERE id = ? and owner_id = ?');
		$stmt->execute([$id, $ownerId]);

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
