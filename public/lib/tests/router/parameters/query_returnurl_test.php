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
use core\url;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the query_returnurl parameter.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(query_returnurl::class)]
final class query_returnurl_test extends route_testcase {
    public function test_returnurl_specified(): void {
        $this->resetAfterTest();

        $param = new query_returnurl();
        $request = $param->add_attributes_for_parameter_value(
            new ServerRequest('GET', '/course/edit'),
            '/course/view',
        );

        $this->assertInstanceOf(url::class, $request->getAttribute('returnurl'));
        $this->assertTrue(
            $request->getAttribute('returnurl')->compare(new url('/course/view')),
        );
    }
}
