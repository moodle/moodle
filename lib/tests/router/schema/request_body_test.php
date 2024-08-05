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

use core\router\schema\objects\schema_object;
use core\router\schema\response\content\json_media_type;
use core\router\schema\response\content\payload_response_type;
use core\router\schema\specification;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the request_body object.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\request_body
 * @covers     \core\router\schema\openapi_base
 */
final class request_body_test extends route_testcase {
    public function test_basics(): void {
        $object = new request_body();

        $schema = $object->get_openapi_description(new specification());
        $this->assertEquals((object) [
            'description' => '',
            'required' => false,
            'content' => [],
        ], $schema);
        $this->assertFalse($schema->required);
        $this->assertFalse($object->is_required());
    }

    public function test_content_wrong_type(): void {
        $this->expectException(\coding_exception::class);
        new request_body(
            content: [new schema_object(content: [])],
        );
    }

    public function test_content_array(): void {
        $object = new request_body(
            content: [
                new json_media_type(content: [], required: true),
            ],
        );

        $schema = $object->get_openapi_schema(new specification());
        $this->assertObjectHasProperty('content', $schema);
        $this->assertObjectHasProperty('application/json', (object) $schema->content);

        $request = new ServerRequest('GET', 'http://example.com', [
            'Content-Type' => json_media_type::get_encoding(),
        ]);
        $body = $object->get_body_for_request($request);
        $this->assertInstanceOf(json_media_type::class, $body);
        $this->assertTrue($body->is_required());
    }

    public function test_content_not_matching(): void {
        $object = new request_body(
            content: [
                new json_media_type(content: []),
            ],
        );
        $this->expectException(\invalid_parameter_exception::class);
        $object->get_body_for_request(new ServerRequest('GET', 'http://example.com'));
    }

    public function test_content_payload_type(): void {
        $content = new payload_response_type(content: [], required: true);
        $object = new request_body(
            content: $content,
        );

        $schema = $object->get_openapi_schema(new specification());
        $this->assertObjectHasProperty('content', $schema);

        foreach ($content->get_supported_content_types() as $contenttypeclass) {
            $encoding = $contenttypeclass::get_encoding();
            $this->assertObjectHasProperty($encoding, $schema->content);
            $this->assertObjectNotHasProperty('$ref', $schema->content->{$encoding});

            $request = new ServerRequest('GET', 'http://example.com', [
                'Content-Type' => $encoding,
            ]);
            $body = $object->get_body_for_request($request);
            $this->assertInstanceOf($contenttypeclass, $body);
        }
    }

    /**
     * Test object referencing.
     *
     * @covers \core\router\schema\openapi_base
     */
    public function test_referenced_object(): void {
        $object = new class extends request_body implements referenced_object {
        };

        // Note: The status code is not in the OpenAPI schema, but in the parent.
        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('description', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('description', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }

    public function test_reference_content(): void {
        $object = new request_body(
            content: [],
        );
        $object = new class extends request_body implements referenced_object {
        };

        $schema = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('content', $schema);
    }
}
