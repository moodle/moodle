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
use mod_bigbluebuttonbn\recording;

/**
 * Class containing the scheduled task for converting recordings for the BigBlueButton version 2.5 in Moodle 4.0.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Jesus Federico, Blindside Networks Inc <jesus at blindsidenetworks dot com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_recordings extends adhoc_task {

    /** @var int Chunk size to use when resetting recordings */
    protected static $chunksize = 100;

    /**
     * Run the migration task.
     */
    public function execute() {
        if ($this->process_reset_recordings()) {
            \core\task\manager::queue_adhoc_task(new static());
        }
    }

    /**
     * Process all bigbluebuttonbn_recordings looking for entries which should be reset to be fetched again.
     *
     * @return bool Whether any more recodgins are waiting to be processed
     */
    protected function process_reset_recordings(): bool {
        global $DB;

        $classname = static::class;

        mtrace("Executing {$classname}...");

        // Read a block of recordings to be updated.
        $recs = $this->get_recordngs_to_reset();

        if (empty($recs)) {
            mtrace("No recordings were found for reset...");
            // No more logs. Stop queueing.
            return false;
        }

        // Reset status of {chunksize} recordings.
        mtrace("Reset status of " . self::$chunksize . " recordings...");
        $sql = "UPDATE {bigbluebuttonbn_recordings}
                SET status = :status_reset
                WHERE id = " . implode(' OR id = ', array_keys($recs));
        $DB->execute($sql,
            ['status_reset' => recording::RECORDING_STATUS_RESET]
        );

        return true;
    }

    /**
     * Get the list of recordings to be reset.
     *
     * @return array
     */
    protected function get_recordngs_to_reset(): array {
        global $DB;

        return $DB->get_records_sql(
            'SELECT * FROM {bigbluebuttonbn_recordings}
             WHERE status = :status_processed OR status = :status_notified
             ORDER BY timecreated DESC', [
                'status_processed' => recording::RECORDING_STATUS_PROCESSED,
                'status_notified' => recording::RECORDING_STATUS_NOTIFIED
            ],
            0,
            self::$chunksize
        );
    }

}
