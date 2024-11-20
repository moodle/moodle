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

namespace core\router\response;

use core\tests\route_testcase;

/**
 * Tests for the access denied response.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\response\exception_response
 */
final class exception_response_test extends route_testcase {
    public function test_basics(): void {
        $instance = new class extends exception_response { // phpcs:ignore
            #[\Override]
            protected static function get_response_description(): string {
                return 'Access was denied to the resource.';
            }
        };

        $rc = new \ReflectionClass($instance);
        $rcm = new \ReflectionMethod($instance, 'get_exception_status_code');

        $this->assertIsInt($rcm->invoke(null));
        $this->assertEquals(500, $rcm->invoke(null));
    }
}
