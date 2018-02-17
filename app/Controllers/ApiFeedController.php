<?php
/**
 * This is a Controller class is to handle php-wrest-api requests.
 *
 * The class handles requests of both types get and post. Based on the type and features, requests are delegated 
 * to this class from routes file. Handle request functions in this class implements the process of verifying 
 * the request, fetching data from an external API and sending response to the calling function.
 *
 * @category   POC
 * @package    php-wrest-api
 * @author     Original Author <giridhar.holla@gmail.com>
 * @copyright  
 * @license    
 */
	class ApiFeedController
	{
	   /**
	    * class variable to hold reference of container object.
	    *
	    * @var $container
	    */
	   protected $container;

	   /**
	    * Constructor function
		*
		* @param Container $ci
		*
		* @return void
		*/
	   public function __construct($ci) {
		   $this->container = $ci;
	   }

	   /**
	    * Function to handle all get requests of an end point
		*
		* This function handles get requests for end points Requirement 1 and Requirement 3. Function extracts
		* value of parameters from url token which are available in $args and validates. Http request uri 
		* constructed and sent to external api on recieving mandatory inputs. Http request sent via a http client 
		* object. Response from external api is validated and further processed before sending response to client
		* which has called this function.
		*
		* @param Request $request, the request object to hold input information
		* @param Response $response, the response object to hold output/response 
		* @param Array $args, holds arguments extracted from the incoming uri
		*
		* @return string $jsonResponse
		*/
	   public function handleGetRequest($request, $response, $args)
	   {
			$this->container->logger->info("handleGetRequest using guzzle...");
			
			//initialize a response object
			$data = $this->getEmptyResponse();

			//initialize vars for parameters
			$modelYear = null; $make = null; $vehicleModel = null; $withRating = false;

			//extract input values from args
			if(isset($args[API_REQUEST_PARAM_MODEL_YEAR])){ $modelYear = $args[API_REQUEST_PARAM_MODEL_YEAR]; }
			if(isset($args[API_REQUEST_PARAM_MODEL_MAKE])){ $make = $args[API_REQUEST_PARAM_MODEL_MAKE]; }
			if(isset($args[API_REQUEST_PARAM_MODEL])){ $vehicleModel = $args[API_REQUEST_PARAM_MODEL]; }

			//get the withRating param value from query string
			$withRating = $request->getParam('withRating');
			$withRating = filter_var($withRating, FILTER_VALIDATE_BOOLEAN);

			$params = array();
			
			//validate and prepare params array
			if($this->isValidToken($modelYear)){ $params[API_REQUEST_PARAM_MODEL_YEAR] = $modelYear; }
			if($this->isValidToken($make)){ $params[API_REQUEST_PARAM_MODEL_MAKE] = $make; }
			if($this->isValidToken($vehicleModel)){$params[API_REQUEST_PARAM_MODEL] = $vehicleModel; }

			//validate modelYear and proceed			
			if( $this->isValidToken($modelYear) && is_numeric($modelYear))
		    {
				//prepare external(NHTSA) api rquest uri using params
				$uri = $this->getApiRequestUri($params);
				$this->container->logger->info("to external api url ".$uri);

				// Create a http client 
				$client = new GuzzleHttp\Client();

				//send request to NHTSA
				$apiResponse = $client->get($uri);

				try{
					// get josn data from response
					$json =  $apiResponse->json();

					//populate response data
					if(!empty($json) && isset($json[API_RESPONSE_KEY_COUNT]))
					{
						$data[API_RESPONSE_KEY_COUNT] = $json[API_RESPONSE_KEY_COUNT];
					}
					if(!empty($json) && isset($json[API_RESPONSE_KEY_RESULTS]))
					{
						$data[API_RESPONSE_KEY_RESULTS] = $json[API_RESPONSE_KEY_RESULTS];
					}

					//check if api request is for crash rating as well
					if($withRating == true)
					{
						$data = $this->getCrashRatingData($client, $data);

					}//end if rating selected

					$this->container->logger->info("json data from external api ".json_encode($json));

				}catch(\GuzzleHttp\Exception\RequestException $e) {
					$this->container->logger->info("Error connecting to host ".$e->getMessage());
				}catch(\GuzzleHttp\Exception\ParseException $e){
					$this->container->logger->info("Error parsing json ".$e->getMessage());
				}catch(Exception $e) {
					$this->container->logger->info("Other Exception ".$e->getMessage());
				}
			}

 			return $response->withHeader('Content-type', 'application/json')->withJson( $data );	   

	   }//end of handleGetRequest()


	   /**
	    * Function to handle all POST requests of an end point
		*
		* This function handles POST requests for end points Requirement 2. Function extracts
		* value of parameters from request body and validates. Http request uri  constructed and sent to external
		* api on recieving mandatory inputs. Http request sent via a http client object. Response from 
		* external api is validated and further processed before sending response to client which has called this
		* function.
		*
		* @param Request $request, the request object to hold input information
		* @param Response $response, the response object to hold output/response 
		* @param Array $args, holds arguments extracted from the incoming uri
		*
		* @return string $jsonResponse
		*/
	   public function handlePostRequest($request, $response, $args)
	   {
			$this->container->logger->info("handlePostRequest using guzzle...");

			//initialize a response object
			$data = $this->getEmptyResponse();

			//extract paramter values from request
			$modelYear = $request->getParam('modelYear');
			$manufacturer = $request->getParam('manufacturer');
			$vehicleModel = $request->getParam(API_REQUEST_PARAM_MODEL);

			$params = array();

			//validate and prepare parms array
			if($this->isValidToken($modelYear)){ $params[API_REQUEST_PARAM_MODEL_YEAR] = $modelYear; }
			if($this->isValidToken($manufacturer)){ $params[API_REQUEST_PARAM_MODEL_MAKE] = $manufacturer; }
			if($this->isValidToken($vehicleModel)){$params[API_REQUEST_PARAM_MODEL] = $vehicleModel; }

			//validate modelYear parameter and proceed
			if( $this->isValidToken($modelYear) && is_numeric($modelYear))
		    {
				//prepare external api request uri from params
				$uri = $this->getApiRequestUri($params);
				$this->container->logger->info("to external api url ".$uri);

				// Create a http client
				$client = new GuzzleHttp\Client();

				//send http request to get data from external api
				$apiResponse = $client->get($uri);

				try{
					//extract json data from response
					$json =  $apiResponse->json();

					//populate response data
					if(!empty($json) && isset($json[API_RESPONSE_KEY_COUNT]))
					{
						$data[API_RESPONSE_KEY_COUNT] = $json[API_RESPONSE_KEY_COUNT];
					}
					if(!empty($json) && isset($json[API_RESPONSE_KEY_RESULTS]))
					{
						$data[API_RESPONSE_KEY_RESULTS] = $json[API_RESPONSE_KEY_RESULTS];
					}

					$this->container->logger->info("json data from external api ".json_encode($json));

				}catch(\GuzzleHttp\Exception\RequestException $e) {
					$this->container->logger->info("Error connecting to host ".$e->getMessage());
				}catch(\GuzzleHttp\Exception\ParseException $e){
					$this->container->logger->info("Error parsing json.".$e->getMessage());
				}catch(Exception $e) {
					$this->container->logger->info("Other Exception.".$e->getMessage());
				}
			}
			
			//return response data in json format
 			return $response->withHeader('Content-type', 'application/json')->withJson( $data );	   

	   }//end of handlePostRequest()

		/**
		  * Function to fetch Crash Rating information
		  *
		  * This function constructs external api request for fetching crash rating info. Function receives 
		  * crash rating info for each of the vehicle listed in the input array and populates the CrashRating field
		  * in each vehicle info object.
		  * 
		  *
		  * @param Array $data, contains json array of vehicle info 
		  * @param Client $client, GuzzleHttp client object 
		  *
		  * @return Array $data
		  */
		function getCrashRatingData($client, $data)
		{
			//check if response has results
			if($data[API_RESPONSE_KEY_COUNT] > 0 && count($data[API_RESPONSE_KEY_RESULTS]) > 0)
			{
				$vehicles = $data[API_RESPONSE_KEY_RESULTS];
				$count = count($vehicles);
				//$client = new GuzzleHttp\Client();

				//prepare NHTSA request and get data for each result
				for($i=0; $i < $count; $i++)
				{
					$item = $vehicles[$i];
					// do not send API request if ID is 0 or null
					if(isset($item[API_REQUEST_PARAM_VEHICLE_ID]) && !empty($item[API_REQUEST_PARAM_VEHICLE_ID]) )
					{
						//prepare NHTSA request if item has valid VehilceID
						$uri = $this->getApiRequestUri(
							array(API_REQUEST_PARAM_VEHICLE_ID => $item[API_REQUEST_PARAM_VEHICLE_ID])
							);
						
						//send request to get rating info
						$apiResponse = $client->get($uri);
						$this->container->logger->info("Rating URI ".$uri);
						$json =  $apiResponse->json();

						//check if json data has results
						if(!empty($json) && isset($json[API_RESPONSE_KEY_RESULTS]) && count($json[API_RESPONSE_KEY_RESULTS]) > 0 )
						{
							$results = $json[API_RESPONSE_KEY_RESULTS];

							$result = $results[0];

							//check if result has rating info
							if(isset($result[API_RESPONSE_KEY_CRASH_RATING_AVG]))
							{
								//update the vehicle info with crash rating obtained from result
								$item[API_RESPONSE_KEY_CRASH_RATING]= $result[API_RESPONSE_KEY_CRASH_RATING_AVG];
							}
							

							$vehicles[$i] = $item;								
						}//end if we get crash rating data

					}//end if valid vehicle id is not 0

				}//end of loop 
				
				//update data with the CrashRating info 
				$data[API_RESPONSE_KEY_RESULTS] = $vehicles;

			}//end if data has elements

			return $data;

		}//end of getCrashRatingData

		/**
		  * Function to construct uri.
		  *
		  * This function constructs external api request based on array of parameters given. encoded url
		  * will be constructed after validating each parameter.
		  * 
		  *
		  * @param Array $params, contains ke-value pairs of parameters submitted from client 
		  *
		  * @return string $uri
		  */
		function getApiRequestUri($params)
		{
			 $uri = '';
			 if( !empty($params) && count($params) > 0)
			 {
				$uri = EXTERNAL_API_BASE_URL; $count = 0;
				foreach($params as $key => $value)
				{
					//if($count > 0){ $uri .= '/'; }
					$uri .= '/'.$key.'/'.urlencode($this->stripSpecialCharacters($value));
					//$count+=1;
				}

				$uri .= '?format=json';
			 }

			 return ($uri);

		}

		/**
		  * Function to provide an empty json response data array.
		  *
		  *
		  * @return Array $data
		  */
		function getEmptyResponse()
		{
			$data = array(
				API_RESPONSE_KEY_COUNT => 0,
				API_RESPONSE_KEY_RESULTS => array()
			);
			return $data;
		}

		/**
		  * Function to validate a parameter for empty or undefined value.
		  *
		  *
		  * @return boolean $valid
		  */
		function isValidToken($token)
		{
			$valid = false;
			if(!empty($token) && $token != 'undefined'){ $valid = true; }
			return($valid);
		}

		/**
		  * Function to remove all special characters from the input.
		  *
		  *
		  * @return string $string
		  */
		function stripSpecialCharacters($string)
		{
			return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		}


	}//end of class
?>