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

use local_intellidata\helpers\SettingsHelper;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * IntelliData tracking lib.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_trackinglib extends external_api {

    /**
     * Tracking validate params.
     *
     * @return external_function_parameters
     */
    public static function save_tracking_parameters() {
        return new external_function_parameters(
            [
                'page'   => new external_value(PARAM_TEXT, 'page identifier'),
                'param' => new external_value(PARAM_INT, 'page param'),
            ]
        );
    }

    /**
     * Save IntelliBoard tracking.
     *
     * @param $page
     * @param $param
     * @return int[]
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function save_tracking($page, $param) {
        global $SESSION;

        $params = self::validate_parameters(
            self::save_tracking_parameters(),
            [
                'page' => $page,
                'param' => $param,
            ]
        );

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/intellidata:trackdata', $context);

        $ajaxfrequency = (int)SettingsHelper::get_setting('ajaxfrequency');
        $params['time'] = 0;
        if (isset($SESSION->local_intellidata_last_tracked_time)
            && $SESSION->local_intellidata_last_tracked_time <= time()
            && $SESSION->local_intellidata_last_tracked_time > (time() - ($ajaxfrequency * 2))) {
            $params['time'] = time() - $SESSION->local_intellidata_last_tracked_time;
        } else if (isset($SESSION->local_intellidata_last_tracked_time)
            && $SESSION->local_intellidata_last_tracked_time <= time() - $ajaxfrequency
            && $SESSION->local_intellidata_last_tracked_time < (time() - ($ajaxfrequency * 2))) {
            $params['time'] = $ajaxfrequency;
        }

        if ($params['time'] > 0) {
            $tracking = new \local_intellidata\services\tracking_service(true, $params);
            $tracking->track();
        }

        return ['time' => $params['time']];
    }

    /**
     * Tracking return params.
     *
     * @return external_single_structure
     */
    public static function save_tracking_returns() {
        return new external_single_structure(
            [
                'time' => new external_value(PARAM_INT, 'time'),
            ]
        );
    }

}
