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
 * Calendar external API for deleting the subscription.
 *
 * @package core_calendar
 * @category external
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external\subscription;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;

/**
 * Calendar external API for deleting the subscription.
 *
 * @package core_calendar
 * @category external
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete extends external_api {

    /**
     * Describes the parameters for deleting the subscription.
     *
     * @return external_function_parameters
     * @since Moodle 4.0
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'subscriptionid' => new external_value(PARAM_INT, 'The id of the subscription', VALUE_REQUIRED)
        ]);
    }

    /**
     * External function to delete the calendar subscription.
     *
     * @param int $subscriptionid Subscription id.
     * @return array
     */
    public static function execute(int $subscriptionid): array {
        [
            'subscriptionid' => $subscriptionid
        ] = self::validate_parameters(self::execute_parameters(), [
            'subscriptionid' => $subscriptionid
        ]);
        $status = false;
        $warnings = [];
        if (calendar_can_edit_subscription($subscriptionid)) {
            // Fetch the subscription from the database making sure it exists.
            $sub = calendar_get_subscription($subscriptionid);
            calendar_delete_subscription($subscriptionid);
            $status = true;
        } else {
            $warnings = [
                'item' => $subscriptionid,
                'warningcode' => 'errordeletingsubscription',
                'message' => get_string('nopermissions', 'error')
            ];
        }
        return [
            'status' => $status,
            'warnings' => $warnings
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 4.0
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status: true if success'),
            'warnings' => new external_warnings()
        ]);
    }
}
