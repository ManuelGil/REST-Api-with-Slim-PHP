<?php

use src\Controllers\MainController;

use PHPUnit\Framework\TestCase;
use Slim\Environment;

class ApiTest extends TestCase
{
	private $controller;

	/**
	 * Initial method
	 */
	public function setUp()
	{
		// Creates the controller
		$this->controller = new MainController();
	}

	/**
	 * testHello method
	 */
	public function hello()
	{
		$environment = \Slim\Http\Environment::mock([
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI' => '/public/webresources/unit_testing/hello/testUser',
			'SERVER_NAME' => 'localhost',
			'CONTENT_TYPE' => 'application/json;charset=utf8'
		]);
		$request = \Slim\Http\Request::createFromEnvironment($environment);
		$response = new \Slim\Http\Response();

		$response = $this->controller->__invoke($request, $response);
		$this->assertEquals('Hello, testUser', $response->getBody());
	}

	/**
	 * tesLogin method
	 */
	public function login()
	{
		$environment = \Slim\Http\Environment::mock([
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI' => '/public/webresources/unit_testing/login/testUser/testPwd',
			'SERVER_NAME' => 'localhost',
			'CONTENT_TYPE' => 'application/json;charset=utf8'
		]);
		$request = \Slim\Http\Request::createFromEnvironment($environment);
		$response = new \Slim\Http\Response();

		$response = $this->controller->__invoke($request, $response);
		$this->assertEquals('{ "status": "foo" }', $response->getBody());
	}

	/**
	 * testPost method
	 */
	public function post()
	{
		$environment = \Slim\Http\Environment::mock([
			'REQUEST_METHOD' => 'POST',
			'REQUEST_URI' => '/public/webresources/unit_testing/post',
			'QUERY_STRING' => 'quote=abc',
			'SERVER_NAME' => 'localhost',
			'CONTENT_TYPE' => 'application/json;charset=utf8',
			'HTTP_AUTHORIZATION' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJoZWFkZXIiOnsiaWQiOiIwIiwidXNlciI6Ik1hbnVlbEdpbCJ9LCJwYXlsb2FkIjp7ImlhdCI6IjIwMTktMDEtMDEgMDA6MDA6MDAiLCJleHAiOiIyMDIwLTAxLTAxIDAwOjAwOjAwIn19.p_PtmXsDe3l_osPEzb4edkf-M2SCdUVBQBKs8ZAbpn8'
		]);
		$request = \Slim\Http\Request::createFromEnvironment($environment);
		$response = new \Slim\Http\Response();

		$response = $this->controller->__invoke($request, $response);
		$this->assertEquals('{ "status": "foo" }', $response->getBody());
	}

}

?>