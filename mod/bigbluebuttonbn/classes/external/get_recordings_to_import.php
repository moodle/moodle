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
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\bigbluebutton\recordings\recording_data;
use mod_bigbluebuttonbn\recording;

/**
 * External service to fetch a list of recordings from the BBB service.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_recordings_to_import extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'destinationinstanceid' => new external_value(
                PARAM_INT,
                'Id of the other BBB we target for importing recordings into.
                The idea here is to remove already imported recordings.',
                VALUE_REQUIRED
            ),
            'sourcebigbluebuttonbnid' => new external_value(PARAM_INT,
                'bigbluebuttonbn instance id',
                VALUE_DEFAULT,
                0),
            'sourcecourseid' => new external_value(PARAM_INT,
                'source courseid to filter by',
                VALUE_DEFAULT,
                0),
            'tools' => new external_value(PARAM_RAW, 'a set of enabled tools', VALUE_DEFAULT,
                'protect,unprotect,publish,unpublish,delete'),
            'groupid' => new external_value(PARAM_INT, 'Group ID', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Get a list of recordings
     *
     * @param int $destinationinstanceid the bigbluebuttonbn instance id where recordings have been already imported.
     * @param int|null $sourcebigbluebuttonbnid the bigbluebuttonbn instance id to which the recordings are referred.
     * @param int|null $sourcecourseid the source courseid to filter by
     * @param string|null $tools
     * @param int|null $groupid
     * @return array of warnings and status result
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     */
    public static function execute(
        int $destinationinstanceid,
        ?int $sourcebigbluebuttonbnid = 0,
        ?int $sourcecourseid = 0,
        ?string $tools = 'protect,unprotect,publish,unpublish,delete',
        ?int $groupid = null
    ): array {
        global $USER, $DB;

        $returnval = [
            'status' => false,
            'warnings' => [],
        ];

        // Validate the sourcebigbluebuttonbnid ID.
        [
            'destinationinstanceid' => $destinationinstanceid,
            'sourcebigbluebuttonbnid' => $sourcebigbluebuttonbnid,
            'sourcecourseid' => $sourcecourseid,
            'tools' => $tools,
            'groupid' => $groupid
        ] = self::validate_parameters(self::execute_parameters(), [
            'destinationinstanceid' => $destinationinstanceid,
            'sourcebigbluebuttonbnid' => $sourcebigbluebuttonbnid,
            'sourcecourseid' => $sourcecourseid,
            'tools' => $tools,
            'groupid' => $groupid
        ]);

        $tools = explode(',', $tools ?? 'protect,unprotect,publish,unpublish,delete');

        // Fetch the session, features, and profile.
        $sourceinstance = null;
        $sourcecourse = null;
        if ($sourcecourseid) {
            $sourcecourse = $DB->get_record('course', ['id' => $sourcecourseid], '*', MUST_EXIST);
        }

        if (!empty($sourcebigbluebuttonbnid)) {
            $sourceinstance = instance::get_from_instanceid($sourcebigbluebuttonbnid);
            if (!$sourceinstance) {
                throw new \invalid_parameter_exception('Source Bigbluebutton Id is invalid');
            }
            $sourcecourse = $sourceinstance->get_course();
            // Validate that the user has access to this activity.
            self::validate_context($sourceinstance->get_context());
        }
        $destinstance = instance::get_from_instanceid($destinationinstanceid);
        // Validate that the user has access to this activity.
        self::validate_context($destinstance->get_context());
        if (!$destinstance->user_has_group_access($USER, $groupid)) {
            throw new \invalid_parameter_exception('Invalid group for this user ' . $groupid);
        }
        if ($groupid) {
            $destinstance->set_group_id($groupid);
        }
        // Exclude itself from the list if in import mode.
        $excludedids = [$destinstance->get_instance_id()];
        if ($sourceinstance) {
            $recordings = $sourceinstance->get_recordings($excludedids);
        } else {
            // There is a course id or a 0, so we fetch all recording including deleted recordings from this course.
            $recordings = recording::get_recordings_for_course(
                $sourcecourseid,
                $excludedids,
                true,
                false,
                ($sourcecourseid == 0 || $sourcebigbluebuttonbnid == 0),
                ($sourcecourseid == 0 || $sourcebigbluebuttonbnid == 0)
            );
        }

        if ($destinationinstanceid) {
            // Remove recording already imported in this specific activity.
            $destinationinstance = instance::get_from_instanceid($destinationinstanceid);
            $importedrecordings = recording::get_recordings_for_instance(
                $destinationinstance,
                true,
                true
            );
            // Unset from $recordings if recording is already imported.
            // Recording $recordings are indexed by $id (moodle table column id).
            foreach ($recordings as $index => $recording) {
                foreach ($importedrecordings as $irecord) {
                    if ($irecord->get('recordingid') == $recording->get('recordingid')) {
                        unset($recordings[$index]);
                    }
                }
            }
        }
        $tabledata = recording_data::get_recording_table($recordings, $tools, $sourceinstance, $sourcecourseid);
        $returnval['tabledata'] = $tabledata;
        $returnval['status'] = true;

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
                ])),
                'data' => new external_value(PARAM_RAW), // For now it will be json encoded.
            ], '', VALUE_OPTIONAL),
            'warnings' => new external_warnings()
        ]);
    }
}
