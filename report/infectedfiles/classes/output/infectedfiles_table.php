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
 * Infected file report
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_infectedfiles\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Infected file report
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class infectedfiles_table extends \table_sql implements \renderable {
    /**
     * Table constructor
     *
     * @param int $uniqueid table id
     * @param \moodle_url $url page url
     * @param int $page current page
     * @param int $perpage number or record per page
     * @throws \coding_exception
     */
    public function __construct($uniqueid, \moodle_url $url, $page = 0, $perpage = 30) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'report_infectedfiles');

        // Set protected properties.
        $this->pagesize = $perpage;
        $this->page = $page;

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs($url);
    }

    /**
     * Table columns and corresponding headers
     *
     * @throws \coding_exception
     */
    protected function define_table_columns() {
        $cols = array(
            'filename' => get_string('filename', 'report_infectedfiles'),
            'quarantinedfile' => get_string('quarantinedfile', 'report_infectedfiles'),
            'author' => get_string('author', 'report_infectedfiles'),
            'reason' => get_string('reason', 'report_infectedfiles'),
            'timecreated' => get_string('timecreated', 'report_infectedfiles'),
            'actions' => get_string('actions'),
        );

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Define table configuration
     *
     * @param \moodle_url $url
     */
    protected function define_table_configs(\moodle_url $url) {
        // Set table url.
        $this->define_baseurl($url);

        // Set table configs.
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return array containing sql to use and an array of params.
     */
    protected function get_sql_and_params($count = false) {
        if ($count) {
            $select = "COUNT(1)";
        } else {
            $select = "*";
        }

        $sql = "SELECT $select
                  FROM {infected_files}";

        $params = array();

        if (!$count) {
            $sql .= " ORDER BY timecreated DESC";
        }

        return array($sql, $params);
    }

    /**
     * Get data.
     *
     * @param int $pagesize number of records to fetch
     * @param bool $useinitialsbar initial bar
     * @throws \dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        list($countsql, $countparams) = $this->get_sql_and_params(true);
        list($sql, $params) = $this->get_sql_and_params();
        $total = $DB->count_records_sql($countsql, $countparams);
        $this->pagesize($pagesize, $total);
        $this->rawdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Custom actions column
     *
     * @param \stdClass $row an incidence record
     * @return string content of action column
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function col_actions($row) {
        global $OUTPUT;
        $filename = $row->quarantinedfile;
        $zipfile = \core\antivirus\quarantine::get_quarantine_folder() . $filename;
        if (!file_exists($zipfile)) {
            return '';
        }
        $links = '';
        $managefilepage = new \moodle_url('/report/infectedfiles/manage_infected_files.php');

        // Download.
        $downloadparams = ['filename' => $filename, 'action' => 'download', 'sesskey' => sesskey()];
        $downloadurl = new \moodle_url($managefilepage, $downloadparams);
        $icon = $OUTPUT->pix_icon('t/download', get_string('download'));
        $downloadlink = \html_writer::link($downloadurl, $icon);
        $links .= ' ' . $downloadlink;

        // Delete.
        $deleteparams = ['filename' => $filename, 'action' => 'confirmdelete', 'sesskey' => sesskey()];
        $deleteurl = new \moodle_url($managefilepage, $deleteparams);
        $icon = $OUTPUT->pix_icon('t/delete', get_string('delete'));
        $deletelink = \html_writer::link($deleteurl, $icon);
        $links .= ' ' . $deletelink;

        return $links;
    }

    /**
     * Custom time column
     *
     * @param \stdClass $row an incidence record
     * @return string time created in user-friendly format
     */
    protected function col_timecreated($row) {
        return userdate($row->timecreated);
    }

    /**
     * Display table with download all and delete all buttons
     *
     * @param int $pagesize number or records perpage
     * @param bool $useinitialsbar use the bar or not
     * @param string $downloadhelpbutton help button
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function display($pagesize, $useinitialsbar, $downloadhelpbutton='') {
        $this->out($pagesize, $useinitialsbar, $downloadhelpbutton);

        $managefilepage = new \moodle_url('/report/infectedfiles/manage_infected_files.php');
        // Delete All.
        $button = \html_writer::tag('button', get_string('deleteall'), ['class' => 'btn btn-primary']);
        $deleteallparams = ['action' => 'confirmdeleteall', 'sesskey' => sesskey()];
        $deleteallurl = new \moodle_url($managefilepage, $deleteallparams);
        echo \html_writer::link($deleteallurl, $button);

        echo "&nbsp";

        // Download All.
        $button = \html_writer::tag('button', get_string('downloadall'), ['class' => 'btn btn-primary']);
        $downloadallparams = ['action' => 'downloadall', 'sesskey' => sesskey()];
        $downloadallurl = new \moodle_url($managefilepage, $downloadallparams);
        echo \html_writer::link($downloadallurl, $button);
    }

}
