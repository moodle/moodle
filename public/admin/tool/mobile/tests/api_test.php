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

/**
 * Moodle Mobile admin tool api tests.
 *
 * @package     tool_mobile
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.1
 */
final class api_test extends \core_external\tests\externallib_testcase {
    /**
     * Test subscription plan normalisation.
     *
     * @covers \tool_mobile\api::get_normalized_plan
     */
    public function test_get_normalized_plan(): void {
        $this->resetAfterTest(true);

        $this->assertSame('premium', api::get_normalized_plan([
            'subscription' => ['plan' => ' Premium '],
        ]));
        $this->assertSame('bma', api::get_normalized_plan([
            'subscription' => ['plan' => 'BMA'],
        ]));
        $this->assertSame('free', api::get_normalized_plan([
            'subscription' => ['plan' => ' free '],
        ]));
        $this->assertNull(api::get_normalized_plan([
            'subscription' => [],
        ], false));
        $this->assertNull(api::get_normalized_plan(null, false));

        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cache->set(0, [
            'subscription' => ['plan' => ' Premium '],
        ]);

        $this->assertSame('premium', api::get_normalized_plan(null));
    }

    /**
     * Test Premium and BMA plan detection.
     *
     * @covers \tool_mobile\api::is_premium_or_bma_plan
     */
    public function test_is_premium_or_bma_plan(): void {
        $this->resetAfterTest(true);

        $this->assertTrue(api::is_premium_or_bma_plan([
            'subscription' => ['plan' => 'premium'],
        ]));
        $this->assertTrue(api::is_premium_or_bma_plan([
            'subscription' => ['plan' => ' BMA '],
        ]));
        $this->assertFalse(api::is_premium_or_bma_plan([
            'subscription' => ['plan' => 'free'],
        ]));
        $this->assertFalse(api::is_premium_or_bma_plan([
            'subscription' => [],
        ]));
        $this->assertFalse(api::is_premium_or_bma_plan(null));

        // If the provided data is void or invalid, the function should request cached API data.
        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cache->set(0, [
            'subscription' => ['plan' => ' Premium '],
        ]);
        $this->assertTrue(api::is_premium_or_bma_plan(null));
        $this->assertTrue(api::is_premium_or_bma_plan([
            'subscription' => ['plan' => 123],
        ]));
        $this->assertFalse(api::is_premium_or_bma_plan([
            'subscription' => ['plan' => 'free'],
        ]));
        $this->assertFalse(api::is_premium_or_bma_plan(null, false));
    }

    /**
     * Test Matomo detection against common identifiers.
     *
     * @covers \tool_mobile\api::contains_matomo_tracking
     */
    public function test_contains_matomo_tracking(): void {
        $samples = [
            'var _paq = window._paq || [];',
            '<script src="https://example.com/matomo.js"></script>',
            '<img src="https://example.com/matomo.php?idsite=1">',
            '<script src="https://example.com/piwik.js"></script>',
            '<img src="https://example.com/piwik.php?idsite=1">',
        ];

        foreach ($samples as $sample) {
            $this->assertTrue(api::contains_matomo_tracking($sample));
        }

        $this->assertFalse(api::contains_matomo_tracking('trackPageView'));
        $this->assertFalse(api::contains_matomo_tracking('enableLinkTracking'));
        $this->assertFalse(api::contains_matomo_tracking('setTrackerUrl'));
        $this->assertFalse(api::contains_matomo_tracking('setSiteId'));
        $this->assertFalse(api::contains_matomo_tracking('Google Analytics content only'));
        $this->assertFalse(api::contains_matomo_tracking(''));
        $this->assertFalse(api::contains_matomo_tracking(null));
    }

    /**
     * Test Matomo detection in the Additional HTML settings.
     *
     * @covers \tool_mobile\api::has_matomo_additional_html
     */
    public function test_has_matomo_additional_html(): void {
        global $CFG;

        $this->resetAfterTest(true);

        set_config('additionalhtmlhead', '<script>console.log("no matomo")</script>');
        set_config('additionalhtmltopofbody', '<script>var _paq = window._paq || [];</script>');
        set_config('additionalhtmlfooter', '');
        $CFG->additionalhtmlhead = '<script>console.log("no matomo")</script>';
        $CFG->additionalhtmltopofbody = '<script>var _paq = window._paq || [];</script>';
        $CFG->additionalhtmlfooter = '';

        $this->assertTrue(api::has_matomo_additional_html());

        set_config('additionalhtmlhead', '');
        set_config('additionalhtmltopofbody', '');
        set_config('additionalhtmlfooter', '');
        $CFG->additionalhtmlhead = '';
        $CFG->additionalhtmltopofbody = '';
        $CFG->additionalhtmlfooter = '';

        $this->assertFalse(api::has_matomo_additional_html());
    }

    /**
     * Test get_autologin_key.
     */
    public function test_get_autologin_key(): void {
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
    public function test_get_potential_config_issues(): void {
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
    public function test_pre_processor_message_send_callback(): void {
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
