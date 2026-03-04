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

namespace core\route\controller;

use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the ESM controller shim route.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(esm_controller::class)]
final class esm_controller_test extends route_testcase {
    #[\PHPUnit\Framework\Attributes\After]
    public function reset(): void {
        \core\di::reset_container();
    }

    /**
     * Inject a stub import_map into the DI container that always returns a given fixture file,
     * then return a plain esm_controller that will use it.
     *
     * @return esm_controller
     */
    private function make_test_controller(?string $filename = null): esm_controller {
        if ($filename === null) {
            $filename = make_request_directory() . '/test.js';
        }
        file_put_contents($filename, 'export default {};');

        \core\di::set(
            \core\output\requirements\import_map::class,
            new class ($filename) extends \core\output\requirements\import_map {
                // phpcs:ignore
                public function __construct(private readonly string $fixture) {}
                // phpcs:ignore
                public function get_path_for_script(string $requestedpath): ?string {
                    return $this->fixture;
                }
            },
        );
        return new esm_controller();
    }

    /**
     * Data provider for serve() not-found cases.
     *
     * @return array
     */
    public static function serve_not_found_provider(): array {
        return [
            'unmatched specifier'                   => ['some-unknown-lib'],
            'matched prefix, missing module file'   => ['@moodle/lms/core/nonexistentmodule'],
        ];
    }

    /**
     * serve() throws not_found_exception for paths that cannot be resolved to a file.
     *
     * @param string $scriptpath
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('serve_not_found_provider')]
    public function test_serve_not_found(string $scriptpath): void {
        $this->expectException(\core\exception\not_found_exception::class);
        (new esm_controller())->serve(
            new ServerRequest('GET', "/12345/{$scriptpath}"),
            new Response(),
            12345,
            $scriptpath,
        );
    }

    /**
     * A valid component module returns 200 with an application/javascript Content-Type.
     */
    public function test_serve_component_module_returns_javascript(): void {
        $response = $this->make_test_controller()->serve(
            new ServerRequest('GET', '/-1/mod_test/index'),
            new Response(),
            -1,
            'mod_test/index',
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/javascript', $response->getHeaderLine('Content-Type'));
    }

    /**
     * An invalid revision (−1) results in a short-lived cache response with no ETag.
     */
    public function test_serve_invalid_revision_uses_short_cache(): void {
        $response = $this->make_test_controller()->serve(
            new ServerRequest('GET', '/-1/mod_test/index'),
            new Response(),
            -1,
            'mod_test/index',
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse($response->hasHeader('ETag'), 'Short-cache responses must not include an ETag.');
        $this->assertFalse($response->hasHeader('Cache-Control'), 'Short-cache responses must not include Cache-Control.');
    }

    /**
     * A valid revision results in a long-lived immutable cache response with an ETag.
     */
    public function test_serve_valid_revision_sets_long_cache(): void {
        $clock = $this->mock_clock_with_frozen();
        $revision = $clock->time();

        $response = $this->make_test_controller()->serve(
            new ServerRequest('GET', "/{$revision}/mod_test/index"),
            new Response(),
            $revision,
            'mod_test/index',
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('ETag'), 'Long-cache responses must include an ETag.');
        $this->assertStringContainsString('immutable', $response->getHeaderLine('Cache-Control'));
    }

    /**
     * When the request carries an If-None-Match header that matches the ETag, serve() returns 304.
     */
    public function test_serve_matching_etag_returns_304(): void {
        $filename = make_request_directory() . '/test.js';
        $controller = $this->make_test_controller($filename);
        $clock = $this->mock_clock_with_frozen();
        $revision = $clock->time();

        // Create the file content.
        $etag = sha1("{$revision}:{$filename}");

        $request = (new ServerRequest('GET', "/{$revision}/mod_test/index"))
            ->withHeader('If-None-Match', $etag);

        $response = $controller->serve($request, new Response(), $revision, 'mod_test/index');

        $this->assertEquals(304, $response->getStatusCode());
    }
}
