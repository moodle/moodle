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

namespace core\router;

use core\router\schema\response\response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the response_validator.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\response_validator
 */
final class response_validator_test extends \advanced_testcase {
    public function test_validate_response_without_moodle_route(): void {
        $validator = new response_validator();

        $request = new ServerRequest('GET', 'http://example.com');
        $response = new \GuzzleHttp\Psr7\Response();
        $this->assertNull($validator->validate_response($request, $response));
    }

    public function test_validate_response_with_route_no_responses(): void {
        $validator = new response_validator();

        $route = new route(path: '/test');
        $request = (new ServerRequest('GET', 'http://example.com/test'))
            ->withAttribute(route::class, $route);
        $response = new \GuzzleHttp\Psr7\Response();
        $this->assertNull($validator->validate_response($request, $response));
    }

    public function test_validate_response_with_route_and_response(): void {
        $validator = new response_validator();

        $routeresponse = new class extends response {
            // phpcs:ignore
            public function __construct() {
                parent::__construct(
                    statuscode: 200,
                    description: 'Test response',
                );
            }

            // phpcs:ignore
            public function validate(
                ResponseInterface $response,
            ): void {
                throw new \Exception('Test exception');
            }
        };

        $route = new route(path: '/test', responses: [200 => $routeresponse]);
        $request = (new ServerRequest('GET', 'http://example.com/test'))
            ->withAttribute(route::class, $route);
        $response = new \GuzzleHttp\Psr7\Response();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test exception');
        $validator->validate_response($request, $response);
    }
}
