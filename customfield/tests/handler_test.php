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

declare(strict_types=1);

namespace core_customfield;

use advanced_testcase;
use core_course\customfield\course_handler;
use moodle_exception;

/**
 * Unit tests for the abstract custom fields handler
 *
 * @package     core_customfield
 * @covers      \core_customfield\handler
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class handler_test extends advanced_testcase {

    /**
     * Test retrieving handler for given component/area
     */
    public function test_get_handler(): void {
        $handler = handler::get_handler('core_course', 'course');
        $this->assertInstanceOf(course_handler::class, $handler);
    }

    /**
     * Test retrieving handler for invalid component/area
     */
    public function test_get_handler_invalid(): void {
        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Unable to find handler for custom fields for component core_blimey and area test');
        handler::get_handler('core_blimey', 'test');
    }
}
