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

namespace mod_subsection\task;

use core\task\adhoc_task;
use mod_subsection\manager;

/**
 * An ad-hoc task to remove existing descriptions from subsection instances.
 *
 * NOTE:
 *  - This task processes subsections in batches of 100 to reduce server overload.
 *  - It will be removed in Moodle 7.0. By then, the remaining descriptions will be removed.
 *
 * @package    mod_subsection
 * @copyright  2026 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_subsection_descriptions_task extends adhoc_task {
    /**
     * Execute the task.
     */
    public function execute(): void {
        global $DB;

        // Process subsections in batches to reduce server overload.
        $removedcount = 0;
        $subsections = $DB->get_recordset_select(
            table: 'course_sections',
            select: 'component = :component AND summary != :empty',
            params: ['component' => 'mod_subsection', 'empty' => ''],
            limitnum: 100,
        );
        $transaction = $DB->start_delegated_transaction();
        foreach ($subsections as $subsection) {
            manager::create_from_id($subsection->course, $subsection->itemid)->clear_description();
            $removedcount++;
        }
        $transaction->allow_commit();
        if ($removedcount > 0) {
            mtrace('Subsection descriptions removal task completed. Total removed subsection descriptions: ' . $removedcount);
        } else {
            mtrace('No subsection descriptions found to remove.');
        }
        $subsections->close();

        $pendingcount = $DB->count_records_select(
            table: 'course_sections',
            select: 'component = :component AND summary != :empty',
            params: ['component' => 'mod_subsection', 'empty' => ''],
        );
        if ($pendingcount > 0) {
            $task = new self();
            \core\task\manager::queue_adhoc_task($task);
            mtrace('Subsection descriptions removal task pending subsections: ' . $pendingcount . '. Scheduled new ad-hoc task.');
        }
    }
}
