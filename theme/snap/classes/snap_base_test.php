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
 * Base php unit test file.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;

abstract class snap_base_test extends \advanced_testcase {
    /**
     * Setup course with a group and users.
     * @return array
     */
    protected function course_group_user_setup() {
        $dg = $this->getDataGenerator();
        $student = $dg->create_user();
        $teacher = $dg->create_user();
        $course = $dg->create_course();
        $group = $dg->create_group((object)['courseid' => $course->id]);
        $dg->enrol_user($student->id, $course->id, 'student');
        $dg->create_group_member((object)['groupid' => $group->id, 'userid' => $student->id]);
        $dg->enrol_user($teacher->id, $course->id, 'teacher');
        return [$student, $teacher, $course, $group];
    }
}
