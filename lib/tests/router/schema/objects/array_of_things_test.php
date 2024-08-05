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

namespace core\router\schema\objects;

use core\param;
use core\router\schema\referenced_object;
use core\router\schema\specification;
use core\tests\route_testcase;

/**
 * Tests for the an array of other objects.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\objects\array_of_things
 * @covers     \core\router\schema\objects\type_base
 * @covers     \core\router\schema\openapi_base
 */
final class array_of_things_test extends route_testcase {
    public function test_referenced_object(): void {
        $object = new class ( // phpcs:ignore
            thingtype: 'integer',
            content: [
                'example' => new schema_object(content: []),
            ],
        ) extends array_of_things implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('type', $schema);
        $this->assertEquals('object', $schema->type);
        $this->assertObjectNotHasProperty('properties', $schema);
        $this->assertObjectHasProperty('additionalProperties', $schema);
        $this->assertArrayHasKey('type', $schema->additionalProperties);
        $this->assertEquals('integer', $schema->additionalProperties['type']);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_referenced_object_typebase(): void {
        $object = new class ( // phpcs:ignore
            thingtype: new scalar_type(param::INT),
        ) extends array_of_things implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertArrayHasKey('$ref', $schema->additionalProperties);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_basics(): void {
        $object = new array_of_things(
            thingtype: 'integer',
        );

        $schema = $object->get_openapi_description(new specification());
        $this->assertEquals((object) [
            'type' => 'object',
            'additionalProperties' => [
                'type' => 'integer',
            ],
        ], $schema);
    }


    /**
     * Test tha the validate_data method successfully validates content.
     *
     * @dataProvider successful_validation_provider
     * @param param|string|type_base|null $valueparamtype
     * @param mixed $data
     */
    public function test_validation_success(
        param|string|type_base|null $valueparamtype,
        mixed $data,
    ): void {
        $object = new array_of_things(
            $valueparamtype,
        );

        $result = $object->validate_data($data);
        $this->assertEquals($data, $result);
    }

    /**
     * Data provider for test_validation_success.
     *
     * @return array
     */
    public static function successful_validation_provider(): array {
        return [
            [
                param::INT,
                [
                    'example' => 123,
                    'other' => 321,
                    'more' => 5634543456543456,
                ],
            ],
            [
                'int',
                [
                    'example' => 123,
                    'other' => 321,
                    'more' => 5634543456543456,
                ],
            ],
            [
                new scalar_type(param::INT),
                [
                    'example' => 123,
                    'other' => 321,
                    'more' => 5634543456543456,
                ],
            ],
            [
                null,
                [
                    'example' => 123,
                    'other' => 321,
                    'more' => 'This is a string',
                ],
            ],
        ];
    }

    /**
     * Test tha the validate_data method throws an exception when the data is invalid.
     *
     * @dataProvider failed_validation_provider
     * @param param|string|type_base|null $valueparamtype
     * @param mixed $data
     */
    public function test_validation_failures(
        param|string|type_base|null $valueparamtype,
        mixed $data,
    ): void {
        $object = new array_of_things(
            $valueparamtype,
        );

        $this->expectException(\invalid_parameter_exception::class);
        $object->validate_data($data);
    }

    /**
     * Data provider for test_validation_failures.
     *
     * @return array
     */
    public static function failed_validation_provider(): array {
        return [
            [
                'int',
                [
                    'example' => 123,
                    'other' => 'threetwoone',
                ],
            ],
            [
                new scalar_type(param::INT),
                [
                    'example' => 123,
                    'other' => 'threetwoone',
                ],
            ],
            [
                param::INT,
                [
                    'example' => 123,
                    'other' => 'threetwoone',
                ],
            ],
            [
                param::INT,
                'string',
            ],
        ];
    }
}
