<?php

	defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
	defined('SITE_ROOT') ? null : define('SITE_ROOT', 'D:'.DS.'codebase'.DS.'php'.DS.'apes');
	
	defined('DB_SERVER') ? null : define('DB_SERVER', 'localhost');
	defined('DB_USERNAME') ? null : define('DB_USERNAME', 'root');
	defined('DB_PASSWORD') ? null : define('DB_PASSWORD', '');
	defined('DB_NAME') ? null : define('DB_NAME', 'apes_db');

	defined('ALLOWED_TYPE') ? ALLOWED_TYPE : define('ALLOWED_TYPE', ['jpg','jpeg','png','csv']);
	defined('TARGET_DIR') ? TARGET_DIR : define('TARGET_DIR', SITE_ROOT.DS."uploads".DS);
	defined('UPLOAD_ERR_OK') ? UPLOAD_ERR_OK : define('UPLOAD_ERR_OK', 1);

	//  database related constants

	defined('TBL_PRIMARY') ? TBL_PRIMARY : define('TBL_PRIMARY', 'primary_schools');
	defined('TBL_SECONDARY') ? TBL_SECONDARY : define('TBL_SECONDARY', 'secondary_schools');
	defined('TBL_USERS') ? TBL_USERS : define('TBL_USERS', 'users');
	defined('TBL_REGIONS') ? TBL_REGIONS : define('TBL_REGIONS', 'regions');
	defined('TBL_DISTRICTS') ? TBL_DISTRICTS : define('TBL_DISTRICTS', 'districts');
	defined('TBL_CSV') ? TBL_CSV : define('TBL_CSV', 'tbl_csv');
	defined('TBL_PHOTO') ? TBL_PHOTO : define('TBL_PHOTO', 'tbl_photos');


	function openConnection() {
	  $instance = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
	  $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	  return $instance;
	}