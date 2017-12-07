<?php

  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  /**
   * This method restricts access to addresses. <br/>
   * <b>post: </b>To access is required a valid token.
   */
  $app->add(new \Slim\Middleware\JwtAuthentication([
      // The secret key
      "secret" => SECRET,
      "rules" => [
          new \Slim\Middleware\JwtAuthentication\RequestPathRule([
              // Degenerate access to '/webresources'
              "path" => "/webresources",
              // It allows access to 'login' without a token
              "passthrough" => ["/webresources/mobile_app/login"]
          ])
      ]
  ]));

  /**
   * This method a url group. <br/>
   * <b>post: </b>establishes the base url '/public/webresources/mobile_app/'.
   */
  $app->group('/webresources/mobile_app', function () use ($app) {

    /**
     * This method gets a user into the database.
     * @param String $user - username
     * @param String $pass - password
     */
    $app->get('/login/{user}/{password}', function (Request $request, Response $response) {
      // Gets username and password
      $user = $request->getAttribute("user");
      $pass = $request->getAttribute("password");

      // Gets the database connection
      $conn = PDOConnection::getConnection();

  		try {
  			// Gets the user into the database
  			$sql = "SELECT * FROM USERS WHERE USERNAME=:user";
  			$stmt = $conn->prepare($sql);
  			$stmt->bindParam(":user", $user);
  			$stmt->execute();
  			$query = $stmt->fetchObject();

  			// If user exist
  			if ($query) {
  				// If password is correct
  				if (password_verify($pass, $query->PASSWORD)) {
            // Create a new resource
            $data['token'] = JWTAuth::getToken($query->ID_USER, $query->USERNAME);

            // Return the resource
            $response = $response->withHeader('Content-Type','application/json');
            $response = $response->withStatus(201);
            $response = $response->withJson($data);
            return $response;
          } else {
  					// Password wrong
            die("Error: The password you have entered is wrong.");
  				}
  			} else {
  				// Username wrong
          die("Error: The user specified does not exist.");
  			}
      } catch (PDOException $e) {
        die($e->getMessage());
      } finally {
        // Destroy the database connection
        $conn = null;
      }
    });

    /**
     * This method publish short text messages of no more than 120 characters
     * @param String $quote - The text of post
     * @param Int $id - The user id
     */
    $app->post('/post', function (Request $request, Response $response) {
      // Gets quote and user id
      $quote = $request->getParam('quote');
      $id = $request->getParam('id');

      // Gets the database connection
      $conn = PDOConnection::getConnection();

      try {
        // Gets the user into the database
  			$sql = "SELECT * FROM USERS WHERE ID_USER=:id";
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
          $sql = "INSERT INTO QUOTES(QUOTE, ID_USER) VALUES(:quote, :id)";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(":quote", $quote);
          $stmt->bindParam(":id", $id);
          $result = $stmt->execute();

          // Return the result
          $data['status'] = $result;
          $response = $response->withHeader('Content-Type','application/json');
          $response = $response->withStatus(200);
          $response = $response->withJson($data);
          return $response;
        } else {
  				// Username wrong
          die("Error: The user specified does not exist.");
  			}
      } catch (PDOException $e) {
        die($e->getMessage());
      } finally {
        // Destroy the database connection
        $conn = null;
      }
    });

    /**
     * This method list the latest published messages
     */
    $app->get('/list', function (Request $request, Response $response) {
      // Gets the database connection
      $conn = PDOConnection::getConnection();

      try {
        // Gets the posts into the database
        $sql = "SELECT Q.ID_QUOTE AS id, Q.QUOTE AS quote, Q.POST_DATE AS postdate, Q.LIKES AS likes, U.USERNAME AS user FROM QUOTES AS Q, USERS AS U WHERE Q.ID_USER=U.ID_USER ORDER BY likes DESC";
        $stmt = $conn->query($sql);
        $data = $stmt->fetchAll();

        // Return a list
        $response = $response->withHeader('Content-Type','application/json');
        $response = $response->withStatus(200);
        $response = $response->withJson($data);
        return $response;
      } catch (PDOException $e) {
        die($e->getMessage());
      } finally {
        // Destroy the database connection
        $conn = null;
      }
    });

    /**
     * This method searches for messages by your text.
     * @param String $quote - The text of post
     */
    $app->get('/search/{quote}', function (Request $request, Response $response) {
      // Gets quote
      $quote = '%' . $request->getAttribute('quote') . '%';

      // Gets the database connection
      $conn = PDOConnection::getConnection();

      try {
        // Search into the database
        $sql = "SELECT Q.ID_QUOTE AS id, Q.QUOTE AS quote, Q.POST_DATE AS postdate, Q.LIKES AS likes, U.USERNAME AS user FROM QUOTES AS Q, USERS AS U WHERE QUOTE LIKE :quote AND Q.ID_USER=U.ID_USER ORDER BY likes DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quote', $quote);
        $stmt->execute();
        $data = $stmt->fetchAll();

        // Return the result
        $response = $response->withHeader('Content-Type','application/json');
        $response = $response->withStatus(200);
        $response = $response->withJson($data);
        return $response;
      } catch (PDOException $e) {
        die($e->getMessage());
      } finally {
        // Destroy the database connection
        $conn = null;
      }
    });

    /**
     * This method deletes a specific message by its id.
     * @param Int $id - The quote id
     */
    $app->delete('/delete', function (Request $request, Response $response) {
      // Gets quote id
      $id = $request->getParam('id');

      // Gets the database connection
      $conn = PDOConnection::getConnection();

      try {
        // Delete the quote
        $sql = "DELETE FROM QUOTES WHERE ID_QUOTE=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        // Return the result
        $data['status'] = $result;
        $response = $response->withHeader('Content-Type','application/json');
        $response = $response->withStatus(200);
        $response = $response->withJson($data);
        return $response;
      } catch (PDOException $e) {
        die($e->getMessage());
      } finally {
        // Destroy the database connection
        $conn = null;
      }
    });

  });

?>
