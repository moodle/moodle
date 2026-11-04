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
 * @package    tool_mobile
 * @copyright  2026 Daniel Ureña <daniel.urena@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\tool_mobile\task\notify_push_notification_limit_to_admins::class)]
final class notify_push_notification_limit_to_admins_test extends \advanced_testcase {
    /**
     * Data provider for {@see self::test_execute_logs_no_notification_information_for_current_month()}.
     *
     * Covers the dates where the original modify('-1 month') calculation overflowed into the
     * current month (the 29th to 31st of a month whose predecessor has fewer days), plus the
     * year boundary cases: the same month in the previous year and January with the previous
     * December must not be mistaken for the current month.
     *
     * @return array
     */
    public static function execute_logs_no_notification_information_for_current_month_provider(): array {
        return [
            'March 29' => ['2026-03-29 12:00:00', 2026, 2],
            'March 31' => ['2026-03-31 12:00:00', 2026, 2],
            'May 31' => ['2026-05-31 12:00:00', 2026, 4],
            'July 31' => ['2026-07-31 12:00:00', 2026, 6],
            'October 31' => ['2026-10-31 12:00:00', 2026, 9],
            'December 31' => ['2026-12-31 12:00:00', 2026, 11],
            'Same month last year' => ['2026-11-15 12:00:00', 2025, 11],
            'January, previous December' => ['2027-01-15 12:00:00', 2026, 12],
        ];
    }

    /**
     * Test that no notification is sent when there is no stats entry for this month.
     *
     * @param string $datetime The datetime to freeze the clock at.
     * @param int $year The year of the seeded monthly statistics, which must not match the current month.
     * @param int $month The month of the seeded monthly statistics, which must not match the current month.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('execute_logs_no_notification_information_for_current_month_provider')]
    public function test_execute_logs_no_notification_information_for_current_month(
        string $datetime,
        int $year,
        int $month,
    ): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->mock_clock_with_frozen_datetime(
            new \DateTimeImmutable($datetime, \core_date::get_server_timezone_object()),
        );
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache_with_monthly_data([
            [
                'year' => $year,
                'month' => $month,
                'limitreachedtime' => 1234567890,
            ],
        ]);

        $sink = $this->redirectMessages();
        $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
        $output = $this->capture_task_output($task);

        $this->assertCount(0, $sink->get_messages());
        $this->assertStringContainsString('tool_mobile: Running scheduled push notification limit check task...', $output);
        $this->assertStringContainsString(
            'tool_mobile: no notification information for this month, no notification sent to admins.',
            $output,
        );
    }

    /**
     * Test that no notification is sent when the limit has not been reached.
     */
    public function test_execute_logs_no_limit_reached_for_current_month(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $clock = $this->mock_clock_with_frozen();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache(0, $clock->time());

        $sink = $this->redirectMessages();
        $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
        $output = $this->capture_task_output($task);

        $this->assertCount(0, $sink->get_messages());
        $this->assertStringContainsString('tool_mobile: Running scheduled push notification limit check task...', $output);
        $this->assertStringContainsString('tool_mobile: no limit reached this month, no notification sent to admins.', $output);
    }

    /**
     * Test that a message is sent to admins when the monthly limit has been reached.
     */
    public function test_execute_sends_notification_to_admins(): void {
        global $CFG, $SITE;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $SITE->fullname = 'Moodle & Learning';
        $clock = $this->mock_clock_with_frozen();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache(1234567890, $clock->time());

        $sink = $this->redirectMessages();
        $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
        $output = $this->capture_task_output($task);

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
                'You\'re receiving this notification because you\'re an administrator of Moodle & Learning.',
                $message->fullmessage,
            );
            $this->assertStringContainsString(
                'You&#039;re receiving this notification because you&#039;re an administrator of Moodle &amp; Learning.',
                $message->fullmessagehtml,
            );
            $this->assertStringNotContainsString('&amp;amp;', $message->fullmessagehtml);
            $docsurl = get_docs_url('Moodle_app_plans');
            $this->assertStringContainsString('Moodle app plans documentation [' . $docsurl . ']', $message->fullmessage);
            $this->assertStringContainsString($docsurl, $message->fullmessagehtml);
            $this->assertStringContainsString('target="_blank"', $message->fullmessagehtml);
            $this->assertStringContainsString(get_string('opensinnewwindow'), $message->fullmessagehtml);
            $this->assertStringNotContainsString(get_string('opensinnewwindow'), $message->fullmessage);
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
        $this->assertStringContainsString('tool_mobile: Running scheduled push notification limit check task...', $output);
        $this->assertStringContainsString(
            'tool_mobile: limit reached this month at ' . date('Y-m-d H:i:s', 1234567890) . ', notification sent to admins.',
            $output,
        );
        $this->assertStringContainsString('tool_mobile: push notification limit check completed successfully.', $output);
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
        $firstoutput = $this->capture_task_output($task);

        $this->assertEquals('1234567890', get_config('tool_mobile', 'pushnotificationlimitlastnotified'));
        $this->assertStringContainsString(
            'tool_mobile: limit reached this month at ' . date('Y-m-d H:i:s', 1234567890) . ', notification sent to admins.',
            $firstoutput,
        );

        $output = $this->capture_task_output($task);

        $messages = $sink->get_messages();
        $this->assertCount(count(get_admins()), $messages);
        $this->assertEquals('1234567890', get_config('tool_mobile', 'pushnotificationlimitlastnotified'));
        $this->assertStringContainsString(
            'tool_mobile: limit reached this month at ' . date('Y-m-d H:i:s', 1234567890) .
                ' and previously notified, no notification sent to admins.',
            $output,
        );
    }

    /**
     * Test that premium and BMA plans do not trigger limit checks.
     */
    public function test_execute_skips_premium_and_bma_plans(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enablemobilewebservice = 1;

        foreach (['premium', 'bma'] as $plan) {
            set_config('pushnotificationlimitlastnotified', '', 'tool_mobile');
            $cache = \cache::make('tool_mobile', 'subscriptioninfo');
            $cache->set(0, [
                'subscription' => ['plan' => $plan],
            ]);

            $sink = $this->redirectMessages();
            $task = new \tool_mobile\task\notify_push_notification_limit_to_admins();
            $output = $this->capture_task_output($task);

            $this->assertCount(0, $sink->get_messages());
            $this->assertStringContainsString('tool_mobile: premium or BMA plans, no push notifications limits.', $output);
        }
    }

    /**
     * Test that only the current month statistics are considered.
     */
    public function test_get_current_month_notification_stats_ignores_other_months(): void {
        $clock = $this->mock_clock_with_frozen_datetime(
            new \DateTimeImmutable('2026-11-15 12:00:00', \core_date::get_server_timezone_object()),
        );

        $stats = notify_push_notification_limit_to_admins::get_current_month_notification_stats(
            [
                'statistics' => [
                    'notifications' => [
                        'monthly' => [
                            [
                                'year' => 2026,
                                'month' => 10,
                                'limitreachedtime' => 111,
                            ],
                            [
                                'year' => 2026,
                                'month' => 11,
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
        $this->assertSame(11, $stats['month']);
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

        $this->seed_subscription_cache_with_monthly_data([[
            'year' => $currentyear,
            'month' => $currentmonth,
            'sentnotifications' => 120,
            'ignorednotifications' => 15,
            'newdevices' => 8,
            'activedevices' => 60,
            'limitreachedtime' => $limitreachedtime,
        ]]);
    }

    /**
     * Seed the subscription cache with current plan and provided monthly stats.
     *
     * @param array $monthlystats
     */
    private function seed_subscription_cache_with_monthly_data(array $monthlystats): void {
        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cache->set(0, [
            'statistics' => [
                'notifications' => [
                    'monthly' => $monthlystats,
                ],
            ],
            'subscription' => [
                'plan' => 'free',
                'features' => [[
                    'name' => 'pushnotificationsdevices',
                    'limit' => 50,
                ]],
            ],
        ]);
    }

    /**
     * Capture the output produced by a task execute call.
     *
     * @param \core\task\task_base $task
     * @return string
     */
    private function capture_task_output(\core\task\task_base $task): string {
        ob_start();
        $task->execute();

        return (string) ob_get_clean();
    }
}
