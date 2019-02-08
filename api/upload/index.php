<?php
	
	use Slim\Http\Request;
	use Slim\Http\Response;

	require __DIR__ . '/../../vendor/autoload.php';
	require __DIR__ . '/../../include/controllers/upload.php';

	$app = new \Slim\App;
		
	$app->get('/list-photos', 'select_photo');
	$app->get('/list-photos/{uid}', 'photo_by_uploader');

	$app->get('/list-csv/{uid}', 'list_students');

	// routes to upload csv, photos
	$app->post('/photo-upload', 'photo_upload');
	$app->post('/csv-upload', 'csv_upload');

	
	require __DIR__ . '/../../include/config/middleware.php';
	$app->run();
