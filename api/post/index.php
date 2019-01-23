<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	require '../../vendor/autoload.php';
	require_once '../../include/DBops/Database.php';

	$connect = new MySQLDatabase();
	$database = $connect->openConnection();

	$app = new \Slim\App;
		
	$app->get('/recent', function (Request $request, Response $response, array $args) {
		$response->getBody()->write("GET");
		return $response;
	});

	$app->get('/', function (Request $request, Response $response, array $args) use($database) {
		$query =  "SELECT * FROM users";
		$result = $database->query($query);
		$users = $result->fetchAll(PDO::FETCH_OBJ);
		return $response->withJson($users);
	});

	$app->post('/', function (Request $request, Response $response, array $args) {
		$body = $request->getParsedBody();
		return $response->withJson($body);
	});

	$app->put('/', function (Request $request, Response $response, array $args) {
		$body = $request->getParsedBody();
		return $response->withJson($body);
	});

	$app->delete('/{id}', function (Request $request, Response $response, array $args) {
		$id = $request->getAttribute('id');
		return $response->withJson($id);
	});
	
	// start slimApi
	$app->run();
