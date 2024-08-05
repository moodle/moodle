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
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

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
    public function test_set_page_to_uri(): void {
        global $PAGE;
        $app = $this->get_simple_app();
        $app->add(di::get(moodle_bootstrap_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/example', function ($request, $response) {
            return $response;
        });

        // Handle the request.
        $request = new ServerRequest('GET', '/example');
        $app->handle($request);

        $expect = new \moodle_url('/example');
        $this->assertEquals($expect->out(), $PAGE->url->out());
    }
}
