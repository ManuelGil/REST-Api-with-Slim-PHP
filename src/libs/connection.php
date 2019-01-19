<?php

/**
 * Class PDO Connection
 */
class PDOConnection
{

	/**
	 * This function create a database connection
	 *
	 * @return	object	database connection
	 */
	public static function getConnection()
	{
		/** @var string $host - Hostname */
		$host = DB_HOST;
		/** @var string $user - Database username */
		$user = DB_USER;
		/** @var string $pass - Database password */
		$pass = DB_PASS;
		/** @var string $name - Database name */
		$name = DB_NAME;

		/** @var string $dsn - Connection string */
		$dsn = "mysql:host=$host;dbname=$name;charset=utf8";

		// Create a new PDO connection
		$connection = new PDO($dsn, $user, $pass);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		// Return the connection
		return $connection;
	}
}

?>
