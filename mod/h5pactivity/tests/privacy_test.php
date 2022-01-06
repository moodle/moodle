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
 * mod_h5pactivity privacy tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\privacy;

use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\writer;
use \core_privacy\tests\provider_testcase;

/**
 * Privacy tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_h5pactivity_privacy_testcase extends provider_testcase {

    /** @var stdClass User without any attempt. */
    protected $student0;

    /** @var stdClass User with some attempt. */
    protected $student1;

    /** @var stdClass User with some attempt. */
    protected $student2;

    /** @var context context_module of the H5P activity. */
    protected $context;

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->h5pactivity_setup_test_scenario_data();

        // The student0 hasn't any attempt.
        $contextlist = provider::get_contexts_for_userid($this->student0->id);
        $this->assertCount(0, (array) $contextlist->get_contextids());

        // The student1 has data in the mod_h5pactivity context.
        $contextlist = provider::get_contexts_for_userid($this->student1->id);
        $this->assertCount(1, (array) $contextlist->get_contextids());
        $this->assertContains($this->context->id, $contextlist->get_contextids());
    }

    /**
     * Test getting the user IDs for the context related to this plugin.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->h5pactivity_setup_test_scenario_data();
        $component = 'mod_h5pactivity';

        $userlist = new \core_privacy\local\request\userlist($this->context, $component);
        provider::get_users_in_context($userlist);

        // Students 1 and 2 have attempts in the H5P context, student 0 does not.
        $this->assertCount(2, $userlist);

        $expected = [$this->student1->id, $this->student2->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->h5pactivity_setup_test_scenario_data();
        $component = 'mod_h5pactivity';

        // Validate exported data for student0 (without any attempt).
        $this->setUser($this->student0);
        $writer = writer::with_context($this->context);

        $this->export_context_data_for_user($this->student0->id, $this->context, $component);
        $subcontextattempt1 = [
            get_string('myattempts', 'mod_h5pactivity'),
            get_string('attempt', 'mod_h5pactivity'). " 1"
        ];
        $data = $writer->get_data($subcontextattempt1);
        $this->assertEmpty($data);

        // Validate exported data for student1.
        writer::reset();
        $this->setUser($this->student1);
        $writer = writer::with_context($this->context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->student1->id, $this->context, $component);

        $data = $writer->get_data([]);
        $this->assertEquals('H5P 1', $data->name);

        $data = $writer->get_data($subcontextattempt1);
        $this->assertCount(1, (array) $data);
        $this->assertCount(3, (array) reset($data));
        $subcontextattempt2 = [
            get_string('myattempts', 'mod_h5pactivity'),
            get_string('attempt', 'mod_h5pactivity'). " 2"
        ];
        $data = $writer->get_data($subcontextattempt2);
        $this->assertCount(3, (array) reset($data));
        // The student1 has only 1 tracked attempts.
        $subcontextattempt3 = [
            get_string('myattempts', 'mod_h5pactivity'),
            get_string('attempt', 'mod_h5pactivity'). " 3"
        ];
        $data = $writer->get_data($subcontextattempt3);
        $this->assertEmpty($data);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->h5pactivity_setup_test_scenario_data();

        // Before deletion, we should have 4 entries in the attempts table.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(4, $count);
        // Before deletion, we should have 12 entries in the results table.
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(12, $count);

        // Delete data based on the context.
        provider::delete_data_for_all_users_in_context($this->context);

        // After deletion, the attempts entries should have been deleted.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(0, $count);
        // After deletion, the results entries should have been deleted.
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->h5pactivity_setup_test_scenario_data();

        $params = ['userid' => $this->student1->id];

        // Before deletion, we should have 4 entries in the attempts table.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(4, $count);
        // Before deletion, we should have 12 entries in the results table.
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(12, $count);

        // Save student1 attempts ids.
        $attemptsids = $DB->get_records_menu('h5pactivity_attempts', $params, '', 'attempt, id');
        list($resultselect, $attemptids) = $DB->get_in_or_equal($attemptsids);
        $resultselect = 'id ' . $resultselect;

        $approvedcontextlist = new approved_contextlist($this->student1, 'h5pactivity', [$this->context->id]);
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, the h5pactivity_attempts entries for the first student should have been deleted.
        $count = $DB->count_records('h5pactivity_attempts', $params);
        $this->assertEquals(0, $count);

        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        // After deletion, the results entries for the first student should have been deleted.
        $count = $DB->count_records_select('h5pactivity_attempts_results', $resultselect, $attemptids);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(6, $count);

        // Confirm that the h5pactivity hasn't been removed.
        $h5pactivitycount = $DB->get_records('h5pactivity');
        $this->assertCount(1, (array) $h5pactivitycount);

        // Delete track for student0 (nothing has to be removed).
        $approvedcontextlist = new approved_contextlist($this->student0, 'h5pactivity', [$this->context->id]);
        provider::delete_data_for_user($approvedcontextlist);

        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(6, $count);
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;
        $component = 'mod_h5pactivity';

        $this->resetAfterTest(true);
        $this->setAdminUser();
        // In this scenario we need a 3rd user to test batch deletion.
        // Create student2 with 2 attempts.
        $this->h5pactivity_setup_test_scenario_data(true);

        // Before deletion, we should have 6 entries in the attempts table.
        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(6, $count);
        // Before deletion, we should have 18 entries in the results table.
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(18, $count);

        // Save student1 and student2 attempts ids.
        $params1 = ['userid' => $this->student1->id];
        $attempts1ids = $DB->get_records_menu('h5pactivity_attempts', $params1, '', 'attempt, id');
        $params2 = ['userid' => $this->student2->id];
        $attempts2ids = $DB->get_records_menu('h5pactivity_attempts', $params2, '', 'attempt, id');
        list($resultselect, $attemptids) = $DB->get_in_or_equal(array_merge($attempts1ids, $attempts2ids));
        $resultselect = 'id ' . $resultselect;

        // Delete student 1 ans 2 data, retain student 3 data.
        $approveduserids = [$this->student1->id, $this->student2->id];
        $approvedlist = new approved_userlist($this->context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // After deletion, the h5pactivity_attempts entries for student1 and student2 should have been deleted.
        $count = $DB->count_records('h5pactivity_attempts', $params1);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts', $params2);
        $this->assertEquals(0, $count);

        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        // After deletion, the results entries for the first and second student should have been deleted.
        $count = $DB->count_records_select('h5pactivity_attempts_results', $resultselect, $attemptids);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(6, $count);

        // Confirm that the h5pactivity hasn't been removed.
        $h5pactivitycount = $DB->get_records('h5pactivity');
        $this->assertCount(1, (array) $h5pactivitycount);

        // Delete results track for student0 (nothing has to be removed).
        $approveduserids = [$this->student0->id];
        $approvedlist = new approved_userlist($this->context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $count = $DB->count_records('h5pactivity_attempts');
        $this->assertEquals(2, $count);
        $count = $DB->count_records('h5pactivity_attempts_results');
        $this->assertEquals(6, $count);
    }

    /**
     * Helper function to setup 3 users and 2 H5P attempts for student1 and student2.
     * $this->student0 is always created without any attempt.
     *
     * @param bool $extrauser generate a 3rd user (default false).
     */
    protected function h5pactivity_setup_test_scenario_data(bool $extrauser = false): void {
        global $DB;

        $generator = $this->getDataGenerator();

        $course = $this->getDataGenerator()->create_course();
        $params = ['course' => $course];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);
        $this->context = \context_module::instance($activity->cmid);

        // Users enrolments.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // Create student0 withot any attempt.
        $this->student0 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create student1 with 2 attempts.
        $this->student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $params = ['cmid' => $cm->id, 'userid' => $this->student1->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // Create student2 with 2 attempts.
        $this->student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $params = ['cmid' => $cm->id, 'userid' => $this->student2->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        if ($extrauser) {
            $this->student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $params = ['cmid' => $cm->id, 'userid' => $this->student3->id];
            $generator->create_content($activity, $params);
            $generator->create_content($activity, $params);
        }
    }
}
