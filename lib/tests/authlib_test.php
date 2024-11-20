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


namespace core;

/**
 * Authentication related tests.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \auth_plugin_base
 */
class authlib_test extends \advanced_testcase {
    public function test_lockout(): void {
        global $CFG;
        require_once("$CFG->libdir/authlib.php");

        $this->resetAfterTest();

        $oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log"); // Prevent standard logging.

        unset_config('noemailever');

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
        $sink = $this->redirectEmails();
        login_attempt_failed($user);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
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

        $sink = $this->redirectEmails();
        login_attempt_failed($user);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
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

    public function test_authenticate_user_login(): void {
        global $CFG;

        $this->resetAfterTest();

        $oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log"); // Prevent standard logging.

        unset_config('noemailever');

        set_config('lockoutthreshold', 0);
        set_config('lockoutwindow', 60*20);
        set_config('lockoutduration', 60*30);

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.

        $user1 = $this->getDataGenerator()->create_user(array('username'=>'username1', 'password'=>'password1', 'email'=>'email1@example.com'));
        $user2 = $this->getDataGenerator()->create_user(array('username'=>'username2', 'password'=>'password2', 'email'=>'email2@example.com', 'suspended'=>1));
        $user3 = $this->getDataGenerator()->create_user(array('username'=>'username3', 'password'=>'password3', 'email'=>'email2@example.com', 'auth'=>'nologin'));

        // Normal login.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1');
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($user1->id, $result->id);

        // Normal login with reason.
        $reason = null;
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        // Test login via email
        $reason = null;
        $this->assertEmpty($CFG->authloginviaemail);
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('email1@example.com', 'password1', false, $reason);
        $sink->close();
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $reason);

        set_config('authloginviaemail', 1);
        $this->assertNotEmpty($CFG->authloginviaemail);
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('email1@example.com', 'password1');
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($user1->id, $result->id);

        $reason = null;
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('email2@example.com', 'password2', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $reason);
        set_config('authloginviaemail', 0);

        $reason = null;
        // Capture failed login event.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        // Test Event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username1');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_FAILED);
        $this->assertEventContextNotUsed($event);

        // Capture failed login token.
        unset($CFG->alternateloginurl);
        unset($CFG->disablelogintoken);
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, 'invalidtoken');
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        // Test Event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username1');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_FAILED);
        $this->assertEventContextNotUsed($event);

        // Login should work with invalid token if CFG login token settings override it.
        $CFG->alternateloginurl = 'http://localhost/';
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, 'invalidtoken');
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        unset($CFG->alternateloginurl);
        $CFG->disablelogintoken = true;

        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, 'invalidtoken');
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        unset($CFG->disablelogintoken);
        // Normal login with valid token.
        $reason = null;
        $token = \core\session\manager::get_login_token();
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, $token);
        $events = $sink->get_events();
        $sink->close();
        $this->assertEmpty($events);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        $reason = null;
        // Capture failed login event.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username2', 'password2', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_SUSPENDED, $reason);
        // Test Event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username2');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_SUSPENDED);
        $this->assertEventContextNotUsed($event);

        $reason = null;
        // Capture failed login event.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username3', 'password3', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_SUSPENDED, $reason);
        // Test Event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username3');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_SUSPENDED);
        $this->assertEventContextNotUsed($event);

        $reason = null;
        // Capture failed login event.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username4', 'password3', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $reason);
        // Test Event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username4');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_NOUSER);
        $this->assertEventContextNotUsed($event);

        set_config('lockoutthreshold', 3);

        $reason = null;
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);
        $sink = $this->redirectEmails();
        $result = authenticate_user_login('username1', 'nopass', false, $reason);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED, $reason);

        $result = authenticate_user_login('username1', 'password1', false, $reason);
        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_LOCKOUT, $reason);

        $result = authenticate_user_login('username1', 'password1', true, $reason);
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);

        ini_set('error_log', $oldlog);

        // Test password policy check on login.
        $CFG->passwordpolicy = 0;
        $CFG->passwordpolicycheckonlogin = 1;

        // First test with password policy disabled.
        $user4 = $this->getDataGenerator()->create_user(array('username' => 'username4', 'password' => 'a'));
        $sink = $this->redirectEvents();
        $reason = null;
        $result = authenticate_user_login('username4', 'a', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $notifications = notification::fetch();
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);
        $this->assertEquals(get_user_preferences('auth_forcepasswordchange', false, $result), false);
        // Check no events.
        $this->assertEquals(count($events), 0);
        // Check no notifications.
        $this->assertEquals(count($notifications), 0);

        // Now test with the password policy enabled, flip reset flag.
        $sink = $this->redirectEvents();
        $reason = null;
        $CFG->passwordpolicy = 1;
        $result = authenticate_user_login('username4', 'a', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);
        $this->assertEquals(get_user_preferences('auth_forcepasswordchange', true, $result), true);
        // Check that an event was emitted for the policy failure.
        $this->assertEquals(count($events), 1);
        $this->assertEquals(reset($events)->eventname, '\core\event\user_password_policy_failed');
        // Check notification fired.
        $notifications = notification::fetch();
        $this->assertEquals(count($notifications), 1);

        // Now the same tests with a user that passes the password policy.
        $user5 = $this->getDataGenerator()->create_user(array('username' => 'username5', 'password' => 'ThisPassword1sSecure!'));
        $reason = null;
        $CFG->passwordpolicy = 0;
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username5', 'ThisPassword1sSecure!', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $notifications = notification::fetch();
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);
        $this->assertEquals(get_user_preferences('auth_forcepasswordchange', false, $result), false);
        // Check no events.
        $this->assertEquals(count($events), 0);
        // Check no notifications.
        $this->assertEquals(count($notifications), 0);

        $reason = null;
        $CFG->passwordpolicy = 1;
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username5', 'ThisPassword1sSecure!', false, $reason);
        $events = $sink->get_events();
        $sink->close();
        $notifications = notification::fetch();
        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals(AUTH_LOGIN_OK, $reason);
        $this->assertEquals(get_user_preferences('auth_forcepasswordchange', false, $result), false);
        // Check no events.
        $this->assertEquals(count($events), 0);
        // Check no notifications.
        $this->assertEquals(count($notifications), 0);

        // Capture failed login reCaptcha.
        $CFG->recaptchapublickey = 'randompublickey';
        $CFG->recaptchaprivatekey = 'randomprivatekey';
        $CFG->enableloginrecaptcha = true;

        // Login with blank captcha.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, false, '');
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED_RECAPTCHA, $reason);

        // Test event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username1');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_FAILED_RECAPTCHA);
        $this->assertEventContextNotUsed($event);

        // Login with invalid captcha.
        $sink = $this->redirectEvents();
        $result = authenticate_user_login('username1', 'password1', false, $reason, false, 'invalidcaptcha');
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertFalse($result);
        $this->assertEquals(AUTH_LOGIN_FAILED_RECAPTCHA, $reason);

        // Test event.
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], 'username1');
        $this->assertSame($eventdata['other']['reason'], AUTH_LOGIN_FAILED_RECAPTCHA);
        $this->assertEventContextNotUsed($event);

        // Unset settings.
        unset($CFG->recaptchapublickey);
        unset($CFG->recaptchaprivatekey);
        unset($CFG->enableloginrecaptcha);
    }

    public function test_user_loggedin_event_exceptions(): void {
        try {
            $event = \core\event\user_loggedin::create(array('objectid' => 1));
            $this->fail('\core\event\user_loggedin requires other[\'username\']');
        } catch(\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test the {@link signup_validate_data()} duplicate email validation.
     */
    public function test_signup_validate_data_same_email(): void {
        global $CFG;
        require_once($CFG->libdir . '/authlib.php');
        require_once($CFG->libdir . '/phpmailer/moodle_phpmailer.php');
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $this->resetAfterTest();

        $CFG->registerauth = 'email';
        $CFG->passwordpolicy = false;

        // In this test, we want to check accent-sensitive email search. However, accented email addresses do not pass
        // the default `validate_email()` and Moodle does not yet provide a CFG switch to allow such emails.  So we
        // inject our own validation method here and revert it back once we are done. This custom validator method is
        // identical to the default 'php' validator with the only difference: it has the FILTER_FLAG_EMAIL_UNICODE set
        // so that it allows to use non-ASCII characters in email addresses.
        $defaultvalidator = \moodle_phpmailer::$validator;
        \moodle_phpmailer::$validator = function($address) {
            return (bool) filter_var($address, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
        };

        // Check that two users cannot share the same email address if the site is configured so.
        // Emails in Moodle are supposed to be case-insensitive (and accent-sensitive but accents are not yet supported).
        $CFG->allowaccountssameemail = false;

        $u1 = $this->getDataGenerator()->create_user([
            'username' => 'abcdef',
            'email' => 'abcdef@example.com',
        ]);

        $formdata = [
            'username' => 'newuser',
            'firstname' => 'First',
            'lastname' => 'Last',
            'password' => 'weak',
            'email' => 'ABCDEF@example.com',
        ];

        $errors = signup_validate_data($formdata, []);
        $this->assertStringContainsString('This email address is already registered.', $errors['email']);

        // Emails are accent-sensitive though so if we change a -> á in the u1's email, it should pass.
        // Please note that Moodle does not normally support such emails yet. We test the DB search sensitivity here.
        $formdata['email'] = 'ábcdef@example.com';
        $errors = signup_validate_data($formdata, []);
        $this->assertArrayNotHasKey('email', $errors);

        // Check that users can share the same email if the site is configured so.
        $CFG->allowaccountssameemail = true;
        $formdata['email'] = 'abcdef@example.com';
        $errors = signup_validate_data($formdata, []);
        $this->assertArrayNotHasKey('email', $errors);

        // Restore the original email address validator.
        \moodle_phpmailer::$validator = $defaultvalidator;
    }

    /**
     * Test the find_cli_user method
     */
    public function test_find_cli_user(): void {
        global $CFG, $USER;
        require_once("$CFG->libdir/authlib.php");
        require_once("$CFG->libdir/tests/fixtures/testable_auth_plugin_base.php");

        $this->resetAfterTest();

        $user = \testable_auth_plugin_base::find_cli_admin_user();
        $this->assertEmpty($user);

        $u1 = $this->getDataGenerator()->create_user([
            'username' => 'abcdef',
            'email' => 'abcdef@example.com',
        ]);
        $user = \testable_auth_plugin_base::find_cli_admin_user();
        $this->assertEmpty($user); // User is not an admin yet.

        \testable_auth_plugin_base::login_cli_admin_user();
        $this->assertEquals($USER->id, 0); // User is not logged in.

        $CFG->siteadmins .= "," . $u1->id;

        \testable_auth_plugin_base::login_cli_admin_user();
        $this->assertEquals($USER->id, $u1->id); // User is now logged in.

        $user = \testable_auth_plugin_base::find_cli_admin_user();
        $this->assertNotEmpty($user);
    }

    /**
     * Test the get_enabled_auth_plugin_classes method
     */
    public function test_get_enabled_auth_plugin_classes(): void {
        global $CFG;
        require_once("$CFG->libdir/authlib.php");
        $plugins = \auth_plugin_base::get_enabled_auth_plugin_classes();
        $this->assertEquals(get_class($plugins[0]), 'auth_plugin_manual');
        $this->assertEquals(count($plugins), 3);
    }

    /**
     * Test case for checking the email greetings in account lockout notification emails.
     *
     * @covers ::login_lock_account()
     */
    public function test_email_greetings(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $sink = $this->redirectEmails(); // Make sure we are redirecting emails.
        login_lock_account($user);
        $result = $sink->get_messages();
        $sink->close();
        // Test greetings.
        $this->assertStringContainsString('Hi ' . $user->firstname, quoted_printable_decode($result[0]->body));
    }

}
