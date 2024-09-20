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

namespace core\router\middleware;

use core\di;
use core\url;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Slim\Exception\HttpNotFoundException;

/**
 * Tests for the Moodle Bootstrap middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\middleware\moodle_bootstrap_middleware
 */
final class moodle_bootstrap_middleware_test extends route_testcase {
    /**
     * Test setting of the page URI based on the request URI.
     *
     * @dataProvider page_url_provider
     *
     * @param string $pattern The pattern to register with the app.
     * @param string $basepath The basepath of the wwwroot.
     * @param string $uri The URI to test.
     * @param array $cfg The configuration to use.
     * @param string|false $expected The expected page URI, or false if no page URI is expected.
     */
    public function test_set_page_to_uri(
        string $pattern,
        string $basepath,
        string $uri,
        array $cfg,
        string|false $expected
    ): void {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        foreach ($cfg as $key => $value) {
            $CFG->{$key} = $value;
        }

        $app = $this->get_simple_app();
        $app->setBasePath($basepath);
        $app->add(di::get(moodle_bootstrap_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(
            methods: ['GET'],
            pattern: $pattern,
            callable: fn ($request, $response) => $response,
        );

        // Handle the request.
        $request = new ServerRequest('GET', $uri);

        if ($expected) {
            $app->handle($request);
            $expect = new url($expected);
            $this->assertEquals($expect->out(), $PAGE->url->out());
        } else {
            $this->expectException(HttpNotFoundException::class);
            $app->handle($request);
        }
    }

    /**
     * Data provider for test_set_page_to_uri.
     *
     * @return array
     */
    public static function page_url_provider(): array {
        return [
            'A basic URI' => [
                'pattern' => '/example',
                'basepath' => '',
                'uri' => '/example',
                'cfg' => [],
                'expected' => '/example',
            ],
            'A basic URI including wwwroot' => [
                'pattern' => '/example',
                'basepath' => '/example/path',
                'uri' => "https://example.com/example/path/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/example/path',
                ],
                'expected' => '/example',
            ],
            'A basic URI with query parameters' => [
                'pattern' => '/example',
                'basepath' => '',
                'uri' => "https://example.com/example?foo=bar&baz=qux",
                'cfg' => [
                    'wwwroot' => 'https://example.com',
                ],
                'expected' => "https://example.com/example?foo=bar&baz=qux",
            ],
            'A request behind a terminating SSL proxy' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:443/moodle/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'sslproxy' => true,
                ],
                'expected' => 'https://example.com/moodle/example',
            ],
            'A request behind a terminating SSL proxy with query parameters' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:443/moodle/example?foo=bar&baz=qux",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'sslproxy' => true,
                ],
                'expected' => 'https://example.com/moodle/example?foo=bar&baz=qux',
            ],
            'A request behind a terminating SSL proxy and random port' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:1024/moodle/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'sslproxy' => true,
                ],
                'expected' => 'https://example.com/moodle/example',
            ],
            'A request behind a terminating SSL proxy and random port where the wwwroot has an explicit port' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:1024/moodle/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com:8443/moodle',
                    'sslproxy' => true,
                ],
                'expected' => 'https://example.com:8443/moodle/example',
            ],
            'A request behind a terminating SSL proxy where the wwwroot has an explicity port and query parameters' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:1024/moodle/example?foo=bar&baz=qux",
                'cfg' => [
                    'wwwroot' => 'https://example.com:8443/moodle',
                    'sslproxy' => true,
                ],
                'expected' => 'https://example.com:8443/moodle/example?foo=bar&baz=qux',
            ],
            'A request behind a SSL proxy proxy with different path is invalid' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://example.com:443/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'sslproxy' => true,
                ],
                'expected' => false,
            ],
            'A request behind a reverse proxy' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://172.30.10.101:443/moodle/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'reverseproxy' => true,
                ],
                'expected' => 'https://example.com/moodle/example',
            ],
            'A request behind a reverse proxy with query params' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://172.30.10.101:443/moodle/example?foo=bar&baz=qux",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'reverseproxy' => true,
                ],
                'expected' => 'https://example.com/moodle/example?foo=bar&baz=qux',
            ],
            'A request behind a reverse proxy with different path is invalid' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://172.30.10.101:443/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com/moodle',
                    'reverseproxy' => true,
                ],
                'expected' => false,
            ],
            'A request behind a reverse proxy where the wwwroot has an explicit port' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://172.30.10.101:443/moodle/example",
                'cfg' => [
                    'wwwroot' => 'https://example.com:8443/moodle',
                    'reverseproxy' => true,
                ],
                'expected' => 'https://example.com:8443/moodle/example',
            ],
            'A request behind a reverse proxy where the wwwroot has an explicit port and parameters' => [
                'pattern' => '/example',
                'basepath' => '/moodle',
                'uri' => "http://172.30.10.101:443/moodle/example?foo=bar&baz=qux",
                'cfg' => [
                    'wwwroot' => 'https://example.com:8443/moodle',
                    'reverseproxy' => true,
                ],
                'expected' => 'https://example.com:8443/moodle/example?foo=bar&baz=qux',
            ],
        ];
    }
}
