# PHP Wrapper REST Api

This is a wrapper REST api developed in php. The api provides customized end points arround an external api to fetch  Safety Ratings for vehicles. The backend programs communicate with the external API to fetch Safety Rating data for given search parameters.

This application uses the latest Slim Framework v3 with the PHP-View template renderer and Guzzle as http client. It also uses the Monolog logger.


## Installing the Application

* Download or Clone the repository from http://github.com/giridharholla/php-wrest-api
* Extract the .zip file into webroot folder of your local webserver, if you download the .zip file.
* Open command propmt and move to the folder `php-wrest-api`

Run this command from the directory `php-wrest-api` to install required dependent packages.

	composer install


## Basic Usage
Use postman rest client or any other rest client to access API end points. 
Can find postman rest client <a href="https://www.getpostman.com/postman" target="_new">here</a> or you can find Google Chrome extension <a href="https://chrome.google.com/webstore/detail/postman/fhbjgbiflinjbdggehcddcbncdddomop?hl=en" target="_new">here</a>


#### About the base url of API in localhost 
* If your local web server is running on default port (80), url is http://localhost/php-wrest-api/vehicles ...
* If your local web server is running on other port eg. 8080, url is http://localhost:8080/php-wrest-api/vehicles ...
* If your local web server does not support mod_rewrite, use this pattern http://localhost:8080/php-wrest-api/index.php/vehicles ...


### Endpoint 1

```
GET http://localhost/php-wrest-api/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>

```

The `php-wrest-api` will respond with the JSON data as listed below if there are results for the given parameters

```
{
    Count: <NUMBER OF RESULTS>,
    Results: [
        {
            Description: "<VEHICLE DESCRIPTION>",
            VehicleId: <VEHICLE ID>
        },
        {
            Description: "<VEHICLE DESCRIPTION>",
            VehicleId: <VEHICLE ID>
        },
        {
            Description: "<VEHICLE DESCRIPTION>",
            VehicleId: <VEHICLE ID>
        },
        {
            Description: "<VEHICLE DESCRIPTION>",
            VehicleId: <VEHICLE ID>
        }
    ]
}
```
Below JSON [`empty-response`] will be displayed in case of no result for the given parameters.

```
{
    Count: 0,
    Results: []
}
```
Where:

* `<MODEL YEAR>`, `<MANUFACTURER>` and `<MODEL>` are variables that are used when calling the API.  
Example values for these are:
    * `<MODEL YEAR>`: 2015
    * `<MANUFACTURER>`: Audi
    * `<MODEL>`: A3
* `<NUMBER OF RESULTS>` is the number of records returned from the API and is an integer
* `<VEHICLE DESCRIPTION>` is the name of the vehicle model returned from the API and is a string


### End Point 2

```
POST http://localhost/php-wrest-api/vehicles

```
This end point accepts input only in POST method. Search parameters can be submitted through REST CLIENT using post method. The end point expects one or many of the following search parameters.

* `<MODEL YEAR>`, `<MANUFACTURER>` and `<MODEL>`

Example json data inputs:

```
{
    "modelYear": 2015,
    "manufacturer": "Audi",
    "model": "A3"
}
```

<b>Note:</b> End Point 2 using post method will respond to the input query in same way as End point 1. Output json structure of End Point 1 holds good for End point 2.

### End Point 3

```
GET http://localhost/php-wrest-api/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>?withRating=true

```
This endpoint accepts accepts an additional parameter `withRating` as query string along with `<MODEL YEAR>`, `<MANUFACTURER>` and `<MODEL>`.The value of parameter 'withRating' suggests the API to fetch or not to fetch Crash Rating data for the searched vehicles. API fetches the Crash Rating data only when 'withRating' value is set to `true` while requesting.

Endpoint 3 returns the same JSON specified in Endpoint 1, but with an additional attribute `CrashRating` for each car model.

Example input and output for Endpoint 3

```
INPUT http://localhost/php-wrest-api/vehicles/2015/Audi/A3?withRatiing=true

```
OUTPUT / RESPONSE from API

```
{
    Count: 4,
    Results: [
        {
            CrashRating: "5",
            Description: "2015 Audi A3 4 DR AWD",
            VehicleId: 9403
        },
        {
            CrashRating: "5",
            Description: "2015 Audi A3 4 DR FWD",
            VehicleId: 9408
        },
        {
            CrashRating: "Not Rated",
            Description: "2015 Audi A3 C AWD",
            VehicleId: 9405
        },
        {
            CrashRating: "Not Rated",
            Description: "2015 Audi A3 C FWD",
            VehicleId: 9406
        }
    ]
}
```

### PHPUnit Test Suite

Run this command in the application directory to run the test suite

	php composer.phar test  

If you have installed composer package 

	composer test



