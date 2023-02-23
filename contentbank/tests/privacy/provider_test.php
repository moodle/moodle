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
 * Base class for unit tests for core_contentbank.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\privacy;

use stdClass;
use context_system;
use context_coursecat;
use context_course;
use context_user;
use core_contentbank\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for contentbank\classes\privacy\provider.php
 *
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Testing againts Manager who has content in the three contexts.
        $contextlist = provider::get_contexts_for_userid($scenario->manager->id);
        // There are three contexts in the list.
        $contextlistids = $contextlist->get_contextids();
        $this->assertCount(3, $contextlistids);
        // Check the list against the expected list of contexts.
        $this->assertContainsEquals($scenario->systemcontext->id, $contextlistids);
        $this->assertContainsEquals($scenario->coursecategorycontext->id,
            $contextlistids);
        $this->assertContainsEquals($scenario->coursecontext->id, $contextlistids);

        // Testing againts Teacher who has content in the one context.
        $contextlist = provider::get_contexts_for_userid($scenario->teacher->id);
        // There are only one context in the list.
        $contextlistids = $contextlist->get_contextids();
        $this->assertCount(1, $contextlistids);
        // Check the againts Course Context.
        $this->assertContainsEquals($scenario->coursecontext->id, $contextlistids);
        // And there is not a System and Course Category Context.
        $this->assertNotContainsEquals($scenario->systemcontext->id, $contextlistids);
        $this->assertNotContainsEquals($scenario->coursecategorycontext->id, $contextlistids);
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Get the userlist to Context System, only Manager will be there.
        $userlist = new userlist($scenario->systemcontext, 'core_contentbank');
        provider::get_users_in_context($userlist);
        $this->assertEquals([$scenario->manager->id], $userlist->get_userids());
        // Teacher will not be there.
        $this->assertNotEquals([$scenario->teacher->id], $userlist->get_userids());

        // Get the userlist to Context Course, Manager and Teacher will be there.
        $userlist = new userlist($scenario->coursecontext, 'core_contentbank');
        provider::get_users_in_context($userlist);

        $expected = [$scenario->manager->id, $scenario->teacher->id];
        sort($expected);
        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test for provider::test_export_user_data().
     */
    public function test_export_user_data() {

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        $subcontexts = [
            get_string('name', 'core_contentbank')
        ];
        // Get the data for the System Context.
        $writer = writer::with_context($scenario->systemcontext);
        $this->assertFalse($writer->has_any_data());
        // Export data for Manager.
        $this->export_context_data_for_user($scenario->manager->id,
            $scenario->systemcontext, 'core_contentbank');
        $data = $writer->get_data($subcontexts);
        $this->assertCount(3, (array) $data);
        $this->assertCount(3, $writer->get_files($subcontexts));

        // Get the data for the Course Categoy Context.
        $writer = writer::with_context($scenario->coursecategorycontext);
        // Export data for Manager.
        $this->export_context_data_for_user($scenario->manager->id,
            $scenario->coursecategorycontext, 'core_contentbank');
        $data = $writer->get_data($subcontexts);
        $this->assertCount(2, (array) $data);
        $this->assertCount(2, $writer->get_files($subcontexts));

        // Get the data for the Course Context.
        $writer = writer::with_context($scenario->coursecontext);
        // Export data for Manager.
        $this->export_context_data_for_user($scenario->manager->id,
            $scenario->coursecontext, 'core_contentbank');
        $data = $writer->get_data($subcontexts);
        $this->assertCount(2, (array) $data);
        $this->assertCount(2, $writer->get_files($subcontexts));

        // Export data for Teacher.
        $writer = writer::reset();
        $writer = writer::with_context($scenario->coursecontext);
        $this->export_context_data_for_user($scenario->teacher->id,
            $scenario->coursecontext, 'core_contentbank');
        $data = $writer->get_data($subcontexts);
        $this->assertCount(3, (array) $data);
        $this->assertCount(3, $writer->get_files($subcontexts));
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Before delete data, we have 4 contents.
        // - 3 in a system context.
        // - 2 in a course category context.
        // - 5 in a course context (2 by manager and 3 by teacher).

        // Delete data based on system context.
        provider::delete_data_for_all_users_in_context($scenario->systemcontext);
        $count = $DB->count_records('contentbank_content');
        // 3 content should be deleted.
        // 7 contents should be remain.
        $this->assertEquals(7, $count);

        // Delete data based on course category context.
        provider::delete_data_for_all_users_in_context($scenario->coursecategorycontext);
        $count = $DB->count_records('contentbank_content');
        // 2 contents should be deleted.
        // 5 content should be remain.
        $this->assertEquals(5, $count);

        // Delete data based on course context.
        provider::delete_data_for_all_users_in_context($scenario->coursecontext);
         $count = $DB->count_records('contentbank_content');
        // 5 content should be deleted.
        // 0 content should be remain.
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::test_delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Before delete data, we have 4 contents.
        // - 3 in a system context.
        // - 2 in a course category context.
        // - 5 in a course context (2 by manager and 3 by teacher).

        // A list of users who has created content in Course Category Context.
        $userlist1 = new userlist($scenario->coursecategorycontext,
            'core_contentbank');
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        // Only Manager should be.
        $this->assertEquals([$scenario->manager->id], $userlist1->get_userids());

        // A list of users who has created content in Course Context.
        $userlist2 = new userlist($scenario->coursecontext, 'core_contentbank');
        provider::get_users_in_context($userlist2);
        $this->assertCount(2, $userlist2);

        // Manager and Teacher should be.
        $expected = [$scenario->manager->id, $scenario->teacher->id];
        sort($expected);
        $actual = $userlist2->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($scenario->coursecategorycontext, 'core_contentbank', $userlist1->get_userids());
        // Delete data for users in course category context.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in course category context.
        $userlist1 = new userlist($scenario->coursecategorycontext,
            'core_contentbank');
        provider::get_users_in_context($userlist1);
        // The user data in course category context should be deleted.
        $this->assertCount(0, $userlist1);
        // Re-fetch users in course category context.
        $userlist2 = new userlist($scenario->coursecontext, 'core_contentbank');
        provider::get_users_in_context($userlist2);
        // The user data in course context should be still present.
        $this->assertCount(2, $userlist2);

        // Convert $userlist2 into an approved_contextlist.
        $approvedlist2 = new approved_userlist($scenario->coursecontext,
            'core_contentbank', $userlist2->get_userids());
        // Delete data for users in course context.
        provider::delete_data_for_users($approvedlist2);
        $userlist2 = new userlist($scenario->coursecontext, 'core_contentbank');
        provider::get_users_in_context($userlist2);
        // The user data in course context should be deleted.
        $this->assertCount(0, $userlist2);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
         global $DB;

        $this->resetAfterTest();
        // Setup scenario.
        $scenario = $this->setup_scenario();

        // Before delete data, we have 4 contents.
        // - 3 in a system context.
        // - 2 in a course category context.
        // - 5 in a course context (2 by manager and 3 by teacher).

        // Get all the context for Manager.
        $contextlist = provider::get_contexts_for_userid($scenario->manager->id);
        $approvedcontextlist = new approved_contextlist($scenario->manager,
            'core_contentbank', $contextlist->get_contextids());
        // Delete all the data created by the Manager in all the contexts.
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, only 3 content for teacher should be present.
        $count = $DB->count_records('contentbank_content');
        $this->assertEquals(3, $count);

        // Confirm that the remaining content was created by the teacher.
        $count = $DB->count_records('contentbank_content',
            ['usercreated' => $scenario->teacher->id]);
        $this->assertEquals(3, $count);

        // Get all the context for Teacher.
        $contextlist = provider::get_contexts_for_userid($scenario->teacher->id);
        $approvedcontextlist = new approved_contextlist($scenario->teacher,
            'core_contentbank', $contextlist->get_contextids());
        // Delete all the data created by the Teacher in all the contexts.
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, no content should be present.
        $count = $DB->count_records('contentbank_content');
        $this->assertEquals(0, $count);
    }

    /**
     * Create a complex scenario to use into the tests.
     *
     * @return stdClass $scenario
     */
    protected function setup_scenario() {
        global $DB;

        $systemcontext = context_system::instance();
        $manager = $this->getDataGenerator()->create_user();
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $this->getDataGenerator()->role_assign($managerroleid, $manager->id);

        $coursecategory = $this->getDataGenerator()->create_category();
        $coursecategorycontext = context_coursecat::instance($coursecategory->id);

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacher = $this->getDataGenerator()->create_and_enrol($course,
            'editingteacher');

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add contents by Manager in Context System.
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $systemcontext, false, 'systemtestfile1.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $systemcontext, false, 'systemtestfile2.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $systemcontext, false, 'systemtestfile3.h5p');
        // Add contents by Manager in Context Course Category.
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $coursecategorycontext, false, 'coursecattestfile1.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $coursecategorycontext, false, 'coursecattestfile2.h5p');
        // Add contents by Manager in Context Course.
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $coursecontext, false, 'coursetestfile1.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $manager->id, $coursecontext, false, 'coursetestfile2.h5p');
        // Add contents by Teacher.
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $teacher->id, $coursecontext, false, 'courseteacherfile1.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $teacher->id, $coursecontext, false, 'courseteacherfile2.h5p');
        $records = $generator->generate_contentbank_data('contenttype_testable',
            1, $teacher->id, $coursecontext, false, 'courseteacherfile3.h5p');

        $scenario = new stdClass();
        $scenario->systemcontext = $systemcontext;
        $scenario->coursecategorycontext = $coursecategorycontext;
        $scenario->coursecontext = $coursecontext;
        $scenario->manager = $manager;
        $scenario->teacher = $teacher;

        return $scenario;
    }

    /**
     * Ensure that export_user_preferences returns no data if the user has not visited any content bank.
     */
    public function test_export_user_preferences_no_pref() {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $this->getDataGenerator()->role_assign($managerroleid, $user->id);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences() {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('core_contentbank_view_list', 1);
        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $prefs = $writer->get_user_preferences('core_contentbank');
        $this->assertCount(1, (array) $prefs);
        $this->assertEquals(1, $prefs->core_contentbank_view_list->value);
        $this->assertEquals(
                get_string('privacy:request:preference:set', 'core_contentbank', (object) [
                        'name' => 'core_contentbank_view_list',
                        'value' => $prefs->core_contentbank_view_list->value,
                ]),
                $prefs->core_contentbank_view_list->description
        );
    }
}
