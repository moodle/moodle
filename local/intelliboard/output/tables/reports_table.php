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
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class reports_table extends table_sql {
    function __construct($uniqueid) {
        global $PAGE, $DB;

        parent::__construct($uniqueid);

        $this->define_headers(array(
            get_string('sqlreportname', 'local_intelliboard'),
            get_string('status', 'local_intelliboard'),
            get_string('sqlreportdate', 'local_intelliboard'),
            get_string('actions', 'local_intelliboard')
        ));
        $this->define_columns(array('name', 'status', 'timecreated','actions'));

        $fields = "*, '' AS actions";
        $from = "{local_intelliboard_reports}";

        $this->set_sql($fields, $from, 'id > 0', []);
        $this->define_baseurl($PAGE->url);
    }

    function col_timecreated($values) {
       return date('m/d/Y',$values->timecreated);
    }

    function col_status($values) {
       return [
            get_string('sqlreportinactive', 'local_intelliboard'),
            get_string('sqlreportactive', 'local_intelliboard'),
        ][$values->status];
    }

    function col_actions($values) {
        global $CFG, $OUTPUT;

        $buttons = array();

        $urlparams = array('id' => $values->id);


        $buttons[] = html_writer::link(new moodle_url($CFG->wwwroot.'/local/intelliboard/sqlreport.php', $urlparams),
            get_string('edit'), array('title' => get_string('edit')));

        $buttons[] = html_writer::link(new moodle_url($CFG->wwwroot.'/local/intelliboard/sqlreport.php', $urlparams+array('delete'=>1)),
            get_string('delete'), array('title' => get_string('delete')));


        return implode(' | ', $buttons);
    }

}
