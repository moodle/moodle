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
 * Upgrade related functions.
 * @package     mod_board
 * @copyright   2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Deletes board note ratings database records where ratings are not
 * attached to any existing notes.
 *
 * @return void
 */
function mod_board_remove_unattached_ratings(): void {
    global $DB;
    // Getting the ratings.
    $sql = "SELECT r.id, n.id AS noteid
              FROM {board_note_ratings} r
         LEFT JOIN {board_notes} n ON r.noteid = n.id";
    $recordset = $DB->get_recordset_sql($sql);
    // Iterating.
    foreach ($recordset as $record) {
        if (!isset($record->noteid)) {
            // If the noteid wasn't set, delete the record.
            $DB->delete_records('board_note_ratings', ['id' => $record->id]);
        }
    }
    $recordset->close();
}

/**
 * Move MEDIATYPE_IMAGE info from url to filename field
 * and fix files restored with wrong itemid.
 *
 * @return void
 */
function mod_board_migrate_image_url_to_filename(): void {
    global $DB;

    $fs = get_file_storage();
    $sql = "SELECT bn.id, bn.url, ctx.id AS cid
              FROM {board_notes} bn
              JOIN {board_columns} bc ON bc.id = bn.columnid
              JOIN {board} b ON b.id = bc.boardid
              JOIN {course_modules} cm ON cm.instance = b.id
              JOIN {modules} md ON md.id = cm.module AND md.name = 'board'
              JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = 70
             WHERE bn.type = 2 AND bn.url IS NOT NULL";
    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $note) {
        if (preg_match('|/(\d+)/([^/]+\.[a-zA-Z0-9]+$)|', $note->url, $matches)) {
            $oldnoteid = $matches[1];
            $contextid = $note->cid;
            unset($note->cid);
            $note->filename = $matches[2];
            $note->url = null;
            $DB->update_record('board_notes', $note);
            if ($oldnoteid != $note->id) {
                // This must be incorrectly restored board image attachment.
                $file = $fs->get_file($contextid, 'mod_board', 'images', $oldnoteid, '/', $note->filename);
                if ($file) {
                    $fs->create_file_from_storedfile(['itemid' => $note->id], $file);
                    // NOTE: do not delete files in case there is an accidental match after restore,
                    // full cleanup of notes and files should be done later.
                }
            }
        }
    }
    $rs->close();
}
