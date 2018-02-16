<?php

	$app = new \Slim\App($appSettings);

	$container = $app->getContainer();

	// view renderer
	$container['renderer'] = function ($c) {
		$settings = $c->get('settings')['renderer'];
		return new Slim\Views\PhpRenderer($settings['template_path']);
	};

	// monolog for logging purpose
	$container['logger'] = function ($c) {
		$settings = $c->get('settings')['logger'];
		$logger = new Monolog\Logger($settings['name']);
		$logger->pushProcessor(new Monolog\Processor\UidProcessor());
		$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
		return $logger;
	};

	
	//Override the default Not Found Handler
	$container['notFoundHandler'] = function ($c) {
		return function ($request, $response) use ($c) {

			return $c['response']->withStatus(404)
			->withHeader('Content-Type', 'text/html')
			->write('Request not recognized. Please check your request URL for typo or un-recognized parameters or the URL ending with / ');
		};
	};

	//load the container with controller object
	$container['ApiFeedController'] = function ($c) {
		return new ApiFeedController($c);
	};

?>