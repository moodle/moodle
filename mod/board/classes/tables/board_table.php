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
 * Table sql definition for exporting the full board.
 * @package     mod_board
 * @author      Bas Brands <bas@sonsbeekmedia.nl>
 * @copyright   2023 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_board\tables;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

use flexible_table;
use moodle_url;
use html_writer;
use mod_board\board as board;

/**
 * Define board table class.
 */
class board_table extends flexible_table {

    /** @var int The board id. */
    protected $boardid;

    /** @var int The group id. */
    protected $groupid;

    /** @var int The owner id. */
    protected $ownerid;

    /** @var bool Include deleted notes. */
    protected $includedeleted;

    /** @var bool Is board rating enabled. */
    protected $hasrating;

    /** @var array Holds additional prefernces of the board. */
    protected $prefs;

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
        parent::__construct('mod_board_table');

        $this->boardid = $boardid;
        $this->groupid = $groupid;
        $this->includedeleted = $includedeleted;
        $this->ownerid = $ownerid;
        $this->hasrating = board::board_rating_enabled($boardid);

        // Get the construct paramaters and add them to the export url.
        $exportparams = [
            'id' => $cmid,
            'group' => $groupid,
            'tabletype' => 'board',
            'ownerid' => $ownerid,
            'includedeleted' => $includedeleted
        ];
        $exporturl = new moodle_url('/mod/board/export.php', $exportparams);
        $this->define_baseurl($exporturl);

        // Get the columns from the database.
        $columns = $DB->get_records('board_columns', ['boardid' => $boardid], 'sortorder', 'id, name, sortorder');

        $columnids = array_map(function($column) {
            return $column->name . $column->id;
        }, $columns);

        $columnnames = array_map(function($column) {
            return $column->name;
        }, $columns);

        // In the $columnids and $columnnames array add a rating array value after each item in the array.
        if ($this->hasrating) {
            $columnids = array_map(function($column) {
                return [$column, $column . 'rating'];
            }, $columnids);
            $columnids = array_reduce($columnids, 'array_merge', []);
            $columnnames = array_map(function($column) {
                return [$column, get_string('sortbyrating', 'mod_board')];
            }, $columnnames);
            $columnnames = array_reduce($columnnames, 'array_merge', []);
        }

        $this->define_columns(array_values($columnids));
        $this->define_headers(array_values($columnnames));
        // Get the colours for each column.
        $boardcolours = board::get_column_colours();
        foreach ($columns as $column) {
            $color = $boardcolours[$column->id % count($boardcolours)];
            $this->column_style($column->name . $column->id, 'border-top', '3px solid #' . $color);
            if ($this->hasrating) {
                $this->column_style($column->name . $column->id . 'rating', 'border-top', '3px solid #' . $color);
            }
        }

        $this->setup();
    }

    /**
     * Displays the table.
     */
    public function display() {
        global $DB;

        // Get the columns from the database.
        $columns = $DB->get_records('board_columns', ['boardid' => $this->boardid], 'sortorder', 'id, name, sortorder');
        // Get the notes for each column.
        foreach ($columns as $column) {
            $params = ['columnid' => $column->id];
            if (!$this->includedeleted) {
                $params['deleted'] = 0;
            }
            if ($this->ownerid > 0) {
                $params['ownerid'] = $this->ownerid;
            }
            if ($this->groupid > 0) {
                $params['groupid'] = $this->groupid;
            }
            $column->notes = $DB->get_records('board_notes', $params,
                'sortorder', 'id, heading, content, info, url, type');
        }

        // Get the column with the most notes.
        $maxnotes = max(array_map(function($column) {
            return count($column->notes);
        }, $columns));

        // Add the notes to the columnnames.
        for ($i = 0; $i < $maxnotes; $i++) {
            $row = [];
            foreach ($columns as $column) {
                // Get the current note for this column.
                $note = array_shift($column->notes);
                if ($note) {
                    $notetext = board::get_export_note($note);
                    if (!empty($notetext)) {
                        $row[] = $notetext;
                    } else {
                        $row[] = ' video ';
                    }
                } else {
                    $row[] = ' - ';
                }
                if ($this->hasrating) {
                    if ($note) {
                        $row[] = board::get_note_rating($note->id);
                    } else {
                        $row[] = '';
                    }
                }
            }
            $this->add_data($row);
        }
        $this->finish_output();
    }

    /**
     * Generate html code for the passed row.
     *
     * @param array $row Row data.
     * @param string $classname classes to add.
     *
     * @return string $html html code for the row passed.
     */
    public function get_row_html($row, $classname = '') {
        static $suppresslastrow = null;
        $rowclasses = array();

        if ($classname) {
            $rowclasses[] = $classname;
        }

        $rowid = $this->uniqueid . '_r' . $this->currentrow;
        $html = '';

        $html .= html_writer::start_tag('tr', array('class' => implode(' ', $rowclasses), 'id' => $rowid));

        // If we have a separator, print it.
        if ($row === null) {
            $colcount = count($this->columns);
            $html .= html_writer::tag('td', html_writer::tag('div', '',
                    array('class' => 'tabledivider')), array('colspan' => $colcount));

        } else {
            $colbyindex = array_flip($this->columns);
            foreach ($row as $index => $data) {
                $column = $colbyindex[$index];

                $attributes = [
                    'class' => "cell c{$index}" . $this->column_class[$column],
                    'id' => "{$rowid}_c{$index}",
                    'style' => $this->make_styles_string($this->column_style[$column]),
                ];

                $celltype = 'td';
                if ($this->headercolumn && $column == $this->headercolumn) {
                    $celltype = 'th';
                    $attributes['scope'] = 'row';
                } else {
                    $attributes['style'] = '';
                }

                if (empty($this->prefs['collapse'][$column])) {
                    if ($this->column_suppress[$column] && $suppresslastrow !== null && $suppresslastrow[$index] === $data) {
                        $content = '&nbsp;';
                    } else {
                        $content = $data;
                    }
                } else {
                    $content = '&nbsp;';
                }

                $html .= html_writer::tag($celltype, $content, $attributes);
            }
        }

        $html .= html_writer::end_tag('tr');

        $suppressenabled = array_sum($this->column_suppress);
        if ($suppressenabled) {
            $suppresslastrow = $row;
        }
        $this->currentrow++;
        return $html;
    }
}
