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
 * Privacy provider tests.
 *
 * @package    enrol_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_lti\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    enrol_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_lti_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * @var stdClass The user
     */
    protected $user = null;

    /**
     * @var stdClass The course
     */
    protected $course = null;

    /**
     * @var stdClass The activity
     */
    protected $activity = null;

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->user = $this->getDataGenerator()->create_user();
        $this->activity = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id]);

        // Get the course and activity contexts.
        $coursecontext = \context_course::instance($this->course->id);
        $cmcontext = \context_module::instance($this->activity->cmid);

        // Create LTI tools in different contexts.
        $this->create_lti_users($coursecontext, $this->user->id);
        $this->create_lti_users($coursecontext, $this->user->id);
        $this->create_lti_users($cmcontext, $this->user->id);

        // Create another LTI user.
        $user = $this->getDataGenerator()->create_user();
        $this->create_lti_users($coursecontext, $user->id);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $contextlist = provider::get_contexts_for_userid($this->user->id);

        $this->assertCount(2, $contextlist);

        $coursectx = context_course::instance($this->course->id);
        $activityctx = context_module::instance($this->activity->cmid);
        $expectedids = [$coursectx->id, $activityctx->id];

        $actualids = $contextlist->get_contextids();
        $this->assertEquals($expectedids, $actualids, '', 0.0, 10, true);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        $coursecontext = context_course::instance($this->course->id);
        $cmcontext = \context_module::instance($this->activity->cmid);

        // Export all of the data for the course context.
        $this->export_context_data_for_user($this->user->id, $coursecontext, 'enrol_lti');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext);
        $this->assertTrue($writer->has_any_data());

        $data = (array) $writer->get_data(['enrol_lti_users']);
        $this->assertCount(2, $data);
        foreach ($data as $ltiuser) {
            $this->assertArrayHasKey('lastgrade', $ltiuser);
            $this->assertArrayHasKey('timecreated', $ltiuser);
            $this->assertArrayHasKey('timemodified', $ltiuser);
        }

        // Export all of the data for the activity context.
        $this->export_context_data_for_user($this->user->id, $cmcontext, 'enrol_lti');
        $writer = \core_privacy\local\request\writer::with_context($cmcontext);
        $this->assertTrue($writer->has_any_data());

        $data = (array) $writer->get_data(['enrol_lti_users']);
        $this->assertCount(1, $data);
        foreach ($data as $ltiuser) {
            $this->assertArrayHasKey('lastgrade', $ltiuser);
            $this->assertArrayHasKey('timecreated', $ltiuser);
            $this->assertArrayHasKey('timemodified', $ltiuser);
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $count = $DB->count_records('enrol_lti_users');
        $this->assertEquals(4, $count);

        // Delete data based on context.
        $coursecontext = context_course::instance($this->course->id);
        provider::delete_data_for_all_users_in_context($coursecontext);

        $ltiusers = $DB->get_records('enrol_lti_users');
        $this->assertCount(1, $ltiusers);

        $ltiuser = reset($ltiusers);
        $this->assertEquals($ltiuser->userid, $this->user->id);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $cmcontext = context_module::instance($this->activity->cmid);
        $coursecontext = context_course::instance($this->course->id);

        $count = $DB->count_records('enrol_lti_users');
        $this->assertEquals(4, $count);

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->user, 'enrol_lti',
            [context_system::instance()->id, $coursecontext->id, $cmcontext->id]);
        provider::delete_data_for_user($contextlist);

        $ltiusers = $DB->get_records('enrol_lti_users');
        $this->assertCount(1, $ltiusers);

        $ltiuser = reset($ltiusers);
        $this->assertNotEquals($ltiuser->userid, $this->user->id);
    }

    /**
     * Creates a LTI user given the provided context
     *
     * @param context $context
     * @param int $userid
     */
    private function create_lti_users(\context $context, $userid) {
        global $DB;

        // Create a tool.
        $ltitool = (object) [
            'enrolid' => 5,
            'contextid' => $context->id,
            'roleinstructor' => 5,
            'rolelearner' => 5,
            'timecreated' => time(),
            'timemodified' => time() + DAYSECS
        ];
        $toolid = $DB->insert_record('enrol_lti_tools', $ltitool);

        // Create a user.
        $ltiuser = (object) [
            'userid' => $userid,
            'toolid' => $toolid,
            'lastgrade' => 50,
            'lastaccess' => time() + DAYSECS,
            'timecreated' => time()
        ];
        $DB->insert_record('enrol_lti_users', $ltiuser);
    }
}
