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
namespace report_infectedfiles\table;

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

    /** @var int current page. */
    protected $page;

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
    protected function get_sql_and_params($count = false) : array {
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
     * Column to display the authors fullname from userid.
     *
     * @param \stdClass $row the row from sql.
     * @return string the authors name.
     */
    protected function col_author($row) : string {
        // Get user fullname from ID.
        $user = \core_user::get_user($row->userid);
        $url = new \moodle_url('/user/profile.php', ['id' => $row->userid]);
        return \html_writer::link($url, fullname($user));
    }

    /**
     * Column to display the failure reason.
     *
     * @param \stdClass $row the row from sql.
     * @return string the formatted reason.
     */
    protected function col_reason($row) {
        return format_text($row->reason);
    }

    /**
     * Custom actions column
     *
     * @param \stdClass $row an incident record.
     * @return string content of action column.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function col_actions($row) : string {
        global $OUTPUT;
        $filename = $row->quarantinedfile;
        $fileid = $row->id;
        // If the file isn't found, we can do nothing in this column.
        // This shouldn't happen, unless the file is manually deleted from the server externally.
        if (!\core\antivirus\quarantine::quarantined_file_exists($filename)) {
            return '';
        }
        $links = '';
        $managefilepage = new \moodle_url('/report/infectedfiles/index.php');

        // Download.
        $downloadparams = ['file' => $fileid, 'action' => 'download', 'sesskey' => sesskey()];
        $downloadurl = new \moodle_url($managefilepage, $downloadparams);

        $downloadconfirm = new \confirm_action(get_string('confirmdownload', 'report_infectedfiles'));
        $links .= $OUTPUT->action_icon(
            $downloadurl,
            new \pix_icon('t/download', get_string('download')),
            $downloadconfirm
        );

        // Delete.
        $deleteparams = ['file' => $fileid, 'action' => 'delete', 'sesskey' => sesskey()];
        $deleteurl = new \moodle_url($managefilepage, $deleteparams);
        $deleteconfirm = new \confirm_action(get_string('confirmdelete', 'report_infectedfiles'));
        $links .= $OUTPUT->action_icon(
            $deleteurl,
            new \pix_icon('t/delete', get_string('delete')),
            $deleteconfirm
        );

        return $links;
    }

    /**
     * Custom time column.
     *
     * @param \stdClass $row an incident record.
     * @return string time created in user-friendly format.
     */
    protected function col_timecreated($row) : string {
        return userdate($row->timecreated);
    }

    /**
     * Display table with download all and delete all buttons
     *
     * @param int $pagesize number or records perpage
     * @param bool $useinitialsbar use the bar or not
     * @param string $downloadhelpbutton help button
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function display($pagesize, $useinitialsbar, $downloadhelpbutton='') {
        global $OUTPUT;
        // Output the table, and then display buttons.
        $this->out($pagesize, $useinitialsbar, $downloadhelpbutton);
        $managefilepage = new \moodle_url('/report/infectedfiles/index.php');

        // If there are no rows, dont bother rendering extra buttons.
        if (empty($this->rawdata)) {
            return;
        }

        // Delete All.
        $deleteallparams = ['action' => 'deleteall', 'sesskey' => sesskey()];
        $deleteallurl = new \moodle_url($managefilepage, $deleteallparams);
        $deletebutton = new \single_button($deleteallurl, get_string('deleteall'), 'post', \single_button::BUTTON_PRIMARY);
        $deletebutton->add_confirm_action(get_string('confirmdeleteall', 'report_infectedfiles'));
        echo $OUTPUT->render($deletebutton);

        echo "&nbsp";

        // Download All.
        $downloadallparams = ['action' => 'downloadall', 'sesskey' => sesskey()];
        $downloadallurl = new \moodle_url($managefilepage, $downloadallparams);
        $downloadbutton = new \single_button($downloadallurl, get_string('downloadall'), 'post', \single_button::BUTTON_PRIMARY);
        $downloadbutton->add_confirm_action(get_string('confirmdownloadall', 'report_infectedfiles'));
        echo $OUTPUT->render($downloadbutton);
    }

}
