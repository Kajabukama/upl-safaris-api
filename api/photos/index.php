<?php
	
	use Slim\Http\Request;
	use Slim\Http\Response;

	require __DIR__ . '/../../vendor/autoload.php';
	require __DIR__ . '/../../include/controllers/photos.php';

	$app = new \Slim\App;
		
	$app->get('/', 'allPhoto');
	$app->post('/photo-upload', 'photo_upload');
	$app->post('/csv-upload', 'csv_upload');

	$app->get('/pdf-generate', 'generate_pdf');
	
	// start slimApi
	$app->run();
