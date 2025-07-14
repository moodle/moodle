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
 * Class for submit selected courses from form.
 *
 * @package    tool_dataprivacy
 * @copyright  2021 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core\notification;
use context_system;

/**
 * Class for submit selected courses from form.
 *
 * @copyright  2021 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.3
 */
class submit_selected_courses_form extends external_api {
    /**
     * Parameter description for get_data_request().
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The id of data request'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data of selected courses form, encoded as a json array'),
        ]);
    }

    /**
     * Fetch the list of course which user can select to export data.
     *
     * @param int $requestid The request ID.
     * @param string $jsonformdata The data of selected courses form.
     * @return array
     */
    public static function execute(int $requestid, string $jsonformdata): array {

        $warnings = [];
        $result = false;
        $params = self::validate_parameters(self::execute_parameters(), [
            'requestid' => $requestid,
            'jsonformdata' => $jsonformdata,
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        // Make sure the user has the proper capability.
        require_capability('tool/dataprivacy:managedatarequests', $context);

        $requestid = $params['requestid'];
        $serialiseddata = json_decode($params['jsonformdata']);
        $data = array();
        parse_str($serialiseddata, $data);

        $mform = new \tool_dataprivacy\form\exportfilter_form(null, ['requestid' => $requestid], 'post', '', null, true, $data);

        if (PHPUNIT_TEST) {
            $validateddata = $mform->mock_ajax_submit($data);
        } else {
            $validateddata = $mform->get_data();
        }
        if ($validateddata) {
            // Ensure the request exists.
            $requestexists = \tool_dataprivacy\data_request::record_exists($requestid);

            if ($requestexists) {
                $coursecontextids = [];
                if (!empty($validateddata->coursecontextids)) {
                    $coursecontextids = $validateddata->coursecontextids;
                }
                if (PHPUNIT_TEST) {
                    if (!empty($validateddata['coursecontextids'])) {
                        $coursecontextids = $validateddata['coursecontextids'];
                    }
                }
                $result = \tool_dataprivacy\api::approve_data_request($requestid, $coursecontextids);

                // Add notification in the session to be shown when the page is reloaded on the JS side.
                notification::success(get_string('requestapproved', 'tool_dataprivacy'));
            } else {
                $warnings = [
                    'item' => $requestid,
                    'warningcode' => 'errorrequestnotfound',
                    'message' => get_string('errorrequestnotfound', 'tool_dataprivacy'),
                ];
            }
        }
        return [
            'result' => $result,
            'warnings' => $warnings,
        ];
    }

    /**
     * Parameter description for submit_selected_courses_form().
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings(),
        ]);
    }
}
