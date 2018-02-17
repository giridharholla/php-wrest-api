<?php

	/**
	* Define all seeetings required by the app in a variable of type assosiative array.
	* @var array $appSettings
	**/

	$appSettings =  [
		'settings' => [
			'displayErrorDetails' => false, // set to false in production
			'addContentLengthHeader' => false, // Allow the web server to send the content-length header

			// Renderer settings
			'renderer' => [
				'template_path' => __DIR__ . '/../../templates/',
			],

			// Monolog settings
			'logger' => [
				'name' => 'php-wrest-api',
				'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../../logs/app.log',
				'level' => \Monolog\Logger::ERROR,
			],
		],
	];


	//constants defined for string literals used across app
	define('EXTERNAL_API_BASE_URL','https://one.nhtsa.gov/webapi/api/SafetyRatings');

	define('API_REQUEST_PARAM_MODEL_YEAR','modelyear');
	define('API_REQUEST_PARAM_MODEL_MAKE','make');
	define('API_REQUEST_PARAM_MODEL','model');
	define('API_REQUEST_PARAM_VEHICLE_ID','VehicleId');

	define('API_RESPONSE_KEY_COUNT','Count');
	define('API_RESPONSE_KEY_RESULTS','Results');
	define('API_RESPONSE_KEY_CRASH_RATING_AVG','OverallRating');
	define('API_RESPONSE_KEY_CRASH_RATING','CrashRating');



?>