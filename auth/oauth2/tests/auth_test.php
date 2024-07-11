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
 * Auth oauth2 auth functions tests.
 *
 * @package    auth_oauth2
 * @category   test
 * @copyright  2019 Shamim Rezaie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \auth_oauth2\auth
 */
class auth_test extends \advanced_testcase {

    public function test_get_password_change_info(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['auth' => 'oauth2']);
        $auth = get_auth_plugin($user->auth);
        $info = $auth->get_password_change_info($user);

        $this->assertEqualsCanonicalizing(['subject', 'message'], array_keys($info));
        $this->assertStringContainsString(
                'your password cannot be reset because you are using your account on another site to log in',
                $info['message']);
    }

    /**
     * Test complete_login for oauth2.
     * @covers ::complete_login
     */
    public function test_oauth2_complete_login(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $wantsurl = new \moodle_url('/');

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        $info['firstname'] = 'Apple';
        $info['lastname'] = 'Fruit';
        $info['url'] = 'http://apple.com/';
        $info['alternamename'] = 'Beatles';
        $info['auth'] = 'oauth2';

        $user = \auth_oauth2\api::create_new_confirmed_account($info, $issuer);
        $auth = get_auth_plugin($user->auth);

        // Set up mock data.
        $client = $this->createMock(\core\oauth2\client::class);
        $client->expects($this->once())->method('get_raw_userinfo')->willReturn((object)$info);
        $client->expects($this->once())->method('get_userinfo')->willReturn($info);
        $client->expects($this->once())->method('get_issuer')->willReturn($issuer);

        $sink = $this->redirectEvents();
        try {
            // Need @ as it will fail at \core\session\manager::login_user for session_regenerate_id.
            @$auth->complete_login($client, $wantsurl);
        } catch (\Exception $e) {
            // This happens as complete login is using 'redirect'.
            $this->assertInstanceOf(\moodle_exception::class, $e);
        }
        $events = $sink->get_events();
        $sink->close();

        // There are 2 events. First is core\event\user_updated and second is core\event\user_loggedin.
        $event = $events[1];
        $this->assertInstanceOf('core\event\user_loggedin', $event);

        // Make sure the extra record is in the user_loggedin event.
        $extrauserinfo = $event->other['extrauserinfo'];
        $this->assertEquals($info, $extrauserinfo);
    }

    /**
     *  Test case for checking the email greetings in the password change information email.
     */
    public function test_email_greetings(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user(['auth' => 'oauth2']);
        $auth = get_auth_plugin($user->auth);
        $info = $auth->get_password_change_info($user);
        $this->assertStringContainsString('Hi ' . $user->firstname, quoted_printable_decode($info['message']));
    }
}
