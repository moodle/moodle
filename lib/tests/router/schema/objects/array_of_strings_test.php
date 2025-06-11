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
use core\router\schema\specification;
use core\tests\router\route_testcase;

/**
 * Tests for the an array of other objects.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\objects\array_of_strings
 * @covers     \core\router\schema\objects\type_base
 * @covers     \core\router\schema\openapi_base
 */
final class array_of_strings_test extends route_testcase {
    public function test_referenced_object(): void {
        $object = new array_of_strings();

        $schema = $object->get_openapi_description(new specification());
        $this->assertEquals((object) [
            'type' => 'object',
            'additionalProperties' => [
                'type' => 'string',
            ],
        ], $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_validation(): void {
        $object = new array_of_strings(
            keyparamtype: param::ALPHANUM,
            valueparamtype: param::INT,
        );

        $data = $object->validate_data([
            'example' => 123,
            'other' => 321,
        ]);

        $this->assertCount(2, $data);
        $this->assertEquals([
            'example' => '123',
            'other' => '321',
        ], $data);
    }

    /**
     * Test tha the validate_data method throws an exception when the data is invalid.
     *
     * @dataProvider failed_validation_provider
     * @param param $keyparamtype
     * @param param $valueparamtype
     * @param array $data
     */
    public function test_validation_failures(
        param $keyparamtype,
        param $valueparamtype,
        array $data,
    ): void {
        $object = new array_of_strings(
            keyparamtype: $keyparamtype,
            valueparamtype: $valueparamtype,
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
                param::ALPHANUM,
                param::INT,
                [
                    'example' => 123,
                    'other' => 'threetwoone',
                ],
            ],
        ];
    }
}
