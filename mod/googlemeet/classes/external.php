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
 * Google Meet external API
 *
 * @package     mod_googlemeet
 * @category    external
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/mod/googlemeet/lib.php");

/**
 * Google Meet module external functions.
 *
 * @package     mod_googlemeet
 * @category    external
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_googlemeet_external extends external_api {

    /**
     * Describes the parameters for sync_recordings.
     *
     * @return external_function_parameters
     */
    public static function sync_recordings_parameters() {
        return new external_function_parameters(
            [
                'googlemeetid' => new external_value(PARAM_INT, ''),
                'creatoremail' => new external_value(PARAM_EMAIL, ''),
                'files' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'recordingId' => new external_value(PARAM_TEXT, 'Google Drive file ID'),
                            'name' => new external_value(PARAM_TEXT, 'Recording name'),
                            'createdTime' => new external_value(PARAM_INT, 'Creation date timestamp'),
                            'duration' => new external_value(PARAM_TEXT, 'Recording time'),
                            'webViewLink' => new external_value(PARAM_URL, 'Link to preview'),
                        ]
                    )
                ),
                'coursemoduleid' => new external_value(PARAM_INT, ''),
            ]
        );
    }

    /**
     * Synchronizes Google Drive recordings with the database.
     *
     * @param int $googlemeetid the googlemeet ID
     * @param string $creatoremail the room creator email
     * @param array $files the array of recordings
     * @param int $coursemoduleid the course module ID
     * @return array of recordings
     */
    public static function sync_recordings($googlemeetid, $creatoremail, $files, $coursemoduleid) {
        global $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(
            self::sync_recordings_parameters(),
            [
                'googlemeetid' => $googlemeetid,
                'creatoremail' => $creatoremail,
                'files' => $files,
                'coursemoduleid' => $coursemoduleid
            ]
        );

        $context = context_module::instance($coursemoduleid);
        require_capability('mod/googlemeet:syncgoogledrive', $context);

        $googlemeetrecordings = $DB->get_records('googlemeet_recordings', ['googlemeetid' => $googlemeetid]);

        $recordingids = array_column($googlemeetrecordings, 'recordingid');
        $fileids = array_column($files, 'recordingId');

        $updaterecordings = [];
        $insertrecordings = [];
        $deleterecordings = [];

        foreach ($files as $file) {
            if (in_array($file['recordingId'], $recordingids, true)) {
                array_push($updaterecordings, $file);
            } else {
                array_push($insertrecordings, $file);
            }
        }

        foreach ($googlemeetrecordings as $googlemeetrecording) {
            if (!in_array($googlemeetrecording->recordingid, $fileids)) {
                $deleterecordings['id'] = $googlemeetrecording->id;
            }
        }

        if ($deleterecordings) {
            $DB->delete_records('googlemeet_recordings', $deleterecordings);
        }

        if ($updaterecordings) {
            foreach ($updaterecordings as $updaterecording) {
                $recording = $DB->get_record('googlemeet_recordings', [
                    'googlemeetid' => $googlemeetid,
                    'recordingid' => $updaterecording['recordingId']
                ]);

                $recording->createdtime     = $updaterecording['createdTime'];
                $recording->duration        = $updaterecording['duration'];
                $recording->webviewlink     = $updaterecording['webViewLink'];
                $recording->timemodified    = time();

                $DB->update_record('googlemeet_recordings', $recording);
            }

            $googlemeetrecord = $DB->get_record('googlemeet', ['id' => $googlemeetid]);
            $googlemeetrecord->lastsync = time();
            $DB->update_record('googlemeet', $googlemeetrecord);
        }

        if ($insertrecordings) {
            $recordings = [];

            foreach ($insertrecordings as $insertrecording) {
                $recording = new stdClass();
                $recording->googlemeetid      = $googlemeetid;
                $recording->recordingid     = $insertrecording['recordingId'];
                $recording->name            = $insertrecording['name'];
                $recording->createdtime     = $insertrecording['createdTime'];
                $recording->duration        = $insertrecording['duration'];
                $recording->webviewlink     = $insertrecording['webViewLink'];
                $recording->timemodified    = time();

                array_push($recordings, $recording);
            }

            $DB->insert_records('googlemeet_recordings', $recordings);

            $googlemeetrecord = $DB->get_record('googlemeet', ['id' => $googlemeetid]);
            $googlemeetrecord->lastsync = time();

            if (!$googlemeetrecord->creatoremail) {
                $googlemeetrecord->creatoremail = $creatoremail;
            }

            $DB->update_record('googlemeet', $googlemeetrecord);
        }

        return googlemeet_list_recordings(['googlemeetid' => $googlemeetid]);
    }

    /**
     * Describes the sync_recordings return value.
     *
     * @return external_single_structure
     */
    public static function sync_recordings_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'Recording instance ID'),
                    'name' => new external_value(PARAM_TEXT, 'Recording name'),
                    'createdtime' => new external_value(PARAM_INT, 'Creation date timestamp'),
                    'createdtimeformatted' => new external_value(PARAM_TEXT, 'Formatted creation date'),
                    'duration' => new external_value(PARAM_TEXT, 'Recording time'),
                    'webviewlink' => new external_value(PARAM_URL, 'Link to preview'),
                    'visible' => new external_value(PARAM_BOOL, 'If recording visible')
                ]
            )
        );
    }

    /**
     * Describes the parameters for recording_edit_name.
     *
     * @return external_function_parameters
     */
    public static function recording_edit_name_parameters() {
        return new external_function_parameters(
            [
                'recordingid' => new external_value(PARAM_INT, ''),
                'name' => new external_value(PARAM_TEXT, ''),
                'coursemoduleid' => new external_value(PARAM_INT, ''),
            ]
        );
    }

    /**
     * Edit the name of the recording
     *
     * @param int $recordingid the recording ID
     * @param string $name the new name of recording
     * @param int $coursemoduleid the course module ID
     * @return object containing the new name of the recording
     */
    public static function recording_edit_name($recordingid, $name, $coursemoduleid) {
        global $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(
            self::recording_edit_name_parameters(),
            [
                'recordingid' => $recordingid,
                'name' => $name,
                'coursemoduleid' => $coursemoduleid
            ]
        );

        $context = context_module::instance($coursemoduleid);
        require_capability('mod/googlemeet:editrecording', $context);

        $recording = $DB->get_record('googlemeet_recordings', ['id' => $recordingid]);

        $recording->name = $name;
        $recording->timemodified = time();

        $DB->update_record('googlemeet_recordings', $recording);

        return (object)[
            'name' => $recording->name
        ];
    }

    /**
     * Describes the recording_edit_name return value.
     *
     * @return external_single_structure
     */
    public static function recording_edit_name_returns() {
        return new external_single_structure(
            [
                'name' => new external_value(PARAM_RAW, 'New recording name'),
            ]
        );
    }

    /**
     * Describes the parameters for showhide_recording.
     *
     * @return external_function_parameters
     */
    public static function showhide_recording_parameters() {
        return new external_function_parameters(
            [
                'recordingid' => new external_value(PARAM_INT, ''),
                'coursemoduleid' => new external_value(PARAM_INT, ''),
            ]
        );
    }

    /**
     * Toggle recording visibility.
     *
     * @param int $recordingid the recording ID
     * @param int $coursemoduleid the course module ID
     * @return object containing the visibility of the recording
     */
    public static function showhide_recording($recordingid, $coursemoduleid) {
        global $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(
            self::showhide_recording_parameters(),
            [
                'recordingid' => $recordingid,
                'coursemoduleid' => $coursemoduleid
            ]
        );

        $context = context_module::instance($coursemoduleid);
        require_capability('mod/googlemeet:editrecording', $context);

        $recording = $DB->get_record('googlemeet_recordings', ['id' => $recordingid]);

        if ($recording->visible) {
            $recording->visible = false;
        } else {
            $recording->visible = true;
        }

        $recording->timemodified = time();

        $DB->update_record('googlemeet_recordings', $recording);

        return (object)[
            'visible' => $recording->visible
        ];
    }

    /**
     * Describes the showhide_recording return value.
     *
     * @return external_single_structure
     */
    public static function showhide_recording_returns() {
        return new external_single_structure(
            [
                'visible' => new external_value(PARAM_RAW, 'Visible or hidden recording'),
            ]
        );
    }

    /**
     * Describes the parameters for delete_all_recordings.
     *
     * @return external_function_parameters
     */
    public static function delete_all_recordings_parameters() {
        return new external_function_parameters(
            [
                'googlemeetid' => new external_value(PARAM_INT, ''),
                'coursemoduleid' => new external_value(PARAM_INT, ''),
            ]
        );
    }

    /**
     * Removes all recordings from Google Meet.
     *
     * @param int $googlemeetid the googlemeet ID
     * @param int $coursemoduleid the course module ID
     * @return array empty
     */
    public static function delete_all_recordings($googlemeetid, $coursemoduleid) {
        global $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(
            self::delete_all_recordings_parameters(),
            [
                'googlemeetid' => $googlemeetid,
                'coursemoduleid' => $coursemoduleid
            ]
        );

        $context = context_module::instance($coursemoduleid);
        require_capability('mod/googlemeet:removerecording', $context);

        $DB->delete_records('googlemeet_recordings', ['googlemeetid' => $googlemeetid]);

        $googlemeetrecord = $DB->get_record('googlemeet', ['id' => $googlemeetid]);
        $googlemeetrecord->lastsync = time();
        $DB->update_record('googlemeet', $googlemeetrecord);

        return [];
    }

    /**
     * Describes the delete_all_recordings return value.
     *
     * @return external_single_structure
     */
    public static function delete_all_recordings_returns() {
        return new external_single_structure([]);
    }
}
