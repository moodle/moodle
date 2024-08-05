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

namespace core\router\schema\parameters;

use core\param;
use core\router\route;
use core\router\schema\referenced_object;
use core\router\schema\specification;
use core\tests\route_testcase;
use invalid_parameter_exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Tests for the path parameter.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\parameter
 * @covers     \core\router\schema\parameters\path_parameter
 * @covers     \core\router\schema\openapi_base
 */
final class path_parameter_test extends route_testcase {
    public function test_in_path(): void {
        $param = new path_parameter(name: 'example');
        $this->assertEquals('path', $param->get_in());
        $this->assertEquals('example', $param->get_name());
    }

    /**
     * Test the is_required method.
     *
     * @dataProvider is_required_provider
     * @param string $path
     * @param bool $expected
     */
    public function test_is_required(string $path, bool $expected): void {
        $route = new route(
            path: $path,
        );
        $param = new path_parameter(name: 'value');
        $this->assertEquals($expected, $param->is_required($route));
    }

    /**
     * Data provider for the is_required method.
     *
     * @return array
     */
    public static function is_required_provider(): array {
        return [
            ['/is/required/{value}', true],
            ['/is/optional/[{value}]', false],
            ['/is/[optional/[{value}]]', false],
        ];
    }

    /**
     * Test fo the OPenAPI description in different configurations.
     *
     * @dataProvider openapi_required_values_provider
     * @param string $path If the a value is a required part of the path
     * @param bool $required If the value is expected to be required
     */
    public function test_get_openapi_description_required_values(
        string $path,
        bool $required,
    ): void {
        $param = new path_parameter(
            name: 'value',
            type: param::INT,
        );

        $api = new specification();
        $result = $param->get_openapi_description($api, $path);
        if ($required) {
            $this->assertNotNull($result);
            $this->assertTrue($result->required);
            $this->assertEquals('value', $result->name);
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * Data provider for OpenAPI Required values.
     *
     * @return array
     */
    public static function openapi_required_values_provider(): array {
        return [
            ['/is/required/{value}/with/children', true],
            ['/is/required/{value}', true],
            ['/is/optional', false],
        ];
    }

    /**
     * Ensure that a validation failure results in an invalid_parameter_exception.
     */
    public function test_validation_failure(): void {
        $param = new path_parameter(
            name: 'example',
            type: param::INT,
        );
        $value = "example";

        $request = $this->create_route(
            '/example/{example}',
            "/example/{$value}",
        );
        $route = $request->getAttribute(RouteContext::ROUTE);

        $this->expectException(invalid_parameter_exception::class);
        $param->validate($request, $route);
    }

    public function test_validation_success(): void {
        $param = new path_parameter(
            name: 'example',
            type: param::INT,
        );
        $value = 12345;

        $request = $this->create_route(
            '/example/{example}',
            "/example/{$value}",
        );
        $route = $request->getAttribute(RouteContext::ROUTE);

        $validatedresult = $param->validate($request, $route);
        $this->assertInstanceOf(ServerRequestInterface::class, $validatedresult);
    }

    public function test_referenced_object(): void {
        $object = new class (
            name: 'example',
            type: param::INT,
        ) extends path_parameter implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('schema', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('schema', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }
}
