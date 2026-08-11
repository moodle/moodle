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

namespace tool_mobile\task;

/**
 * Tests for the push notification limit task.
 *
 * @covers \tool_mobile\task\notify_push_notification_limit_to_admins
 * @package    tool_mobile
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class notify_push_notification_limit_to_admins_test extends \advanced_testcase {
    /**
     * Test that a message is sent to admins when the monthly limit has been reached.
     */
    public function test_execute_sends_notification_to_admins(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $clock = $this->mock_clock_with_frozen();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache(1234567890, $clock->time());

        $sink = $this->redirectMessages();
        $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
        $task->execute();

        $messages = $sink->get_messages();
        $expectedrecipientids = array_map(static function ($admin) {
            return (int) $admin->id;
        }, get_admins());

        $this->assertCount(count($expectedrecipientids), $messages);

        $actualrecipientids = [];
        foreach ($messages as $message) {
            $actualrecipientids[] = (int) $message->useridto;
            $this->assertEquals(\core_user::get_noreply_user()->id, $message->useridfrom);
            $this->assertEquals('tool_mobile', $message->component);
            $this->assertEquals('pushlimitreached', $message->eventtype);
            $this->assertEquals(get_string('limitreachedpushnotifications', 'tool_mobile'), $message->subject);
            $this->assertStringContainsString(
                'Your monthly device limit for push notifications has been reached',
                $message->fullmessage
            );
            $this->assertStringContainsString('60 / 50', $message->fullmessage);
            $this->assertStringContainsString(
                'New devices added over the limit will not receive push notifications.',
                $message->fullmessage
            );
            $this->assertStringNotContainsString('/admin/tool/mobile/subscription.php', $message->fullmessage);
            $this->assertStringContainsString(
                'Your monthly device limit for push notifications has been reached',
                $message->fullmessagehtml
            );
            $this->assertStringContainsString('60 / 50', $message->fullmessagehtml);
            $this->assertStringContainsString('Upgrade your plan', $message->fullmessagehtml);
            $this->assertStringContainsString('/admin/tool/mobile/subscription.php', $message->fullmessagehtml);
            $this->assertStringContainsString('/admin/tool/mobile/pix/push_notification.svg', $message->fullmessagehtml);
            $this->assertStringContainsString('/pix/i/risk_xss.svg', $message->fullmessagehtml);
        }

        sort($expectedrecipientids);
        sort($actualrecipientids);
        $this->assertSame($expectedrecipientids, $actualrecipientids);
        $this->assertEquals('1234567890', get_config('tool_mobile', 'pushnotificationlimitlastnotified'));
    }

    /**
     * Test that the same limit reached event is not notified twice.
     */
    public function test_execute_does_not_send_duplicate_notification(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $clock = $this->mock_clock_with_frozen();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache(1234567890, $clock->time());

        $sink = $this->redirectMessages();
        $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
        $task->execute();

        $this->assertEquals('1234567890', get_config('tool_mobile', 'pushnotificationlimitlastnotified'));

        $task->execute();

        $messages = $sink->get_messages();
        $this->assertCount(count(get_admins()), $messages);
        $this->assertEquals('1234567890', get_config('tool_mobile', 'pushnotificationlimitlastnotified'));
    }

    /**
     * Test that only the current month statistics are considered.
     */
    public function test_get_current_month_notification_stats_ignores_other_months(): void {
        $clock = $this->mock_clock_with_frozen();
        $currentyear = (int) date('Y', $clock->time());
        $currentmonth = (int) date('n', $clock->time());
        $previousmonth = $currentmonth === 1 ? 12 : $currentmonth - 1;
        $previousyear = $currentmonth === 1 ? $currentyear - 1 : $currentyear;

        $stats = notify_push_notification_limit_to_admins::get_current_month_notification_stats(
            [
                'statistics' => [
                    'notifications' => [
                        'monthly' => [
                            [
                                'year' => $previousyear,
                                'month' => $previousmonth,
                                'limitreachedtime' => 111,
                            ],
                            [
                                'year' => $currentyear,
                                'month' => $currentmonth,
                                'limitreachedtime' => 222,
                                'activedevices' => 60,
                            ],
                        ],
                    ],
                ],
            ],
            $clock->now(),
        );

        $this->assertNotNull($stats);
        $this->assertSame(222, $stats['limitreachedtime']);
        $this->assertSame($currentmonth, $stats['month']);
    }

    /**
     * Seed the subscription cache with current month notification statistics.
     *
     * @param int $limitreachedtime The timestamp used to identify the limit reached event.
     * @param int $currenttime The current timestamp used to build matching monthly statistics.
     */
    private function seed_subscription_cache(int $limitreachedtime, int $currenttime): void {
        $currentyear = (int) date('Y', $currenttime);
        $currentmonth = (int) date('n', $currenttime);

        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cache->set(0, [
            'statistics' => [
                'notifications' => [
                    'monthly' => [[
                        'year' => $currentyear,
                        'month' => $currentmonth,
                        'sentnotifications' => 120,
                        'ignorednotifications' => 15,
                        'newdevices' => 8,
                        'activedevices' => 60,
                        'limitreachedtime' => $limitreachedtime,
                    ]],
                ],
            ],
            'subscription' => [
                'features' => [[
                    'name' => 'pushnotificationsdevices',
                    'limit' => 50,
                ]],
            ],
        ]);
    }
}
