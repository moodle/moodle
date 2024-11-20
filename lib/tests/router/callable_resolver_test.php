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

use core\di;
use core\router\middleware\cors_middleware;
use Invoker\Exception\NotCallableException;

/**
 * Tests for callable resolver.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\callable_resolver
 */
final class callable_resolver_test extends \advanced_testcase {
    public function test_can_resolve_slim_notation(): void {
        $resolver = di::get(callable_resolver::class);

        $result = $resolver->resolve('core\router\apidocs:openapi_docs');
        $this->assertEquals([new apidocs(), 'openapi_docs'], $result);
    }
    public function test_can_resolve_array(): void {
        $resolver = di::get(callable_resolver::class);

        $result = $resolver->resolve([\core\router\apidocs::class, 'openapi_docs']);
        $this->assertEquals([new apidocs(), 'openapi_docs'], $result);
    }

    public function test_can_resolve_di_notation(): void {
        $resolver = di::get(callable_resolver::class);

        $result = $resolver->resolve('core\router\apidocs::openapi_docs');
        $this->assertEquals([new apidocs(), 'openapi_docs'], $result);
    }

    public function test_resolve_middleware(): void {
        $resolver = di::get(callable_resolver::class);

        $result = $resolver->resolveMiddleware(\core\router\middleware\cors_middleware::class);
        $this->assertEquals([new cors_middleware(), 'process'], $result);
    }

    public function test_resolve_middleware_not_middleware(): void {
        $resolver = di::get(callable_resolver::class);

        $this->expectException(NotCallableException::class);
        $resolver->resolveMiddleware('core\router\apidocs');
    }

    public function test_resolve_route(): void {
        self::load_fixture('core', 'router/route_implementing_request_handler_interface.php');
        $resolver = di::get(callable_resolver::class);

        $result = $resolver->resolveRoute(\core\router\route_implementing_request_handler_interface::class);
        $this->assertEquals([new route_implementing_request_handler_interface(), 'handle'], $result);
    }

    public function test_resolve_route_not_route(): void {
        $resolver = di::get(callable_resolver::class);

        $this->expectException(NotCallableException::class);
        $resolver->resolveRoute(\core\router\apidocs::class);
    }
}
