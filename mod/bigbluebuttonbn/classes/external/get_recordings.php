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

namespace mod_bigbluebuttonbn\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\restricted_context_exception;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\bigbluebutton\recordings\recording_data;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;

/**
 * External service to fetch a list of recordings from the BBB service.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_recordings extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbn instance id'),
            'tools' => new external_value(PARAM_RAW, 'a set of enabled tools', VALUE_DEFAULT,
                'protect,unprotect,publish,unpublish,delete'),
            'groupid' => new external_value(PARAM_INT, 'Group ID', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Get a list of recordings
     *
     * @param int $bigbluebuttonbnid the bigbluebuttonbn instance id to which the recordings are referred.
     * @param string|null $tools
     * @param int|null $groupid
     * @return array of warnings and status result
     * @throws \invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function execute(
        int $bigbluebuttonbnid = 0,
        ?string $tools = 'protect,unprotect,publish,unpublish,delete',
        ?int $groupid = null
    ): array {
        global $USER;

        $returnval = [
            'status' => false,
            'warnings' => [],
        ];

        // Validate the bigbluebuttonbnid ID.
        [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'tools' => $tools,
            'groupid' => $groupid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'tools' => $tools,
            'groupid' => $groupid,
        ]);

        $tools = explode(',', $tools ?? 'protect,unprotect,publish,unpublish,delete');

        // Fetch the session, features, and profile.
        $instance = instance::get_from_instanceid($bigbluebuttonbnid);
        if (!$instance) {
            $returnval['warnings'][] = [
                'item' => $bigbluebuttonbnid,
                'warningcode' => 'nosuchinstance',
                'message' => get_string('nosuchinstance', 'mod_bigbluebuttonbn',
                    (object) ['id' => $bigbluebuttonbnid, 'entity' => 'bigbluebuttonbn'])
            ];
        } else {
            $typeprofiles = bigbluebutton_proxy::get_instance_type_profiles();
            $profilefeature = $typeprofiles[$instance->get_type()]['features'];
            $showrecordings = in_array('all', $profilefeature) || in_array('showrecordings', $profilefeature);
            if ($showrecordings) {
                $context = $instance->get_context();
                // Validate that the user has access to this activity.
                self::validate_context($context);
                if (!$instance->user_has_group_access($USER, $groupid)) {
                    new restricted_context_exception();
                }
                if ($groupid) {
                    $instance->set_group_id($groupid);
                }
                $recordings = $instance->get_recordings([], $instance->get_instance_var('recordings_deleted') ?? false);
                $tabledata = recording_data::get_recording_table($recordings, $tools, $instance);

                $returnval['tabledata'] = $tabledata;
                $returnval['status'] = true;
            } else {
                $returnval['warnings'][] = [
                    'item' => $bigbluebuttonbnid,
                    'warningcode' => 'instanceprofilewithoutrecordings',
                    'message' => get_string('instanceprofilewithoutrecordings', 'mod_bigbluebuttonbn')
                ];
            }
        }
        return $returnval;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Whether the fetch was successful'),
            'tabledata' => new external_single_structure([
                'activity' => new external_value(PARAM_ALPHANUMEXT),
                'ping_interval' => new external_value(PARAM_INT),
                'locale' => new external_value(PARAM_TEXT),
                'profile_features' => new external_multiple_structure(new external_value(PARAM_TEXT)),
                'columns' => new external_multiple_structure(new external_single_structure([
                    'key' => new external_value(PARAM_ALPHA),
                    'label' => new external_value(PARAM_TEXT),
                    'width' => new external_value(PARAM_ALPHANUMEXT),
                    // See https://datatables.net/reference/option/columns.type .
                    'type' => new external_value(PARAM_ALPHANUMEXT, 'Column type', VALUE_OPTIONAL),
                    'sortable' => new external_value(PARAM_BOOL, 'Whether this column is sortable', VALUE_OPTIONAL, false),
                    'allowHTML' => new external_value(PARAM_BOOL, 'Whether this column contains HTML', VALUE_OPTIONAL, false),
                    'formatter' => new external_value(PARAM_ALPHANUMEXT, 'Formatter name', VALUE_OPTIONAL),
                ])),
                'data' => new external_value(PARAM_RAW), // For now it will be json encoded.
            ], '', VALUE_OPTIONAL),
            'warnings' => new external_warnings()
        ]);
    }
}
