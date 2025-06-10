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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class TrackingHelper {

    /**
     * Validate is plugin enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function enabled() {
        return (get_config('local_intellidata', 'enabled') && SettingsHelper::is_plugin_setup())
            ? true : false;
    }

    /**
     * Enable plugin.
     *
     * @return bool
     */
    public static function enable() {
        return set_config('local_intellidata', true, 'enabled');
    }

    /**
     * Disable plugin.
     *
     * @return bool
     */
    public static function disable() {
        return set_config('local_intellidata', false, 'enabled');
    }

    /**
     * Validate is eventstracking enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function eventstracking_enabled() {
        return (self::enabled() && SettingsHelper::get_setting('eventstracking') && !self::new_tracking_enabled()) ? true : false;
    }

    /**
     * Validate is eventstracking enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function new_tracking_enabled() {
        return (self::enabled() && SettingsHelper::get_setting('newtracking')) ? true : false;
    }

    /**
     * Validate is tracking enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function tracking_enabled() {
        $tracking = get_config('local_intellidata', 'enabledtracking');

        if ($tracking && !CLI_SCRIPT && !AJAX_SCRIPT) {
            return true;
        }

        return false;
    }

    /**
     * Validate is tracklogsdatatypes enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function trackinglogs_enabled() {
        return (SettingsHelper::get_setting('tracklogsdatatypes')) ? true : false;
    }

    /**
     * Enable tracking.
     *
     * @return bool
     */
    public static function enable_tracking() {
        return set_config('local_intellidata', true, 'enabledtracking');
    }

    /**
     * Disable tracking.
     *
     * @return bool
     */
    public static function disable_tracking() {
        return set_config('local_intellidata', false, 'enabledtracking');
    }

    /**
     * Extract tracking records.
     *
     * @param $trackingdata
     * @return array
     */
    public static function extract_tracking_records($trackingdata) {

        $trackingrecords = [];

        foreach ($trackingdata as $data) {
            $trackingrecords[] = (object)$data['tracking'];
        }

        return $trackingrecords;
    }

    /**
     * Extract tracking logs.
     *
     * @param $userdata
     * @param $trackingrecords
     * @return array
     */
    public static function extract_tracking_logs($userdata, $trackingrecords) {

        $logsrecords = [];

        foreach ($userdata as $data) {
            $tracking = (object)$data['tracking'];
            $tracking->id = (isset($trackingrecords[$tracking->page . '_' . $tracking->param]))
                ? $trackingrecords[$tracking->page . '_' . $tracking->param]->id
                : 0;
            $logs = $data['logs'];

            if (count($logs)) {
                foreach ($logs as $logrecord) {
                    if ($tracking->id) {
                        $logrecord = (object)$logrecord;

                        $logrecord->trackid = $tracking->id;
                        $logsrecords[] = $logrecord;
                    }
                }
            }
        }

        return $logsrecords;
    }

    /**
     * Extract tracking logs.
     *
     * @param $userdata
     * @param $trackingrecords
     * @return array
     */
    public static function extract_tracking_details($userdata, $trackingrecords, $logsrecords) {

        $detailsrecords = [];

        foreach ($userdata as $data) {
            $tracking = (object)$data['tracking'];
            $tracking->id = (isset($trackingrecords[$tracking->page . '_' . $tracking->param]))
                ? $trackingrecords[$tracking->page . '_' . $tracking->param]->id
                : 0;

            foreach ($data['logs'] as $logrecord) {
                $logrecord = (object)$logrecord;
                $logrecord->id = (isset($logsrecords[$tracking->id . '_' . $logrecord->timepoint]))
                    ? $logsrecords[$tracking->id . '_' . $logrecord->timepoint]->id
                    : 0;

                $details = $data['details'][$logrecord->timepoint];
                foreach ($details as $detailrecord) {
                    $detailrecord = (object)$detailrecord;

                    if ($logrecord->id) {
                        $detailrecord->logid = $logrecord->id;
                        $detailsrecords[] = $detailrecord;
                    }
                }
            }
        }

        return $detailsrecords;
    }
}
