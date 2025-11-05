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

use mod_board\board;

/**
 * Restore steps.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_board_activity_structure_step extends restore_activity_structure_step {
    /**
     * Structure definition.
     * @return mixed
     */
    protected function define_structure() {

        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('board', '/activity/board');
        $paths[] = new restore_path_element('board_column', '/activity/board/columns/column');
        if ($userinfo) {
            $paths[] = new restore_path_element('board_note', '/activity/board/columns/column/notes/note');
            $paths[] = new restore_path_element('board_note_rating', '/activity/board/columns/column/notes/note/ratings/rating');
            $paths[] = new restore_path_element('board_comments', '/activity/board/columns/column/notes/note/comments/comment');
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the restore.
     * @param array $data
     */
    protected function process_board($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->historyid = 0;

        // Do not apply offset to board modification date.

        if ($data->postby) {
            $data->postby = $this->apply_date_offset($data->postby);
        }

        $newitemid = $DB->insert_record('board', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restore columns.
     * @param array $data
     */
    protected function process_board_column($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->boardid = $this->get_new_parentid('board');

        $newitemid = $DB->insert_record('board_columns', $data);
        $this->set_mapping('board_column', $oldid, $newitemid);
    }

    /**
     * Restore notes.
     * @param array $data
     */
    protected function process_board_note($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->columnid = $this->get_new_parentid('board_column');
        $column = $DB->get_record('board_columns', ['id' => $data->columnid]);
        if (!$column) {
            return;
        }
        $board = $DB->get_record('board', ['id' => $column->boardid]);
        if (!$board) {
            return;
        }

        $data->userid = $this->get_mappingid('user', $data->userid, 0);
        if (!empty($data->ownerid)) {
            $data->ownerid = $this->get_mappingid('user', $data->ownerid);
        }
        if (empty($data->ownerid)) {
            $data->ownerid = $data->userid;
        }

        if ($board->singleusermode != \mod_board\board::SINGLEUSER_DISABLED) {
            // Group is used only for user selection in private and public single user mode.
            $data->groupid = null;
        } else {
            if (!empty($data->groupid)) {
                $data->groupid = $this->get_mappingid('group', $data->groupid);
            }
            if (!$data->groupid) {
                $data->groupid = null;
            }
        }

        if ($data->type == board::MEDIATYPE_IMAGE) {
            if (!isset($data->filename) && $data->url) {
                // Migrate legacy full image URL to filename field.
                if (preg_match('|/([^/]+\.[a-zA-Z0-9]+$)|', $data->url, $matches)) {
                    $data->filename = $matches[1];
                    $data->url = null;
                }
            }
        }

        // Do not apply offset to note creation date.

        $newitemid = $DB->insert_record('board_notes', $data);
        $this->set_mapping('board_note', $oldid, $newitemid, true);
    }

    /**
     * Restore ratings.
     * @param array $data
     */
    protected function process_board_note_rating($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->noteid = $this->get_new_parentid('board_note');
        if (!$data->noteid) {
            return;
        }

        if (!empty($data->userid)) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }

        // Do not apply offset to rating creation date.

        $newitemid = $DB->insert_record('board_note_ratings', $data);
        $this->set_mapping('board_note_rating', $oldid, $newitemid, true);
    }

    /**
     * Restore comments.
     * @param array $data
     */
    protected function process_board_comments($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->noteid = $this->get_new_parentid('board_note');
        if (!$data->noteid) {
            return;
        }

        if (!empty($data->userid)) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }

        // Do not apply offset to comment creation date.

        $newitemid = $DB->insert_record('board_comments', $data);
        $this->set_mapping('board_comments', $oldid, $newitemid, true);
    }

    /**
     * After execution steps.
     */
    protected function after_execute() {
        $this->add_related_files('mod_board', 'background', null);
        $this->add_related_files('mod_board', 'intro', null);
        $this->add_related_files('mod_board', 'images', 'board_note');
        $this->add_related_files('mod_board', 'files', 'board_note');
    }
}
