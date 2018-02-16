<?php

	if (PHP_SAPI == 'cli-server') {
		// To help the built-in PHP dev server, check if the request was actually for
		// something which should probably be served as a static file
		$url  = parse_url($_SERVER['REQUEST_URI']);
		$file = __DIR__ . $url['path'];
		if (is_file($file)) {
			return false;
		}
	}

	session_start();

	require __DIR__ . '/vendor/autoload.php';

	require __DIR__ . '/app/Config/config.php';

	require __DIR__ . '/app/Controllers/ApiFeedController.php';

	require __DIR__ . '/app/Config/bootstrap.php';

	require __DIR__ . '/app/Config/routes.php';

	// Run app
	$app->run();

?>
