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

namespace core_availability;

/**
 * Unit tests for the capability checker class.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capability_checker_test extends \advanced_testcase {
    /**
     * Tests loading a class from /availability/classes.
     */
    public function test_capability_checker(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course with teacher and student.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $teacher = $generator->create_user();
        $student = $generator->create_user();
        $generator->enrol_user($teacher->id, $course->id, $roleids['teacher']);
        $generator->enrol_user($student->id, $course->id, $roleids['student']);

        // Check a capability which they both have.
        $context = \context_course::instance($course->id);
        $checker = new capability_checker($context);
        $result = array_keys($checker->get_users_by_capability('mod/forum:replypost'));
        sort($result);
        $this->assertEquals(array($teacher->id, $student->id), $result);

        // And one that only teachers have.
        $result = array_keys($checker->get_users_by_capability('mod/forum:deleteanypost'));
        $this->assertEquals(array($teacher->id), $result);

        // Check the caching is working.
        $before = $DB->perf_get_queries();
        $result = array_keys($checker->get_users_by_capability('mod/forum:deleteanypost'));
        $this->assertEquals(array($teacher->id), $result);
        $this->assertEquals($before, $DB->perf_get_queries());
    }
}
