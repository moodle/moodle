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
use core\tests\route_testcase;

/**
 * Tests for examples.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\example
 * @covers     \core\router\schema\openapi_base
 */
final class example_test extends route_testcase {
    public function test_value(): void {
        $example = new example(
            name: 'First example',
            value: 'This is a value',
        );
        $schema = $example->get_openapi_schema(new specification());
        $this->assertEquals(
            'This is a value',
            $schema->value,
        );
        $this->assertObjectNotHasProperty('externalValue', $schema);
    }

    public function test_externalvalue(): void {
        $example = new example(
            name: 'First example',
            externalvalue: 'https://example.com/example/value'
        );
        $schema = $example->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('value', $schema);
        $this->assertEquals(
            'https://example.com/example/value',
            $schema->externalValue,
        );
    }

    public function test_value_or_externalvalue(): void {
        $this->expectException(\coding_exception::class);
        new example(
            name: 'First example',
            value: 'This is a value',
            externalvalue: 'This is an external value',
        );
    }

    public function test_basics(): void {
        $example = new example(
            name: 'First example',
            summary: 'This is a summary',
            description: 'This is a description',
            value: 'This is a value',
        );

        $schema = $example->get_openapi_schema(new specification());

        $this->assertEquals('First example', $example->get_name());
        $this->assertEquals('This is a summary', $schema->summary);
        $this->assertEquals('This is a description', $schema->description);
    }

    public function test_referenced_object(): void {
        $object = new class (
            name: 'example',
            value: 'Some value',
        ) extends example implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('value', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('value', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }
}
