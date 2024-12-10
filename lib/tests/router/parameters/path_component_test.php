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

use core\tests\router\route_testcase;
use invalid_parameter_exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Tests for the Component path parameter.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\parameters\path_component
 * @covers     \core\router\schema\parameters\path_parameter
 */
final class path_component_test extends route_testcase {
    /**
     * Test that the parameter is valid when the component is not specified.
     */
    public function test_component_not_specified(): void {
        $param = new path_component();

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
        $param = new path_component(name: 'not_the_default_name');
        $this->assertEquals('not_the_default_name', $param->get_name());
    }

    /**
     * Test valid components.
     *
     * @param string $component
     * @dataProvider valid_components
     */
    public function test_valid_value(string $component): void {
        $param = new path_component();

        $app = $this->get_simple_app();
        $app->get('/example/{component}', fn () => new Response());

        $request = $this->route_request($app, new ServerRequest('GET', "/example/{$component}"));

        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();

        $this->assertInstanceOf(
            ServerRequestInterface::class,
            $param->validate($request, $route),
        );
    }

    /**
     * Test invalid components.
     *
     * @param string $component
     * @dataProvider invalid_components
     */
    public function test_invalid_value(string $component): void {
        $this->resetAfterTest();
        $param = new path_component();

        $app = $this->get_simple_app();
        $app->get('/example/{component}', fn () => new Response());

        $request = $this->route_request($app, new ServerRequest('GET', "/example/{$component}"));

        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();

        $this->expectException(invalid_parameter_exception::class);
        $param->validate($request, $route);
    }

    /**
     * Data provider containing seemingly-valid components.
     *
     * @return array
     */
    public static function valid_components(): array {
        return [
            ['core'],
            ['core_message'],
            ['mod_forum'],
            ['assignsubmission_file'],
            // Note: This is handled with a regex, not an actual lookup.
            ['blueberry_jam'],
        ];
    }

    /**
     * Data provider containing invalid components.
     *
     * @return array
     */
    public static function invalid_components(): array {
        return [
            ['4things_todo'],
            ['EASY_AS'],
        ];
    }
}
