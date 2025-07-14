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
use core\tests\router\route_testcase;
use invalid_parameter_exception;

/**
 * Tests for a scalar type.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\objects\scalar_type
 */
final class scalar_type_test extends route_testcase {
    public function test_referenced_object(): void {
        $object = new class (
            type: param::ALPHANUM,
        ) extends scalar_type implements referenced_object {
        };

        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('type', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('type', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_validation(): void {
        $object = new scalar_type(
            type: param::ALPHA,
        );

        // This should return whatever was input.
        $data = $object->validate_data('alpha');
        $this->assertEquals('alpha', $data);

        // By default parameters are optional.
        $this->assertNull($object->validate_data(null));
    }

    public function test_validation_nullable(): void {
        $object = new scalar_type(
            type: param::ALPHA,
            required: false,
        );

        $this->assertNull($object->validate_data(null));
    }

    public function test_validation_not_nullable(): void {
        $object = new scalar_type(
            type: param::ALPHA,
            required: true,
        );

        $this->expectException(invalid_parameter_exception::class);
        $object->validate_data(null);
    }
}
