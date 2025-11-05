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
use mod_board\board;
use stdClass;
use mod_board\local\note;

/**
 * Define comments table class.
 */
class comments_table extends table_sql {
    /** @var stdClass board record */
    private $board;
    /** @var array cached columns records */
    private $columnscache;
    /** @var \context_module */
    private $context;

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
        parent::__construct('mod_board_comments_table');

        $this->board = board::get_board($boardid, MUST_EXIST);
        $this->columnscache = $DB->get_records('board_columns', ['boardid' => $this->board->id]);
        $this->context = board::context_for_board($this->board);

        // Get the construct parameters and add them to the export url.
        $exportparams = [
            'id' => $cmid,
            'group' => $groupid,
            'tabletype' => 'comments',
            'ownerid' => $ownerid,
            'includedeleted' => $includedeleted,
        ];
        $exporturl = new moodle_url('/mod/board/export.php', $exportparams);
        $this->define_baseurl($exporturl);

        // Define the list of columns to show.
        $columns = ['heading', 'firstname', 'lastname', 'content', 'timecreated', 'deleted'];
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array_map(function ($column) {
            // Remove the p from the column name.
            if (substr($column, 0, 1) === 'p') {
                $column = substr($column, 1);
            }
            return get_string('export_' . $column, 'board');
        }, $columns);
        $this->define_headers($headers);

        // Define the SQL used to get the data.
        $this->sql = (object)[];
        $this->sql->fields = 'c.id AS cid, c.content AS ccontent, c.timecreated AS ctimecreated, c.deleted AS cdeleted,
        u.firstname, u.lastname, bn.*';
        $this->sql->from = '{board_comments} c
            JOIN {board_notes} bn ON bn.id = c.noteid
            JOIN {user} u ON u.id = c.userid
            JOiN {user} u2 ON u2.id = bn.userid
            JOIN {board_columns} bc ON bc.id = bn.columnid';
        $this->sql->where = 'bc.boardid = :boardid';
        $this->sql->params = ['boardid' => $boardid];
        if ($groupid > 0 && $this->board->singleusermode == board::SINGLEUSER_DISABLED) {
            $this->sql->where .= ' AND bn.groupid = :groupid';
            $this->sql->params['groupid'] = $groupid;
        }
        if ($ownerid > 0) {
            $this->sql->where .= ' AND bn.ownerid = :ownerid';
            $this->sql->params['ownerid'] = $ownerid;
        } else if ($groupid > 0 && $this->board->singleusermode != board::SINGLEUSER_DISABLED) {
            // phpcs:ignore moodle.Files.LineLength.TooLong
            $this->sql->where .= " AND EXISTS (SELECT 'x' FROM {groups_members} gm WHERE gm.userid = bn.ownerid AND gm.groupid = :groupid)";
            $this->sql->params['groupid'] = $groupid;
        }
        if (!$includedeleted) {
            $this->sql->where .= ' AND bn.deleted = 0 AND c.deleted = 0';
        }
    }

    /**
     * Displays deleted in readable format.
     *
     * @param stdClass $row The row.
     * @return string returns deleted.
     */
    public function col_deleted(stdClass $row): string {
        return ($row->cdeleted || $row->deleted) ? get_string('yes') : get_string('no');
    }

    /**
     * Displays comment content.
     *
     * @param stdClass $row The row.
     * @return string returns deleted.
     */
    public function col_content(stdClass $row): string {
        return note::format_plain_text($row->ccontent);
    }

    /**
     * Displays comment content.
     *
     * @param stdClass $row The row.
     * @return string returns deleted.
     */
    public function col_timecreated(stdClass $row): string {
        return userdate($row->ctimecreated, get_string('strftimedatetimeshort', 'langconfig'));
    }

    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     *
     * @param string $column The name of the column.
     * @param stdClass $row The row.
     * @return ?string return processed value. Return NULL if no change has
     *     been made.
     */
    public function other_cols($column, $row): ?string {
        if ($column === 'heading') {
            $notetext = note::get_export_info($row);
            if ($notetext === '') {
                return ' - ';
            } else {
                return $notetext;
            }
        }

        if ($column === 'firstname' || $column === 'lastname') {
            return s($row->$column);
        }

        return null;
    }

    /**
     * Preformat the whole note.
     *
     * @param stdClass|array $row
     * @return array
     */
    public function format_row($row) {
        $row = (object)(array)$row;

        $row = note::format_for_display(
            $row,
            $this->columnscache[$row->columnid],
            $this->board,
            $this->context
        );

        return parent::format_row($row);
    }

    /**
     * Displays the table.
     */
    public function display(): void {
        $this->out(10, true);
    }
}
