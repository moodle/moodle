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

namespace core\router\schema\response\content;

use core\router\schema\example;
use core\router\schema\objects\schema_object;
use core\router\schema\referenced_object;
use core\router\schema\specification;

/**
 * Tests for the abstract media type response content container.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\response\content\json_media_type
 * @covers     \core\router\schema\response\content\media_type
 */
final class json_media_type_test extends \advanced_testcase {
    public function test_basics(): void {
        $this->assertEquals(
            'application/json',
            json_media_type::get_encoding(),
        );

        $object = new json_media_type();
        $this->assertEquals(
            'application/json',
            $object->get_mimetype(),
        );
    }

    /**
     * Tests for the is_required method.
     *
     * @dataProvider is_required_provider
     * @param bool|null $required
     * @param bool $expected
     */
    public function test_is_required(?bool $required, bool $expected): void {
        // Note: This related to the _body_ being required.
        $object = new json_media_type(
            required: $required,
        );

        $schema = $object->get_openapi_schema(new specification());
        if ($expected) {
            $this->assertTrue($object->is_required());
            $this->assertTrue($schema->required);
        } else {
            $this->assertFalse($object->is_required());
            $this->assertObjectNotHasProperty('required', $schema);
        }
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
        ];
    }

    public function test_example_or_examples(): void {
        $this->expectException(\coding_exception::class);

        new json_media_type(
            example: new example(name: 'example'),
            examples: [
                new example(name: 'example2'),
            ],
        );
    }

    public function test_single_example(): void {
        $example = new example(name: 'examplename');
        $object = new json_media_type(
            example: $example,
        );

        $spec = new specification();
        $schema = $object->get_openapi_schema($spec);

        // There is no schema specified here, so none in the OpenAPI object.
        $this->assertObjectNotHasProperty('schema', $schema);

        // The 'example' attribute is going to be deprecated in a future version of the spec.
        // We normalise a single example into the examples array instead which is the preferred way.
        $this->assertObjectNotHasProperty('example', $schema);
        $this->assertObjectHasProperty('examples', $schema);

        // The example will be listed by name.
        $this->assertArrayHasKey('examplename', $schema->examples);
        $this->assertEquals(
            $example->get_openapi_schema($spec),
            $schema->examples['examplename'],
        );
    }

    public function test_schema(): void {
        $schemaobject = new schema_object(
            content: [
                'example' => new schema_object(content: []),
            ],
        );

        $object = new json_media_type(
            schema: $schemaobject,
        );

        $this->assertSame($schemaobject, $object->get_schema());

        $spec = new specification();
        $schema = $object->get_openapi_schema($spec);
        $this->assertObjectHasProperty('schema', $schema);
        $this->assertEquals(
            $schemaobject->get_openapi_description($spec),
            $schema->schema,
        );
    }

    public function test_referenced_schema(): void {
        $schemaobject = new class (
            content: [],
        ) extends schema_object implements referenced_object {
        };

        $object = new json_media_type(
            schema: $schemaobject,
        );

        $this->assertSame($schemaobject, $object->get_schema());

        $spec = new specification();
        $schema = $object->get_openapi_schema($spec);
        $this->assertObjectNotHasProperty('schema', $schema->schema);
        $this->assertObjectHasProperty('$ref', $schema->schema);
    }
}
