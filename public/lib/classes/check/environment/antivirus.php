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

namespace core\check\environment;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Checks status of antivirus scanners by looking back at any recent scans.
 *
 * @package    core
 * @category   check
 * @author     Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright  Catalyst IT, 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class antivirus extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_antivirus_name', 'report_security');
    }

    /**
     * A link to a place to action this
     *
     * @return \action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/settings.php', ['section' => 'manageantiviruses']),
            get_string('antivirussettings', 'antivirus'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG, $DB;
        $details = \html_writer::tag('p', get_string('check_antivirus_details', 'report_security'));

        // If no scanners are enabled, then return an NA status since the results do not matter.
        if (empty($CFG->antiviruses)) {
            $status = result::NA;
            $summary = get_string('check_antivirus_info', 'report_security');
            return new result($status, $summary, $details);
        }

        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers('\core\log\sql_internal_table_reader');

        // If reader is not a sql_internal_table_reader return UNKNOWN since we
        // aren't able to fetch the required information. Legacy logs are not
        // supported here. They do not hold enough adequate information to be
        // used for these checks.
        if (empty($readers)) {
            $status = result::UNKNOWN;
            $summary = get_string('check_antivirus_logstore_not_supported', 'report_security');
            return new result($status, $summary, $details);
        }

        $reader = reset($readers);

        // If there has been a recent timestamp within threshold period, then
        // set the status to ERROR and describe the problem, e.g. X issues in
        // the last N period.
        $threshold = get_config('antivirus', 'threshold');
        $params = [];
        $params['lookback'] = time() - $threshold;

        // Type of "targets" to include.
        list($targetsqlin, $inparams) = $DB->get_in_or_equal([
            'antivirus_scan_file',
            'antivirus_scan_data',
        ], SQL_PARAMS_NAMED);
        $params = array_merge($inparams, $params);

        // Specify criteria for search.
        $selectwhere = "timecreated > :lookback
                        AND target $targetsqlin
                        AND action = 'error'";

        $totalerrors = $reader->get_events_select_count($selectwhere, $params);
        if (!empty($totalerrors)) {
            $status = result::ERROR;
            $summary = get_string('check_antivirus_error', 'report_security', [
                'errors' => $totalerrors,
                'lookback' => format_time($threshold)
            ]);
        } else if (!empty($CFG->antiviruses)) {
            $status = result::OK;
            // Fetch count of enabled antiviruses (we don't care about which ones).
            $totalantiviruses = !empty($CFG->antiviruses) ? count(explode(',', $CFG->antiviruses)) : 0;
            $summary = get_string('check_antivirus_ok', 'report_security', [
                'scanners' => $totalantiviruses,
                'lookback' => format_time($threshold)
            ]);
        }
        return new result($status, $summary, $details);
    }
}

