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
 * Privacy test for local_onenote
 *
 * @package local_onenote
 * @author Remote-Learner.net Inc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2019 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use \local_onenote\privacy\provider;

/**
 * Privacy test for local_onenote
 *
 * @group local_onenote
 * @group local_onenote_privacy
 * @group office365
 * @group office365_privacy
 */
class local_onenote_privacy_testcase extends provider_testcase {
    /**
     * Tests set up.
     */
    public function setUp() : void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Check that a user context is returned if there is any user data for this user.
     */
    public function test_get_contexts_for_userid() {
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create user records.
        self::create_userdata($user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned and is the expected context.
        $usercontext = context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'local_onenote';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        // The list of users should not return anything yet (related data still haven't been created).
        $userlist = new userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create user records.
        self::create_userdata($user->id);

        // The list of users for user context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $userlist = new userlist(context_system::instance(), $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data() {
        // Create a user record.
        $user = $this->getDataGenerator()->create_user();

        // Create user records.
        $userrecords = self::create_userdata($user->id);

        $usercontext = context_user::instance($user->id);

        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $approvedlist = new core_privacy\local\request\approved_contextlist($user, 'local_onenote', [$usercontext->id]);
        provider::export_user_data($approvedlist);

        foreach ($userrecords as $table => $record) {
            $data = $writer->get_data([get_string('privacy:metadata:local_onenote', 'local_onenote'),
                get_string('privacy:metadata:' . $table, 'local_onenote')]);
            foreach ($record as $k => $v) {
                $this->assertEquals((string) $v, $data->$k);
            }
        }
    }

    /**
     * Test deleting all user data for a specific context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Create user data.
        $user1 = $this->getDataGenerator()->create_user();
        $user1records = self::create_userdata($user1->id);
        $user1context = context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);

        // Get all accounts. There should be two.
        foreach ($user1records as $table => $record) {
            $this->assertCount(2, $DB->get_records($table, []));
        }

        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($user1context);

        $this->assertCount(0, $DB->get_records('local_onenote_user_sections', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_onenote_assign_pages', ['user_id' => $user1->id]));

        // Get all accounts. There should be one.
        foreach ($user1records as $table => $record) {
            $this->assertCount(1, $DB->get_records($table, []));
        }
    }

    /**
     * This should work identical to the above test.
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Create a user record.
        $user1 = $this->getDataGenerator()->create_user();
        $user1records = self::create_userdata($user1->id);
        $user1context = context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);

        // Get all accounts. There should be two.
        foreach ($user1records as $table => $record) {
            $this->assertCount(2, $DB->get_records($table, []));
        }

        // Delete everything for the first user.
        $approvedlist = new approved_contextlist($user1, 'local_onenote', [$user1context->id]);
        provider::delete_data_for_user($approvedlist);

        $this->assertCount(0, $DB->get_records('local_onenote_user_sections', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_onenote_assign_pages', ['user_id' => $user1->id]));

        // Get all accounts. There should be one.
        foreach ($user1records as $table => $record) {
            $this->assertCount(1, $DB->get_records($table, []));
        }
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'local_onenote';
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $user1records = self::create_userdata($user1->id);
        $usercontext1 = context_user::instance($user1->id);

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);
        $usercontext2 = context_user::instance($user2->id);

        // The list of users for usercontext1 should return user1.
        $userlist1 = new userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for usercontext2 should return user2.
        $userlist2 = new userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$user2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);

        // Add userlist1 to the approved user list.
        $approvedlist = new approved_userlist($usercontext1, $component, $userlist1->get_userids());

        // Delete user data using delete_data_for_user for usercontext1.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in usercontext1 - The user list should now be empty.
        $userlist1 = new userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // User data should be only removed in the user context.
        $systemcontext = context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
    }

    /**
     * Create all userdata records for a userid
     *
     * @param int $userid The user's ID.
     * @return array Array of records, indexed by table name.
     */
    private static function create_userdata(int $userid) {
        $records = ['local_onenote_user_sections' => self::create_usersections_record($userid),
            'local_onenote_assign_pages' => self::create_assignpages_record($userid),];
        return $records;
    }

    /**
     * Create a local_onenote_user_sections record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_usersections_record(int $userid) : \stdClass {
        global $DB;
        $record = new stdClass();
        $record->user_id = $userid;
        $record->course_id = 2;
        $record->section_id = 'sectionid123';
        $record->id = $DB->insert_record('local_onenote_user_sections', $record);
        return $record;
    }

    /**
     * Create a local_onenote_assign_pages record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_assignpages_record(int $userid) : \stdClass {
        global $DB;
        $record = new stdClass();
        $record->user_id = $userid;
        $record->assign_id = 2;
        $record->submission_student_page_id = "pageid1";
        $record->feedback_student_page_id = "pageid2";
        $record->feedback_teacher_page_id = "pageid3";
        $record->teacher_lastviewed = 123456;
        $record->student_lastmodified = 123458;
        $record->id = $DB->insert_record('local_onenote_assign_pages', $record);
        return $record;
    }
}
