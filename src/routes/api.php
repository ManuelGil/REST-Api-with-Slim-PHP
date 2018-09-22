<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$container = $app->getContainer();

$container["logger"] = function ($c) {
	// create a log channel
	$log = new Logger("api");
	$log->pushHandler(new StreamHandler(__DIR__ . "/../logs/app.log", Logger::INFO));

	return $log;
};

/**
 * This method restricts access to addresses. <br/>
 * <b>post: </b>To access is required a valid token.
 */
$app->add(new \Slim\Middleware\JwtAuthentication([
	// The secret key
	"secret" => SECRET,
	"rules" => [
		new \Slim\Middleware\JwtAuthentication\RequestPathRule([
			// Degenerate access to "/webresources"
			"path" => "/webresources",
			// It allows access to "login" without a token
			"passthrough" => [
				"/webresources/mobile_app/ping",
				"/webresources/mobile_app/login",
				"/webresources/mobile_app/register",
				"/webresources/mobile_app/validate"
			]
		])
	]
]));

/**
 * This method creates a urls group. <br/>
 * <b>post: </b>establishes the base url "/public/webresources/mobile_app/".
 */
$app->group("/webresources/mobile_app", function () use ($app) {
	/**
	 * This method is used for testing the api.<br/>
	 */
	$app->get("/ping", function (Request $request, Response $response) {
		return "pong";
	});

	/**
	 * This method gets a user into the database.
	 * @param string $user - username
	 * @param string $pass - password
	 */
	$app->get("/login/{user}/{password}", function (Request $request, Response $response) {
		// Gets username and password
		$user = $request->getAttribute("user");
		$pass = $request->getAttribute("password");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Gets the user into the database
			$sql = "SELECT * FROM USERS
					WHERE USERNAME = :user
					AND STATUS = 1";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":user", $user);
			$stmt->execute();
			$query = $stmt->fetchObject();

			// If user exist
			if ($query) {
				// If password is correct
				if (password_verify($pass, $query->PASSWORD)) {
					// Create a new resource
					$data["token"] = JWTAuth::getToken($query->ID_USER, $query->USERNAME);
				} else {
					// Password wrong
					$data["status"] = "Error: The password you have entered is wrong.";
				}
			} else {
				// Username wrong
				$data["status"] = "Error: The user specified does not exist.";
			}

			// Return the result
			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(201, "Created");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method sets a user into the database.
	 * @param string $user - username
	 * @param string $pass - password
	 * @param int $country - country id
	 */
	$app->post("/register", function (Request $request, Response $response) {
		// Unique ID
		$guid = uniqid();
		// Activation token
		$token = bin2hex(openssl_random_pseudo_bytes(16));
		// Gets username, email and password
		$user = $request->getParam("user");
		$pass = password_hash($request->getParam("password"), PASSWORD_DEFAULT);
		$email = trim(strtolower($request->getParam("email")));
		// Date of created
		$created = date("Y-m-d");
		// Country ID
		$country = (int)$request->getParam("country");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Gets the user into the database
			$sql = "INSERT INTO USERS (GUID, TOKEN, USERNAME, PASSWORD, CREATED_AT, ID_COUNTRY)
					VALUES (:guid, :token, :user, :pass, :created, :country)";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":guid", $guid);
			$stmt->bindParam(":token", $token);
			$stmt->bindParam(":user", $user);
			$stmt->bindParam(":pass", $pass);
			$stmt->bindParam(":created", $created);
			$stmt->bindParam(":country", $country);
			$result = $stmt->execute();

			// If user has been registered
			if ($result) {
				$data["status"] = "Your account has been successfully created. We will send you an email to confirm that your email address is valid.";

				$from = "username@gmail.com";
				$to = $email;
				$name = $user;
				$subject = "Confirm your email address";
				// Example of the confirmation link: http://localhost/rest/public/webresources/mobile_app/validate/testUser/326f0911657d94d0a48530058ca2a383
				$html = "Click on the link to verify your email <a href='http://{yourdomain}/public/webresources/mobile_app/validate/{$user}/{$token}' target='_blank'>Link</a>";
				$text = "Go to the link to verify your email: http://{yourdomain}/public/webresources/mobile_app/validate/{$user}/{$token}";

				// Sent mail verification
				Mailer::send($from, $to, $name, $subject, $html, $text);
			} else {
				$data["status"] = "Error: Your account cannot be created at this time. Please try again later.";
			}

			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method sets a user into the database.
	 * @param string $user - username
	 * @param string $pass - password
	 * @param int $country - country id
	 */
	$app->get("/validate/{user}/{token}", function (Request $request, Response $response) {
		// Gets username and password
		$user = $request->getAttribute("user");
		$token = $request->getAttribute("token");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			$sql = "SELECT * FROM USERS
					WHERE USERNAME = :user
					AND TOKEN = :token";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":user", $user);
			$stmt->bindParam(":token", $token);
			$stmt->execute();
			$query = $stmt->fetchObject();

			// If user exist
			if ($query) {
				// Gets the user into the database
				$sql = "UPDATE USERS
						SET TOKEN = NULL,
							STATUS = 1
						WHERE USERNAME = :user";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(":user", $user);
				$result = $stmt->execute();

				// If user has been verified
				if ($result) {
					$data["status"] = "Your account has been successfully verified.";
				} else {
					$data["status"] = "Error: Your account cannot be verified.";
				}
			} else {
				// Username wrong
				$data["status"] = "Error: The user specified does not exist.";
				
			}

			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method cheks the token.
	 */
	$app->get("/verify", function (Request $request, Response $response) {
		// Gets the token of the header.
		$token = str_replace("Bearer ", "", $request->getServerParams()["HTTP_AUTHORIZATION"]);
		// Verify the token.
		$result = JWTAuth::verifyToken($token);
		// Return the result
		$data["status"] = $result;
		$response = $response->withHeader("Content-Type", "application/json");
		$response = $response->withStatus(200, "OK");
		$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		return $response;
	});

	/**
	 * This method publish short text messages of no more than 120 characters.
	 * @param string $quote - The text of post
	 * @param int $id - The user id
	 */
	$app->post("/post", function (Request $request, Response $response) {
		// Gets quote and user id
		$quote = $request->getParam("quote");
		$id = $request->getParam("id");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Gets the user into the database
			$sql = "SELECT * FROM USERS
					WHERE ID_USER = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":id", $id);
			$stmt->execute();
			$query = $stmt->fetchObject();

			// If user exist
			if ($query) {
				// Truncate the text
				if (strlen($quote) > 120) {
					$quote = substr($quote, 0, 120);
				}

				// Insert post into the database
				$sql = "INSERT INTO QUOTES (QUOTE, ID_USER)
						VALUES (:quote, :id)";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(":quote", $quote);
				$stmt->bindParam(":id", $id);
				$result = $stmt->execute();

				$data["status"] = $result;
			} else {
				// Username wrong
				$data["status"] = "Error: The user specified does not exist.";
			}
		
			// Return the result
			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method list the latest published messages.
	 */
	$app->get("/list", function (Request $request, Response $response) {
		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Gets the posts into the database
			$sql = "SELECT 
						Q.ID_QUOTE AS id,
						Q.QUOTE AS quote,
						Q.POST_DATE AS postdate,
						Q.LIKES AS likes,
						U.USERNAME AS user
					FROM QUOTES AS Q
					INNER JOIN USERS AS U ON Q.ID_USER = U.ID_USER
					ORDER BY likes DESC";
			$stmt = $conn->query($sql);
			$data = $stmt->fetchAll();

			// Return a list
			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method list the users for likes.
	 * @param int $id - quote id
	 */
	$app->get("/likes/{id}", function (Request $request, Response $response) {
		// Gets quote
		$id = $request->getAttribute("id");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Gets the posts into the database
			$sql = "SELECT
						U.GUID AS guid,
						U.USERNAME AS user
					FROM LIKES AS L
					INNER JOIN USERS AS U ON L.ID_USER = U.ID_USER
					AND L.ID_QUOTE = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":id", $id);
			$stmt->execute();
			$data = $stmt->fetchAll();

			// Return a list
			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method searches for messages by your text.
	 * @param string $quote - The text of post
	 */
	$app->get("/search/{quote}", function (Request $request, Response $response) {
		// Gets quote
		$quote = "%" . $request->getAttribute("quote") . "%";

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Search into the database
			$sql = "SELECT
						Q.ID_QUOTE AS id,
						Q.QUOTE AS quote,
						Q.POST_DATE AS postdate,
						Q.LIKES AS likes,
						U.USERNAME AS user
					FROM QUOTES AS Q
					INNER JOIN USERS AS U ON Q.ID_USER = U.ID_USER
					WHERE QUOTE LIKE :quote
					ORDER BY likes DESC";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":quote", $quote);
			$stmt->execute();
			$data = $stmt->fetchAll();

			// Return the result
			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

	/**
	 * This method deletes a specific message by its id.
	 * @param int $id - The quote id
	 */
	$app->delete("/delete", function (Request $request, Response $response) {
		// Gets quote id
		$id = $request->getParam("id");

		try {
			// Gets the database connection
			$conn = PDOConnection::getConnection();

			// Delete the quote
			$sql = "DELETE FROM QUOTES
					WHERE ID_QUOTE = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":id", $id);
			$result = $stmt->execute();

			// Return the result
			$data["status"] = $result;

			$response = $response->withHeader("Content-Type", "application/json");
			$response = $response->withStatus(200, "OK");
			$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
			return $response;
		} catch (PDOException $e) {
			$this["logger"]->error("DataBase Error: {$e->getMessage()}");
		} catch (Exception $e) {
			$this["logger"]->error("General Error: {$e->getMessage()}");
		}
		finally {
			// Destroy the database connection
			$conn = null;
		}
	});

});

?>
