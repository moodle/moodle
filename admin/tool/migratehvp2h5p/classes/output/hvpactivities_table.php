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
 * View HVP activities to migrate.
 *
 * @package     tool_migratehvp2h5p
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_migratehvp2h5p\output;

use tool_migratehvp2h5p\api;
use core\output\checkbox_toggleall;
use html_writer;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/tablelib.php');

/**
 * Class hvpactivities_table
 *
 * @package     tool_migratehvp2h5p
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvpactivities_table extends table_sql {

    /**
     * Constructor.
     *
     */
    public function __construct() {
        global $PAGE;
        parent::__construct('tool_migratehvp2h5p_hvpactivities_table');

        $this->define_baseurl('admin/tool/migratehvp2h5p/index.php');

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();
    }


    /**
     * Setup the headers for the table.
     */
    protected function define_table_columns() {
        global $OUTPUT;

        $selectallcheckbox = new checkbox_toggleall('activities', true, [
            'id' => 'select-all', 'name' => 'select-all',
            'value' => 1, 'label' => get_string('selectall'),
        ], false);
        $checkbox = $OUTPUT->render($selectallcheckbox);

        $columnheaders = [
            'select' => $checkbox,
            'id' => get_string('id', 'tool_migratehvp2h5p'),
            'course' => get_string('course'),
            'name' => get_string('name'),
            'contenttype' => get_string('contenttype', 'tool_migratehvp2h5p'),
            'graded' => get_string('graded', 'tool_migratehvp2h5p'),
            'attempted' => get_string('attempted', 'tool_migratehvp2h5p'),
            'savedstate' => get_string('savedstate', 'tool_migratehvp2h5p'),
        ];

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));
        $this->column_class('id', 'd-none d-sm-table-cell');
        $this->column_class('contenttype', 'd-none d-sm-table-cell');
        $this->column_class('graded', 'd-none d-md-table-cell');
        $this->column_class('attempted', 'd-none d-md-table-cell');
        $this->column_class('savedstate', 'd-none d-md-table-cell');
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(false);
        $this->sortable(true, 'name', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('select');
        $this->no_sorting('graded');
        $this->no_sorting('attempted');
        $this->no_sorting('savedstate');
    }

    /**
     * The select column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function col_select(stdClass $data):string {
        global $OUTPUT;

        $stringdata = [
            'activityname' => $data->name,
        ];

        $selectallcheckbox = new checkbox_toggleall('activities', false, [
            'id' => "select-activity-{$data->id}",
            'name' => "activityids[{$data->id}]",
            'value' => $data->id,
            'title' => get_string('selecthvpactivity', 'tool_migratehvp2h5p', $data->name),
        ], false);
        return $OUTPUT->render($selectallcheckbox);

    }

    /**
     * The graded column, to display the total of users who have a grade for each HVP activity.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function col_graded(stdClass $data): string {
        global $DB;

        $sql = "SELECT COUNT(*)
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gi.id = gg.itemid AND gi.iteminstance = :hvpid
                   AND gi.courseid = :courseid AND gi.itemtype = 'mod' AND gi.itemmodule = 'hvp'";
        $params = ['hvpid' => $data->id, 'courseid' => $data->courseid];
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * The attempted column, to display the users with xAPI logs.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function col_attempted(stdClass $data): string {
        global $DB;

        $sql = "SELECT COUNT(*)
                  FROM {hvp} h
                  JOIN {hvp_xapi_results} hx ON hx.content_id = h.id AND hx.parent_id IS NULL
                 WHERE h.id = :hvpid";
        $params['hvpid'] = $data->id;
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * The name column, to add a link to the HVP activity.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function col_name(stdClass $data): string {
        $url = new moodle_url('/mod/hvp/view.php', ['id' => $data->instanceid]);
        return html_writer::link($url, format_string($data->name), ['target' => '_blank']);
    }

    /**
     * Builds the SQL query.
     *
     * @param  bool $count When true, return the count SQL.
     * @return array containing sql to use and an array of params.
     */
    protected function get_sql_and_params(bool $count = false): array {
        // Add order by if needed.
        $sort = ($count) ? null : $this->get_sql_sort();

        list($sql, $params) = api::get_sql_hvp_to_migrate($count, $sort);

        return [$sql, $params];
    }

    /**
     * Query the DB.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
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
     * Override default implementation to display a more meaningful information to the user.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        echo $this->render_reset_button();
        $this->print_initials_bar();

        $message = get_string('nohvpactivities', 'tool_migratehvp2h5p');
        echo $OUTPUT->notification($message, 'warning');
    }
}
