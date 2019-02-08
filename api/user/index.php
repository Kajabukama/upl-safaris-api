<?php
	
	use Slim\Http\Request;
	use Slim\Http\Response;

	require __DIR__ . '/../../vendor/autoload.php';
	require __DIR__ . '/../../include/controllers/user.php';

	$app = new \Slim\App;

		
	$app->get('/', 'users');
	$app->get('/{id}', 'userById');
	$app->get('/email/{email}', 'userByEmail');
	$app->post('/create', 'createUser');
	$app->post('/authenticate', 'authenticate');
	$app->post('/upload', 'uploadPhoto');
	$app->put('/user/{id}', 'updateUser');
	$app->delete('/user/{id}', 'deleteUser');
	
	// start slimApi
	require __DIR__ . '/../../include/config/middleware.php';
	$app->run();
