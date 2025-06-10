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
 * Unit tests for local_remote_courses.
 *
 * @package    local_remote_courses
 * @copyright  2016 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/remote_courses/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Primary test cases for local_remote_courses.
 *
 * @package    local_remote_courses
 * @copyright  2016 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_remote_courses_testcase extends externallib_advanced_testcase {
    public function test_get_courses() {
        global $DB;

        $this->resetAfterTest(true);
        $this->preventResetByRollback();
        $contextid = context_system::instance()->id;

        // We need logging enabled.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');

        $role = new stdClass();
        $role->name = 'Web service user';
        $r1 = $this->getDataGenerator()->create_role($role);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->role_assign($r1, $user->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', $contextid, $r1);
        $this->assignUserCapability('moodle/course:viewparticipants', $contextid, $r1);
        $this->assignUserCapability('moodle/course:view', $contextid, $r1);
        $this->assignUserCapability('moodle/user:viewdetails', $contextid, $r1);
        $this->setUser($user);
        set_config('extracttermcode', '/[0-9]+.([0-9]+)/', 'local_remote_courses');

        // Create classes.
        $course1 = new stdClass();
        $course1->fullname  = 'Test Course 1';
        $course1->shortname = 'CF101';
        $course1->idnumber  = '123456.201610';
        $course2 = new stdClass();
        $course2->fullname  = 'Test Course 2';
        $course2->shortname = 'CF102';
        $course2->idnumber  = '123456.201620';
        $course3 = new stdClass();
        $course3->fullname  = 'Test Course 3';
        $course3->shortname = 'CF103';
        $course3->idnumber  = '123456.201510';
        $course3->visible   = 0;

        $c1 = $this->getDataGenerator()->create_course($course1);
        $c2 = $this->getDataGenerator()->create_course($course2);

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id,
            $c1->id,
            $studentrole->id);
        $this->getDataGenerator()->enrol_user($student->id,
            $c2->id,
            $studentrole->id);

        $results = local_remote_courses_external::get_courses_by_username($student->username);
        $this->assertEquals(2, count($results));
        $this->assertEquals('201620', $results[0]['term']);
        $this->assertEquals('201610', $results[1]['term']);
        $this->assertEquals('student', $results[0]['roles'][0]);
        $this->assertEquals(1, count($results[1]['roles']));

        // Test sorting; user will "visit" course 1.
        $context = context_course::instance($c1->id);
        $eventparams = array();
        $eventparams['context'] = $context;
        $eventparams['userid'] = $student->id;
        $event = \core\event\course_viewed::create($eventparams);
        $event->trigger();

        $results = local_remote_courses_external::get_courses_by_username($student->username);
        $this->assertEquals(2, count($results));
        $this->assertEquals('Test Course 1', $results[0]['fullname']);
        $this->assertEquals('Test Course 2', $results[1]['fullname']);
    }
}
