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

use core\clock;
use core\di;
use tool_mobile\api;
use tool_mobile\output\push_notification_limit_message;

/**
 * Scheduled task to notify admins when the mobile app push notification limit is reached.
 *
 * @package    tool_mobile
 * @copyright  2026 Daniel Ureña <daniel.urena@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_push_notification_limit_to_admins extends \core\task\scheduled_task {
    /** @var string Config key used to avoid repeated notifications for the same limit reach event. */
    private const LAST_NOTIFIED_CONFIG = 'pushnotificationlimitlastnotified';

    /**
     * Return the task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasknotifypushnotificationlimittoadmins', 'tool_mobile');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;

        if (empty($CFG->enablemobilewebservice)) {
            return;
        }

        $subscriptiondata = api::get_subscription_information(true);
        $now = di::get(clock::class)->now();
        $notificationstats = self::get_current_month_notification_stats($subscriptiondata, $now);
        if ($notificationstats === null || empty($notificationstats['limitreachedtime'])) {
            return;
        }

        $limitreachedtime = (int) $notificationstats['limitreachedtime'];
        if ($limitreachedtime <= 0) {
            return;
        }

        if ((int) get_config('tool_mobile', self::LAST_NOTIFIED_CONFIG) === $limitreachedtime) {
            return;
        }

        if (!self::notify_admins($subscriptiondata, $notificationstats)) {
            return;
        }

        set_config(self::LAST_NOTIFIED_CONFIG, (string) $limitreachedtime, 'tool_mobile');
    }

    /**
     * Extract the current month notification stats from subscription data.
     *
     * @param ?array $subscriptiondata Subscription information returned by the Apps Portal API.
     * @param \DateTimeImmutable $now Current time used to identify the matching monthly stats.
     * @return ?array
     */
    public static function get_current_month_notification_stats(
        ?array $subscriptiondata,
        \DateTimeImmutable $now,
    ): ?array {
        if (
            empty($subscriptiondata['statistics']['notifications']['monthly']) ||
            !is_array($subscriptiondata['statistics']['notifications']['monthly'])
        ) {
            return null;
        }

        $currentyear = (int) $now->format('Y');
        $currentmonth = (int) $now->format('n');

        foreach ($subscriptiondata['statistics']['notifications']['monthly'] as $monthstats) {
            if (!is_array($monthstats)) {
                continue;
            }

            if ((int) ($monthstats['year'] ?? 0) !== $currentyear) {
                continue;
            }

            if ((int) ($monthstats['month'] ?? 0) !== $currentmonth) {
                continue;
            }

            return $monthstats;
        }

        return null;
    }

    /**
     * Notify all site admins about the reached push notification limit.
     *
     * @param array $subscriptiondata Subscription information returned by the Apps Portal API.
     * @param array $notificationstats Current month notification statistics.
     * @return bool True if at least one message was attempted.
     */
    protected static function notify_admins(array $subscriptiondata, array $notificationstats): bool {
        global $PAGE;

        $recipients = self::get_notification_recipients();
        if (empty($recipients)) {
            return false;
        }

        $subscriptionurl = new \moodle_url('/admin/tool/mobile/subscription.php');
        $subject = get_string('limitreachedpushnotifications', 'tool_mobile');
        $devicelimit = self::get_push_notification_device_limit($subscriptiondata);
        $renderable = new push_notification_limit_message(
            currentactivedevices: (int) ($notificationstats['activedevices'] ?? 0),
            ignorednotifications: (int) ($notificationstats['ignorednotifications'] ?? 0),
            devicelimit: $devicelimit,
            buttonurl: $subscriptionurl,
        );
        $renderer = $PAGE->get_renderer('tool_mobile');
        $context = $renderable->export_for_template($renderer);
        $fullmessage = self::get_plain_text_message($renderer, $context);
        $fullmessagehtml = $renderer->render($renderable);

        foreach ($recipients as $recipient) {
            $message = new \core\message\message();
            $message->component = 'tool_mobile';
            $message->name = 'pushlimitreached';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $recipient;
            $message->subject = $subject;
            $message->fullmessage = $fullmessage;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = $fullmessagehtml;
            $message->smallmessage = $subject;
            $message->notification = 1;
            $message->contexturl = $subscriptionurl->out(false);
            $message->contexturlname = get_string('mobileappsubscription', 'tool_mobile');

            message_send($message);
        }

        return true;
    }

    /**
     * Build the plain text notification message using the same content as the HTML version.
     *
     * @param \core\output\renderer_base $renderer Plugin renderer used to render the text template.
     * @param array $context Message placeholders already prepared for the template.
     * @return string
     */
    protected static function get_plain_text_message(
        \core\output\renderer_base $renderer,
        array $context
    ): string {
        return $renderer->render_from_template('tool_mobile/push_notification_limit_message_textemail', $context);
    }

    /**
     * Get the configured push notification device limit for the current subscription.
     *
     * @param array $subscriptiondata Subscription information returned by the Apps Portal API.
     * @return ?int
     */
    protected static function get_push_notification_device_limit(array $subscriptiondata): ?int {
        if (empty($subscriptiondata['subscription']['features'])) {
            return null;
        }

        foreach ($subscriptiondata['subscription']['features'] as $feature) {
            if (($feature['name'] ?? null) !== 'pushnotificationsdevices') {
                continue;
            }

            if (!isset($feature['limit']) || $feature['limit'] === null || $feature['limit'] === '') {
                return null;
            }

            return (int) $feature['limit'];
        }

        return null;
    }

    /**
     * Get all users who should receive this notification.
     *
     * @return array<int, \stdClass>
     */
    protected static function get_notification_recipients(): array {
        $recipients = [];

        foreach (get_admins() as $admin) {
            $recipients[$admin->id] = $admin;
        }

        return $recipients;
    }
}
