<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test hook for displaying a notification for an admin setting.
 *
 * @coversDefaultClass \core\hook\admin_setting_notification
 *
 * @package    core
 * @copyright  2026 Abhinav Gandham <abhinavgandham@catalyst-au.net>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class admin_setting_notification_test extends \advanced_testcase {
    /**
     * Test the construction of the hook with a sample setting object.
     *
     * @covers ::__construct
     */
    public function test_hook_construction(): void {
        $setting = new \stdClass();
        $setting->name = 'test_setting';
        $setting->plugin = '';
        $hook = new admin_setting_notification($setting);
        $this->assertInstanceOf(admin_setting_notification::class, $hook);
        $this->assertSame($setting, $hook->setting);
    }

    /**
     * Test adding a notification and retrieving it.
     *
     * @covers ::add_notification
     * @covers ::get_notifications
     */
    public function test_add_notification_single(): void {
        $setting = new \stdClass();
        $setting->name = 'test_setting';
        $setting->plugin = '';
        $hook = new admin_setting_notification($setting);

        $message = 'This is a test notification.';
        $messagetype = \core\output\notification::NOTIFY_SUCCESS;

        $hook->add_notification($message, $messagetype);
        $notifications = $hook->get_notifications();

        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(\core\output\notification::class, $notifications[0]);
        $this->assertSame($message, $notifications[0]->get_message());
        $this->assertSame($messagetype, $notifications[0]->get_message_type());
    }

    /**
     * Data provider for test_add_notification_multiple.
     *
     * @return array[] List of notification messages.
     */
    public static function notification_provider(): array {
        return [[
            [
                ['message' => 'This is a success notification.', 'messagetype' => \core\output\notification::NOTIFY_SUCCESS],
                ['message' => 'This is an error notification.', 'messagetype' => \core\output\notification::NOTIFY_ERROR],
                ['message' => 'This is a warning notification.', 'messagetype' => \core\output\notification::NOTIFY_WARNING],
                ['message' => 'This is an info notification.', 'messagetype' => \core\output\notification::NOTIFY_INFO],
            ],
        ]];
    }

    #[DataProvider('notification_provider')]
    /**
     * Test adding multiple notifications and retrieving them.
     *
     * @param array $messages List of notification messages to add.
     *
     * @covers ::add_notification
     * @covers ::get_notifications
     */
    public function test_add_notification_multiple(array $messages): void {
        $setting = new \stdClass();
        $setting->name = 'test_setting';
        $setting->plugin = '';
        $hook = new admin_setting_notification($setting);

        array_walk($messages, fn(array $msg) => $hook->add_notification($msg['message'], $msg['messagetype']));

        $actual = array_map(
            fn(\core\output\notification $notification): array => [
                'message' => $notification->get_message(),
                'messagetype' => $notification->get_message_type(),
            ],
            $hook->get_notifications(),
        );

        $this->assertSame($messages, $actual);
    }

    /**
     * Test dispatching the hook with a listener that adds a notification.
     *
     * @covers ::add_notification
     * @covers ::get_notifications
     * @covers \core\hook\manager::dispatch
     */
    public function test_hook_dispatch_with_listener(): void {
        $setting = new \stdClass();
        $setting->name = 'test_setting';
        $setting->plugin = '';
        $hook = new admin_setting_notification($setting);
        $callback = function (admin_setting_notification $hook): void {
            $hook->add_notification('Listener notification.', \core\output\notification::NOTIFY_SUCCESS);
        };
        $this->redirectHook(admin_setting_notification::class, $callback);
        \core\hook\manager::get_instance()->dispatch($hook);

        $notifications = $hook->get_notifications();
        $this->assertCount(1, $notifications);
        $this->assertSame('Listener notification.', $notifications[0]->get_message());
        $this->assertSame(\core\output\notification::NOTIFY_SUCCESS, $notifications[0]->get_message_type());
    }

    /**
     * Test dispatching the hook with no listeners redirected, ensuring no notifications are added.
     *
     * @covers ::get_notifications
     * @covers \core\hook\manager::dispatch
     */
    public function test_hook_dispatch_with_no_listeners(): void {
        $setting = new \stdClass();
        $setting->name = 'test_setting';
        $setting->plugin = '';
        $hook = new admin_setting_notification($setting);
        \core\hook\manager::get_instance()->dispatch($hook);

        $notifications = $hook->get_notifications();
        $this->assertCount(0, $notifications);
    }
}
