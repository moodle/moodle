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

namespace tool_mobile\output;

/**
 * Push notification limit alert message.
 *
 * @package    tool_mobile
 * @copyright  2026 Daniel Ureña <daniel.urena@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class push_notification_limit_message implements \renderable, \templatable {
    /** @var int Devices currently receiving push notifications. */
    protected int $currentactivedevices;

    /** @var int Notifications not sent because the limit was exceeded. */
    protected int $ignorednotifications;

    /** @var ?int Configured device limit for push notifications. */
    protected ?int $devicelimit;

    /** @var \moodle_url Subscription management URL. */
    protected \moodle_url $buttonurl;

    /**
     * Constructor.
     *
     * @param int $currentactivedevices Devices currently receiving push notifications.
     * @param int $ignorednotifications Notifications not sent because the limit was exceeded.
     * @param ?int $devicelimit Configured device limit for push notifications.
     * @param \moodle_url $buttonurl Subscription management URL.
     */
    public function __construct(
        int $currentactivedevices,
        int $ignorednotifications,
        ?int $devicelimit,
        \moodle_url $buttonurl,
    ) {
        $this->currentactivedevices = $currentactivedevices;
        $this->ignorednotifications = $ignorednotifications;
        $this->devicelimit = $devicelimit;
        $this->buttonurl = $buttonurl;
    }

    /**
     * Export the template context for the push notification limit HTML message.
     *
     * @param \core\output\renderer_base $output
     * @return array<string, mixed>
     */
    public function export_for_template(\core\output\renderer_base $output): array {
        $currentdevices = $this->currentactivedevices;
        $ignorednotifications = $this->ignorednotifications;
        $devicelimit = $this->devicelimit;
        $ratiolabel = $devicelimit !== null ? $currentdevices . ' / ' . $devicelimit : (string) $currentdevices;
        $progresswidth = 100;
        if ($devicelimit !== null && $devicelimit > 0) {
            $progresswidth = min(100, (int) ceil(($currentdevices / $devicelimit) * 100));
        }

        if ($ignorednotifications === 1) {
            $subheading = get_string('misseduserssingle', 'tool_mobile');
        } else if ($ignorednotifications > 1) {
            $subheading = get_string('missedusersmultiple', 'tool_mobile', $ignorednotifications);
        } else {
            $subheading = get_string('missedadditionalusers', 'tool_mobile');
        }

        return [
            'heading' => get_string('limitreachedmonthly', 'tool_mobile'),
            'subheading' => $subheading,
            'metriclabel' => get_string('activedeviceslabel', 'tool_mobile'),
            'limitlabel' => get_string('limitreached', 'tool_mobile'),
            'footer' => get_string('limitnewdevices', 'tool_mobile'),
            'buttonlabel' => get_string('upgradeyourplanaction', 'tool_mobile'),
            'buttonurl' => $this->buttonurl->out(false),
            'ratiolabel' => $ratiolabel,
            'progresswidth' => $progresswidth,
            'illustrationurl' => (new \moodle_url('/admin/tool/mobile/pix/push_notification.svg'))->out(false),
            'alerticonurl' => (new \moodle_url('/pix/i/risk_xss.svg'))->out(false),
        ];
    }
}
