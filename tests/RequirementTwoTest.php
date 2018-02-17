<?php
require_once __DIR__ .'/test-config.php';
require __DIR__ .'/../vendor/autoload.php';


class RequirementTwoTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client();
    }

    public function testPost_Vehicles_With_Valid_Parameters()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'Audi',
					'model'    => 'A3'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(4, $data['Count']);
    }

    public function testPost_Vehicles_For_Known_Results()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'Ford',
					'model'    => 'Fusion'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(2, count($data['Results']));
    }

    public function testPost_Vehicles_For_No_Results()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'Ford',
					'model'    => 'Crown Victoria'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(0, count($data['Results']));
    }

    public function testPost_Vehicles_With_Invalid_ModelYear()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => 'undefined',
					'manufacturer'     => 'Audi',
					'model'    => 'A3'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertEquals(0, $data['Count']);
    }

    public function testPost_Vehicles_With_Invalid_Make()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'undefined',
					'model'    => 'A3'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(0, count($data['Results']));
    }

    public function testPost_Vehicles_With_Invalid_Model()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'Audi',
					'model'    => 'undefined'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Results', $data);
        $this->assertEquals(27, count($data['Results']));
    }

    public function testPost_Vehicles_Without_Model_Parameter_Input()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '2015',
					'manufacturer'     => 'Audi',
					'model'    => ''
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertEquals(27, $data['Count']);
    }

    public function testPost_Vehicles_With_Special_Characters_In_ModelYear_Input()
    {
        $response = $this->client->post(
			UNIT_TEST_BASE_URL,
			[
				'json' => [
					'modelYear'    => '!@#$%^&*()+_',
					'manufacturer'     => 'Audi',
					'model'    => 'A3'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Count', $data);
        $this->assertEquals(0, $data['Count']);
    }

}

?>