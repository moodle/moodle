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
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the Moodle Authentication middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(moodle_authentication_middleware_test::class)]
final class moodle_authentication_middleware_test extends route_testcase {
    public function test_require_login_without_course(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(moodle_authentication_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', function ($request, $response) {
            return $response;
        });

        $route = new \core\router\route(
            requirelogin: new \core\router\require_login(
                requirelogin: true,
                autologinguest: false,
            ),
        );

        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute(\core\router\route::class, $route);

        // We expect a redirect to be returned as the user is not logged in and autologin guest is disabled.
        // This will be updated in the future to check for a 401 response once `require_login` and `require_course_login`
        // are rewritten.
        $this->expectException(\core\exception\moodle_exception::class);

        // Handle the request.
        $returns = $app->handle($request);
    }
}
