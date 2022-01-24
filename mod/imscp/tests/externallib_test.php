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

namespace mod_imscp;

use externallib_advanced_testcase;
use mod_imscp_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External mod_imscp functions unit tests
 *
 * @package    mod_imscp
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Test view_imscp
     */
    public function test_view_imscp() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $imscp = $this->getDataGenerator()->create_module('imscp', array('course' => $course->id));
        $context = \context_module::instance($imscp->cmid);
        $cm = get_coursemodule_from_instance('imscp', $imscp->id);

        // Test invalid instance id.
        try {
            mod_imscp_external::view_imscp(0);
            $this->fail('Exception expected due to invalid mod_imscp instance id.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_imscp_external::view_imscp($imscp->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_imscp_external::view_imscp($imscp->id);
        $result = \external_api::clean_returnvalue(mod_imscp_external::view_imscp_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_imscp\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/imscp/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/imscp:view', CAP_PROHIBIT, $studentrole->id, $context->id);
        // Empty all the caches that may be affected by this change.
        accesslib_clear_all_caches_for_unit_testing();
        \course_modinfo::clear_instance_cache();

        try {
            mod_imscp_external::view_imscp($imscp->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

    }

    /**
     * Test get_imscps_by_courses
     */
    public function test_get_imscps_by_courses() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        // As admin.
        $this->setAdminUser();
        $course1 = self::getDataGenerator()->create_course();
        $imscpoptions1 = array(
          'course' => $course1->id,
          'name' => 'First IMSCP'
        );
        $imscp1 = self::getDataGenerator()->create_module('imscp', $imscpoptions1);
        $course2 = self::getDataGenerator()->create_course();

        $imscpoptions2 = array(
          'course' => $course2->id,
          'name' => 'Second IMSCP'
        );
        $imscp2 = self::getDataGenerator()->create_module('imscp', $imscpoptions2);
        $student1 = $this->getDataGenerator()->create_user();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enroll Student1 in Course1.
        self::getDataGenerator()->enrol_user($student1->id,  $course1->id, $studentrole->id);

        $this->setUser($student1);
        $imscps = mod_imscp_external::get_imscps_by_courses(array());
        $imscps = \external_api::clean_returnvalue(mod_imscp_external::get_imscps_by_courses_returns(), $imscps);
        $this->assertCount(1, $imscps['imscps']);
        $this->assertEquals('First IMSCP', $imscps['imscps'][0]['name']);
        // As Student you cannot see some IMSCP properties like 'section'.
        $this->assertFalse(isset($imscps['imscps'][0]['section']));

        // Student1 is not enrolled in this Course.
        // The webservice will give a warning!
        $imscps = mod_imscp_external::get_imscps_by_courses(array($course2->id));
        $imscps = \external_api::clean_returnvalue(mod_imscp_external::get_imscps_by_courses_returns(), $imscps);
        $this->assertCount(0, $imscps['imscps']);
        $this->assertEquals(1, $imscps['warnings'][0]['warningcode']);

        // Now as admin.
        $this->setAdminUser();
        // As Admin we can see this IMSCP.
        $imscps = mod_imscp_external::get_imscps_by_courses(array($course2->id));
        $imscps = \external_api::clean_returnvalue(mod_imscp_external::get_imscps_by_courses_returns(), $imscps);
        $this->assertCount(1, $imscps['imscps']);
        $this->assertEquals('Second IMSCP', $imscps['imscps'][0]['name']);
        // As an Admin you can see some IMSCP properties like 'section'.
        $this->assertEquals(0, $imscps['imscps'][0]['section']);

        // Now, prohibit capabilities.
        $this->setUser($student1);
        $contextcourse1 = \context_course::instance($course1->id);
        // Prohibit capability = mod:imscp:view on Course1 for students.
        assign_capability('mod/imscp:view', CAP_PROHIBIT, $studentrole->id, $contextcourse1->id);
        // Empty all the caches that may be affected by this change.
        accesslib_clear_all_caches_for_unit_testing();
        \course_modinfo::clear_instance_cache();

        $imscps = mod_imscp_external::get_imscps_by_courses(array($course1->id));
        $imscps = \external_api::clean_returnvalue(mod_imscp_external::get_imscps_by_courses_returns(), $imscps);
        $this->assertCount(0, $imscps['imscps']);
    }
}
