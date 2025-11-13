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

/**
 * Test filter loop avoidance.
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2020 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package filter_ally
 */
namespace filter_ally;

/**
 * @group     text_filter
 * @group     ally
 * @package filter_ally
 */
final class annotating_state_test extends \advanced_testcase {
    public function test_annotating_state(): void {
        $this->resetAfterTest();
        $courseid = 3;

        $this->assertFalse(text_filter::is_annotating($courseid));
        text_filter::start_annotating($courseid);
        $this->assertTrue(text_filter::is_annotating($courseid));
        text_filter::end_annotating($courseid);
        $this->assertFalse(text_filter::is_annotating($courseid));
    }
}
