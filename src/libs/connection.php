<?php

  /**
   * Class PDO Connection
   */
  class PDOConnection {

    private function __construct() {}

    /**
     * This function create a database connection
     * @return object database connection
     */
    public static function getConnection() {
      /** @var string hostname */
      $host = DB_HOST;
      /** @var string database username */
      $user = DB_USER;
      /** @var string database password */
      $pass = DB_PASS;
      /** @var string database name */
      $name = DB_NAME;

      /** @var string connection string */
      $dsn = "mysql:host=$host;dbname=$name;charset=utf8";

      try {
        // Create a new PDO connection
        $connection = new PDO($dsn, $user, $pass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Return the connection
        return $connection;
      } catch (PDOException $e) {
        die($e);
      }
    }
  }

?>
