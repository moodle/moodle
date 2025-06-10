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
 * Table sql definition for exporting the board notes.
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

/**
 * Define notes table class.
 */
class notes_table extends table_sql {

    /** @var bool $showemail Determine if the email should be displayed in the CSV export. */
    private $showemail = false;

    /**
     * Constructor
     * @param int $cmid The course module id.
     * @param int $boardid The board id.
     * @param int $groupid The group id.
     * @param int $ownerid The owner id.
     * @param int $includedeleted Include deleted notes.
     */
    public function __construct($cmid, $boardid, $groupid, $ownerid, $includedeleted) {
        parent::__construct('mod_board_notes_table');

        // Set the showemail variable based on if the user has either capabilities.
        $cm = get_coursemodule_from_id('board', $cmid);
        $context = \context_course::instance($cm->course);
        if (has_capability('moodle/user:viewhiddendetails', $context) ||
            has_capability('moodle/course:viewhiddenuserfields', $context)) {
            $this->showemail = true;
        }

        // Get the construct paramaters and add them to the export url.
        $exportparams = [
            'id' => $cmid,
            'group' => $groupid,
            'tabletype' => 'notes',
            'ownerid' => $ownerid,
            'includedeleted' => $includedeleted
        ];
        $exporturl = new moodle_url('/mod/board/export.php', $exportparams);
        $this->define_baseurl($exporturl);

        // Define the list of columns to show.
        $columns = array('firstname', 'lastname', 'email', 'heading', 'content', 'info', 'url', 'timecreated', 'deleted');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array_map(function($column) {
            return get_string('export_' . $column, 'board');
        }, $columns);
        $this->define_headers($headers);

        // Define the SQL used to get the data.
        $this->sql = (object)[];
        $this->sql->fields = 'bn.id, u.firstname, u.lastname, u.email, bn.heading, bn.content, bn.info, bn.url, bn.timecreated,
            bn.deleted';
        $this->sql->from = '{board_columns} bc
        JOIN {board_notes} bn ON bn.columnid = bc.id JOIN {user} u ON u.id = bn.ownerid';
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
            $this->sql->where .= ' AND bn.deleted = 0';
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
        if ($colname == 'timecreated') {
            return userdate($value->timecreated, get_string('strftimedatetimeshort', 'langconfig'));
        }

        // If the user does not posess either of the capabilities, display the email as a '-' instead of the email.
        if ($colname == 'email') {
            if ($this->showemail) {
                return $value->email;
            } else {
                return '-';
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
