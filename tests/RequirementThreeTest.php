<?php
require_once __DIR__ .'/test-config.php';
require_once __DIR__ .'/../vendor/autoload.php';


class RequirementThreeTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client();
    }

    public function testGet_Vehicles_Without_Crash_Rating_Flag()
    {
        $response = $this->client->get(
			UNIT_TEST_BASE_URL.'/2015/Audi/A3'
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
		$firstItem = array();
		if(isset($data['Results']) && !empty($data['Results']))
		{
			$firstItem = $data['Results'][0];
		}

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertArrayNotHasKey('CrashRating', $firstItem);
        $this->assertEquals(4, $data['Count']);
    }

    public function testGet_Vehicles_With_Crash_Rating_True()
    {
        $response = $this->client->get(
			UNIT_TEST_BASE_URL.'/2015/Toyota/Yaris',
			[
				'query' => [
					'withRating' => 'true'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
		$firstItem = array();

		if(isset($data['Results']) && !empty($data['Results']))
		{
			$firstItem = $data['Results'][0];
		}

        $this->assertArrayHasKey('Results', $data);
        $this->assertArrayHasKey('CrashRating', $firstItem);
        $this->assertEquals(2, count($data['Results']));
    }

    public function testGet_Vehicles_With_Crash_Rating_False()
    {
        $response = $this->client->get(
			UNIT_TEST_BASE_URL.'/2015/Audi/A3',
			[
				'query' => [
					'withRating' => 'false'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
		$firstItem = array();
		if(isset($data['Results']) && !empty($data['Results']))
		{
			$firstItem = $data['Results'][0];
		}

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertArrayNotHasKey('CrashRating', $firstItem);
        $this->assertEquals(4, $data['Count']);
    }

    public function testGet_Vehicles_With_Invalid_Crash_Rating_Input()
    {
        $response = $this->client->get(
			UNIT_TEST_BASE_URL.'/2015/Audi/A3',
			[
				'query' => [
					'withRating' => 'bananas'
				]
			]
		);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
		$firstItem = array();
		if(isset($data['Results']) && !empty($data['Results']))
		{
			$firstItem = $data['Results'][0];
		}

        $this->assertArrayHasKey('Count', $data);
        $this->assertArrayHasKey('Results', $data);
        $this->assertArrayNotHasKey('CrashRating', $firstItem);
        $this->assertEquals(4, $data['Count']);
    }


}

?>