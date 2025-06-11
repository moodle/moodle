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
 * Privacy test for auth_oidc
 *
 * @package auth_oidc
 * @author Remote-Learner.net Inc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2019 Remote Learner.net Inc http://www.remote-learner.net
 */

namespace auth_oidc;

use auth_oidc\privacy\provider;

/**
 * Privacy test for auth_oidc
 *
 * @group auth_oidc
 * @group auth_oidc_privacy
 * @group office365
 * @group office365_privacy
 */
final class privacy_provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Tests set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Check that a user context is returned if there is any user data for this user.
     *
     * @covers \auth_oidc\privacy\provider::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create user records.
        self::create_token($user->id);
        self::create_prevlogin($user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned and is the expected context.
        $usercontext = \context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that only users with a user context are fetched.
     *
     * @covers \auth_oidc\privacy\provider::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();

        $component = 'auth_oidc';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        // The list of users should not return anything yet (related data still haven't been created).
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create user records.
        self::create_token($user->id);
        self::create_prevlogin($user->id);

        // The list of users for user context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $userlist = new \core_privacy\local\request\userlist(\context_system::instance(), $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that user data is exported correctly.
     *
     * @covers \auth_oidc\privacy\provider::export_user_data
     */
    public function test_export_user_data(): void {
        // Create a user record.
        $user = $this->getDataGenerator()->create_user();
        $tokenrecord = self::create_token($user->id);
        $prevloginrecord = self::create_prevlogin($user->id);

        $usercontext = \context_user::instance($user->id);

        $writer = \core_privacy\local\request\writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'auth_oidc', [$usercontext->id]);
        provider::export_user_data($approvedlist);
        // Token.
        $data = $writer->get_data([
                get_string('privacy:metadata:auth_oidc', 'auth_oidc'),
                get_string('privacy:metadata:auth_oidc_token', 'auth_oidc'),
        ]);
        $this->assertEquals($tokenrecord->userid, $data->userid);
        $this->assertEquals($tokenrecord->token, $data->token);
        // Previous login.
        $data = $writer->get_data([
                get_string('privacy:metadata:auth_oidc', 'auth_oidc'),
                get_string('privacy:metadata:auth_oidc_prevlogin', 'auth_oidc'),
        ]);
        $this->assertEquals($prevloginrecord->userid, $data->userid);
        $this->assertEquals($prevloginrecord->method, $data->method);
        $this->assertEquals($prevloginrecord->password, $data->password);
    }

    /**
     * Test deleting all user data for a specific context.
     *
     * @covers \auth_oidc\privacy\provider::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        // Create a user record.
        $user1 = $this->getDataGenerator()->create_user();
        self::create_token($user1->id);
        self::create_prevlogin($user1->id);
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        self::create_token($user2->id);
        self::create_prevlogin($user2->id);

        // Get all accounts. There should be two.
        $this->assertCount(2, $DB->get_records('auth_oidc_token', []));
        $this->assertCount(2, $DB->get_records('auth_oidc_prevlogin', []));

        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($user1context);

        $this->assertCount(0, $DB->get_records('auth_oidc_token', ['userid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('auth_oidc_prevlogin', ['userid' => $user1->id]));

        // Get all accounts. There should be one.
        $this->assertCount(1, $DB->get_records('auth_oidc_token', []));
        $this->assertCount(1, $DB->get_records('auth_oidc_prevlogin', []));
    }

    /**
     * This should work identical to the above test.
     *
     * @covers \auth_oidc\privacy\provider::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        // Create a user record.
        $user1 = $this->getDataGenerator()->create_user();
        self::create_token($user1->id);
        self::create_prevlogin($user1->id);
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        self::create_token($user2->id);
        self::create_prevlogin($user2->id);

        // Get all accounts. There should be two.
        $this->assertCount(2, $DB->get_records('auth_oidc_token', []));
        $this->assertCount(2, $DB->get_records('auth_oidc_prevlogin', []));

        // Delete everything for the first user.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'auth_oidc', [$user1context->id]);
        provider::delete_data_for_user($approvedlist);

        $this->assertCount(0, $DB->get_records('auth_oidc_token', ['userid' => $user1->id]));
        $this->assertCount(0, $DB->get_records('auth_oidc_prevlogin', ['userid' => $user1->id]));

        // Get all accounts. There should be one.
        $this->assertCount(1, $DB->get_records('auth_oidc_token', []));
        $this->assertCount(1, $DB->get_records('auth_oidc_prevlogin', []));
    }

    /**
     * Test that data for users in approved userlist is deleted.
     *
     * @covers \auth_oidc\privacy\provider::delete_data_for_users
     */
    public function test_delete_data_for_users(): void {
        $this->resetAfterTest();

        $component = 'auth_oidc';
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        self::create_token($user1->id);
        self::create_prevlogin($user1->id);

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        self::create_token($user2->id);
        self::create_prevlogin($user2->id);

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
        $systemcontext = \context_system::instance();
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
     * Create a token record for the specified userid.
     *
     * @param int $userid
     * @return \stdClass
     * @throws \dml_exception
     */
    private static function create_token(int $userid): \stdClass {
        global $DB;
        $record = new \stdClass();
        $record->oidcuniqid = "user@example.com";
        $record->username = "user@example.com";
        $record->userid = $userid;
        $record->oidcusername = "user@example.com";
        $record->useridentifier = "user@example.com";
        $record->scope = "All";
        $record->tokenresource = "https://graph.microsoft.com";
        $record->authcode = "authcode123";
        $record->token = "token123";
        $record->expiry = 12345;
        $record->refreshtoken = "refresh123";
        $record->idtoken = "idtoken123";
        $record->id = $DB->insert_record('auth_oidc_token', $record);
        return $record;
    }

    /**
     * Create a previous login record for the specified userid.
     *
     * @param int $userid
     * @return \stdClass
     * @throws \dml_exception
     */
    private static function create_prevlogin(int $userid): \stdClass {
        global $DB;
        $record = new \stdClass();
        $record->userid = $userid;
        $record->method = "manual";
        $record->password = "abc123";
        $record->id = $DB->insert_record('auth_oidc_prevlogin', $record);
        return $record;
    }

}
