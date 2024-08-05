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

use core\param;
use core\router\schema\parameters\path_parameter;
use core\router\schema\parameters\query_parameter;
use core\router\schema\request_body;
use core\router\schema\response\content\payload_response_type;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

/**
 * Tests for the request validator.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\request_validator
 */
final class request_validator_test extends route_testcase {
    /**
     * Request validation on a route which does not have a matching Moodle route attribute.
     */
    public function test_validate_request_not_moodle_route(): void {
        $request = new ServerRequest('GET', '/example');
        $validator = \core\di::get(request_validator::class);

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $validator->validate_request($request),
        );
    }

    /**
     * A basic test of request validation.
     */
    public function test_validate_request(): void {
        // The route being tested.
        $route = new route(
            path: '/example/{required}',
            pathtypes: [
                new path_parameter(
                    name: 'required',
                    type: param::INT,
                ),
            ],
        );
        $request = $this->get_request_for_routed_route($route, '/example/123');
        $validator = \core\di::get(request_validator::class);

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $validator->validate_request($request),
        );
    }

    /**
     * A basic test of request validation.
     */
    public function test_validate_request_missing_pathtype(): void {
        // A route with a parameter defined in the path, but no pathtype for it.
        $route = new route(
            path: '/example/{required}',
        );

        $request = $this->get_request_for_routed_route($route, '/example/123');

        $validator = \core\di::get(request_validator::class);
        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $validator->validate_request($request),
        );
    }

    /**
     * When a defined pathtype is missing from the path.
     */
    public function test_validate_request_missing_path_component(): void {
        // A route with a parameter defined in the path, but no pathtype for it.
        $route = new route(
            path: '/example/123',
            pathtypes: [
                new path_parameter(
                    name: 'required',
                    type: param::INT,
                ),
            ],
        );

        $request = $this->get_request_for_routed_route($route, '/example/123');

        $validator = \core\di::get(request_validator::class);
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches('/Route.*has 0 arguments.* 1 pathtypes./');
        $result = $validator->validate_request($request);

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $result,
        );
    }

    /**
     * When a pathtype fails to validate, it will result in an HttpNotFoundException.
     */
    public function test_validate_request_invalid_path_component(): void {
        // Most of the path types are converted to regexes and will lead to a 404 before they get this far.
        $type = param::INT;
        $this->assertEmpty(
            $type->get_clientside_expression(),
            'This test requires a type with no clientside expression. Please update the test.',
        );

        $route = new route(
            path: '/example/{required}',
            pathtypes: [
                new path_parameter(
                    name: 'required',
                    type: $type,
                ),
            ],
        );

        $request = $this->get_request_for_routed_route($route, '/example/abc');

        $validator = \core\di::get(request_validator::class);
        $this->expectException(HttpNotFoundException::class);
        $validator->validate_request($request);
    }

    /**
     * When a pathtype fails to validate, it will result in an HttpNotFoundException.
     */
    public function test_validate_request_invalid_path_component_native(): void {
        // Most of the path types are converted to regexes and will lead to a 404 before they get this far.
        $type = param::ALPHA;
        $this->assertNotEmpty(
            $type->get_clientside_expression(),
            'This test requires a type with clientside expression. Please update the test.',
        );

        $route = new route(
            path: '/example/{required}',
            pathtypes: [
                new path_parameter(
                    name: 'required',
                    type: $type,
                ),
            ],
        );

        // A value which does not meet the param validation.
        $request = $this->get_request_for_routed_route($route, '/example/123');

        $validator = \core\di::get(request_validator::class);
        $this->expectException(HttpNotFoundException::class);
        $validator->validate_request($request);
    }

    /**
     * Query parameter validation.
     */
    public function test_validate_request_query_parameter_valid(): void {
        $type = param::INT;
        $value = 123;

        $route = new route(
            path: '/example',
            queryparams: [
                new query_parameter(
                    name: 'required',
                    type: $type,
                ),
            ],
        );

        $request = $this->get_request_for_routed_route($route, "/example?required={$value}");
        $this->assertEquals($value, $request->getQueryParams()['required']);

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $validatedrequest = $validator->validate_request($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $validatedrequest);
        $this->assertEquals($value, $validatedrequest->getQueryParams()['required']);
    }

    /**
     * Query parameter validation failure.
     */
    public function test_validate_request_query_parameter_invalid(): void {
        $type = param::INT;
        $value = 'abc';

        $route = new route(
            path: '/example',
            queryparams: [
                new query_parameter(
                    name: 'required',
                    type: $type,
                ),
            ],
        );

        $request = $this->get_request_for_routed_route($route, "/example?required={$value}");
        $this->assertEquals($value, $request->getQueryParams()['required']);

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $this->expectException(\invalid_parameter_exception::class);
        $validator->validate_request($request);
    }

    /**
     * Validate a request body which is expected.
     */
    public function test_validate_request_body_valid(): void {
        $route = new route(
            path: '/example',
            requestbody: new request_body(
                content: new payload_response_type(
                    schema: new \core\router\schema\objects\schema_object(
                        content: [
                            'preferences' => new \core\router\schema\objects\array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::INT,
                            ),
                        ],
                    ),
                ),
            ),
        );

        $request = $this->get_request_for_routed_route($route, "/example");
        $request = $request->withParsedBody([
            'preferences' => [
                'key' => 42,
            ],
        ]);

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $result = $validator->validate_request($request);
    }

    /**
     * Validate a request body which is expected.
     */
    public function test_validate_request_body_invalid(): void {
        $route = new route(
            path: '/example',
            requestbody: new request_body(
                content: new payload_response_type(
                    schema: new \core\router\schema\objects\schema_object(
                        content: [
                            'preferences' => new \core\router\schema\objects\array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::INT,
                            ),
                        ],
                    ),
                ),
            ),
        );

        $request = $this->get_request_for_routed_route($route, "/example");
        $request = $request->withParsedBody([
            'preferences' => [
                'key' => 'value',
            ],
        ]);

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $this->expectException(\invalid_parameter_exception::class);
        $validator->validate_request($request);
    }

    /**
     * Validate a request body which is optional.
     */
    public function test_validate_request_body_missing_optional(): void {
        $route = new route(
            path: '/example',
            requestbody: new request_body(
                content: new payload_response_type(
                    schema: new \core\router\schema\objects\schema_object(
                        content: [
                            'preferences' => new \core\router\schema\objects\array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::INT,
                            ),
                        ],
                        required: false,
                    ),
                ),
            ),
        );

        $request = $this->get_request_for_routed_route($route, "/example");

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $result = $validator->validate_request($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $result);
        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $result,
        );
    }

    /**
     * Validate a request body which is expected.
     */
    public function test_validate_request_body_missing_required(): void {
        $route = new route(
            path: '/example',
            requestbody: new request_body(
                content: new payload_response_type(
                    schema: new \core\router\schema\objects\schema_object(
                        content: [
                            'preferences' => new \core\router\schema\objects\array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::INT,
                            ),
                        ],
                    ),
                ),
                required: true,
            ),
        );

        $request = $this->get_request_for_routed_route($route, "/example");

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $this->expectException(\invalid_parameter_exception::class);
        $validator->validate_request($request);
    }

    /**
     * Validate a request header is appropriately handled.
     */
    public function test_validate_request_header_valid(): void {
        $route = new route(
            path: '/example',
            headerparams: [
                new \core\router\schema\parameters\header_object(
                    name: 'Accept',
                    description: 'The media type of the response',
                    type: param::TEXT,
                ),
                new \core\router\schema\parameters\header_object(
                    name: 'X-Multiple',
                    description: 'A header with multiple values',
                    type: param::TEXT,
                    multiple: true,
                ),
            ],
        );

        $request = $this->get_request_for_routed_route($route, "/example")
            // A known header.
            ->withHeader('Accept', 'application/json')
            // An unknown header is kept.
            ->withHeader('X-Example', 'example')
            // A known header with multiple values.
            ->withAddedHeader('X-Multiple', 'value1')
            ->withAddedHeader('X-Multiple', 'value2')
            // An unknown header with multiple values.
            ->withAddedHeader('X-Unknown', 'value1')
            ->withAddedHeader('X-Unknown', 'value2');

        // Validate the request.
        $validator = \core\di::get(request_validator::class);
        $result = $validator->validate_request($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $result);

        $this->assertEquals('application/json', $result->getHeaderLine('Accept'));
        $this->assertEquals('example', $result->getHeaderLine('X-Example'));
        $this->assertEquals(['value1', 'value2'], $result->getHeader('X-Multiple'));
        $this->assertEquals(['value1', 'value2'], $result->getHeader('X-Unknown'));
    }
}
