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
 * Table sql definition for exporting comments.
 * @package     mod_board
 * @author      Bas Brands <bas@sonsbeekmedia.nl>
 * @copyright   2023 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_board\tables;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

use table_sql;
use moodle_url;
use mod_board\board as board;

/**
 * Define comments table class.
 */
class comments_table extends table_sql {

    /**
     * Constructor
     * @param int $cmid The course module id.
     * @param int $boardid The board id.
     * @param int $groupid The owner id.
     * @param int $ownerid The owner id.
     * @param bool $includedeleted Include deleted notes.
     */
    public function __construct($cmid, $boardid, $groupid, $ownerid, $includedeleted) {
        global $DB;
        parent::__construct('mod_board_notes_table');

        // Get the construct paramaters and add them to the export url.
        $exportparams = [
            'id' => $cmid,
            'group' => $groupid,
            'tabletype' => 'comments',
            'ownerid' => $ownerid,
            'includedeleted' => $includedeleted
        ];
        $exporturl = new moodle_url('/mod/board/export.php', $exportparams);
        $this->define_baseurl($exporturl);

        // Define the list of columns to show.
        $columns = array('heading', 'firstname', 'lastname', 'content', 'timecreated', 'deleted');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array_map(function($column) {
            // Remove the p from the column name.
            if (substr($column, 0, 1) == 'p') {
                $column = substr($column, 1);
            }
            return get_string('export_' . $column, 'board');
        }, $columns);
        $this->define_headers($headers);

        // Define the SQL used to get the data.
        $this->sql = (object)[];
        $this->sql->fields = 'c.id, bn.heading, u.firstname, u.lastname, c.content, c.timecreated,
            c.deleted, bn.info, bn.url, bn.content as pcontent, bn.type';
        $this->sql->from = '{board_comments} c
            JOIN {board_notes} bn ON bn.id = c.noteid
            JOIN {user} u ON u.id = c.userid
            JOiN {user} u2 ON u2.id = bn.userid
            JOIN {board_columns} bc ON bc.id = bn.columnid';
        $this->sql->where = 'bc.boardid = :boardid';
        $this->sql->params = ['boardid' => $boardid];
        if ($groupid > 0) {
            $this->sql->where .= ' AND bn.groupid = :groupid';
            $this->sql->params['groupid'] = $groupid;
        }
        if ($ownerid > 0) {
            $this->sql->where .= ' AND bn.ownerid = :ownerid';
            $this->sql->params['ownerid'] = $ownerid;
        }
        if (!$includedeleted) {
            $this->sql->where .= ' AND bn.deleted = 0 AND c.deleted = 0';
        }
    }

    /**
     * Displays deleted in readable format.
     *
     * @param object $value The value of the column.
     * @return string returns deleted.
     */
    public function col_deleted($value) {
        return ($value->deleted) ? get_string('yes') : get_string('no');
    }

    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     *
     * @param string $colname The name of the column.
     * @param object $value The value of the column.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    public function other_cols($colname, $value) {
        if ($colname == 'timecreated' || $colname == 'ptimecreated') {
            return userdate($value->$colname, get_string('strftimedatetimeshort', 'langconfig'));
        }
        if ($colname == 'heading' && empty($value->heading)) {
            $truecontent = $value->content;
            $value->content = $value->pcontent;
            $notetext = board::get_export_note($value);
            $value->content = $truecontent;
            if (!empty($notetext)) {
                return $notetext;
            } else {
                return ' - ';
            }
        }
    }

    /**
     * Displays the table.
     */
    public function display() {
        $this->out(10, true);
    }
}
