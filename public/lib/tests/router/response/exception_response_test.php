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

namespace core\router\response;

use core\router\schema\objects\schema_object;
use core\router\schema\specification;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the access denied response.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core\router\response\exception_response::class)]
final class exception_response_test extends route_testcase {
    public function test_basics(): void {
        $instance = new class extends exception_response { // phpcs:ignore
            #[\Override]
            protected static function get_response_description(): string {
                return 'Access was denied to the resource.';
            }
        };

        $rcm = new \ReflectionMethod($instance, 'get_exception_status_code');

        $this->assertIsInt($rcm->invoke(null));
        $this->assertEquals(500, $rcm->invoke(null));
    }

    /**
     * The response schema must define the fields required by RFC-9457.
     */
    public function test_get_response_schema(): void {
        $rcm = new \ReflectionMethod(exception_response::class, 'get_response_schema');
        $schema = $rcm->invoke(null);

        $this->assertInstanceOf(schema_object::class, $schema);

        foreach (['type', 'title', 'status', 'detail', 'instance'] as $field) {
            $this->assertTrue($schema->has($field), "Schema is missing the '{$field}' field");
        }
    }

    /**
     * When no type URL is defined, the payload must default to 'about:blank' as required by the RFC.
     */
    public function test_get_payload_data_defaults_type_to_about_blank(): void {
        $exception = new \Exception('Some error message');
        $request = new ServerRequest('GET', '/example');

        $instance = new class extends exception_response { // phpcs:ignore
            #[\Override]
            protected static function get_response_description(): string {
                return 'Example description';
            }
        };
        $rc = get_class($instance);

        $rcm = new \ReflectionMethod($rc, 'get_response_payload');
        $data = $rcm->invoke(null, $exception, request: $request);

        $this->assertEquals('about:blank', $data['type']);
        $this->assertEquals('Example description', $data['title']);
        $this->assertEquals($exception->getMessage(), $data['detail']);
        $this->assertEquals('/example', $data['instance']);
    }

    /**
     * When a type URL is defined, it must be used, out as an unescaped url string.
     */
    public function test_get_response_payload_uses_response_type_when_set(): void {
        $exception = new \Exception('Some error message');
        $request = new ServerRequest('GET', '/example');

        $instance = new class extends exception_response { // phpcs:ignore
            #[\Override]
            protected static function get_response_description(): string {
                return 'Example description';
            }

            #[\Override]
            protected static function get_response_type(): ?\core\url {
                return new \core\url('https://example.com/problems/example');
            }
        };
        $rc = get_class($instance);

        $rcm = new \ReflectionMethod($rc, 'get_response_payload');
        $data = $rcm->invoke(null, $exception, request: $request);

        $this->assertEquals('https://example.com/problems/example', $data['type']);
    }

    /**
     * Debug information already present in an exception message must not be duplicated.
     */
    public function test_get_response_payload_does_not_duplicate_debuginfo(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->debug = DEBUG_DEVELOPER;
        $exception = new \core\exception\invalid_parameter_exception('Unique debug information');
        $this->assertStringContainsString('Unique debug information', $exception->getMessage());

        $payload = invalid_parameter_response::get_response(new ServerRequest('GET', '/example'), $exception)->payload;

        $this->assertSame($exception->getMessage(), $payload['detail']);
        $this->assertSame(1, substr_count($payload['detail'], 'Unique debug information'));
        $this->assertSame($payload['detail'], $payload['message']);
        $this->assertSame('invalidparameter', $payload['errorcode']);
    }

    /**
     * Developer debugging appends missing debug information and includes a sanitised stack trace.
     */
    public function test_get_response_payload_adds_debug_information(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->debug = DEBUG_DEVELOPER;
        $exception = new \core\exception\invalid_parameter_exception();
        // Simulate an exception created without debug information in its message.
        $exception->debuginfo = 'Unique debug information';

        $payload = invalid_parameter_response::get_response(new ServerRequest('GET', '/example'), $exception)->payload;

        $this->assertSame(rtrim($exception->getMessage(), '.') . '. Unique debug information', $payload['detail']);
        $this->assertNotEmpty($payload['stacktrace']);
        foreach ($payload['stacktrace'] as $frame) {
            $this->assertArrayNotHasKey('args', $frame);
            if (isset($frame['file'])) {
                $this->assertStringNotContainsString(realpath($CFG->root), $frame['file']);
            }
        }
    }

    /**
     * Debug information not already in the message and stack traces are omitted without developer debugging.
     */
    public function test_get_response_payload_without_developer_debugging(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->debug = DEBUG_NONE;
        $exception = new \core\exception\invalid_parameter_exception();
        $exception->debuginfo = 'Unique debug information';

        $payload = invalid_parameter_response::get_response(new ServerRequest('GET', '/example'), $exception)->payload;

        $this->assertSame($exception->getMessage(), $payload['detail']);
        $this->assertStringNotContainsString('Unique debug information', $payload['detail']);
        $this->assertArrayNotHasKey('stacktrace', $payload);
    }

    /**
     * Exception responses advertise the problem details media type in OpenAPI.
     */
    public function test_openapi_problem_media_type(): void {
        $schema = (new invalid_parameter_response())->get_openapi_description(new specification());

        $this->assertObjectHasProperty('application/problem+json', $schema->content);
        $this->assertObjectNotHasProperty('application/json', $schema->content);
    }

    /**
     * The 'instance' field must only be included when a request was passed as extra data.
     */
    public function test_get_payload_data_omits_instance_without_request(): void {
        $exception = new \Exception('Some error message');

        $instance = new class extends exception_response { // phpcs:ignore
            #[\Override]
            protected static function get_response_description(): string {
                return 'Example description';
            }
        };
        $rc = get_class($instance);

        $rcm = new \ReflectionMethod($rc, 'get_payload_data');
        $data = $rcm->invoke(null, $exception);
        $this->assertDebuggingNotCalled();

        $this->assertArrayNotHasKey('instance', $data);
    }
}
