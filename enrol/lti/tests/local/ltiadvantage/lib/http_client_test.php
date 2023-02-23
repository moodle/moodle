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
 * Tests for the http_client class.
 *
 * @package enrol_lti
 * @copyright 2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\lib\http_client
 */
class http_client_test extends \basic_testcase {

    /**
     * Verify the http_client delegates to curl during a "GET" request.
     *
     * @covers ::request
     */
    public function test_client_get_request() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $mockcurl = $this->createMock(\curl::class);
        $mockcurl->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('https://example.com'),
                $this->equalTo([]),
                $this->equalTo(['CURLOPT_HEADER' => 1])
            );
        $mockcurl->expects($this->any())
            ->method('get_info')
            ->willReturnCallback(function() {
                return ['header_size' => 0, 'http_code' => 200];
            });
        $mockcurl->expects($this->once())
            ->method('setHeader')
            ->with($this->equalTo(['someheader' => 'someheader: headervalue']));

        $client = new http_client($mockcurl);
        $client->request('GET', 'https://example.com', ['headers' => ['someheader' => 'headervalue']]);
    }

    /**
     * Verify the http_client delegates to curl during a "POST" request.
     *
     * @covers ::request
     */
    public function test_client_post_request() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $mockcurl = $this->createMock(\curl::class);
        $mockcurl->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('https://example.com'),
                $this->equalTo('examplebody'),
                $this->equalTo(['CURLOPT_HEADER' => 1])
            );
        $mockcurl->expects($this->any())
            ->method('get_info')
            ->willReturnCallback(function() {
                return ['header_size' => 0, 'http_code' => 200];
            });
        $mockcurl->expects($this->once())
            ->method('setHeader')
            ->with($this->equalTo(['someheader' => 'someheader: headervalue']));

        $client = new http_client($mockcurl);
        $client->request('POST', 'https://example.com', ['headers' => ['someheader' => 'headervalue'], 'body' => 'examplebody']);
    }

    /**
     * Test a few of the unsupported HTTP methods.
     *
     * @dataProvider unsupported_methods_provider
     * @param string $httpmethod the http method.
     * @covers ::request
     */
    public function test_request_unsupported_method(string $httpmethod) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $mockcurl = $this->createMock(\curl::class);

        $client = new http_client($mockcurl);
        $this->expectException(\Exception::class);
        $client->request($httpmethod, 'https://example.com', []);
    }

    /**
     * Data provider for testing unsupported http methods.
     *
     * @return array the test case data.
     */
    public function unsupported_methods_provider() {
        return [
            'head' => ['HEAD'],
            'put' => ['PUT'],
            'delete' => ['DELETE'],
        ];
    }
}
