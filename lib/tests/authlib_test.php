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
 * Authentication related tests.
 *
 * @package    core_auth
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Functional test for authentication related APIs.
 */
class authlib_testcase extends advanced_testcase {
    public function test_lockout() {
        global $CFG;
        require_once("$CFG->libdir/authlib.php");

        $this->resetAfterTest();

        $oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log"); // Prevent standard logging.

        set_config('lockoutthreshold', 0);
        set_config('lockoutwindow', 60*20);
        set_config('lockoutduration', 60*30);

        $user = $this->getDataGenerator()->create_user();


        // Test lockout is disabled when threshold not set.

        $this->assertFalse(login_is_lockedout($user));
        login_attempt_failed($user);
        login_attempt_failed($user);
        login_attempt_failed($user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));


        // Test lockout threshold works.

        set_config('lockoutthreshold', 3);
        login_attempt_failed($user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));
        ob_start();
        login_attempt_failed($user);
        $output = ob_get_clean();
        $this->assertContains('noemailever', $output);
        $this->assertTrue(login_is_lockedout($user));


        // Test unlock works.

        login_unlock_account($user);
        $this->assertFalse(login_is_lockedout($user));


        // Test lockout window works.

        login_attempt_failed($user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));
        set_user_preference('login_failed_last', time()-60*20-10, $user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));


        // Test valid login resets window.

        login_attempt_valid($user);
        $this->assertFalse(login_is_lockedout($user));
        login_attempt_failed($user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));


        // Test lock duration works.

        ob_start(); // Prevent nomailever notice.
        login_attempt_failed($user);
        $output = ob_get_clean();
        $this->assertContains('noemailever', $output);
        $this->assertTrue(login_is_lockedout($user));
        set_user_preference('login_lockout', time()-60*30+10, $user);
        $this->assertTrue(login_is_lockedout($user));
        set_user_preference('login_lockout', time()-60*30-10, $user);
        $this->assertFalse(login_is_lockedout($user));


        // Test lockout ignored pref works.

        set_user_preference('login_lockout_ignored', 1, $user);
        login_attempt_failed($user);
        login_attempt_failed($user);
        login_attempt_failed($user);
        login_attempt_failed($user);
        $this->assertFalse(login_is_lockedout($user));

        ini_set('error_log', $oldlog);
    }

    public function test_authenticate_user_login() {
        global $CFG;

        $this->resetAfterTest();

        $oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log"); // Prevent standard logging.

        set_config('lockoutthreshold', 0);
        set_config('lockoutwindow', 60*20);
        set_config('lockoutduration', 60*30);

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.

        $user1 = $this->getDataGenerator()->create_user(array('username'=>'username1', 'password'=>'password1'));
        $user2 = $this->getDataGenerator()->create_user(array('username'=>'username2', 'password'=>'password2', 'suspended'=>1));
        $user3 = $this->getDataGenerator()->create_user(array('username'=>'username3', 'password'=>'password3', 'auth'=>'nologin'));

        $result = authenticate_user_login('username1', 'password1');
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($user1->id, $result->id);

        $reason = null;
        $result = authenticate_user_login('username1', 'password1', false, $reason);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        $reason = null;
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);

        $reason = null;
        $result = authenticate_user_login('username2', 'password2', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_SUSPENDED, $reason);

        $reason = null;
        $result = authenticate_user_login('username3', 'password3', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_SUSPENDED, $reason);

        $reason = null;
        $result = authenticate_user_login('username4', 'password3', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $reason);


        set_config('lockoutthreshold', 3);
        $reason = null;
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        ob_start(); // Prevent nomailever notice.
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        ob_end_clean();
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);

        $result = authenticate_user_login('username1', 'password1', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_LOCKOUT, $reason);

        $result = authenticate_user_login('username1', 'password1', true, $reason);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        ini_set('error_log', $oldlog);
    }
}
