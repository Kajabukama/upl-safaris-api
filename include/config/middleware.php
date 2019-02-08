<?php

	$corsOptions = array(
	    "origin" => "*",
	    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
	    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
	);

	$cors = new \CorsSlim\CorsSlim($corsOptions);
	$app->add($cors);
