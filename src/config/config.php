<?php

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();

// Configuration for Database
define("DB_HOST", getenv('DB_HOST'));
define("DB_USER", getenv('DB_USER'));
define("DB_PASS", getenv('DB_PASS'));
define("DB_NAME", getenv('DB_NAME'));

// Secret for JWT Auth
define("SECRET", getenv('SECRET_KEY'));

// Configuration for Mail
define("MAIL_HOST", getenv('MAIL_HOST'));
define("MAIL_USER", getenv('MAIL_USER'));
define("MAIL_PASS", getenv('MAIL_PASS'));
define("MAIL_NAME", getenv('MAIL_NAME'));

?>
