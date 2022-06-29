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
 * External function test for view_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   external
 * @since      Moodle 3.9
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use external_api;
use externallib_advanced_testcase;
use stdClass;
use context_module;
use course_modinfo;

/**
 * External function test for view_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_h5pactivity_test extends externallib_advanced_testcase {

    /**
     * Test test_view_h5pactivity invalid id.
     */
    public function test_view_h5pactivity_invalid_id() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        $result = view_h5pactivity::execute(0);
    }

    /**
     * Test test_view_h5pactivity user not enrolled.
     */
    public function test_view_h5pactivity_user_not_enrolled() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        $this->expectException('moodle_exception');
        $result = view_h5pactivity::execute($scenario->h5pactivity->id);
    }

    /**
     * Test test_view_h5pactivity user student.
     */
    public function test_view_h5pactivity_user_student() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup scenario.
        $scenario = $this->setup_scenario();

        $cm = get_coursemodule_from_instance('h5pactivity', $scenario->h5pactivity->id);
        $this->setUser($scenario->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = view_h5pactivity::execute($scenario->h5pactivity->id);
        $result = external_api::clean_returnvalue(view_h5pactivity::execute_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_h5pactivity\event\course_module_viewed', $event);
        $this->assertEquals($scenario->contextmodule, $event->get_context());
        $h5pactivity = new \moodle_url('/mod/h5pactivity/view.php', array('id' => $cm->id));
        $this->assertEquals($h5pactivity, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test test_view_h5pactivity user missing capabilities.
     */
    public function test_view_h5pactivity_user_missing_capabilities() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup scenario.
        $scenario = $this->setup_scenario();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/h5pactivity:view', CAP_PROHIBIT, $studentrole->id, $scenario->contextmodule->id);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        $this->setUser($scenario->student);
        $this->expectException('moodle_exception');
        $result = view_h5pactivity::execute($scenario->h5pactivity->id);
    }

    /**
     * Create a scenario to use into the tests.
     *
     * @return stdClass $scenario
     */
    protected function setup_scenario() {

        $course = $this->getDataGenerator()->create_course();
        $h5pactivity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $contextmodule = context_module::instance($h5pactivity->cmid);

        $scenario = new stdClass();
        $scenario->contextmodule = $contextmodule;
        $scenario->student = $student;
        $scenario->h5pactivity = $h5pactivity;

        return $scenario;
    }
}
