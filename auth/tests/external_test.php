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
 * Auth external functions tests.
 *
 * @package    core_auth
 * @category   external
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External auth API tests.
 *
 * @package     core_auth
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.2
 */
class core_auth_external_testcase extends externallib_advanced_testcase {

    /** @var string Original error log */
    protected $oldlog;

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->registerauth = 'email';

        // Discard error logs.
        $this->oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log");
    }

    /**
     * Tear down to restore old logging..
     */
    protected function tearDown(): void {
        ini_set('error_log', $this->oldlog);
        parent::tearDown();
    }

    /**
     * Test confirm_user
     */
    public function test_confirm_user() {
        global $DB;

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $secret = $DB->get_field('user', 'secret', array('username' => $username));

        // Confirm the user.
        $result = core_auth_external::confirm_user($username, $secret);
        $result = external_api::clean_returnvalue(core_auth_external::confirm_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $confirmed = $DB->get_field('user', 'confirmed', array('username' => $username));
        $this->assertEquals(1, $confirmed);

        // Try to confirm the user again.
        $result = core_auth_external::confirm_user($username, $secret);
        $result = external_api::clean_returnvalue(core_auth_external::confirm_user_returns(), $result);
        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('alreadyconfirmed', $result['warnings'][0]['warningcode']);

        // Try to use an invalid secret.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidconfirmdata', 'error'));
        $result = core_auth_external::confirm_user($username, 'zzZZzz');
    }

    /**
     * Test age digital consent not enabled.
     */
    public function test_age_digital_consent_verification_is_not_enabled() {
        global $CFG;

        $CFG->agedigitalconsentverification = 0;
        $result = core_auth_external::is_age_digital_consent_verification_enabled();
        $result = external_api::clean_returnvalue(
            core_auth_external::is_age_digital_consent_verification_enabled_returns(), $result);
        $this->assertFalse($result['status']);
    }

    /**
     * Test age digital consent is enabled.
     */
    public function test_age_digital_consent_verification_is_enabled() {
        global $CFG;

        $CFG->agedigitalconsentverification = 1;
        $result = core_auth_external::is_age_digital_consent_verification_enabled();
        $result = external_api::clean_returnvalue(
            core_auth_external::is_age_digital_consent_verification_enabled_returns(), $result);
        $this->assertTrue($result['status']);
    }

    /**
     * Test resend_confirmation_email.
     */
    public function test_resend_confirmation_email() {
        global $DB;

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);

        $result = core_auth_external::resend_confirmation_email($username, $password);
        $result = external_api::clean_returnvalue(core_auth_external::resend_confirmation_email_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);
        $confirmed = $DB->get_field('user', 'confirmed', array('username' => $username));
        $this->assertEquals(0, $confirmed);
    }

    /**
     * Test resend_confirmation_email invalid username.
     */
    public function test_resend_confirmation_email_invalid_username() {

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('error/invalidlogin');
        $result = core_auth_external::resend_confirmation_email('abc', $password);
    }

    /**
     * Test resend_confirmation_email invalid password.
     */
    public function test_resend_confirmation_email_invalid_password() {

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('error/invalidlogin');
        $result = core_auth_external::resend_confirmation_email($username, 'abc');
    }

    /**
     * Test resend_confirmation_email already confirmed user.
     */
    public function test_resend_confirmation_email_already_confirmed_user() {
        global $DB;

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $secret = $DB->get_field('user', 'secret', array('username' => $username));

        // Confirm the user.
        $result = core_auth_external::confirm_user($username, $secret);
        $result = external_api::clean_returnvalue(core_auth_external::confirm_user_returns(), $result);
        $this->assertTrue($result['success']);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('error/alreadyconfirmed');
        core_auth_external::resend_confirmation_email($username, $password);
    }
}
