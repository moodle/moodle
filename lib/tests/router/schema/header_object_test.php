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
use core\tests\route_testcase;

/**
 * Tests for header objects.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\header_object
 * @covers     \core\router\schema\parameters\header_object
 * @covers     \core\router\schema\openapi_base
 */
final class header_object_test extends route_testcase {
    public function test_in_path(): void {
        $param = new header_object(
            name: 'example',
            type: param::INT,
        );
        $this->assertEquals('header', $param->get_in());
        $this->assertEquals('example', $param->get_name());

        // Fetch the description directly.
        $schema = $param->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('schema', $schema);
        $this->assertObjectHasProperty('type', $schema->schema);
        $this->assertEquals('integer', $schema->schema->type);
        $this->assertObjectNotHasProperty('in', $schema);
        $this->assertObjectNotHasProperty('name', $schema);

        // Should have the same response a via the get_openapi_schema method.
        $schema = $param->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('schema', $schema);
        $this->assertObjectHasProperty('type', $schema->schema);
        $this->assertEquals('integer', $schema->schema->type);
        $this->assertObjectNotHasProperty('in', $schema);
        $this->assertObjectNotHasProperty('name', $schema);
    }

    public function test_referenced_object(): void {
        $object = new class extends header_object implements referenced_object {
            /**
             * Constructor.
             */
            public function __construct() {
                parent::__construct(
                    name: 'example',
                    type: param::INT,
                );
            }
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('schema', $schema);
        $this->assertObjectHasProperty('type', $schema->schema);
        $this->assertEquals('integer', $schema->schema->type);
        $this->assertObjectNotHasProperty('in', $schema);
        $this->assertObjectNotHasProperty('name', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('schema', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }
}
