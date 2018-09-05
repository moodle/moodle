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
        $this->assertRegExp('/A password reset was requested for your account/', $email->body);
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
        $this->assertRegExp('/A password reset was requested for your account/', $email->body);
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
        $this->assertRegExp('/A password reset was requested for your account/', $email->body);
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
        $this->assertRegExp('/A password reset was requested for your account/', $email->body);
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
        $this->assertRegExp('/Unfortunately your account on this site is disabled/', $email->body);
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
        $this->assertRegExp('/Unfortunately passwords cannot be reset on this site/', $email->body);
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
}
