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

namespace core\router\parameters;

use core\tests\route_testcase;
use invalid_parameter_exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Tests for the Theme name path parameter.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\parameters\path_themename
 * @covers     \core\router\schema\parameters\path_parameter
 */
final class path_themename_test extends route_testcase {
    /**
     * Test that the parameter is valid when the themename is not specified.
     */
    public function test_themename_not_specified(): void {
        $param = new path_themename();

        $app = $this->get_simple_app();
        $app->get('/example', fn () => new Response());

        $request = $this->route_request($app, new ServerRequest('GET', '/example'));

        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $param->validate($request, $route),
        );
    }

    /**
     * Test that the parameter name is respected
     */
    public function test_name(): void {
        $param = new path_themename(name: 'not_the_default_name');
        $this->assertEquals('not_the_default_name', $param->get_name());
    }

    /**
     * Test valid themenames.
     *
     * @param string $themename
     * @dataProvider valid_themenames
     */
    public function test_valid_value(string $themename): void {
        $param = new path_themename();

        $app = $this->get_simple_app();
        $app->get('/example/{themename}', fn () => new Response());

        $request = $this->route_request($app, new ServerRequest('GET', "/example/{$themename}"));

        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $param->validate($request, $route),
        );
    }

    /**
     * Test invalid themenames.
     *
     * @param string $themename
     * @dataProvider invalid_themenames
     */
    public function test_invalid_value(string $themename): void {
        $this->resetAfterTest();
        $param = new path_themename();

        $app = $this->get_simple_app();
        $app->get('/example/{themename}', fn () => new Response());

        $request = $this->route_request($app, new ServerRequest('GET', "/example/{$themename}"));
        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();

        $this->expectException(invalid_parameter_exception::class);
        $param->validate($request, $route);
    }

    /**
     * Data provider containing seemingly-valid themenames.
     *
     * @return array
     */
    public static function valid_themenames(): array {
        return [
            // Note: This is handled with a regex, not an actual lookup.
            ['boost'],
            ['classic'],
            ['blueberry_jam'],
            ['abc-def'],
            ['1theme'],
            ['UPPERCASE'],

        ];
    }

    /**
     * Data provider containing invalid themenames.
     *
     * @return array
     */
    public static function invalid_themenames(): array {
        return [
            ['r|r'],
        ];
    }
}
