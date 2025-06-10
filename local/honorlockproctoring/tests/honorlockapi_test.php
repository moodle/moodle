<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_honorlockproctoring;

/**
 * Honorlock proctoring test for module.
 *
 * @package   local_honorlockproctoring
 * @copyright 2023 Honorlock (https://honorlock.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class honorlockapi_test extends \advanced_testcase {

    /**
     * @var honorlockapi Keeps the Honorlock Class.
     */
    protected $honorlockapi;

    /**
     * @var string Access Token
     */
    protected $accesstoken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI...";

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
        $this->honorlockapi = new honorlockapi();
    }

    /**
     * Test Creation of Honorlock Class returns Class Instance
     *
     * @covers \local_honorlockproctoring\honorlockapi
     */
    public function test_class_creation(): void {
        $honorlockapi = new honorlockapi();
        $reflection = new \ReflectionClass(get_class($honorlockapi));
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        $configvalue = $property->getValue($honorlockapi);

        $this->assertInstanceOf(honorlockapi::class, $honorlockapi);
        $this->assertEquals($configvalue->honorlock_url, 'https://app.honorlock.com');
    }

    /**
     * Test the private send_request method with a GET request
     *
     * @covers \local_honorlockproctoring\honorlockapi::send_request
     */
    public function test_send_get_request(): void {
        $this->generate_token();

        $testresponse = (object)[
        "data" => [
          "iframe_src" => "https://app.honorlock.com/install/extension?locale=en",
          "extension_id" => "easrpoxsvfplyfubtodkzvtjezcsfqrz",
        ]];

        \curl::mock_response(json_encode($testresponse));

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('send_request');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->honorlockapi, ["get", "/api/en/v1/extension/check"]);

        $this->assertIsObject($result);
        $this->assertEquals((array)$result->data, $testresponse->data);
    }

    /**
     * Test the private send_request method with a POST request
     *
     * @covers \local_honorlockproctoring\honorlockapi::send_request
     */
    public function test_send_post_request() {
        $this->generate_token();

        $testresponse = (object)[
        "data" => [
          "event_type" => "string",
          "exam_taker_name" => "TestTaker",
          "created_at" => "2023-08-24T14:15:22Z",
        ],
        ];

        \curl::mock_response(json_encode($testresponse));

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('send_request');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->honorlockapi, ["post", "/api/en/v1/extension/check"]);

        $this->assertIsObject($result);
        $this->assertEquals((array)$result->data, $testresponse->data);
    }

    /**
     * Test the private send_request method with a bad request type
     *
     * @covers \local_honorlockproctoring\honorlockapi::send_request
     */
    public function test_send_bad_method_to_send_request_returns_null(): void {
        $this->generate_token();

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('send_request');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->honorlockapi, ["nonsense", "/api/en/v1/extension/check"]);

        $this->assertNull($result);
    }

    /**
     * Test the private get_token method
     *
     * @covers \local_honorlockproctoring\honorlockapi::get_token
     */
    public function test_get_token(): void {
        $this->generate_token();

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('get_token');
        $method->setAccessible(true);

        $result = $method->invoke($this->honorlockapi);
        $this->assertIsString($result);
    }

    /**
     * Test the private get_token method
     *
     * @covers \local_honorlockproctoring\honorlockapi::get_token
     */
    public function test_get_token_returns_early_with_no_token(): void {

        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('get_token');
        $method->setAccessible(true);

        $testresponse = (object)[
        "data" => null,
        ];

        \curl::mock_response(json_encode($testresponse));

        $result = $method->invoke($this->honorlockapi);

        $this->assertIsString($result);
    }

    /**
     * Test the private generate_token method
     *
     * @covers \local_honorlockproctoring\honorlockapi::generate_token
     */
    public function test_generate_token(): void {
        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('generate_token');
        $method->setAccessible(true);

        $accesstoken = "eyJ0fXAiOiJKV1QiLCJhbGciOiJSUzI...";

        $testresponse = (object)[
            "data" => [
                "access_token" => $accesstoken,
                "expires_in" => 86000,
            ],
        ];

        \curl::mock_response(json_encode($testresponse));

        $result = $method->invoke($this->honorlockapi);

        $this->assertIsObject($result);
        $this->assertEquals($result->access_token, $accesstoken);
    }

    /**
     * Test the private generate_token method
     *
     * @covers \local_honorlockproctoring\honorlockapi::generate_token
     */
    public function test_generate_token_returns_early_with_no_token(): void {
        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('generate_token');
        $method->setAccessible(true);

        $testresponse = (object)[
            [
                "message" => "failed to retrieve token",
            ],
        ];

        \curl::mock_response(json_encode($testresponse));

        $result = $method->invoke($this->honorlockapi);

        $this->assertNull($result);
    }

    /**
     * Function to Generate the Token that will be cached
     *
     */
    private function generate_token() : void {
        $reflection = new \ReflectionClass(get_class($this->honorlockapi));
        $method = $reflection->getMethod('get_token');
        $method->setAccessible(true);

        $tokenresponse = (object)[
        "data" => [
        "access_token" => $this->accesstoken,
        "expires_in" => 86000,
        ],
        ];

        \curl::mock_response(json_encode($tokenresponse));

        $method->invoke($this->honorlockapi);
    }
}
