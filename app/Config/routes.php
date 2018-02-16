<?php
	use Slim\Http\Request;
	use Slim\Http\Response;
	use GuzzleHttp\Client;

	// All routes shall be defined in this file

	//route definition for get request which is handled by ApiFeedController
	$app->get('/vehicles[/{modelyear}[/{make}[/{model}]]]', 'ApiFeedController:handleGetRequest')->setName('api.get.vehicles');

	//route definition for post request which is handled by ApiFeedController
	$app->post('/vehicles', 'ApiFeedController:handlePostRequest')->setName('api.post.vehicles');


	$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
		// log message
		$this->logger->info("php-wrest-api '/' route or any other token");

		// Render index view to show an info page 
		return $this->renderer->render($response, 'index.phtml', $args);
	});	/**/
?>