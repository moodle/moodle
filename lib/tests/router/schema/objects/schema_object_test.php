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

use core\router\schema\referenced_object;
use core\router\schema\specification;
use core\tests\route_testcase;

/**
 * Tests for the schema_object.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\objects\schema_object
 * @covers     \core\router\schema\openapi_base
 */
final class schema_object_test extends route_testcase {
    public function test_referenced_object(): void {
        $object = new class (
            content: [
                'example' => new schema_object(content: []),
            ],
        ) extends schema_object implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('type', $schema);
        $this->assertObjectHasProperty('properties', $schema);
        $this->assertObjectHasProperty('example', $schema->properties);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_basics(): void {
        $object = new schema_object(
            content: [
                'example' => new schema_object(content: []),
            ],
        );

        $this->assertFalse($object->has('missing'));
        $this->assertTrue($object->has('example'));

        $this->assertInstanceOf(schema_object::class, $object->get('example'));
    }

    public function test_invalid_content(): void {
        $this->expectException(\coding_exception::class);

        new schema_object(content: ['invalid']);
    }

    public function test_validation(): void {
        $object = new schema_object(
            content: [
                'example' => new schema_object(content: [
                    'somekey' => new schema_object(content: []),
                ]),
            ],
        );

        // Nothing in, nothing out.
        $this->assertEmpty($object->validate_data([]));

        // Surplus data is ignored.
        $this->assertEmpty($object->validate_data(['surplus' => []]));

        // Valid data is included.
        $data = $object->validate_data(['example' => []]);
        $this->assertArrayHasKey('example', $data);
        $this->assertIsArray($data['example']);

        // Valid data is included and surplus data is removed.
        $data = $object->validate_data(['example' => [], 'surplus' => []]);
        $this->assertArrayHasKey('example', $data);
        $this->assertIsArray($data['example']);
        $this->assertArrayNotHasKey('surplus', $data);

        // Applied to nested values.
        $data = $object->validate_data(['example' => ['somekey' => []]]);
        $this->assertArrayHasKey('example', $data);
        $this->assertIsArray($data['example']);
        $this->assertArrayHasKey('somekey', $data['example']);
    }
}
