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
 * Privacy tests for core_course.
 *
 * @package    core_course
 * @category   test
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/completion/tests/fixtures/completion_creation.php');

/**
 * Unit tests for course/classes/privacy/policy
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_privacy_testcase extends \core_privacy\tests\provider_testcase {

    use completion_creation;

    /**
     * Test getting the appropriate context for the userid. This should only ever
     * return the user context for the user id supplied.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($this->coursecontext->id, $contextlist->current()->id);
    }

    /**
     * Test fetching users within a context.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();
        $component = 'core_course';

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // User1 and user2 complete course.
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);

        // User3 is enrolled but has not completed course.
        $this->getDataGenerator()->enrol_user($user3->id, $this->course->id, 'student');

        // Ensure only users that have course completion are returned.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, $component);
        \core_course\privacy\provider::get_users_in_context($userlist);
        $expected = [$user1->id, $user2->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertCount(2, $actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that user data is exported.
     */
    public function test_export_user_data() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'core_course',
                [$this->coursecontext->id]);
        $writer = \core_privacy\local\request\writer::with_context($this->coursecontext);
        \core_course\privacy\provider::export_user_data($approvedlist);
        $completiondata = $writer->get_data([get_string('privacy:completionpath', 'course')]);
        $this->assertEquals('In progress', $completiondata->status);
        $this->assertCount(2, $completiondata->criteria);
    }

    /**
     * Verify that if a module context is included in the contextlist_collection and its parent course is not, the
     * export_context_data() call picks this up, and that the contextual course information is included.
     */
    public function test_export_context_data_module_context_only() {
        $this->resetAfterTest();

        // Create a course and a single module.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'shortname' => 'C1']);
        $context1 = context_course::instance($course1->id);
        $modassign = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id, 'name' => 'assign test 1']);
        $assigncontext = context_module::instance($modassign->cmid);

        // Now, let's assume during user info export, only the coursemodule context is returned in the contextlist_collection.
        $user = $this->getDataGenerator()->create_user();
        $collection = new \core_privacy\local\request\contextlist_collection($user->id);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'mod_assign', [$assigncontext->id]);
        $collection->add_contextlist($approvedlist);

        // Now, verify that core_course will detect this, and add relevant contextual information.
        \core_course\privacy\provider::export_context_data($collection);
        $writer = \core_privacy\local\request\writer::with_context($context1);
        $this->assertTrue($writer->has_any_data());
        $writerdata = $writer->get_data();
        $this->assertObjectHasAttribute('fullname', $writerdata);
        $this->assertObjectHasAttribute('shortname', $writerdata);
        $this->assertObjectHasAttribute('idnumber', $writerdata);
        $this->assertObjectHasAttribute('summary', $writerdata);
    }

    /**
     * Verify that if a module context and its parent course context are both included in the contextlist_collection, that course
     * contextual information is present in the export.
     */
    public function test_export_context_data_course_and_module_contexts() {
        $this->resetAfterTest();

        // Create a course and a single module.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'shortname' => 'C1', 'format' => 'site']);
        $context1 = context_course::instance($course1->id);
        $modassign = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id, 'name' => 'assign test 1']);
        $assigncontext = context_module::instance($modassign->cmid);

        // Now, assume during user info export, that both module and course contexts are returned in the contextlist_collection.
        $user = $this->getDataGenerator()->create_user();
        $collection = new \core_privacy\local\request\contextlist_collection($user->id);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'mod_assign', [$assigncontext->id]);
        $approvedlist2 = new \core_privacy\local\request\approved_contextlist($user, 'core_course', [$context1->id]);
        $collection->add_contextlist($approvedlist);
        $collection->add_contextlist($approvedlist2);

        // Now, verify that core_course still adds relevant contextual information, even for courses which are explicitly listed in
        // the contextlist_collection.
        \core_course\privacy\provider::export_context_data($collection);
        $writer = \core_privacy\local\request\writer::with_context($context1);
        $this->assertTrue($writer->has_any_data());
        $writerdata = $writer->get_data();
        $this->assertObjectHasAttribute('fullname', $writerdata);
        $this->assertObjectHasAttribute('shortname', $writerdata);
        $this->assertObjectHasAttribute('idnumber', $writerdata);
        $this->assertObjectHasAttribute('summary', $writerdata);
    }

    /**
     * Test deleting all user data for one context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(2, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(2, $records);
        \core_course\privacy\provider::delete_data_for_all_users_in_context($this->coursecontext);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(0, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(0, $records);
    }

    /**
     * Test deleting data for only one user.
     */
    public function test_delete_data_for_user() {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(2, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(2, $records);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_course',
                [$this->coursecontext->id]);
        \core_course\privacy\provider::delete_data_for_user($approvedlist);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(1, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(1, $records);
    }

    /**
     * Test deleting data within a context for an approved userlist.
     */
    public function test_delete_data_for_users() {
        global $DB;
        $this->resetAfterTest();

        $component = 'core_course';
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $this->complete_course($user3);

        // Ensure records exist for all users before delete.
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(3, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(3, $records);

        $approveduserids = [$user1->id, $user3->id];
        $approvedlist = new \core_privacy\local\request\approved_userlist($this->coursecontext, $component, $approveduserids);
        \core_course\privacy\provider::delete_data_for_users($approvedlist);

        // Ensure content is only deleted for approved userlist.
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertEquals($user2->id, $record->userid);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertEquals($user2->id, $record->userid);
    }
}
