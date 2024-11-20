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

namespace core\external\output;

use core\output\stored_progress_bar;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Poll Stored Progress webservice.
 *
 * @package    core
 * @copyright  2023 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class poll_stored_progress extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'The stored_progress ID', VALUE_REQUIRED)
            ),
        ]);
    }

    /**
     * Returns description of method return data
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'stored_progress record id'),
                'uniqueid' => new external_value(PARAM_TEXT, 'unique element id'),
                'progress' => new external_value(PARAM_FLOAT, 'percentage progress'),
                'estimated' => new external_value(PARAM_RAW, 'estimated time left string'),
                'message' => new external_value(PARAM_TEXT, 'message to be displayed with the bar'),
                'error' => new external_value(PARAM_TEXT, 'error', VALUE_OPTIONAL),
                'timeout' => new external_value(PARAM_TEXT, 'timeout to use in the polling', VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * Poll the database for the progress of stored progress objects
     *
     * @param array $ids
     * @return array
     */
    public static function execute(array $ids): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'ids' => $ids,
        ]);

        $return = [];
        $ids = $params['ids'];
        foreach ($ids as $id) {
            // Load the stored progress bar object.
            $bar = stored_progress_bar::get_by_id($id);
            if ($bar) {
                // Return the updated bar data.
                $return[$id] = [
                    'id' => $id,
                    'uniqueid' => $bar->get_id(),
                    'progress' => $bar->get_percent(),
                    'estimated' => $bar->get_estimate_message($bar->get_percent()),
                    'message' => $bar->get_message(),
                    'timeout' => stored_progress_bar::get_timeout(),
                    'error' => $bar->get_haserrored(),
                ];

            } else {
                // If we could not find the record, we still need to return the right arguments in the array for the webservice.
                $return[$id] = [
                    'id' => $id,
                    'uniqueid' => '',
                    'progress' => 0,
                    'estimated' => '',
                    'message' => get_string('invalidrecordunknown', 'error'),
                    'timeout' => stored_progress_bar::get_timeout(),
                    'error' => true,
                ];
            }
        }

        return $return;
    }
}
