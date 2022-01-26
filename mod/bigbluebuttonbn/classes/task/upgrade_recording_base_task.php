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
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\local\proxy\recording_proxy;
use moodle_exception;

/**
 * Class containing the scheduled task for converting recordings for the BigBlueButton version 2.5 in Moodle 4.0.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Jesus Federico, Blindside Networks Inc <jesus at blindsidenetworks dot com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class upgrade_recording_base_task extends adhoc_task {

    /** @var int Chunk size to use when fetching recording */
    protected static $chunksize = 50;

    /**
     * Run the migration task.
     */
    public function execute() {
        if ($this->process_bigbluebuttonbn_logs()) {
            \core\task\manager::queue_adhoc_task(new static());
        }
    }

    /**
     * Process all bigbluebuttonbn logs looking for entries which should be converted to meetings.
     *
     * @return bool Whether any more logs are waiting to be processed
     */
    protected function process_bigbluebuttonbn_logs(): bool {
        global $DB;

        $classname = static::class;

        mtrace("Executing {$classname}...");

        // Fetch the logs queued for upgrade.
        mtrace("Fetching logs for conversion");
        $logs = $this->get_logs_to_convert();

        if (empty($logs)) {
            mtrace("No logs were found for conversion.");
            // No more logs. Stop queueing.
            return false;
        }

        $meetingidmap = [];
        foreach ($logs as $log) {
            $meetingidmap[$log->meetingid] = $log->id;
        }

        // Retrieve recordings from the meetingids with paginated requests.
        $recordings = recording_proxy::fetch_recordings(array_keys($meetingidmap), 'meetingID');

        // Create an instance of bigbluebuttonbn_recording per valid recording.
        mtrace("Creating new recording records...");
        $recordingcount = 0;
        foreach ($recordings as $recordingid => $recording) {
            $meetingid = $recording['meetingID'];
            $logid = $meetingidmap[$meetingid];
            $log = $logs[$logid];

            $importeddata = $this->get_imported_data($recording);
            try {
                $instance = instance::get_from_meetingid($recording['meetingID']);
            } catch (moodle_exception $e) {
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
                    'imported' => empty($importeddata) ? 0 : 1,
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
                    'imported' => empty($importeddata) ? 0 : 1,
                ];

                if (array_key_exists('groupid', $meetingdata)) {
                    $newrecording['groupid'] = $meetingdata['groupid'];
                }
            }

            if ($DB->record_exists('bigbluebuttonbn_recordings', $newrecording)) {
                mtrace("A recording already exists for {$recording['recordingID']}. Skipping.");
                // A recording matching these characteristics alreay exists.
                continue;
            }

            $newrecording['headless'] = 0;
            $newrecording['importeddata'] = $importeddata;
            $newrecording['timecreated'] = $newrecording['timemodified'] = $log->timecreated;

            $newrecording = $DB->insert_record('bigbluebuttonbn_recordings', $newrecording);
            $recordingcount++;
        }
        mtrace("Migrated {$recordingcount} recordings.");

        // Delete processed logs.
        mtrace("Deleting migrated log records...");
        [$inidsql, $params] = $DB->get_in_or_equal(array_keys($meetingidmap));

        $DB->delete_records_select('bigbluebuttonbn_logs', "meetingid {$inidsql}", $params);

        return true;
    }

    /**
     * Get the list of logs to convert.
     *
     * @return array
     */
    abstract protected function get_logs_to_convert(): array;

    /**
     * Fetch the imported data for a recording.
     *
     * @param array $recording
     * @return string
     */
    protected function get_imported_data(array $recording): string {
        return '';
    }
}
