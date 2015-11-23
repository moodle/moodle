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
 * Template cohorts table.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use html_writer;
use moodle_url;
use table_sql;

/**
 * Template cohorts table class.
 *
 * Note that presently this table may display some rows although the current user
 * does not have permission to view those cohorts.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_cohorts_table extends table_sql {

    /** @var context The context. */
    protected $context;

    /** @var \tool_lp\template The template. */
    protected $template;

    /**
     * Sets up the table.
     *
     * @param string $uniqueid Unique id of table.
     * @param \tool_lp\template $template The template.
     */
    public function __construct($uniqueid, \tool_lp\template $template) {
        global $CFG;
        parent::__construct($uniqueid);

        // This object should not be used without the right permissions.
        require_capability('tool/lp:templatemanage', $template->get_context());

        // Set protected properties.
        $this->template = $template;
        $this->context = $this->template->get_context();

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();
    }

    /**
     * Column actions.
     *
     * @param  object $row
     * @return string
     */
    protected function col_actions($row) {
        // TODO MDL-52266 Add delete action icon.
        return '';
    }

    /**
     * Setup the headers for the table.
     */
    protected function define_table_columns() {
        $extrafields = get_extra_user_fields($this->context);

        // Define headers and columns.
        $cols = array(
            'name' => get_string('name', 'cohort'),
            'idnumber' => get_string('idnumber', 'cohort'),
            'actions' => get_string('actions')
        );

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(false);
        $this->sortable(true, 'name', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('actions');
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return array containing sql to use and an array of params.
     */
    protected function get_sql_and_params($count = false) {
        $fields = 'c.id, c.name, c.idnumber';

        if ($count) {
            $select = "COUNT(1)";
        } else {
            $select = "$fields";
        }

        $sql =  "SELECT $select
                   FROM {" . \tool_lp\template_cohort::TABLE . "} tc
                   JOIN {cohort} c ON c.id = tc.cohortid
                  WHERE tc.templateid = :templateid";
        $params = array('templateid' => $this->template->get_id());

        // Add order by if needed.
        if (!$count && $sqlsort = $this->get_sql_sort()) {
            $sql .= " ORDER BY " . $sqlsort;
        }

        return array($sql, $params);
    }

    /**
     * Override the default implementation to set a decent heading level.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);
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
}
