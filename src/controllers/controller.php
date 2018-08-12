<?php

namespace src\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class MainController
{

	/**
	 * This method is used for testing the api.<br/>
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response $response
	 */
	public function hello(Request $request, Response $response)
	{
		// Gets name
		$name = $request->getAttribute("name");

		// Return the result
		$response = $response->getBody()->write("Hello, {$name}");
		return $response;
	}

	/**
	 * This method gets a user into the database.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response $response
	 */
	public function login(Request $request, Response $response)
	{
		// Gets username and password
		$user = $request->getAttribute("user");
		$pass = $request->getAttribute("password");

		// Example credentials
		$DUMMY_CREDENTIALS = array(
			// The MD5 function is not recommended to use this function to secure passwords
			"testUser" => md5("testPwd")
		);

		// If user exist
		if (array_key_exists($user, $DUMMY_CREDENTIALS)) {
			// If password is correct
			if (md5($pass) == $DUMMY_CREDENTIALS[$user]) {
				// Create a new resource
				$data["status"] = "foo";
			}
		}

		if (!isset($data)) {
			// Login failed
			$data["status"] = "bar";
		}

		// Return the result
		$response = $response->withHeader('Content-Type', 'application/json');
		$response = $response->withStatus(201, 'Created');
		$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		return $response;
	}

	/**
	 * This method publish short text messages of no more than 120 characters.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response $response
	 */
	public function post(Request $request, Response $response)
	{
		// Gets quote
		$quote = $request->getParam("quote");

		// Check the text
		if (strlen($quote) > 120) {
			$data["status"] = "foo";
		} else {
			$data["status"] = "bar";
		}

		// Return the result
		$response = $response->withHeader('Content-Type', 'application/json');
		$response = $response->withStatus(200, 'OK');
		$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		return $response;
	}

	/**
	 * Magic method __invoke
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return void
	 */
	public function __invoke(Request $request, Response $response)
	{
		return $response();
	}
}

?>