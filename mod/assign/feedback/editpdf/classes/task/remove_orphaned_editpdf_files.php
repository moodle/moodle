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
 * Cleans up orphaned feedback pdf files and table entries.
 *
 * @package    assignfeedback_editpdf
 * @copyright  2022 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdf\task;

use core\task\adhoc_task;

/**
 * Cleans up orphaned feedback pdf files and table entries.
 *
 * @package    assignfeedback_editpdf
 * @copyright  2022 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_orphaned_editpdf_files extends adhoc_task {

    /**
     * Run the task.
     */
    public function execute() {
        $this->remove_files_and_entries();
        $this->remove_rotated_table_entries();
    }

    /**
     * Removes edit pdf feedback files and table entries that have been orphaned.
     */
    private function remove_files_and_entries(): void {
        global $DB;

        // Patiently remove all orphaned temporary pdf files.
        $sql = "SELECT DISTINCT f.contextid, f.component, f.filearea, f.itemid
                  FROM {files} f
             LEFT JOIN {assign_grades} g ON g.id = f.itemid
                 WHERE f.component = :assigneditpdf
               AND NOT (filearea = :stamps AND f.itemid = 0)
                   AND g.id IS NULL";
        $params = ['assigneditpdf' => 'assignfeedback_editpdf', 'stamps' => 'stamps'];

        $results = $DB->get_recordset_sql($sql, $params);
        foreach ($results as $record) {
            $fs = get_file_storage();
            $fs->delete_area_files($record->contextid, $record->component, $record->filearea, $record->itemid);
        }
        $results->close();
    }

    /**
     * Removes orphaned entries in the feedback edit pdf rotation table.
     */
    private function remove_rotated_table_entries(): void {
        global $DB;
        $rotatesql = "SELECT er.id AS erid
                        FROM {assignfeedback_editpdf_rot} er
                   LEFT JOIN {assign_grades} g ON g.id = er.gradeid
                       WHERE g.id IS NULL";
        $DB->delete_records_subquery('assignfeedback_editpdf_rot', 'id', 'erid', $rotatesql);
    }
}
