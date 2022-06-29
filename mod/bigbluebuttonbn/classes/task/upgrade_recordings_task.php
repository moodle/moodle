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

namespace mod_bigbluebuttonbn\task;

use core\task\adhoc_task;
use core\task\manager;
use Matrix\Exception;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\recording_proxy;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\recording;
use moodle_exception;

/**
 * Class containing the scheduled task for converting recordings for the BigBlueButton version 2.5 in Moodle 4.0.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Jesus Federico, Blindside Networks Inc <jesus at blindsidenetworks dot com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_recordings_task extends adhoc_task {
    /**
     * Run the migration task.
     */
    public function execute() {
        $info = $this->get_custom_data();
        $meetingid = $info->meetingid;
        $isimported = $info->isimported ?? 0;
        $this->process_bigbluebuttonbn_logs($meetingid, $isimported);
    }

    /**
     * Process all bigbluebuttonbn logs looking for entries which should be converted to meetings.
     *
     * @param string $meetingid
     * @param bool $isimported
     * @return bool Whether any more logs are waiting to be processed
     * @throws \dml_exception
     * @throws moodle_exception
     */
    protected function process_bigbluebuttonbn_logs(string $meetingid, bool $isimported): bool {
        global $DB;

        $classname = static::class;
        mtrace("Executing {$classname} for meeting {$meetingid}...");

        // Fetch the logs queued for upgrade.
        mtrace("Fetching logs for conversion");
        // Each log is ordered by timecreated.
        [$select, $params] = $this->get_sql_query_for_logs($meetingid, $isimported);
        $logsrs = $DB->get_recordset_select('bigbluebuttonbn_logs',
            $select,
            $params,
            'timecreated DESC',
            'id, meetingid, timecreated, log');

        if (!$logsrs->valid()) {
            mtrace("No logs were found for conversion.");
            // No more logs. Stop queueing.
            return false;
        }
        // Retrieve recordings from the servers for this meeting.
        $recordings = recording_proxy::fetch_recording_by_meeting_id([$meetingid]);
        // Sort recordings by meetingId, then startTime.
        uasort($recordings, function($a, $b) {
            return $b['startTime'] - $a['startTime'];
        });

        // Create an instance of bigbluebuttonbn_recording per valid recording.
        mtrace("Creating new recording records...");
        $recordingcount = 0;
        foreach ($recordings as $recordingid => $recording) {
            $importeddata = $isimported ? '' : json_encode($recording);
            try {
                $instance = instance::get_from_meetingid($recording['meetingID']);
            } catch (Exception $e) {
                mtrace("Unable to parse meetingID " . $e->getMessage());
                continue;
            }

            if ($instance) {
                $newrecording = [
                    'courseid' => $instance->get_course_id(),
                    'bigbluebuttonbnid' => $instance->get_instance_id(),
                    'groupid' => $instance->get_group_id(), // The groupid should be taken from the meetingID.
                    'recordingid' => $recordingid,
                    'status' => recording::RECORDING_STATUS_PROCESSED,
                ];
            } else {
                mtrace("Unable to find an activity for {$recording['meetingID']}. This recording is headless.");
                // This instance does not exist any more.
                // Use the data in the log instead of the instance.
                $meetingdata = instance::parse_meetingid($recording['meetingID']);
                $newrecording = [
                    'courseid' => $meetingdata['courseid'],
                    'bigbluebuttonbnid' => $meetingdata['instanceid'],
                    'groupid' => 0,
                    'recordingid' => $recordingid,
                    'status' => recording::RECORDING_STATUS_PROCESSED,
                ];

                if (array_key_exists('groupid', $meetingdata)) {
                    $newrecording['groupid'] = $meetingdata['groupid'];
                }
            }

            if ($DB->record_exists('bigbluebuttonbn_recordings', $newrecording)) {
                mtrace("A recording already exists for {$recording['recordID']}. Skipping.");
                // A recording matching these characteristics alreay exists.
                continue;
            }
            // Recording has not been imported, check if we still have more logs.
            // We try to guess which logs matches which recordings are they are classed in the same order.
            // But  this is just an attempt.
            $log = null;
            if ($logsrs->valid()) {
                $log = $logsrs->current();
                $logsrs->next();
            }
            $timecreated = empty($log) ? time() : $log->timecreated;
            $newrecording['imported'] = $isimported;
            $newrecording['headless'] = 0;
            $newrecording['importeddata'] = $importeddata;
            $newrecording['timecreated'] = $newrecording['timemodified'] = $timecreated;

            // If we could not match with a log, we still create the recording.
            $DB->insert_record('bigbluebuttonbn_recordings', $newrecording);
            $recordingcount++;
        }
        mtrace("Migrated {$recordingcount} recordings.");
        // Now deactivate logs by marking all of them as migrated.
        // Reason for this is that we don't want to run another migration here and we don't know
        // which logs matches which recordings.
        $DB->set_field_select('bigbluebuttonbn_logs', 'log',
            $isimported ? logger::EVENT_IMPORT_MIGRATED : logger::EVENT_CREATE_MIGRATED,
            $select,
            $params
        );
        $logsrs->close();
        return true;
    }

    /**
     * Get the query (records_select) for the logs to convert.
     *
     * Each log is ordered by timecreated.
     *
     * @param string $meetingid
     * @param bool $isimported
     * @return array
     * @throws \dml_exception
     */
    protected function get_sql_query_for_logs(string $meetingid, bool $isimported): array {
        global $DB;
        if ($isimported) {
            return [
                'log = :logmatch AND meetingid = :meetingid',
                ['logmatch' => logger::EVENT_IMPORT, 'meetingid' => $meetingid],
            ];
        }
        return [
            'log = :logmatch AND meetingid = :meetingid AND ' . $DB->sql_like('meta', ':match'),
            [
                'logmatch' => logger::EVENT_CREATE,
                'match' => '%true%',
                'meetingid' => $meetingid
            ],
        ];
    }

    /**
     * Schedule all upgrading tasks.
     *
     * @param bool $importedrecordings
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function schedule_upgrade_per_meeting($importedrecordings = false) {
        global $DB;
        $meetingids = $DB->get_fieldset_sql(
            'SELECT DISTINCT meetingid FROM {bigbluebuttonbn_logs} WHERE log = :createorimport',
            ['createorimport' => $importedrecordings ? logger::EVENT_IMPORT : logger::EVENT_CREATE]
        );
        foreach ($meetingids as $mid) {
            $createdrecordingtask = new static();
            $createdrecordingtask->set_custom_data((object) ['meetingid' => $mid, 'isimported' => $importedrecordings]);
            manager::queue_adhoc_task($createdrecordingtask);
        }
    }
}
