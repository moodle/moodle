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
 * Webservices test for attendance plugin.
 *
 * @package    mod_attendance
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

global $CFG;

// Include the code to test.
require_once($CFG->dirroot . '/mod/attendance/classes/attendance_webservices_handler.php');
require_once($CFG->dirroot . '/mod/attendance/classes/structure.php');

/**
 * This class contains the test cases for the functions in attendance_webservices_handler.php.
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_webservices_tests extends advanced_testcase {
    /** @var coursecat */
    protected $category;
    /** @var stdClass */
    protected $course;
    /** @var stdClass */
    protected $attendance;
    /** @var stdClass */
    protected $teacher;
    /** @var array */
    protected $sessions;

    /**
     * Setup class.
     */
    public function setUp() {
        global $DB;

        $this->category = $this->getDataGenerator()->create_category();
        $this->course = $this->getDataGenerator()->create_course(array('category' => $this->category->id));

        $record = new stdClass();
        $record->course = $this->course->id;
        $record->name = "Attendance";
        $record->grade = 100;

        $DB->insert_record('attendance', $record);

        $this->getDataGenerator()->create_module('attendance', array('course' => $this->course->id));

        $moduleid = $DB->get_field('modules', 'id', array('name' => 'attendance'));
        $cm = $DB->get_record('course_modules', array('course' => $this->course->id, 'module' => $moduleid));
        $context = context_course::instance($this->course->id);
        $att = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
        $this->attendance = new mod_attendance_structure($att, $cm, $this->course, $context);

        $this->create_and_enrol_users();

        $this->setUser($this->teacher);

        $session = new stdClass();
        $session->sessdate = time();
        $session->duration = 6000;
        $session->description = "";
        $session->descriptionformat = 1;
        $session->descriptionitemid = 0;
        $session->timemodified = time();
        $session->statusset = 0;
        $session->groupid = 0;
        $session->absenteereport = 1;

        // Creating two sessions.
        $this->sessions[] = $session;

        $this->attendance->add_sessions($this->sessions);
    }

    /** Creating 10 students and 1 teacher. */
    protected function create_and_enrol_users() {
        for ($i = 0; $i < 10; $i++) {
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id, $this->course->id, 5); // Enrol as student.
        }

        $this->teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, 3); // Enrol as teacher.
    }

    public function test_get_courses_with_today_sessions() {
        $this->resetAfterTest(true);

        // Just adding the same session again to check if the method returns the right amount of instances.
        $this->attendance->add_sessions($this->sessions);

        $courseswithsessions = attendance_handler::get_courses_with_today_sessions($this->teacher->id);

        $this->assertTrue(is_array($courseswithsessions));
        $this->assertEquals(count($courseswithsessions), 1);
        $course = array_pop($courseswithsessions);
        $this->assertEquals($course->fullname, $this->course->fullname);
        $attendanceinstance = array_pop($course->attendance_instances);
        $this->assertEquals(count($attendanceinstance['today_sessions']), 2);
    }

    public function test_get_session() {
        $this->resetAfterTest(true);

        $courseswithsessions = attendance_handler::get_courses_with_today_sessions($this->teacher->id);

        $course = array_pop($courseswithsessions);
        $attendanceinstance = array_pop($course->attendance_instances);
        $session = array_pop($attendanceinstance['today_sessions']);

        $sessioninfo = attendance_handler::get_session($session->id);

        $this->assertEquals($this->attendance->id, $sessioninfo->attendanceid);
        $this->assertEquals($session->id, $sessioninfo->id);
        $this->assertEquals(count($sessioninfo->users), 10);
    }

    public function test_update_user_status() {
        $this->resetAfterTest(true);

        $courseswithsessions = attendance_handler::get_courses_with_today_sessions($this->teacher->id);

        $course = array_pop($courseswithsessions);
        $attendanceinstance = array_pop($course->attendance_instances);
        $session = array_pop($attendanceinstance['today_sessions']);

        $sessioninfo = attendance_handler::get_session($session->id);

        $student = array_pop($sessioninfo->users);
        $status = array_pop($sessioninfo->statuses);
        $statusset = $sessioninfo->statusset;
        attendance_handler::update_user_status($session->id, $student->id, $this->teacher->id, $status->id, $statusset);

        $sessioninfo = attendance_handler::get_session($session->id);
        $log = $sessioninfo->attendance_log;
        $studentlog = $log[$student->id];

        $this->assertEquals($status->id, $studentlog->statusid);
    }
}
