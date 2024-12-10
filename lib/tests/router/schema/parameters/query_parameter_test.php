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
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the query parameters.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\parameter
 * @covers     \core\router\schema\parameters\query_parameter
 * @covers     \core\router\schema\openapi_base
 */
final class query_parameter_test extends route_testcase {
    public function test_in_path(): void {
        $param = new query_parameter(name: 'example');
        $this->assertEquals('query', $param->get_in());
        $this->assertEquals('example', $param->get_name());
    }

    /**
     * Test the is_required method.
     *
     * @dataProvider is_required_provider
     * @param bool|null $required
     * @param bool $expected
     */
    public function test_is_required(?bool $required, bool $expected): void {
        $param = new query_parameter(
            name: 'value',
            required: $required,
        );
        $this->assertEquals($expected, $param->is_required(new route()));
    }

    /**
     * Data provider for the is_required method.
     *
     * @return array
     */
    public static function is_required_provider(): array {
        return [
            [true, true],
            [false, false],
            [null, false],
        ];
    }

    public function test_referenced_object(): void {
        $object = new class (
            name: 'example',
            type: param::INT,
        ) extends query_parameter implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('schema', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('schema', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    /**
     * Test for the allowReserved property.
     *
     * @dataProvider allow_reserved_provider
     * @param bool|null $allowreserved
     * @param bool $expected
     */
    public function test_allow_reserved(
        ?bool $allowreserved,
        bool $expected,
    ): void {
        $param = new query_parameter(
            name: 'example',
            type: param::INT,
            allowreserved: $allowreserved,
        );

        $schema = $param->get_openapi_description(new specification());
        if ($expected) {
            $this->assertObjectHasProperty('allowReserved', $schema);
            $this->assertTrue($schema->allowReserved);
        } else {
            $this->assertObjectNotHasProperty('allowReserved', $schema);
        }
    }

    /**
     * Data provider ofr testing whether reserved characters are allowed.
     *
     * @return array
     */
    public static function allow_reserved_provider(): array {
        return [
            [true, true],
            [false, false],
            [null, false],
        ];
    }

    /**
     * Tests of the param validation.
     *
     * @dataProvider validation_provider
     * @param array $properties
     * @param array $params
     * @param array $expected
     */
    public function test_validation(
        array $properties,
        array $params,
        array $expected,
    ): void {
        $param = new query_parameter(...$properties);

        $request = new ServerRequest('GET', '/example');
        $request = $request->withQueryParams(array_merge(
            $request->getQueryParams(),
            $params,
        ));

        $newrequest = $param->validate($request, $request->getQueryParams());
        $this->assertEquals($expected, $newrequest->getQueryParams());
    }

    /**
     * Validation data provider.
     *
     * @return array
     */
    public static function validation_provider(): array {
        return [
            'Basic required param' => [
                [
                    'name' => 'example',
                    'type'  => param::INT,
                    'required' => true,
                ],
                [
                    'example' => 12345,
                    'otherfield' => 'abcde',
                ],
                [
                    'example' => 12345,
                    'otherfield' => 'abcde',
                ],
            ],
            'Basic optional param' => [
                [
                    'name' => 'example',
                    'type'  => param::INT,
                ],
                [
                    'example' => 12345,
                    'otherfield' => 'abcde',
                ],
                [
                    'example' => 12345,
                    'otherfield' => 'abcde',
                ],
            ],
            'Basic optional param not provided' => [
                [
                    'name' => 'example',
                    'type'  => param::INT,
                ],
                [
                    'otherfield' => 'abcde',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => null,
                ],
            ],
            'Basic optional param not provided with defaults' => [
                [
                    'name' => 'example',
                    'type'  => param::INT,
                    'default' => 999,
                ],
                [
                    'otherfield' => 'abcde',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => 999,
                ],
            ],
            'Special handling for a bool (true)' => [
                [
                    'name' => 'example',
                    'type'  => param::BOOL,
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => 'true',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => true,
                ],
            ],
            'Special handling for a bool (false)' => [
                [
                    'name' => 'example',
                    'type'  => param::BOOL,
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => 'false',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => false,
                ],
            ],
            'Special handling for a bool - not specified' => [
                [
                    'name' => 'example',
                    'type'  => param::BOOL,
                ],
                [
                    'otherfield' => 'abcde',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => null,
                ],
            ],
            'Special handling for a bool - default true' => [
                [
                    'name' => 'example',
                    'type'  => param::BOOL,
                    'default' => true,
                ],
                [
                    'otherfield' => 'abcde',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => true,
                ],
            ],
            'Special handling for a bool - default false' => [
                [
                    'name' => 'example',
                    'type'  => param::BOOL,
                    'default' => false,
                ],
                [
                    'otherfield' => 'abcde',
                ],
                [
                    'otherfield' => 'abcde',
                    'example' => false,
                ],
            ],
        ];
    }

    public function test_validation_boolean_failure(): void {
        $param = new query_parameter(
            name: 'example',
            type: param::BOOL,
        );

        $request = new ServerRequest('GET', '/example');
        $request = $request->withQueryParams(array_merge(
            $request->getQueryParams(),
            [
                'example' => 'notaboolean',
            ],
        ));

        $this->expectException(\ValueError::class);
        $param->validate($request, $request->getQueryParams());
    }

    public function test_validation_required_not_set(): void {
        $param = new query_parameter(
            name: 'example',
            type: param::BOOL,
            required: true,
        );

        $request = new ServerRequest('GET', '/example');

        $this->expectException(\coding_exception::class);
        $param->validate($request, $request->getQueryParams());
    }
}
