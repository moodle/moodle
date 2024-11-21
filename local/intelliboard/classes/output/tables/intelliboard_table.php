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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

namespace local_intelliboard\output\tables;

use html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

abstract class intelliboard_table extends \table_sql {
    protected $search = '';
    protected $isdownload = false;
    protected $sqlreqparams = [];
    protected $preferences = [];
    protected $icolumns = [];

    public function __construct($uniqueid, $params = [])
    {
        global $SESSION;

        parent::__construct($uniqueid);

        $this->search = isset($params["search"]) ? $params["search"] : '';
        $this->isdownload = isset($params["download"]) ? (bool) $params["download"] : false;
        $this->icolumns = $this->get_intelliboard_columns();

        if (isset($SESSION->flextable[$uniqueid])) {
            $this->preferences = $SESSION->flextable[$uniqueid];
        }

        $this->isetup_columns();
    }

    abstract protected function get_intelliboard_columns();

    public function icol_text($row, $columnname) {
        return isset($row->$columnname) ? (string) $row->$columnname : "";
    }

    public function icol_date($row, $columnname) {
        return $row->$columnname ? userdate($row->$columnname) : '-';
    }

    public function icol_time($row, $columnname) {
        if($row->$columnname < 0){
            return "00:00:00";
        }

        $delimeter = ':';

        return sprintf("%02d%s%02d%s%02d", floor($row->$columnname/3600), $delimeter, ($row->$columnname/60)%60, $delimeter, $row->$columnname%60);
    }

    public function icol_int($row, $columnname) {
        return (int) $row->$columnname;
    }

    public function icol_float($row, $columnname) {
        return $row->$columnname ? format_float($row->$columnname) : 0;
    }

    public function icol_percentgrade($row, $columnname) {
        if ($row->$columnname) {
            return format_float($row->$columnname) . '%';
        }

        return '-';
    }

    private function isetup_columns() {
        $headers = [];
        $columns = [];

        foreach ($this->icolumns as $column) {
            $columns[] = $column["name"];
            $headers[] = $column["title"];
        }

        list($columns, $headers) = $this->handle_column_visibility($columns, $headers);

        $this->define_columns($columns);
        $this->define_headers($headers);
    }

    public function format_row($row) {
        if (is_array($row)) {
            $row = (object)$row;
        }
        $formattedrow = [];

        foreach ($this->icolumns as $column) {
            $colmethodname = "col_{$column["name"]}";
            $colaltmethodname = "icol_{$column["type"]}";
            $columnname = $column["name"];

            if(method_exists($this, $colmethodname)) {
                $formattedcolumn = $this->$colmethodname($row);
            } elseif (method_exists($this, $colaltmethodname)) {
                $formattedcolumn = $this->$colaltmethodname($row, $columnname);
            } else {
                $formattedcolumn = $this->other_cols($columnname, $row);

                if ($formattedcolumn === NULL) {
                    $formattedcolumn = $row->$columnname;
                }
            }

            $formattedrow[$columnname] = $formattedcolumn;
        }

        return $formattedrow;
    }

    public function handle_column_visibility($columns, $titles) {
        if (!$this->isdownload) {
            return [$columns, $titles];
        }

        $collapsecols = isset($this->preferences["collapse"]) ? $this->preferences["collapse"] : [];

        $newcolumns = [];
        $newtitles = [];

        foreach ($columns AS $index => $column) {
            if (isset($collapsecols[$column]) && $collapsecols[$column]) {
                continue;
            }

            $newcolumns[] = $column;
            $newtitles[] = $titles[$index];
        }

        return [$newcolumns, $newtitles];
    }

    public function start_html() {
        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', array('class' => 'no-overflow'));
        echo html_writer::start_tag('table', $this->attributes);

    }

    /**
     * Get the html for the download buttons
     *
     * Usually only use internally
     */
    public function download_buttons() {
        global $OUTPUT;

        if ($this->is_downloadable() && !$this->is_downloading()) {
            $html = html_writer::start_div("export");
            $html .= $OUTPUT->download_dataformat_selector(
                get_string('downloadas', 'table'),
                $this->baseurl->out_omit_querystring(), 'download', $this->baseurl->params()
            );
            $html .= html_writer::end_div();
            return $html;
        } else {
            return '';
        }
    }

    /**
     * @param int $limit
     * @return false|string
     */
    public function export_for_template($limit = 25) {
        ob_start();
        $this->out($limit, true);
        $tablehtml = ob_get_contents();
        ob_end_clean();

        return $tablehtml;
    }

    protected function get_modules_sql($name = 'activity') {
        global $DB;

        $sql = "";
        $modules = $DB->get_records_sql("SELECT m.id, m.name FROM {modules} m WHERE m.visible = 1");

        foreach($modules as $module){
            $sql .= " WHEN m.name='{$module->name}' THEN (SELECT name FROM {{$module->name}} WHERE id = cm.instance)";
        }
        return ($sql) ? "CASE {$sql} ELSE 'NONE' END AS {$name}" : "'' AS {$name}";
    }
}

