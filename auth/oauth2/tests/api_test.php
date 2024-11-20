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

namespace auth_oauth2;

/**
 * External auth oauth2 API tests.
 *
 * @package     auth_oauth2
 * @copyright   2017 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \auth_oauth2\api
 */
class api_test extends \advanced_testcase {

    /**
     * Test the cleaning of orphaned linked logins for all issuers.
     */
    public function test_clean_orphaned_linked_logins(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('google');
        \core\oauth2\api::create_standard_issuer('microsoft');

        $user = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';
        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);

        \core\oauth2\api::delete_issuer($issuer->get('id'));

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        $this->assertCount(1, $linkedlogins);

        \auth_oauth2\api::clean_orphaned_linked_logins();

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        $this->assertCount(0, $linkedlogins);

        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);
        $this->assertFalse($match);
    }

    /**
     * Test the cleaning of orphaned linked logins for a specific issuer.
     */
    public function test_clean_orphaned_linked_logins_with_issuer_id(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer1 = \core\oauth2\api::create_standard_issuer('google');
        $issuer2 = \core\oauth2\api::create_standard_issuer('microsoft');

        $user1 = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';
        \auth_oauth2\api::link_login($info, $issuer1, $user1->id, false);

        $user2 = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        \auth_oauth2\api::link_login($info, $issuer2, $user2->id, false);

        \core\oauth2\api::delete_issuer($issuer1->get('id'));

        \auth_oauth2\api::clean_orphaned_linked_logins($issuer1->get('id'));

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user1->id, $issuer1);
        $this->assertCount(0, $linkedlogins);

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user2->id, $issuer2);
        $this->assertCount(1, $linkedlogins);
    }

    /**
     * Test creating a new confirmed account.
     * Including testing that user profile fields are correctly set.
     *
     * @covers \auth_oauth2\api::create_new_confirmed_account
     */
    public function test_create_new_confirmed_account(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        $info['firstname'] = 'Apple';
        $info['lastname'] = 'Fruit';
        $info['alternatename'] = 'Beatles';
        $info['idnumber'] = '123456';
        $info['city'] = 'Melbourne';
        $info['country'] = 'AU';
        $info['institution'] = 'ACME Inc';
        $info['department'] = 'Misc Explosives';

        $createduser = \auth_oauth2\api::create_new_confirmed_account($info, $issuer);

        // Get actual user record from DB to check.
        $userdata = $DB->get_record('user', ['id' => $createduser->id]);

        // Confirm each value supplied from issuers is saved into the user record.
        foreach ($info as $key => $value) {
            $this->assertEquals($value, $userdata->$key);
        }

        // Explicitly test the user is confirmed.
        $this->assertEquals(1, $userdata->confirmed);
    }

    /**
     * Test auto-confirming linked logins.
     */
    public function test_linked_logins(): void {
        $this->resetAfterTest();

        $this->setAdminUser();
        $issuer = \core\oauth2\api::create_standard_issuer('google');

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';

        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);

        // Try and match a user with a linked login.
        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);

        $this->assertEquals($user->id, $match->get('userid'));
        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        \auth_oauth2\api::delete_linked_login($linkedlogins[0]->get('id'));

        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);
        $this->assertFalse($match);

        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        $info['firstname'] = 'Apple';
        $info['lastname'] = 'Fruit';
        $info['url'] = 'http://apple.com/';
        $info['alternamename'] = 'Beatles';

        $newuser = \auth_oauth2\api::create_new_confirmed_account($info, $issuer);

        $match = \auth_oauth2\api::match_username_to_user('apple', $issuer);

        $this->assertEquals($newuser->id, $match->get('userid'));
    }

    /**
     * Test that we cannot deleted a linked login for another user
     */
    public function test_delete_linked_login_other_user(): void {
        $this->resetAfterTest();

        $this->setAdminUser();
        $issuer = \core\oauth2\api::create_standard_issuer('google');

        $user = $this->getDataGenerator()->create_user();

        api::link_login([
            'username' => 'banana',
            'email' => 'banana@example.com',
        ], $issuer, $user->id);

        /** @var linked_login $linkedlogin */
        $linkedlogin = api::get_linked_logins($user->id)[0];

        // We are logged in as a different user, so cannot delete this.
        $this->expectException(\dml_missing_record_exception::class);
        api::delete_linked_login($linkedlogin->get('id'));
    }

    /**
     * Test that is_enabled correctly identifies when the plugin is enabled.
     */
    public function test_is_enabled(): void {
        $this->resetAfterTest();

        set_config('auth', 'manual,oauth2');
        $this->assertTrue(\auth_oauth2\api::is_enabled());
    }

    /**
     * Test that is_enabled correctly identifies when the plugin is disabled.
     */
    public function test_is_enabled_disabled(): void {
        $this->resetAfterTest();

        set_config('auth', 'manual');
        $this->assertFalse(\auth_oauth2\api::is_enabled());
    }

    /**
     * Test creating a user via the send confirm account email method.
     * Including testing that user profile fields are correctly set.
     *
     * @covers \auth_oauth2\api::send_confirm_account_email
     */
    public function test_send_confirm_account_email(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        $info['firstname'] = 'Apple';
        $info['lastname'] = 'Fruit';
        $info['alternatename'] = 'Beatles';
        $info['idnumber'] = '123456';
        $info['city'] = 'Melbourne';
        $info['country'] = 'AU';
        $info['institution'] = 'ACME Inc';
        $info['department'] = 'Misc Explosives';

        $createduser = \auth_oauth2\api::send_confirm_account_email($info, $issuer);

        // Get actual user record from DB to check.
        $userdata = $DB->get_record('user', ['id' => $createduser->id]);

        // Confirm each value supplied from issuers is saved into the user record.
        foreach ($info as $key => $value) {
            $this->assertEquals($value, $userdata->$key);
        }

        // Explicitly test the user is not yet confirmed.
        $this->assertEquals(0, $userdata->confirmed);
    }

    /**
     * Test case for checking the email greetings in OAuth2 confirmation emails.
     */
    public function test_email_greetings(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $userinfo = [];
        $userinfo['username'] = 'apple';
        $userinfo['email'] = 'apple@example.com';
        $userinfo['firstname'] = 'Apple';
        $userinfo['lastname'] = 'Fruit';
        $sink = $this->redirectEmails(); // Make sure we are redirecting emails.
        \auth_oauth2\api::send_confirm_account_email($userinfo, $issuer);
        $result = $sink->get_messages();
        $sink->close();
        // Test greetings.
        $this->assertStringContainsString('Hi ' . $userinfo['firstname'], quoted_printable_decode($result[0]->body));

        $userinfo = [];
        $userinfo['username'] = 'banana';
        $userinfo['email'] = 'banana@example.com';
        $userinfo['firstname'] = 'Banana';
        $userinfo['lastname'] = 'Fruit';
        $user = $this->getDataGenerator()->create_user();
        $sink = $this->redirectEmails(); // Make sure we are redirecting emails.
        \auth_oauth2\api::send_confirm_link_login_email($userinfo, $issuer, $user->id);
        $result = $sink->get_messages();
        $sink->close();
        // Test greetings.
        $this->assertStringContainsString('Hi ' . $user->firstname, quoted_printable_decode($result[0]->body));
    }
}
