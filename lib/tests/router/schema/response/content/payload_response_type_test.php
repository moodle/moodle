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
use core\router\schema\specification;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the abstract media type response content container.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\response\content\payload_response_type
 */
final class payload_response_type_test extends \advanced_testcase {
    /**
     * Test supported types.
     */
    public function test_supported_types(): void {
        $object = new payload_response_type();
        $this->assertIsArray($object->get_supported_content_types());
        $this->assertContains(json_media_type::class, $object->get_supported_content_types());
    }

    public function test_get_media_type_instance(): void {
        $example = new example(name: 'This is my example');
        $object = new payload_response_type(example: $example);

        // Request by mimetype.
        $jsoninstance = $object->get_media_type_instance(
            mimetype: json_media_type::get_encoding(),
        );
        $this->assertInstanceOf(json_media_type::class, $jsoninstance);

        $spec = new specification();
        $this->assertEquals(
            $example->get_openapi_description($spec),
            $jsoninstance->get_openapi_description($spec)->examples['This is my example'],
        );

        // Request by classname.
        $jsoninstance = $object->get_media_type_instance(
            classname: json_media_type::class,
        );
        $this->assertInstanceOf(json_media_type::class, $jsoninstance);

        $spec = new specification();
        $this->assertEquals(
            $example->get_openapi_description($spec),
            $jsoninstance->get_openapi_description($spec)->examples['This is my example'],
        );
    }

    public function test_description(): void {
        $object = new payload_response_type(
            description: 'This is a nice description about the content',
        );

        $schema = $object->get_openapi_schema(new specification());
        foreach ($object->get_supported_content_types() as $contenttypeclass) {
            $encoding = $contenttypeclass::get_encoding();
            $this->assertObjectHasProperty($encoding, $schema);
            $this->assertObjectNotHasProperty('$ref', $schema->{$encoding});
        }

        $this->assertObjectNotHasProperty('$ref', $schema);
    }

    public function test_no_media_type_instances(): void {
        // Really this should never happen, but it's nice to know that things work if there's a probelm in future.
        $object = new class () extends payload_response_type { // phpcs:ignore
            #[\Override]
            public function get_supported_content_types(): array {
                return [];
            }
        };

        $this->assertNull($object->get_media_type_instance());
    }

    public function test_required(): void {
        // Note: The payload required property is a Moodle check, and not represented in OpenAPI.
        $object = new payload_response_type(
            required: true,
        );

        $this->assertTrue($object->is_required());

        $object = new payload_response_type(
            required: false,
        );

        $this->assertFalse($object->is_required());
    }
}
