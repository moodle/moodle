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

use Packback\Lti1p3\Interfaces\IHttpResponse;

/**
 * Tests for the http_exception class.
 *
 * @package enrol_lti
 * @copyright 2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\lib\http_exception
 */
class http_exception_test extends \basic_testcase {

    /**
     * Test constructor and getters for a range of inputs.
     *
     * @dataProvider exception_data_provider
     * @param array $args the arguments to the exception constructor.
     * @covers ::__construct
     */
    public function test_exception(array $args) {

        $exception = new http_exception(...array_values($args));
        $this->assertInstanceOf(IHttpResponse::class, $exception->getResponse());
        if (isset($args['message'])) {
            $this->assertEquals($args['message'], $exception->getMessage());
        }
        if (isset($args['code'])) {
            $this->assertEquals($args['code'], $exception->getCode());
        }
        if (isset($args['throwable'])) {
            $this->assertEquals($args['throwable'], $exception->getPrevious());
        }
    }

    /**
     * Data provider for testing http_exception instances.
     *
     * @return array the test case data.
     */
    public function exception_data_provider() {
        return [
            'With only the response object' => [
                'args' => [
                    'response' => new http_response(
                        ['body' => '', 'headers' => ['Content-Type' => 'application/json']],
                        401
                    )
                ]
            ],
            'With response and message, code and throwable omitted' => [
                'args' => [
                    'response' => new http_response(
                        ['body' => '', 'headers' => ['Content-Type' => 'application/json']],
                        401
                    ),
                    'message' => 'HTTP error: 401 Unauthorised'
                ]
            ],
            'With response, message and code, throwable omitted' => [
                'args' => [
                    'response' => new http_response(
                        ['body' => '', 'headers' => ['Content-Type' => 'application/json']],
                        401
                    ),
                    'message' => 'HTTP error: 401 Unauthorised',
                    'code' => 401
                ]
            ],
            'With response, message, code, throwable' => [
                'args' => [
                    'response' => new http_response(
                        ['body' => '', 'headers' => ['Content-Type' => 'application/json']],
                        401
                    ),
                    'message' => 'HTTP error: 401 Unauthorised',
                    'code' => 401,
                    'throwable' => new \Exception('another exception')
                ]
            ]
        ];
    }
}
