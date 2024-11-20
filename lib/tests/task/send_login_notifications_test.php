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

namespace core\task;

use moodle_url;

/**
 * Contains tests for login related notifications.
 *
 * @package    core
 * @copyright  2021 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\task\send_login_notifications
 */
class send_login_notifications_test extends \advanced_testcase {

    /**
     * Test new login notification.
     */
    public function test_login_notification(): void {
        global $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser(0);

        // Mock data for test.
        $loginuser->lastip = '1.2.3.4.6'; // Different ip that current.
        $SESSION->isnewsessioncookie = true; // New session cookie.
        @complete_user_login($loginuser);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // Send notification, new IP and new session.
        $this->assertCount(1, $messages);
        $this->assertEquals($loginuser->id, $messages[0]->useridto);
        $this->assertEquals('newlogin', $messages[0]->eventtype);
    }

    /**
     * Test new login notification is skipped because of same IP from last login.
     */
    public function test_login_notification_skip_same_ip(): void {
        global $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser(0);

        // Mock data for test.
        $SESSION->isnewsessioncookie = true;    // New session cookie.
        @complete_user_login($loginuser);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // Skip notification when we have the same previous IP even if the browser used to connect is new.
        $this->assertCount(0, $messages);
    }

    /**
     * Test new login notification is skipped because of same browser from last login.
     */
    public function test_login_notification_skip_same_browser(): void {
        global $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser(0);

        // Mock data for test.
        $loginuser->lastip = '1.2.3.4.6'; // Different ip that current.
        $SESSION->isnewsessioncookie = false;
        @complete_user_login($loginuser);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // Skip notification, different ip but same browser (probably, mobile phone browser).
        $this->assertCount(0, $messages);
    }

    /**
     * Test new login notification is skipped because of auto-login from the mobile app (skip duplicated notifications).
     */
    public function test_login_notification_skip_mobileapp(): void {
        global $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser(0);

        // Mock data for test.
        $loginuser->lastip = '1.2.3.4.6';   // Different ip that current.
        $SESSION->isnewsessioncookie = true;    // New session cookie.
        \core_useragent::instance(true, 'MoodleMobile'); // Force fake mobile app user agent.
        @complete_user_login($loginuser);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        $this->assertCount(0, $messages);
    }

    /**
     * Test new login notification where the user auth method provides a custom change password URL
     */
    public function test_login_notification_custom_change_password_url(): void {
        global $SESSION;

        $this->resetAfterTest();
        $this->setUser(0);

        // Set LDAP auth change password URL.
        $changepasswordurl = (new moodle_url('/changepassword.php'))->out(false);
        set_config('changepasswordurl', $changepasswordurl, 'auth_ldap');

        $ldapuser = $this->getDataGenerator()->create_user(['auth' => 'ldap']);

        // Mock data for test.
        $ldapuser->lastip = '1.2.3.4';
        $SESSION->isnewsessioncookie = true;
        @complete_user_login($ldapuser);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks(send_login_notifications::class);
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // Send notification, assert custom change password URL is present.
        $this->assertCount(1, $messages);
        $this->assertStringContainsString("If you don't recognise this activity, please " .
            "<a href=\"{$changepasswordurl}\">change your password</a>.", $messages[0]->fullmessagehtml);
    }

    /**
     * Test new mobile app login notification.
     */
    public function test_mobile_app_login_notification(): void {
        global $USER, $DB, $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser($loginuser);

        // Mock data for test.
        $USER->lastip = '1.2.3.4.6'; // Different ip that current.

        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
        $token = \core_external\util::generate_token_for_current_user($service);
        \core_useragent::instance(true, 'MoodleMobile'); // Force fake mobile app user agent.

        // Simulate we are using an new device.
        $fakedevice = (object) [
            'userid' => $USER->id,
            'appid' => 'com.moodle.moodlemobile',
            'name' => 'occam',
            'model' => 'Nexus 4',
            'platform' => 'Android',
            'version' => '4.2.2',
            'pushid' => 'kishUhd',
            'uuid' => 'KIhud7s',
            'timecreated' => time() + MINSECS,
            'timemodified' => time() + MINSECS
        ];
        $DB->insert_record('user_devices', $fakedevice);

        \core_external\util::log_token_request($token);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // We sent a login notification because we are using a new device and different IP.
        $this->assertCount(1, $messages);
        $this->assertEquals($loginuser->id, $messages[0]->useridto);
        $this->assertEquals('newlogin', $messages[0]->eventtype);
    }

    /**
     * Test new mobile app login notification skipped becase of same last ip.
     */
    public function test_mobile_app_login_notification_skip_same_ip(): void {
        global $USER, $DB, $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser($loginuser);

        // Mock data for test.
        $USER->lastip = '0.0.0.0';
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
        $token = \core_external\util::generate_token_for_current_user($service);
        \core_useragent::instance(true, 'MoodleMobile'); // Force fake mobile app user agent.

        // Simulate we are using an new device.
        $fakedevice = (object) [
            'userid' => $USER->id,
            'appid' => 'com.moodle.moodlemobile',
            'name' => 'occam',
            'model' => 'Nexus 4',
            'platform' => 'Android',
            'version' => '4.2.2',
            'pushid' => 'kishUhd',
            'uuid' => 'KIhud7s',
            'timecreated' => time() + MINSECS,
            'timemodified' => time() + MINSECS
        ];
        $DB->insert_record('user_devices', $fakedevice);

        \core_external\util::log_token_request($token);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // While using the same IP avoid sending new login notifications even if we are using a new device.
        $this->assertCount(0, $messages);
    }

    /**
     * Test new mobile app login notification skipped becase of same device.
     */
    public function test_mobile_app_login_notification_skip_same_device(): void {
        global $USER, $DB, $SESSION;

        $this->resetAfterTest();

        $loginuser = self::getDataGenerator()->create_user();
        $this->setUser($loginuser);

        // Mock data for test.
        $USER->lastip = '1.2.3.4.6';    // New ip.
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
        $token = \core_external\util::generate_token_for_current_user($service);
        \core_useragent::instance(true, 'MoodleMobile'); // Force fake mobile app user agent.

        \core_external\util::log_token_request($token);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        ob_start();
        $this->runAdhocTasks('\core\task\send_login_notifications');
        $output = ob_get_contents();
        ob_end_clean();
        $messages = $sink->get_messages();
        $sink->close();

        // While using the same device avoid sending new login notifications even if the IP changes.
        $this->assertCount(0, $messages);
    }
}
