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

namespace core_ai\task;

use core\task\adhoc_task;
use core\task\manager;
use core\local\cli\shutdown;
use core_ai\manager as ai_manager;

/**
 * Backfill the courseid column on ai_action_register for rows logged before it existed.
 *
 * @package    core_ai
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backfill_action_courseid extends adhoc_task {
    /** @var int Number of rows to process per batch. */
    private const BATCH_SIZE = 500;

    #[\Override]
    public function execute() {
        global $DB;

        $rowsprocessed = 0;

        do {
            $records = $DB->get_records_select(
                'ai_action_register',
                'courseid = 0',
                null,
                'id ASC',
                'id, contextid',
                0,
                self::BATCH_SIZE,
            );

            foreach ($records as $record) {
                $courseid = ai_manager::resolve_courseid((int) $record->contextid);
                $DB->set_field('ai_action_register', 'courseid', $courseid, ['id' => $record->id]);
                $rowsprocessed++;
            }

            if (shutdown::should_gracefully_exit()) {
                manager::queue_adhoc_task(new self());
                mtrace("Graceful exit requested, rescheduled backfill_action_courseid after {$rowsprocessed} rows.");
                return;
            }
        } while (count($records) === self::BATCH_SIZE);

        mtrace("Backfilled courseid for {$rowsprocessed} ai_action_register rows.");
    }
}
