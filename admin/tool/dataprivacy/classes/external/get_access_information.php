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

/**
 * External function for retrieving access (permissions) information for the privacy API.
 *
 * @package    tool_dataprivacy
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class get_access_information extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Main method of the external function.
     *
     * @return array current user permissions
     */
    public static function execute(): array {
        global $USER;

        $system = \context_system::instance();
        external_api::validate_context($system);

        return [
            'cancontactdpo' => api::can_contact_dpo(),
            'canmanagedatarequests' => api::can_manage_data_requests($USER->id),
            'cancreatedatadownloadrequest' => api::can_create_data_download_request_for_self($USER->id),
            'cancreatedatadeletionrequest' => api::can_create_data_deletion_request_for_self($USER->id),
            'hasongoingdatadownloadrequest' => api::has_ongoing_request($USER->id, api::DATAREQUEST_TYPE_EXPORT),
            'hasongoingdatadeletionrequest' => api::has_ongoing_request($USER->id, api::DATAREQUEST_TYPE_DELETE),
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
                'cancontactdpo' => new external_value(PARAM_BOOL, 'Can contact dpo.'),
                'canmanagedatarequests' => new external_value(PARAM_BOOL, 'Can manage data requests.'),
                'cancreatedatadownloadrequest' => new external_value(PARAM_BOOL, 'Can create data download request for self.'),
                'cancreatedatadeletionrequest' => new external_value(PARAM_BOOL, 'Can create data deletion request for self.'),
                'hasongoingdatadownloadrequest' => new external_value(PARAM_BOOL, 'Has ongoing data download request.'),
                'hasongoingdatadeletionrequest' => new external_value(PARAM_BOOL, 'Has ongoing data deletion request.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
