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

/**
 * Class containing the scheduled task for converting legacy recordings to 2.5.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Jesus Federico, Blindside Networks Inc <jesus at blindsidenetworks dot com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_imported_recordings extends upgrade_recording_base_task {

    /**
     * Get the list of logs to convert.
     *
     * @return array
     */
    protected function get_logs_to_convert(): array {
        global $DB;

        return $DB->get_records(
            'bigbluebuttonbn_logs',
            ['log' => 'Import'],
            'timecreated DESC',
            'id, meetingid, timecreated',
            0,
            self::$chunksize
        );
    }

    /**
     * Fetch the imported data for a recording.
     *
     * @param array $recording
     * @return string
     */
    protected function get_imported_data(array $recording): string {
        return json_encode($recording);
    }
}
