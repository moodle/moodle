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

namespace core\router\schema\response;

use core\param;
use core\router\schema\header_object;
use core\router\schema\objects\schema_object;
use core\router\schema\referenced_object;
use core\router\schema\response\content\json_media_type;
use core\router\schema\response\content\payload_response_type;
use core\router\schema\specification;
use core\tests\route_testcase;

/**
 * Tests for the response schema definition.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\response\response
 */
final class response_test extends route_testcase {
    public function test_defaults(): void {
        $response = new response();

        // The default status code is 200.
        $this->assertSame(200, $response->get_status_code());
    }

    public function test_basics(): void {
        $response = new response(
            statuscode: 450,
            description: 'This is a nice description about the response',
            headers: [
                new header_object(
                    name: 'X-Header',
                    type: param::ALPHA,
                    description: 'This is a nice description about the header',
                    required: true,
                ),
            ],
            content: new payload_response_type(
                description: 'This is a nice description about the content',
                schema: new schema_object(
                    content: [
                        'example' => new schema_object(content: []),
                    ],
                ),
            ),
        );

        // The default status code is 200.
        $this->assertSame(450, $response->get_status_code());
        $schema = $response->get_openapi_schema(new specification());

        $this->assertObjectHasProperty('description', $schema);
        $this->assertEquals('This is a nice description about the response', $schema->description);

        $this->assertObjectHasProperty('headers', $schema);
        $this->assertArrayHasKey('X-Header', $schema->headers);
        $this->assertObjectHasProperty('description', $schema->headers['X-Header']);
        $this->assertEquals('This is a nice description about the header', $schema->headers['X-Header']->description);
        $this->assertObjectHasProperty('schema', $schema->headers['X-Header']);

        $this->assertObjectHasProperty('content', $schema);
    }

    public function test_default_200_description(): void {
        $response = new response(
            statuscode: 200,
        );

        // The default status code is 200.
        $this->assertSame(200, $response->get_status_code());
        $schema = $response->get_openapi_schema(new specification());
        $this->assertObjectHasProperty('description', $schema);
        $this->assertIsString($schema->description);
        $this->assertEquals('OK', $schema->description);
    }

    public function test_array_content(): void {
        $response = new response(
            statuscode: 450,
            content: [
                new json_media_type(
                    schema: new schema_object(
                        content: [
                            'example' => new schema_object(content: []),
                        ],
                    ),
                ),
            ],
        );

        // The default status code is 200.
        $this->assertSame(450, $response->get_status_code());
        $schema = $response->get_openapi_schema(new specification());
        $this->assertObjectHasProperty('description', $schema);
        $this->assertIsString($schema->description);

        $this->assertObjectHasProperty('content', $schema);
        $this->assertArrayHasKey('application/json', $schema->content);
    }

    public function test_invalid_content(): void {
        $this->expectException(\coding_exception::class);

        $response = new response(
            content: [
                new schema_object(
                    content: [],
                ),
            ],
        );
        $response->get_openapi_schema(new specification());
    }

    /**
     * Tests for object references.
     *
     * @covers \core\router\schema\openapi_base
     */
    public function test_referenced_object(): void {
        $object = new class (
            statuscode: 499,
        ) extends response implements referenced_object {
        };

        // Note: The status code is not in the OpenAPI schema, but in the parent.
        $schema = $object->get_openapi_description(new specification());
        $this->assertObjectNotHasProperty('$ref', $schema);
        $this->assertObjectHasProperty('description', $schema);

        $reference = $object->get_openapi_schema(new specification());
        $this->assertObjectNotHasProperty('description', $reference);
        $this->assertObjectHasProperty('$ref', $reference);
    }
}
