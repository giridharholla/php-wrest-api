<?php
require_once __DIR__ .'/test-config.php';
require_once __DIR__ .'/../vendor/autoload.php';


class RequirementOneTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client();
    }

    public function testGet_Vehicles_With_Valid_Parameters()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/Audi/A3');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(4, $data['Count']);
    }

    public function testGet_Vehicles_For_Known_Results()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/Toyota/Yaris');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(2, count($data['Results']));
    }

    public function testGet_Vehicles_For_No_Results()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/Ford/Crown Victoria');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(0, count($data['Results']));
    }

    public function testGet_Vehicles_With_Invalid_ModelYear()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/undefined/Ford/Fusion');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertEquals(0, $data['Count']);
    }

    public function testGet_Vehicles_With_Invalid_Make()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/unefined/Fusion');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(0, count($data['Results']));
    }

    public function testGet_Vehicles_With_Invalid_Model()
    {
        $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/Ford/unefined');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertEquals(0, $data['Count']);
    }

    public function testGet_Vehicles_With_Invalid_URL()
    {
		$responseText = '';
		try{
		 $response = $this->client->get(UNIT_TEST_BASE_URL.'/2015/Ford/Fusion/');
		}catch(\GuzzleHttp\Exception\ClientException $gce){
			$responseText = $gce->getMessage();
		}
		//fwrite(STDERR, print($responseText));
        $this->assertContains('404', $responseText);
    }


}

?>