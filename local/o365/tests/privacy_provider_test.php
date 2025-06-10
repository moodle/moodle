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
 * Privacy test for local_o365
 *
 * @package local_o365
 * @author Remote-Learner.net Inc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2019 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

use \local_o365\privacy\provider;

/**
 * Privacy test for the local_o365
 *
 * @group local_o365
 * @group local_o365_privacy
 * @group office365
 * @group office365_privacy
 */
class local_o365_privacy_testcase extends \core_privacy\tests\provider_testcase {
    /**
     * Tests set up.
     */
    protected function setUp() : void {
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
        $usercontext = \context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'local_o365';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        // The list of users should not return anything yet (related data still haven't been created).
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
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
        $userlist = new \core_privacy\local\request\userlist(context_system::instance(), $component);
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

        $usercontext = \context_user::instance($user->id);

        $writer = \core_privacy\local\request\writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $approvedlist = new core_privacy\local\request\approved_contextlist($user, 'local_o365', [$usercontext->id]);
        provider::export_user_data($approvedlist);

        foreach ($userrecords as $table => $record) {
            $data = $writer->get_data([
                get_string('privacy:metadata:local_o365', 'local_o365'),
                get_string('privacy:metadata:'.$table, 'local_o365')
            ]);
            foreach ($record as $k => $v) {
                $this->assertEquals((string)$v, $data->$k);
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
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);

        // Get all accounts. There should be two.
        foreach ($user1records as $table => $record) {
            $this->assertCount(2, $DB->get_records($table, []));
        }

        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($user1context);

        $this->assertCount(0, $DB->get_records('local_o365_calidmap', ['userid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_calsub', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_connections', ['muserid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_token', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_objects', ['moodleid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_appassign', ['muserid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_matchqueue', ['musername' => $user1->username]));
        $this->assertCount(0, $DB->get_records('local_o365_calsettings', ['user_id' => $user1->id]));

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
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);

        // Get all accounts. There should be two.
        foreach ($user1records as $table => $record) {
            $this->assertCount(2, $DB->get_records($table, []));
        }

        // Delete everything for the first user.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'local_o365', [$user1context->id]);
        provider::delete_data_for_user($approvedlist);

        $this->assertCount(0, $DB->get_records('local_o365_calidmap', ['userid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_calsub', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_connections', ['muserid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_token', ['user_id' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_objects', ['moodleid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_appassign', ['muserid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('local_o365_matchqueue', ['musername' => $user1->username]));
        $this->assertCount(0, $DB->get_records('local_o365_calsettings', ['user_id' => $user1->id]));

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

        $component = 'local_o365';
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $user1records = self::create_userdata($user1->id);
        $usercontext1 = context_user::instance($user1->id);

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $user2records = self::create_userdata($user2->id);
        $usercontext2 = context_user::instance($user2->id);

        // The list of users for usercontext1 should return user1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for usercontext2 should return user2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$user2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);

        // Add userlist1 to the approved user list.
        $approvedlist = new \core_privacy\local\request\approved_userlist($usercontext1, $component, $userlist1->get_userids());

        // Delete user data using delete_data_for_user for usercontext1.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in usercontext1 - The user list should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // User data should be only removed in the user context.
        $systemcontext = context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new \core_privacy\local\request\approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
    }

    /**
     * Create user data.
     *
     * @param int $userid
     * @return array
     */
    private static function create_userdata(int $userid) {
        $records = [
            'local_o365_calidmap' => self::create_calidmap($userid),
            'local_o365_calsub' => self::create_calsub($userid),
            'local_o365_connections' => self::create_connections($userid),
            'local_o365_token' => self::create_token($userid),
            'local_o365_objects' => self::create_objects($userid),
            'local_o365_appassign' => self::create_appassign($userid),
            'local_o365_matchqueue' => self::create_matchqueue($userid),
            'local_o365_calsettings' => self::create_calsettings($userid),
        ];
        return $records;
    }

    /**
     * Create a calidmap record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_calidmap(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->eventid = 123;
        $record->outlookeventid = "abc123";
        $record->origin = 'moodle';
        $record->userid = $userid;
        $record->id = $DB->insert_record('local_o365_calidmap', $record);
        return $record;
    }

    /**
     * Create a calsub record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_calsub(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->user_id = $userid;
        $record->caltype = "site";
        $record->caltypeid = 123;
        $record->o365calid = "o365calobjectid";
        $record->isprimary = 1;
        $record->syncbehav = "out";
        $record->timecreated = 123456;
        $record->id = $DB->insert_record('local_o365_calsub', $record);
        return $record;
    }

    /**
     * Create a connections record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_connections(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->muserid = $userid;
        $record->aadupn = "user".$userid."@example.com";
        $record->uselogin = 1;
        $record->id = $DB->insert_record('local_o365_connections', $record);
        return $record;
    }

    /**
     * Create a token record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_token(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->user_id = $userid;
        $record->scope = "all";
        $record->tokenresource = "https://graph.microsoft.com";
        $record->token = 'token12345';
        $record->expiry = 123456;
        $record->refreshtoken = 'refreshtoken1234567';
        $record->id = $DB->insert_record('local_o365_token', $record);
        return $record;
    }

    /**
     * Create a objects record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_objects(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->type = 'user';
        $record->subtype = '';
        $record->objectid = "userobjectid123";
        $record->moodleid = $userid;
        $record->o365name = "user@example.com";
        $record->tenant = 'tenant123';
        $record->metadata = '';
        $record->timecreated = 123456;
        $record->timemodified = 123457;
        $record->id = $DB->insert_record('local_o365_objects', $record);
        return $record;
    }

    /**
     * Create a appassign record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_appassign(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->muserid = $userid;
        $record->assigned = 1;
        $record->photoid = 'photoid123';
        $record->photoupdated = 123457;
        $record->id = $DB->insert_record('local_o365_appassign', $record);
        return $record;
    }

    /**
     * Create a matchqueue record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_matchqueue(int $userid): \stdClass {
        global $DB;
        $user = $DB->get_record('user', ['id' => $userid]);
        $record = new stdClass();
        $record->musername = $user->username;
        $record->o365username = 'user@example.com';
        $record->openidconnect = 1;
        $record->completed = 1;
        $record->errormessage = 'some error message';
        $record->id = $DB->insert_record('local_o365_matchqueue', $record);
        return $record;
    }

    /**
     * Create a calsettings record for the specified userid.
     *
     * @param int $userid
     * @return stdClass
     * @throws dml_exception
     */
    private static function create_calsettings(int $userid): \stdClass {
        global $DB;
        $record = new stdClass();
        $record->user_id = $userid;
        $record->o365calid = 'calid1234';
        $record->timecreated = 1234567;
        $record->id = $DB->insert_record('local_o365_calsettings', $record);
        return $record;
    }

}

