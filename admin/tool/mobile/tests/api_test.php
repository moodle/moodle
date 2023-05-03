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

namespace tool_mobile;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Moodle Mobile admin tool api tests.
 *
 * @package     tool_mobile
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.1
 */
class api_test extends \externallib_advanced_testcase {

    /**
     * Test get_autologin_key.
     */
    public function test_get_autologin_key() {
        global $USER, $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set server timezone for test.
        $this->setTimezone('UTC');
        // SEt user to GMT+5.
        $USER->timezone = 5;

        $timenow = $this->setCurrentTimeStart();
        $key = api::get_autologin_key();

        $key = $DB->get_record('user_private_key', array('value' => $key), '*', MUST_EXIST);
        $this->assertTimeCurrent($key->validuntil - api::LOGIN_KEY_TTL);
        $this->assertEquals('0.0.0.0', $key->iprestriction);
    }

    /**
     * Test get_potential_config_issues.
     */
    public function test_get_potential_config_issues() {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set non-SSL wwwroot, to avoid spurious certificate checking.
        $CFG->wwwroot = 'http://www.example.com';
        $CFG->debugdisplay = 1;

        set_config('debugauthdb', 1, 'auth_db');
        set_config('debugdb', 1, 'enrol_database');

        // Get potential issues, obtain their keys for comparison.
        $issues = api::get_potential_config_issues();
        $issuekeys = array_column($issues, 0);

        $this->assertEqualsCanonicalizing([
            'nohttpsformobilewarning',
            'adodbdebugwarning',
            'displayerrorswarning',
        ], $issuekeys);
    }

    /**
     * Test pre_processor_message_send callback.
     */
    public function test_pre_processor_message_send_callback() {
        global $DB, $CFG;

        $this->preventResetByRollback();
        $this->resetAfterTest();

        // Enable mobile services and required configuration.
        $CFG->enablewebservices = 1;
        $CFG->enablemobilewebservice = 1;
        $mobileappdownloadpage = 'htt://mobileappdownloadpage';
        set_config('setuplink', $mobileappdownloadpage, 'tool_mobile');

        $user1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1));
        $user2 = $this->getDataGenerator()->create_user();
        set_config('allowedemaildomains', 'example.com');

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_instantmessage_enabled', 'email', $user2);

        // Extra content for all types of messages.
        $message = new \core\message\message();
        $message->courseid          = 1;
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = $user1;
        $message->userto            = $user2;
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';
        $message->notification      = '0';
        $content = array('*' => array('header' => ' test ', 'footer' => ' test '));
        $message->set_additional_content('email', $content);

        $sink = $this->redirectEmails();
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);

        // Check we got the promotion text.
        $this->assertStringContainsString($mobileappdownloadpage, quoted_printable_decode($email->body));
        $sink->clear();

        // Disable mobile so we don't get mobile promotions.
        $CFG->enablemobilewebservice = 0;
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        // Check we don't get the promotion text.
        $this->assertStringNotContainsString($mobileappdownloadpage, quoted_printable_decode($email->body));
        $sink->clear();

        // Enable mobile again and set current user mobile token so we don't get mobile promotions.
        $CFG->enablemobilewebservice = 1;
        $user3 = $this->getDataGenerator()->create_user();
        $this->setUser($user3);
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
        $token = \core_external\util::generate_token_for_current_user($service);

        $message->userto = $user3;
        $messageid = message_send($message);
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails);
        $email = reset($emails);
        // Check we don't get the promotion text.
        $this->assertStringNotContainsString($mobileappdownloadpage, quoted_printable_decode($email->body));
        $sink->clear();
        $sink->close();
    }
}
