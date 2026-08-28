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

use core\check\result;
use core\check\security\securitymessages;

/**
 * Unit tests for the security messages check.
 *
 * @package    core
 * @category   check
 * @copyright  2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\check\security\securitymessages
 */
final class check_securitymessages_test extends \advanced_testcase {
    /**
     * Set up: isolate DB and reset caches.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Helper to configure the email processor's enabled/locked state for moodle/newlogin.
     *
     * @param bool $enabled Whether email is in the default-enabled list for newlogin.
     * @param bool $locked  Whether email is locked on for newlogin.
     */
    private function set_email_newlogin_state(bool $enabled, bool $locked): void {
        global $DB;

        // Ensure the email processor exists and is enabled + configured in the DB.
        if (!$DB->record_exists('message_processors', ['name' => 'email'])) {
            $DB->insert_record('message_processors', (object)['name' => 'email', 'enabled' => 1]);
        } else {
            $DB->set_field('message_processors', 'enabled', 1, ['name' => 'email']);
        }

        // Disable all other processors so only email is in scope.
        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");

        // Reset static processor cache.
        get_message_processors(false, true, true);

        set_config(
            'email_provider_moodle_newlogin_locked',
            $locked ? '1' : '0',
            'message'
        );

        set_config(
            'message_provider_moodle_newlogin_enabled',
            $enabled ? 'email' : '',
            'message'
        );
    }

    /**
     * When email is enabled and locked the check should return OK.
     */
    public function test_ok_when_enabled_and_locked(): void {
        $this->set_email_newlogin_state(enabled: true, locked: true);

        $check = new securitymessages();
        $result = $check->get_result();

        $this->assertEquals(result::OK, $result->get_status());
    }

    /**
     * When email is enabled but not locked the check should warn and say "Lock".
     */
    public function test_warning_when_enabled_not_locked(): void {
        $this->set_email_newlogin_state(enabled: true, locked: false);

        $check = new securitymessages();
        $result = $check->get_result();

        $this->assertEquals(result::WARNING, $result->get_status());
        $this->assertStringContainsString(
            get_string('check_securitymessages_action_lock', 'report_security'),
            $result->get_details()
        );
        $this->assertStringNotContainsString(
            get_string('check_securitymessages_action_enable', 'report_security'),
            $result->get_details()
        );
    }

    /**
     * When email is not enabled and not locked the check should warn and say "Enable and lock".
     */
    public function test_warning_when_not_enabled_not_locked(): void {
        $this->set_email_newlogin_state(enabled: false, locked: false);

        $check = new securitymessages();
        $result = $check->get_result();

        $this->assertEquals(result::WARNING, $result->get_status());
        $this->assertStringContainsString(
            get_string('check_securitymessages_action_enableandlock', 'report_security'),
            $result->get_details()
        );
    }

    /**
     * When email is locked off (not enabled, but locked) the check should warn and say "Enable".
     */
    public function test_warning_when_not_enabled_but_locked(): void {
        $this->set_email_newlogin_state(enabled: false, locked: true);

        $check = new securitymessages();
        $result = $check->get_result();

        $this->assertEquals(result::WARNING, $result->get_status());
        $this->assertStringContainsString(
            get_string('check_securitymessages_action_enable', 'report_security'),
            $result->get_details()
        );
        $this->assertStringNotContainsString(
            get_string('check_securitymessages_action_lock', 'report_security'),
            $result->get_details()
        );
    }

    /**
     * When a processor is disabled the check should ignore it entirely (no warning).
     */
    public function test_disabled_processor_is_ignored(): void {
        global $DB;

        // Disable all processors including email.
        $DB->set_field_select('message_processors', 'enabled', 0, '1=1');
        get_message_processors(false, true, true);

        // Even if email is not locked, it shouldn't matter because it's not enabled/configured.
        set_config('email_provider_moodle_newlogin_locked', '0', 'message');

        $check = new securitymessages();
        $result = $check->get_result();

        // No enabled processors means nothing to warn about.
        $this->assertEquals(result::OK, $result->get_status());
    }

    /**
     * When the notification top-level toggle is disabled the check should warn even if all outputs are on.
     */
    public function test_warning_when_notification_disabled(): void {
        $this->set_email_newlogin_state(enabled: true, locked: true);

        // Disable the notification itself via its top-level toggle.
        set_config('moodle_newlogin_disable', '1', 'message');

        $check = new securitymessages();
        $result = $check->get_result();

        $this->assertEquals(result::WARNING, $result->get_status());
        $this->assertStringContainsString(
            get_string('check_securitymessages_notification_disabled', 'report_security'),
            $result->get_summary()
        );
        $this->assertStringContainsString(
            get_string('check_securitymessages_action_enable_notification', 'report_security'),
            $result->get_details()
        );
    }
}
