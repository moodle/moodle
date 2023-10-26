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

namespace enrol_lti\local\ltiadvantage\lib;

/**
 * Tests for the http_response class.
 *
 * @package enrol_lti
 * @copyright 2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\lib\http_response
 */
class http_response_test extends \basic_testcase {

    /**
     * Test constructor and getters for a range of inputs.
     *
     * @dataProvider response_data_provider
     * @param array $payload the array of header and body payload data.
     * @param int $status the int status of the http response.
     * @covers ::__construct
     */
    public function test_response(array $payload, int $status) {
        $response = new http_response($payload, $status);
        $this->assertEquals($payload['headers'], $response->getHeaders());
        $this->assertEquals($payload['body'], $response->getBody());
        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * Data provider for testing http_response instances.
     *
     * @return array the test case data.
     */
    public function response_data_provider() {
        return [
            'valid headers and body' => [
                'payload' => [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => '{"something": true}'
                ],
                'httpstatus' => 200
            ],
            'valid headers with empty body' => [
                'payload' => [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => ''
                ],
                'httpstatus' => 200
            ],
            'valid, no headers or body' => [
                'payload' => [
                    'headers' => [],
                    'body' => ''
                ],
                'httpstatus' => 200
            ],
            'valid headers, empty body, non-200 response status' => [
                'payload' => [
                    'headers' => ['httpstatus' => 'HTTP/1.1 401 Unauthorised: message ', 'Content-Type' => 'application/json'],
                    'body' => ''
                ],
                'httpstatus' => 401
            ]
        ];
    }
}
