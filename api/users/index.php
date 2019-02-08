<?php
	
	use Slim\Http\Request;
	use Slim\Http\Response;

	require __DIR__ . '/../../vendor/autoload.php';
	require __DIR__ . '/../../include/controllers/user.php';

	$app = new \Slim\App;
		
	$app->get('/recent', 'recentUsers');
	$app->get('/', 'allUsers');
	$app->post('/', 'createUser');
	$app->put('/', 'updateUser');
	$app->delete('/{id}', 'deleteUser');
	
	// start slimApi
	$app->run();
