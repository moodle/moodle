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
 * Unit tests for login lib.
 *
 * @package    core
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/login/lib.php');

/**
 * Login lib testcase.
 *
 * @package    core
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_login_lib_testcase extends advanced_testcase {

    public function test_core_login_process_password_reset_one_time_without_username_protection() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        $user = $this->getDataGenerator()->create_user(array('auth' => 'manual'));

        $sink = $this->redirectEmails();

        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/A password reset was requested for your account/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_two_consecutive_times_without_username_protection() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        $user = $this->getDataGenerator()->create_user(array('auth' => 'manual'));

        $sink = $this->redirectEmails();

        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        // Request for a second time.
        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(2, $emails); // Two emails sent (one per each request).
        $email = array_pop($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/A password reset was requested for your account/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_three_consecutive_times_without_username_protection() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        $user = $this->getDataGenerator()->create_user(array('auth' => 'manual'));

        $sink = $this->redirectEmails();

        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        // Request for a second time.
        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        // Third time.
        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailalreadysent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(2, $emails); // Third time email is not sent.
    }

    public function test_core_login_process_password_reset_one_time_with_username_protection() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->protectusernames = 1;
        $user = $this->getDataGenerator()->create_user(array('auth' => 'manual'));

        $sink = $this->redirectEmails();

        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailpasswordconfirmmaybesent', $status);   // Generic message not giving clues.
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/A password reset was requested for your account/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_with_preexisting_expired_request_without_username_protection() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        $user = $this->getDataGenerator()->create_user(array('auth' => 'manual'));

        $sink = $this->redirectEmails();

        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        // Request again.
        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);

        $resetrequests = $DB->get_records('user_password_resets');
        $request = reset($resetrequests);
        $request->timerequested = time() - YEARSECS;
        $DB->update_record('user_password_resets', $request);

        // Request again - third time - but it shuld be expired so we should get an email.
        list($status, $notice, $url) = core_login_process_password_reset($user->username, null);
        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(3, $emails); // Normal process, the previous request was deleted.
        $email = reset($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/A password reset was requested for your account/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_disabled_auth() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user(array('auth' => 'oauth2'));

        $sink = $this->redirectEmails();

        core_login_process_password_reset($user->username, null);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/Unfortunately your account on this site is disabled/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_auth_not_supporting_email_reset() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->auth = $CFG->auth . ',mnet';
        $user = $this->getDataGenerator()->create_user(array('auth' => 'mnet'));

        $sink = $this->redirectEmails();

        core_login_process_password_reset($user->username, null);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($user->email, $email->to);
        $this->assertNotEmpty($email->header);
        $this->assertNotEmpty($email->body);
        $this->assertMatchesRegularExpression('/Unfortunately passwords cannot be reset on this site/',
            quoted_printable_decode($email->body));
        $sink->clear();
    }

    public function test_core_login_process_password_reset_missing_parameters() {
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotmailconfirm', 'error'));
        core_login_process_password_reset(null, null);
    }

    public function test_core_login_process_password_reset_invalid_username_with_username_protection() {
        global $CFG;
        $this->resetAfterTest();
        $CFG->protectusernames = 1;
        list($status, $notice, $url) = core_login_process_password_reset('72347234nasdfasdf/Ds', null);
        $this->assertEquals('emailpasswordconfirmmaybesent', $status);
    }

    public function test_core_login_process_password_reset_invalid_username_without_username_protection() {
        global $CFG;
        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        list($status, $notice, $url) = core_login_process_password_reset('72347234nasdfasdf/Ds', null);
        $this->assertEquals('emailpasswordconfirmnotsent', $status);
    }

    public function test_core_login_process_password_reset_invalid_email_without_username_protection() {
        global $CFG;
        $this->resetAfterTest();
        $CFG->protectusernames = 0;
        list($status, $notice, $url) = core_login_process_password_reset(null, 'fakeemail@nofd.zdy');
        $this->assertEquals('emailpasswordconfirmnotsent', $status);
    }

    /**
     * Data provider for \core_login_lib_testcase::test_core_login_validate_forgot_password_data().
     */
    public function forgot_password_data_provider() {
        return [
            'Both username and password supplied' => [
                [
                    'username' => 's1',
                    'email' => 's1@example.com'
                ],
                [
                    'username' => get_string('usernameoremail'),
                    'email' => get_string('usernameoremail'),
                ]
            ],
            'Valid username' => [
                ['username' => 's1']
            ],
            'Valid username, different case' => [
                ['username' => 'S1']
            ],
            'Valid username, different case, username protection off' => [
                ['username' => 'S1'],
                [],
                ['protectusernames' => 0]
            ],
            'Non-existent username' => [
                ['username' => 's2'],
            ],
            'Non-existing username, username protection off' => [
                ['username' => 's2'],
                ['username' => get_string('usernamenotfound')],
                ['protectusernames' => 0]
            ],
            'Valid username, unconfirmed username, username protection on' => [
                ['username' => 's1'],
                [],
                ['confirmed' => 0]
            ],
            'Invalid email' => [
                ['email' => 's1-example.com'],
                ['email' => get_string('invalidemail')]
            ],
            'Multiple accounts with the same email, username protection on' => [
                ['email' => 's1@example.com'],
                [],
                ['allowaccountssameemail' => 1]
            ],
            'Multiple accounts with the same email, username protection off' => [
                ['email' => 's1@example.com'],
                ['email' => get_string('forgottenduplicate')],
                ['allowaccountssameemail' => 1, 'protectusernames' => 0]
            ],
            'Multiple accounts with the same email but with different case, username protection is on' => [
                ['email' => 'S1@EXAMPLE.COM'],
                [],
                ['allowaccountssameemail' => 1]
            ],
            'Multiple accounts with the same email but with different case, username protection is off' => [
                ['email' => 'S1@EXAMPLE.COM'],
                ['email' => get_string('forgottenduplicate')],
                ['allowaccountssameemail' => 1, 'protectusernames' => 0]
            ],
            'Non-existent email, username protection on' => [
                ['email' => 's2@example.com']
            ],
            'Non-existent email, username protection off' => [
                ['email' => 's2@example.com'],
                ['email' => get_string('emailnotfound')],
                ['protectusernames' => 0]
            ],
            'Valid email' => [
                ['email' => 's1@example.com']
            ],
            'Valid email, different case' => [
                ['email' => 'S1@EXAMPLE.COM']
            ],
            'Valid email, unconfirmed user, username protection is on' => [
                ['email' => 's1@example.com'],
                [],
                ['confirmed' => 0]
            ],
            'Valid email, unconfirmed user, username protection is off' => [
                ['email' => 's1@example.com'],
                ['email' => get_string('confirmednot')],
                ['confirmed' => 0, 'protectusernames' => 0]
            ],
        ];
    }

    /**
     * Test for core_login_validate_forgot_password_data().
     *
     * @dataProvider forgot_password_data_provider
     * @param array $data Key-value array containing username and email data.
     * @param array $errors Key-value array containing error messages for the username and email fields.
     * @param array $options Options for $CFG->protectusernames, $CFG->allowaccountssameemail and $user->confirmed.
     */
    public function test_core_login_validate_forgot_password_data($data, $errors = [], $options = []) {
        $this->resetAfterTest();

        // Set config settings we need for our environment.
        $protectusernames = $options['protectusernames'] ?? 1;
        set_config('protectusernames', $protectusernames);

        $allowaccountssameemail = $options['allowaccountssameemail'] ?? 0;
        set_config('allowaccountssameemail', $allowaccountssameemail);

        // Generate the user data.
        $generator = $this->getDataGenerator();
        $userdata = [
            'username' => 's1',
            'email' => 's1@example.com',
            'confirmed' => $options['confirmed'] ?? 1
        ];
        $generator->create_user($userdata);

        if ($allowaccountssameemail) {
            // Create another user with the same email address.
            $generator->create_user(['email' => 's1@example.com']);
        }

        // Validate the data.
        $validationerrors = core_login_validate_forgot_password_data($data);

        // Check validation errors for the username field.
        if (isset($errors['username'])) {
            // If we expect and error for the username field, confirm that it's set.
            $this->assertArrayHasKey('username', $validationerrors);
            // And the actual validation error is equal to the expected validation error.
            $this->assertEquals($errors['username'], $validationerrors['username']);
        } else {
            // If we don't expect that there's a validation for the username field, confirm that it's not set.
            $this->assertArrayNotHasKey('username', $validationerrors);
        }

        // Check validation errors for the email field.
        if (isset($errors['email'])) {
            // If we expect and error for the email field, confirm that it's set.
            $this->assertArrayHasKey('email', $validationerrors);
            // And the actual validation error is equal to the expected validation error.
            $this->assertEquals($errors['email'], $validationerrors['email']);
        } else {
            // If we don't expect that there's a validation for the email field, confirm that it's not set.
            $this->assertArrayNotHasKey('email', $validationerrors);
        }
    }

    /**
     * Test searching for the user record by matching the provided email address when resetting password.
     *
     * Email addresses should be handled as case-insensitive but accent sensitive.
     */
    public function test_core_login_process_password_reset_email_sensitivity() {
        global $CFG;
        require_once($CFG->libdir.'/phpmailer/moodle_phpmailer.php');

        $this->resetAfterTest();
        $sink = $this->redirectEmails();
        $CFG->protectusernames = 0;

        // In this test, we need to mock sending emails on non-ASCII email addresses. However, such email addresses do
        // not pass the default `validate_email()` and Moodle does not yet provide a CFG switch to allow such emails.
        // So we inject our own validation method here and revert it back once we are done. This custom validator method
        // is identical to the default 'php' validator with the only difference: it has the FILTER_FLAG_EMAIL_UNICODE
        // set so that it allows to use non-ASCII characters in email addresses.
        $defaultvalidator = moodle_phpmailer::$validator;
        moodle_phpmailer::$validator = function($address) {
            return (bool) filter_var($address, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
        };

        // Emails are treated as case-insensitive when searching for the matching user account.
        $u1 = $this->getDataGenerator()->create_user(['email' => 'priliszlutouckykunupeldabelskeody@example.com']);

        list($status, $notice, $url) = core_login_process_password_reset(null, 'PrIlIsZlUtOuCkYKuNupELdAbElSkEoDy@eXaMpLe.CoM');

        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($u1->email, $email->to);
        $sink->clear();

        // There may exist two users with same emails.
        $u2 = $this->getDataGenerator()->create_user(['email' => 'PRILISZLUTOUCKYKUNUPELDABELSKEODY@example.com']);

        list($status, $notice, $url) = core_login_process_password_reset(null, 'PrIlIsZlUtOuCkYKuNupELdAbElSkEoDy@eXaMpLe.CoM');

        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame(core_text::strtolower($u2->email), core_text::strtolower($email->to));
        $sink->clear();

        // However, emails are accent sensitive - note this is the u1's email with a single character a -> á changed.
        list($status, $notice, $url) = core_login_process_password_reset(null, 'priliszlutouckykunupeldábelskeody@example.com');

        $this->assertSame('emailpasswordconfirmnotsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(0, $emails);
        $sink->clear();

        $u3 = $this->getDataGenerator()->create_user(['email' => 'PřílišŽluťoučkýKůňÚpělĎálebskéÓdy@example.com']);

        list($status, $notice, $url) = core_login_process_password_reset(null, 'pŘÍLIŠžLuŤOuČkÝkŮŇúPĚLďÁLEBSKÉóDY@eXaMpLe.CoM');

        $this->assertSame('emailresetconfirmsent', $status);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        $this->assertSame($u3->email, $email->to);
        $sink->clear();

        // Restore the original email address validator.
        moodle_phpmailer::$validator = $defaultvalidator;
    }

}
