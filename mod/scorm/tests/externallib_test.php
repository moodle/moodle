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
 * SCORM module external functions tests
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/scorm/lib.php');

/**
 * SCORM module external functions tests
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_scorm_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $this->course->id));
        $this->context = context_module::instance($this->scorm->cmid);
        $this->cm = get_coursemodule_from_instance('scorm', $this->scorm->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Test view_scorm
     */
    public function test_view_scorm() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_scorm_external::view_scorm(0);
            $this->fail('Exception expected due to invalid mod_scorm instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_scorm_external::view_scorm($this->scorm->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id, $this->studentrole->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_scorm_external::view_scorm($this->scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::view_scorm_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_scorm\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/scorm/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test get scorm attempt count
     */
    public function test_mod_scorm_get_scorm_attempt_count_own_empty() {
        // Set to the student user.
        self::setUser($this->student);

        // Retrieve my attempts (should be 0).
        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(0, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_own_with_complete() {
        // Set to the student user.
        self::setUser($this->student);

        // Create attempts.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 2, 'cmi.core.lesson_status', 'completed');

        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(2, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_own_incomplete() {
        // Set to the student user.
        self::setUser($this->student);

        // Create a complete attempt, and an incomplete attempt.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 2, 'cmi.core.credit', '0');

        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id, true);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(1, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_others_as_teacher() {
        // As a teacher.
        self::setUser($this->teacher);

        // Create a completed attempt for student.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');

        // I should be able to view the attempts for my students.
        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(1, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_others_as_student() {
        // Create a second student.
        $student2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $this->course->id, $this->studentrole->id, 'manual');

        // As a student.
        self::setUser($student2);

        // I should not be able to view the attempts of another student.
        $this->setExpectedException('required_capability_exception');
        mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
    }

    public function test_mod_scorm_get_scorm_attempt_count_invalid_instanceid() {
        // As student.
        self::setUser($this->student);

        // Test invalid instance id.
        $this->setExpectedException('moodle_exception');
        mod_scorm_external::get_scorm_attempt_count(0, $this->student->id);
    }

    public function test_mod_scorm_get_scorm_attempt_count_invalid_userid() {
        // As student.
        self::setUser($this->student);

        $this->setExpectedException('moodle_exception');
        mod_scorm_external::get_scorm_attempt_count($this->scorm->id, -1);
    }
}
