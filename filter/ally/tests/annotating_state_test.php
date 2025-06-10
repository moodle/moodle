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
 */
namespace filter_ally;

class annotating_state_test extends \advanced_testcase {
    public function test_annotating_state() {
        $this->resetAfterTest();
        require_once(__DIR__.'/../filter.php');
        $courseid = 3;

        $this->assertFalse(\filter_ally::is_annotating($courseid));
        \filter_ally::start_annotating($courseid);
        $this->assertTrue(\filter_ally::is_annotating($courseid));
        \filter_ally::end_annotating($courseid);
        $this->assertFalse(\filter_ally::is_annotating($courseid));
    }
}
