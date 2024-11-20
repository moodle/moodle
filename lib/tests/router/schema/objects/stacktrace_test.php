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

use core\router\schema\specification;
use core\tests\route_testcase;

/**
 * Tests for a stacktrace.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\objects\stacktrace
 * @covers     \core\router\schema\objects\type_base
 */
final class stacktrace_test extends route_testcase {
    public function test_referenced_object(): void {
        $object = new stacktrace();

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('type', $schema);
        $this->assertObjectHasProperty('items', $schema);
        $this->assertObjectHasProperty('examples', $schema);
        $this->assertEquals('array', $schema->type);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_validation(): void {
        $object = new stacktrace();

        // This should return whatever was input.
        $data = $object->validate_data([
            'example' => 123,
            'other' => 321,
        ]);

        $this->assertCount(2, $data);
        $this->assertEquals([
            'example' => 123,
            'other' => 321,
        ], $data);
    }
}
