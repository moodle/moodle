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

namespace tool_dataprivacy\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_external\external_warnings;
use tool_dataprivacy\api;
use core_user;
use context_system;
use moodle_exception;

/**
 * External function for getting data requests.
 *
 * @package    tool_dataprivacy
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class get_data_requests extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user to get the data requests for. Empty for all users.',
                    VALUE_DEFAULT, 0),
                'statuses' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The status of the data requests to get.'),
                    'The statuses of the data requests to get.
                    0 for pending 1 preprocessing, 2 awaiting approval, 3 approved,
                    4 processed, 5 completed, 6 cancelled, 7 rejected.',
                    VALUE_DEFAULT,
                    []
                ),
                'types' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The type of the data requests to get.'),
                    'The types of the data requests to get. 1 for export, 2 for data deletion.',
                    VALUE_DEFAULT,
                    []
                ),
                'creationmethods' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The creation method of the data requests to get.'),
                    'The creation methods of the data requests to get. 0 for manual, 1 for automatic.',
                    VALUE_DEFAULT,
                    []
                ),
                'sort' => new external_value(PARAM_NOTAGS, 'The field to sort the data requests by.',
                    VALUE_DEFAULT, ''),
                'limitfrom' => new external_value(PARAM_INT, 'The number to start getting the data requests from.',
                    VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'The number of data requests to get.',
                    VALUE_DEFAULT, 0),
            ]
        );
    }


    /**
     * Get data requests.
     *
     * @param int $userid The user id.
     * @param array $statuses The status filters.
     * @param array $types The request type filters.
     * @param array $creationmethods The request creation method filters.
     * @param string $sort The order by clause.
     * @param int $limitfrom Amount of records to skip.
     * @param int $limitnum Amount of records to fetch.
     * @throws moodle_exception
     * @return array containing the data requests and warnings.
     */
    public static function execute($userid = 0, $statuses = [], $types = [], $creationmethods = [],
            $sort = '', $limitfrom = 0, $limitnum = 0): array {

        global $USER, $PAGE;

        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'statuses' => $statuses,
            'types' => $types,
            'creationmethods' => $creationmethods,
            'sort' => $sort,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
        ]);
        $systemcontext = context_system::instance();

        if ($params['userid'] == $USER->id) {
            $userid = $USER->id;
        } else {
            // Additional security checks when obtaining data requests for other users.
            if (!has_capability('tool/dataprivacy:managedatarequests', $systemcontext) || !api::is_site_dpo($USER->id)) {
                $dponamestring = implode (', ', api::get_dpo_role_names());
                throw new moodle_exception('privacyofficeronly', 'tool_dataprivacy', '', $dponamestring);
            }

            $userid = 0;
            if (!empty($params['userid'])) {
                $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
                core_user::require_active_user($user);
                $userid = $user->id;
            }
        }

        // Ensure sort parameter is safe to use. Fallback to default value of the parameter itself.
        $sortorderparts = explode(' ', $params['sort'], 2);
        $sortorder = get_safe_orderby([
            'id' => 'id',
            'status' => 'status',
            'timemodified' => 'timemodified',
            'default' => '',
        ], $sortorderparts[0], $sortorderparts[1] ?? '', false);

        $userrequests = api::get_data_requests($userid, $params['statuses'], $params['types'], $params['creationmethods'],
                $sortorder, $params['limitfrom'], $params['limitnum']);

        $requests = [];
        foreach ($userrequests as $requestpersistent) {
            $exporter = new data_request_exporter($requestpersistent, ['context' => $systemcontext]);
            $renderer = $PAGE->get_renderer('tool_dataprivacy');
            $requests[] = $exporter->export($renderer);
        }

        return [
            'requests' => $requests,
            'warnings' => [],
        ];
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'requests' => new external_multiple_structure(data_request_exporter::get_read_structure(), 'The data requests.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
