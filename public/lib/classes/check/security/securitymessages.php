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
 * Verifies that security-critical message notifications are forced on.
 *
 * @package    core
 * @category   check
 * @copyright  2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\security;

use core\check\check;
use core\check\result;

/**
 * Verifies that security-critical message notifications are forced on.
 *
 * Some message providers should be forced so that users cannot opt out of
 * receiving security notifications such as new login alerts.
 *
 * @copyright  2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class securitymessages extends check {
    /**
     * The message providers that must be forced on for all enabled and configured processors.
     *
     * Note: core's component in the message_providers table is 'moodle'.
     */
    const REQUIRED_FORCED = [
        'moodle/newlogin',
    ];

    /**
     * Get the name of this security check.
     *
     * @return string Name of this security check.
     */
    public function get_name(): string {
        return get_string('check_securitymessages_name', 'report_security');
    }

    /**
     * A link to a place to action this.
     *
     * @return \action_link|null URL to configure message notification settings, or null if unavailable.
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/message.php'),
            get_string('defaultmessageoutputs', 'message')
        );
    }

    /**
     * Check that required security notifications are enabled and locked for all enabled message processors.
     *
     * @return result Result object containing security check data.
     */
    public function get_result(): result {
        global $OUTPUT;

        $notok = [];
        $enabledprocessors = get_message_processors(true);

        // Build a matrix of [providerkey => [processorname => ['enabled' => bool, 'locked' => bool]]].
        $matrix = [];
        foreach (self::REQUIRED_FORCED as $providerkey) {
            [$component, $name] = explode('/', $providerkey);
            // Check whether the notification itself has been disabled via its top-level toggle.
            $disablekey = $component . '_' . $name . '_disable';
            $notificationdisabled = get_config('message', $disablekey) === '1';
            if ($notificationdisabled) {
                $disabledstr = get_string('check_securitymessages_notification_disabled', 'report_security');
                $notok[] = $component . '/' . $name . ' (' . $disabledstr . ')';
            }

            $matrix[$providerkey] = [$notificationdisabled, []];

            // The enabled key stores a comma-separated list of processor names enabled by default.
            $enabledkey = 'message_provider_' . $component . '_' . $name . '_enabled';
            $enabledlist = explode(',', get_config('message', $enabledkey) ?: '');

            foreach ($enabledprocessors as $processor) {
                $lockedkey = $processor->name . '_provider_' . $component . '_' . $name . '_locked';
                $locked = get_config('message', $lockedkey) === '1';
                $enabled = in_array($processor->name, $enabledlist);

                $matrix[$providerkey][1][$processor->name] = ['enabled' => $enabled, 'locked' => $locked];

                if (!$enabled || !$locked) {
                    $notok[] = $component . '/' . $name . ' (' . get_string('pluginname', 'message_' . $processor->name) . ')';
                }
            }
        }

        // Build details table.
        $table = new \html_table();
        $table->attributes['class'] = 'flexible generaltable table table-sm w-auto';

        $processornames = array_keys($enabledprocessors);
        $processorlabels = array_map(
            fn($name) => get_string('pluginname', 'message_' . $name),
            $processornames
        );
        $table->head = array_merge(
            [get_string('notifications', 'message'), get_string('check_securitymessages_notification_enabled', 'report_security')],
            $processorlabels
        );
        $table->data = [];

        foreach ($matrix as $providerkey => [$notificationdisabled, $processors]) {
            $enablestr = get_string('check_securitymessages_action_enable_notification', 'report_security');
            $notificationcell = $notificationdisabled
                ? $OUTPUT->check_result(new result(result::WARNING, '', '')) . ' ' . $enablestr
                : $OUTPUT->check_result(new result(result::OK, '', ''));

            $row = [\html_writer::tag('code', $providerkey), $notificationcell];
            foreach ($processors as $state) {
                ['enabled' => $enabled, 'locked' => $locked] = $state;
                if ($enabled && $locked) {
                    $cell = $OUTPUT->check_result(new result(result::OK, '', ''));
                } else {
                    if (!$enabled && !$locked) {
                        $action = get_string('check_securitymessages_action_enableandlock', 'report_security');
                    } else if (!$enabled) {
                        $action = get_string('check_securitymessages_action_enable', 'report_security');
                    } else {
                        $action = get_string('check_securitymessages_action_lock', 'report_security');
                    }
                    $cell = $OUTPUT->check_result(new result(result::WARNING, '', '')) . ' ' . $action;
                }
                $row[] = $cell;
            }
            $table->data[] = $row;
        }

        $details = get_string('check_securitymessages_details', 'report_security');
        $details .= \html_writer::table($table);

        if (!empty($notok)) {
            $summary = get_string(
                'check_securitymessages_error',
                'report_security',
                implode(', ', $notok)
            );
            return new result(result::WARNING, $summary, $details);
        }

        return new result(result::OK, get_string('check_securitymessages_ok', 'report_security'), $details);
    }
}
