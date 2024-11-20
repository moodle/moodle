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

use core\tests\route_testcase;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Tests for the controller invoker, and related bridge.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\controller_invoker
 * @covers     \core\router\bridge
 * @covers     \core\router\response_handler
 */
final class controller_invoker_test extends route_testcase {
    /**
     * Configure an instance of Slim and fetch the Invoker.
     *
     * @return \Slim\Interfaces\InvocationStrategyInterface
     */
    protected function get_invocation_strategy(): \Slim\Interfaces\InvocationStrategyInterface {
        $container = \core\di::get_container();
        bridge::create($container);
        $app = $container->get(\Slim\App::class);
        return $app->getRouteCollector()->getDefaultInvocationStrategy();
    }

    /**
     * Test that setup of the invoker using the router\bridge sets the correct invoker strategy.
     * @covers \core\router\bridge
     * @covers \core\router\controller_invoker
     */
    public function test_setup_of_invoker(): void {
        $strategy = $this->get_invocation_strategy();
        $this->assertInstanceOf(controller_invoker::class, $strategy);
    }

    public function test_invocation_with_arguments(): void {
        $strategy = $this->get_invocation_strategy();
        $testcase = $this;

        // Providing a callable with no args will mean that none are provided.
        $callable = function () use ($testcase): Response {
            $testcase->assertCount(0, func_get_args());
            return new Response();
        };

        $request = $this->create_request('GET', '/example');
        $response = new Response();
        $strategy($callable, $request, $response, []);

        // Requesting one Response will get us the Response.
        $originalresponse = new Response();
        $callable = function (Response $response) use ($testcase, $originalresponse): Response {
            $testcase->assertNotNull($response);
            $testcase->assertEquals($originalresponse, $response);
            // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue
            $testcase->assertCount(1, func_get_args());
            return $response;
        };

        $request = $this->create_request('GET', '/example');

        $strategy($callable, $request, $originalresponse, []);

        // Requesting the Request will get us the Request.
        $serverrequest = $this->create_request('GET', '/example');
        $callable = function (ServerRequestInterface $request) use ($testcase, $serverrequest): Response {
            $this->assertEquals($serverrequest, $request);
            // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue
            $testcase->assertCount(1, func_get_args());

            return new Response();
        };

        $strategy($callable, $serverrequest, $response, []);

        // Attributes on the request will be passed through if requested.
        $serverrequest = $this->create_request('GET', '/example')
            ->withAttribute('example', 'This is an examplar attribute!');
        $callable = function (
            string $example,
        ) use ($testcase): Response {
            $testcase->assertCount(1, func_get_args());
            $testcase->assertEquals('This is an examplar attribute!', $example);
            return new Response();
        };

        $strategy($callable, $serverrequest, $response, []);

        // Route arguments to request will be passed through if requested.
        $serverrequest = $this->create_request('GET', '/example');
        $callable = function (
            string $example,
        ) use ($testcase): Response {
            $testcase->assertCount(1, func_get_args());
            $testcase->assertEquals('This is an examplar attribute!', $example);
            return new Response();
        };

        $strategy($callable, $serverrequest, $response, [
            'example' => 'This is an examplar attribute!',
        ]);

        // Attributes will be overridden by Route arguments.
        $serverrequest = $this->create_request('GET', '/example')
            ->withAttribute('example', 'This is an examplar attribute!');
        $callable = function (
            string $example,
        ) use ($testcase): Response {
            $testcase->assertCount(1, func_get_args());
            $testcase->assertEquals('This is a different examplar attribute!', $example);
            return new Response();
        };

        $strategy($callable, $serverrequest, $response, [
            'example' => 'This is a different examplar attribute!',
        ]);
    }
}
