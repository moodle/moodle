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
use core_external\external_value;
use core_external\external_warnings;
use tool_dataprivacy\api;
use core_user;
use moodle_exception;

/**
 * External function for creating a data request.
 *
 * @package    tool_dataprivacy
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class create_data_request extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'type' => new external_value(PARAM_INT, 'The type of data request to create. 1 for export, 2 for data deletion.'),
                'comments' => new external_value(PARAM_RAW, 'Comments for the data request.', VALUE_DEFAULT, ''),
                'foruserid' => new external_value(PARAM_INT, 'The id of the user to create the data request for. Empty for current user.',
                    VALUE_DEFAULT, 0),
            ]
        );
    }

    /**
     * Create a data request.
     *
     * @param int $type The type of data request to create.
     * @param string $comments Comments for the data request.
     * @param int $foruserid The id of the user to create the data request for.
     * @return array containing the id of the request created and warnings.
     * @throws moodle_exception
     */
    public static function execute(int $type, string $comments = '', int $foruserid = 0): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'type' => $type,
            'comments' => $comments,
            'foruserid' => $foruserid,
        ]);

        $system = \context_system::instance();
        external_api::validate_context($system);

        $cancontactdpo = api::can_contact_dpo();
        $canmanage = false;

        if (empty($params['foruserid']) || $params['foruserid'] == $USER->id) {
            $user = $USER;
        } else {
            $user = core_user::get_user($params['foruserid'], '*', MUST_EXIST);
            core_user::require_active_user($user);

            if (!$canmanage = api::can_manage_data_requests($user->id)) {
                api::require_can_create_data_request_for_user($user->id);
            }
        }

        if (!$canmanage && !$cancontactdpo) {
            throw new moodle_exception('contactdpoviaprivacypolicy', 'tool_dataprivacy');
        }

        // Validate the data.
        $validationerrors = api::validate_create_data_request((object) ['userid' => $user->id, 'type' => $params['type']]);
        if (!empty($validationerrors)) {
            $error = array_key_first($validationerrors);
            throw new moodle_exception($error, 'tool_dataprivacy');
        }
        // All clear now, create the request.
        $datarequest = api::create_data_request($user->id, $params['type'], $params['comments']);

        return [
            'datarequestid' => $datarequest->get('id'),
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
                'datarequestid' => new external_value(PARAM_INT, 'The id of the created data request.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
