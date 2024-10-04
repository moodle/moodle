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

namespace core\router;

use core\router;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpNotFoundException;

/**
 * Tests for the route_controller trait.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\route_controller
 */
final class route_controller_test extends route_testcase {
    /**
     * Test that the redirect method works as expected.
     *
     * @covers ::redirect
     */
    public function test_redirect(): void {
        $helper = new class (\core\di::get_container()) {
            use route_controller;

            // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
            public function test(
                ResponseInterface $response,
                $url,
            ) {
                return $this->redirect($response, $url);
            }
        };

        $response = $helper->test(new Response(), '/test');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeaderLine('Location'));
    }

    public function test_page_not_found(): void {
        $request = new ServerRequest('GET', '/test');
        $response = new Response();

        $helper = new class (\core\di::get_container()) {
            use route_controller;
        };

        $rc = new \ReflectionClass($helper);
        $rcm = $rc->getMethod('page_not_found');

        $this->expectException(HttpNotFoundException::class);
        $rcm->invokeArgs($helper, [$request, $response]);
    }

    /**
     * Test that get_param works as expected.
     *
     * @covers \core\router\route_controller::get_param
     */
    public function test_get_param(): void {
        $request = (new \GuzzleHttp\Psr7\ServerRequest('GET', '/test'))
            ->withQueryParams(['test' => 'value']);

        $helper = new class (\core\di::get_container()) {
            use route_controller;
        };

        $rc = new \ReflectionClass($helper);
        $rcm = $rc->getMethod('get_param');

        // Test a value that exists.
        $result = $rcm->invokeArgs($helper, [$request, 'test', null]);
        $this->assertEquals('value', $result);

        $result = $rcm->invokeArgs($helper, [$request, 'test', 'Unused default']);
        $this->assertEquals('value', $result);

        // Test a value that does not existexists.
        $result = $rcm->invokeArgs($helper, [$request, 'fake', null]);
        $this->assertEquals(null, $result);
        $this->assertdebuggingcalledcount(1);

        $result = $rcm->invokeArgs($helper, [$request, 'fake', 'Used default']);
        $this->assertEquals('Used default', $result);
        $this->assertdebuggingcalledcount(1);
    }

    /**
     * Test that it is possible to redirect to a callable.
     *
     * @covers \core\router\route_controller::redirect_to_callable
     */
    public function test_redirect_to_callable(): void {
        self::load_fixture('core', '/router/route_on_class.php');

        $rc = new \ReflectionClass(\core\fixtures\route_on_class::class);
        $rcm = $rc->getMethod('method_with_route');
        $route = $rcm->getAttributes(route::class)[0]->newInstance();
        $router = $this->get_router();
        $app = $router->get_app();
        \core\di::get_container()->set(router::class, $router);

        $app
            ->get(
                $route->get_path(),
                ['core\fixtures\route_on_class', 'method_with_route'],
            )
            ->setName('core\fixtures\route_on_class::method_with_route');

        $helper = new class (\core\di::get_container()) {
            use route_controller;
        };
        $rc = new \ReflectionClass($helper);
        $rcm = $rc->getMethod('redirect_to_callable');

        $response = $rcm->invokeArgs(
            $helper,
            [
                new ServerRequest('GET', '/test'),
                new Response(),
                'core\fixtures\route_on_class::method_with_route',
            ],
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));

        $uri = new Uri($response->getHeader('Location')[0]);

        $this->assertEmpty($uri->getQuery());
    }
}
