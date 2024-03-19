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
namespace core_course\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/completion/tests/fixtures/completion_creation.php');

use core_privacy\local\request\transform;

/**
 * Unit tests for course/classes/privacy/policy
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    use \completion_creation;

    /**
     * Test getting the appropriate context for the userid. This should only ever
     * return the user context for the user id supplied.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Make sure contexts are not being returned for user1.
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist->get_contextids());

        // Make sure contexts are not being returned for user2.
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist->get_contextids());

        // Create course completion data for user1.
        $this->create_course_completion();
        $this->complete_course($user1);

        // Make sure the course context is being returned for user1.
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user1->id);
        $expected = [$this->coursecontext->id];
        $actual = $contextlist->get_contextids();
        $this->assertCount(1, $actual);
        $this->assertEquals($expected, $actual);

        // Make sure contexts are still not being returned for user2.
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist->get_contextids());

        // User2 has a favourite course.
        $user2context = \context_user::instance($user2->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $ufservice->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
            $this->coursecontext);

        // Make sure the course context is being returned for user2.
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user2->id);
        $expected = [$this->coursecontext->id];
        $actual = $contextlist->get_contextids();
        $this->assertCount(1, $actual);
        $this->assertEquals($expected, $actual);
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
        $user4 = $this->getDataGenerator()->create_user();

        // User1 and user2 complete course.
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);

        // User3 is enrolled but has not completed course.
        $this->getDataGenerator()->enrol_user($user3->id, $this->course->id, 'student');

        // User4 has a favourited course.
        $systemcontext = \context_system::instance();
        $user4ctx = \context_user::instance($user4->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($user4ctx);
        $ufservice->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);

        // Ensure only users that have course completion or favourites are returned.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, $component);
        \core_course\privacy\provider::get_users_in_context($userlist);
        $expected = [
            $user1->id,
            $user2->id,
            $user4->id
        ];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertCount(3, $actual);
        $this->assertEquals($expected, $actual);

        // Ensure that users are not being returned in other contexts than the course context.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $this->assertCount(0, $actual);
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
        $this->assertEquals('Complete', $completiondata->status);
        $this->assertCount(2, $completiondata->criteria);

        // User has a favourite course.
        $usercontext = \context_user::instance($user->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $favourite = $ufservice->create_favourite('core_course', 'courses',
                $this->coursecontext->instanceid, $this->coursecontext);

        // Ensure that user's favourites data in the course context is being exported.
        $writer = \core_privacy\local\request\writer::with_context($this->coursecontext);
        \core_course\privacy\provider::export_user_data($approvedlist);
        $favouritedata = $writer->get_data([get_string('privacy:favouritespath', 'course')]);

        $this->assertEquals(transform::yesno(true), $favouritedata->starred);
        $this->assertEquals('', $favouritedata->ordering);
        $this->assertEquals(transform::datetime($favourite->timecreated), $favouritedata->timecreated);
        $this->assertEquals(transform::datetime($favourite->timemodified), $favouritedata->timemodified);
    }

    /**
     * Verify that if a module context is included in the contextlist_collection and its parent course is not, the
     * export_context_data() call picks this up, and that the contextual course information is included.
     */
    public function test_export_context_data_module_context_only() {
        $this->resetAfterTest();

        // Create a course and a single module.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'shortname' => 'C1']);
        $context1 = \context_course::instance($course1->id);
        $modassign = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id, 'name' => 'assign test 1']);
        $assigncontext = \context_module::instance($modassign->cmid);

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
        $this->assertObjectHasProperty('fullname', $writerdata);
        $this->assertObjectHasProperty('shortname', $writerdata);
        $this->assertObjectHasProperty('idnumber', $writerdata);
        $this->assertObjectHasProperty('summary', $writerdata);
    }

    /**
     * Verify that if a module context and its parent course context are both included in the contextlist_collection, that course
     * contextual information is present in the export.
     */
    public function test_export_context_data_course_and_module_contexts() {
        $this->resetAfterTest();

        // Create a course and a single module.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'shortname' => 'C1', 'format' => 'site']);
        $context1 = \context_course::instance($course1->id);
        $modassign = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id, 'name' => 'assign test 1']);
        $assigncontext = \context_module::instance($modassign->cmid);

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
        $this->assertObjectHasProperty('fullname', $writerdata);
        $this->assertObjectHasProperty('shortname', $writerdata);
        $this->assertObjectHasProperty('idnumber', $writerdata);
        $this->assertObjectHasProperty('summary', $writerdata);
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

        $systemcontext = \context_system::instance();
        $user1ctx = \context_user::instance($user1->id);
        $user2ctx = \context_user::instance($user2->id);
        // User1 and user2 have a favourite course.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1ctx);
        $ufservice1->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2ctx);
        $ufservice2->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);

        // Ensure only users that have course favourites are returned in the course context (user1 and user2).
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $this->assertCount(2, $actual);

        // Ensure the users does not have a course completion data.
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(0, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(0, $records);

        // Create course completions for user1 and users.
        $this->complete_course($user1);
        $this->complete_course($user2);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(2, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(4, $records);

        // Delete data for all users in a context different than the course context (system context).
        \core_course\privacy\provider::delete_data_for_all_users_in_context($systemcontext);

        // Ensure the data in the course context has not been deleted.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $this->assertCount(2, $actual);

        // Delete data for all users in the course context.
        \core_course\privacy\provider::delete_data_for_all_users_in_context($this->coursecontext);

        // Ensure the completion data has been removed in the course context.
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(0, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(0, $records);

        // Ensure that users are not returned after the deletion in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $this->assertCount(0, $actual);
    }

    /**
     * Test deleting data for only one user.
     */
    public function test_delete_data_for_user() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Create course completion for user1.
        $this->create_course_completion();
        $this->complete_course($user1);

        // Ensure user1 is returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [$user1->id];
        $this->assertCount(1, $actual);
        $this->assertEquals($expected, $actual);

        // User2 and user3 have a favourite course.
        $systemcontext = \context_system::instance();
        $user2ctx = \context_user::instance($user2->id);
        $user3ctx = \context_user::instance($user3->id);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2ctx);
        $ufservice2->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);
        $ufservice3 = \core_favourites\service_factory::get_service_for_user_context($user3ctx);
        $ufservice3->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);

        // Ensure user1, user2 and user3 are returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user1->id,
            $user2->id,
            $user3->id
        ];
        sort($expected);
        sort($actual);
        $this->assertCount(3, $actual);
        $this->assertEquals($expected, $actual);

        // Delete user1's data in the course context.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_course',
                [$this->coursecontext->id]);
        \core_course\privacy\provider::delete_data_for_user($approvedlist);

        // Ensure user1's data is deleted and only user2 and user3 are returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user2->id,
            $user3->id
        ];
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Delete user2's data in a context different than the course context (system context).
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user2, 'core_course',
                [$systemcontext->id]);
        \core_course\privacy\provider::delete_data_for_user($approvedlist);

        // Ensure user2 and user3 are still returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user2->id,
            $user3->id
        ];
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Delete user2's data in the course context.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user2, 'core_course',
                [$this->coursecontext->id]);
        \core_course\privacy\provider::delete_data_for_user($approvedlist);

        // Ensure user2's is deleted and user3 is still returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user3->id
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test deleting data within a context for an approved userlist.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'core_course';
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);

        // Ensure user1, user2 are returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user1->id,
            $user2->id
        ];
        sort($expected);
        sort($actual);
        $this->assertCount(2, $actual);
        $this->assertEquals($expected, $actual);

        $systemcontext = \context_system::instance();
        // User3 has a favourite course.
        $user3ctx = \context_user::instance($user3->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($user3ctx);
        $ufservice->create_favourite('core_course', 'courses', $this->coursecontext->instanceid,
                $this->coursecontext);

        // Ensure user1, user2 and user3 are now returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user1->id,
            $user2->id,
            $user3->id
        ];
        sort($expected);
        sort($actual);
        $this->assertCount(3, $actual);
        $this->assertEquals($expected, $actual);

        // Delete data for user1 and user3 in the course context.
        $approveduserids = [$user1->id, $user3->id];
        $approvedlist = new \core_privacy\local\request\approved_userlist($this->coursecontext, $component, $approveduserids);
        \core_course\privacy\provider::delete_data_for_users($approvedlist);

        // Ensure user1 and user3 are deleted and user2 is still returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [$user2->id];
        $this->assertCount(1, $actual);
        $this->assertEquals($expected, $actual);

        // Try to delete user2's data in a context different than course (system context).
        $approveduserids = [$user2->id];
        $approvedlist = new \core_privacy\local\request\approved_userlist($systemcontext, $component, $approveduserids);
        \core_course\privacy\provider::delete_data_for_users($approvedlist);

        // Ensure user2 is still returned in the course context.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'core_course');
        \core_course\privacy\provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        $expected = [
            $user2->id
        ];
        $this->assertCount(1, $actual);
        $this->assertEquals($expected, $actual);
    }
}
