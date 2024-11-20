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

namespace core;

use core\tests\route_testcase;
use Slim\App;

/**
 * Tests for the router class.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router
 * @covers     \core\router\response_handler
 */
final class router_test extends route_testcase {
    public function test_get_app(): void {
        $router = $this->get_router('/example');
        $app = $router->get_app();
        $this->assertInstanceOf(App::class, $app);

        $this->assertEquals(di::get_container(), $app->getContainer());
    }

    public function test_request_normalisation(): void {
        $router = $this->get_router('');

        // Create handlers for the routes.
        // Note: These must all be created before any are accessed as the data is cached after first use.
        $app = $router->get_app();
        $app->get('/test/path', fn ($response) => $response->withStatus(299));
        $app->get('/', fn ($response) => $response->withStatus(275));
        $app->get('/test/otherpath', fn ($response) => $response->withStatus(250));

        // Duplicate slashes.
        $request = $this->create_request('GET', '/test//path', '');
        $response = $router->handle_request($request);
        $this->assertEquals(299, $response->getStatusCode());

        // An empty route.
        $request = $this->create_request('GET', '', '');
        $response = $router->handle_request($request);
        $this->assertEquals(275, $response->getStatusCode());

        // A route with a trailing slash.
        $request = $this->create_request('GET', '/test/otherpath/', '');
        $response = $router->handle_request($request);
        $this->assertEquals(250, $response->getStatusCode());

        // A route with a trailing double slash.
        $request = $this->create_request('GET', '/test/otherpath/////', '');
        $response = $router->handle_request($request);
        $this->assertEquals(250, $response->getStatusCode());
    }

    /**
     * Test an API route.
     */
    public function test_preferences_no_login(): void {
        $this->add_class_routes_to_route_loader(\core_user\route\api\preferences::class);
        $response = $this->process_api_request('GET', '/current/preferences');

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response);

        $this->assertEmpty((array) $payload);
    }

    public function test_basepath_supplied(): void {
        $router = $this->get_router(
            basepath: '/example',
        );
        $this->assertEquals('/example', $router->basepath);
    }

    public function test_basepath_guessed(): void {
        global $CFG;

        $wwwroot = new \moodle_url($CFG->wwwroot);
        $router = di::get(router::class);

        $this->assertEquals($wwwroot->get_path(), $router->basepath);
    }

    public function test_basepath_guessed_rphp(): void {
        $wwwroot = new \moodle_url('/r.php');
        $_SERVER['SCRIPT_FILENAME'] = 'r.php';
        $_SERVER['REQUEST_URI'] = $wwwroot->get_path();

        $router = di::get(router::class);

        $this->assertEquals($wwwroot->get_path(), $router->basepath);
    }
}
