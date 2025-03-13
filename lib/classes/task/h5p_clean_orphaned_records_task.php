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

namespace core\task;

/**
 * A schedule task to clean orphaned h5p records (for example for deleted activity).
 *
 * @package    core_h5p
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class h5p_clean_orphaned_records_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskh5pcleanup', 'admin');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $sql = "SELECT h5p.id
                  FROM {h5p} h5p
             LEFT JOIN {files} f
                    ON f.pathnamehash = h5p.pathnamehash
                 WHERE f.pathnamehash IS NULL";

        $orphanedrecords = $DB->get_recordset_sql($sql);

        foreach ($orphanedrecords as $orphanedrecord) {

            $sql = "SELECT f.id, f.pathnamehash
                      FROM {files} f
                     WHERE f.itemid = :itemid
                       AND f.filearea = :filearea
                       AND f.component = :component";
            $params = ['itemid' => $orphanedrecord->id, 'filearea' => 'content', 'component' => 'core_h5p'];
            $filerecords = $DB->get_recordset_sql($sql, $params);

            foreach ($filerecords as $filerecord) {
                $fs = get_file_storage();
                $file = $fs->get_file_by_hash($filerecord->pathnamehash);
                if ($file) {
                    $file->delete();
                }
            }
            $filerecords->close();

            $DB->delete_records('h5p', ['id' => $orphanedrecord->id]);
            $DB->delete_records('h5p_contents_libraries', ['h5pid' => $orphanedrecord->id]);
        }
        $orphanedrecords->close();
    }
}
