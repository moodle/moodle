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

namespace core\router\schema;

use core\param;
use core\router\route;
use core\router\schema\objects\array_of_strings;
use core\router\schema\objects\schema_object;
use core\router\schema\specification;
use core\tests\router\route_testcase;

/**
 * Tests for parameters.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\parameter
 * @covers     \core\router\schema\openapi_base
 */
final class parameter_test extends route_testcase {
    public function test_basics(): void {
        $param = new parameter(
            name: 'examplename',
            in: 'examplein',
        );
        $this->assertEquals('examplename', $param->get_name());
        $this->assertEquals('examplein', $param->get_in());

        // The default behaviour is for a parameter to not be required if it was not explicitly specified.
        $this->assertFalse($param->is_required(new route()));
    }

    /**
     * Test for required default.
     *
     * @dataProvider required_default_provider
     * @param array $params
     */
    public function test_required_default(array $params): void {
        $this->expectException(\coding_exception::class);
        new parameter(...$params);
    }

    /**
     * Data provider for required default tests.
     *
     * @return array
     */
    public static function required_default_provider(): array {
        return [
            [
                [
                    'name' => 'example',
                    'in'  => parameter::IN_PATH,
                    'required' => true,
                    'default' => 0,
                ],
                [
                    'name' => 'example',
                    'in'  => parameter::IN_PATH,
                    'required' => true,
                    'default' => false,
                ],
                [
                    'name' => 'example',
                    'in'  => parameter::IN_PATH,
                    'required' => true,
                    'default' => "",
                ],
            ],
        ];
    }

    public function test_get_type(): void {
        $param = new parameter(
            name: 'examplename',
            in: 'examplein',
            type: param::ALPHANUMEXT,
        );
        $this->assertEquals(param::ALPHANUMEXT, $param->get_type());
    }

    /**
     * Test for is_required.
     *
     * @dataProvider is_required_provider
     * @param null|bool $required
     * @param bool $expected
     */
    public function test_is_required(?bool $required, bool $expected): void {
        $param = new parameter(
            name: 'example',
            in: parameter::IN_HEADER,
            required: $required,
        );

        $this->assertSame($expected, $param->is_required(new route()));
    }

    /**
     * Data provider for is_required tests.
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

    public function test_example(): void {
        $example = new example('examplevalue');
        $param = new parameter(
            name: 'example',
            in: 'header',
            type: param::INT,
            example: $example,
        );
        $description = $param->get_openapi_description(new specification());
        $this->assertArrayHasKey('examplevalue', $description->examples);
    }

    public function test_examples(): void {
        $example = new example('examplevalue');
        $param = new parameter(
            name: 'example',
            in: 'header',
            type: param::INT,
            examples: [$example],
        );
        $description = $param->get_openapi_description(new specification());
        $this->assertArrayHasKey('examplevalue', $description->examples);
    }

    public function test_example_and_examples(): void {
        $example = new example('examplevalue');
        $this->expectException(\coding_exception::class);
        new parameter(
            name: 'example',
            in: 'header',
            type: param::INT,
            example: $example,
            examples: [$example],
        );
    }

    public function test_schema(): void {
        $schema = new schema_object(
            content: [
                new array_of_strings(),
            ],
        );
        $param = new parameter(
            name: 'example',
            in: 'header',
            type: param::INT,
            schema: $schema,
        );
        $description = $param->get_openapi_description(new specification());
        $this->assertNotNull($description->schema);
        $this->assertEquals('object', $description->schema->type);
    }

    public function test_schema_includes_clientside_pattern(): void {
        $param = new parameter(
            name: 'example',
            in: 'header',
            type: param::ALPHANUM,
        );
        $description = $param->get_openapi_description(new specification());
        $this->assertNotNull($description->schema);
        $this->assertEquals('string', $description->schema->type);
        $this->assertIsString($description->schema->pattern);
    }
}
