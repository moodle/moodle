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
 * Cohort role assignments table
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cohortroles\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use context_helper;
use context_system;
use html_writer;
use moodle_url;
use table_sql;

/**
 * Cohort role assignments table.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_role_assignments_table extends table_sql {

    /** @var context_system */
    protected ?context_system $context = null;

    /** @var array */
    protected array $rolenames = [];

    /**
     * Sets up the table.
     *
     * @param string $uniqueid Unique id of table.
     * @param moodle_url $url The base URL.
     */
    public function __construct($uniqueid, $url) {
        global $CFG;
        parent::__construct($uniqueid);
        $context = context_system::instance();

        $this->context = $context;

        $this->rolenames = role_get_names();

        // This object should not be used without the right permissions.
        require_capability('moodle/role:manage', $context);

        $this->useridfield = 'userid';

        // Define columns in the table.
        $this->define_table_columns();

        $this->define_baseurl($url);
        // Define configs.
        $this->define_table_configs();
    }

    /**
     * Role name column.
     *
     * @param array $data Row data.
     * @return string
     */
    protected function col_rolename($data) {
        return $this->rolenames[$data->roleid]->localname;
    }

    /**
     * Cohort name column.
     *
     * @param array $data Row data.
     * @return string
     */
    protected function col_cohortname($data) {
        global $OUTPUT;

        $record = (object) array(
            'id' => $data->cohortid,
            'idnumber' => $data->cohortidnumber,
            'description' => $data->cohortdescription,
            'visible' => $data->cohortvisible,
            'name' => $data->cohortname,
            'theme' => $data->cohorttheme
        );
        $context = context_helper::instance_by_id($data->cohortcontextid);

        $exporter = new \core_cohort\external\cohort_summary_exporter($record, array('context' => $context));
        $cohort = $exporter->export($OUTPUT);

        $html = $OUTPUT->render_from_template('tool_cohortroles/cohort-in-list', $cohort);
        return $html;
    }

    /**
     * Actions column.
     *
     * @param array $data Row data.
     * @return string
     */
    protected function col_actions($data) {
        global $OUTPUT;

        $action = new \confirm_action(get_string('removecohortroleassignmentconfirm', 'tool_cohortroles'));
        $url = new moodle_url($this->baseurl);
        $url->params(array('removecohortroleassignment' => $data->id, 'sesskey' => sesskey()));
        $pix = new \pix_icon('t/delete', get_string('removecohortroleassignment', 'tool_cohortroles'));
        return $OUTPUT->action_link($url, '', $action, null, $pix);
    }

    /**
     * Setup the headers for the table.
     */
    protected function define_table_columns() {
        // TODO Does not support custom user profile fields (MDL-70456).
        $extrafields = \core_user\fields::get_identity_fields($this->context, false);

        // Define headers and columns.
        $cols = array(
            'cohortname' => get_string('cohort', 'cohort'),
            'rolename' => get_string('role'),
            'fullname' => get_string('name'),
        );

        // Add headers for extra user fields.
        foreach ($extrafields as $field) {
            if (get_string_manager()->string_exists($field, 'moodle')) {
                $cols[$field] = get_string($field);
            } else {
                $cols[$field] = $field;
            }
        }

        // Add remaining headers.
        $cols = array_merge($cols, array('actions' => get_string('actions')));

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(false);
        $this->sortable(true, 'lastname', SORT_ASC);
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
        $fields = 'uca.id, uca.cohortid, uca.userid, uca.roleid, r.shortname AS rolename, ';
        $fields .= 'c.name as cohortname, c.idnumber as cohortidnumber, c.contextid as cohortcontextid, ';
        $fields .= 'c.visible as cohortvisible, c.description as cohortdescription, c.theme as cohorttheme';

        // Add extra user fields that we need for the graded user.
        // TODO Does not support custom user profile fields (MDL-70456).
        $userfieldsapi = \core_user\fields::for_identity($this->context, false)->with_name();
        $fields .= $userfieldsapi->get_sql('u')->selects;

        if ($count) {
            $select = "COUNT(1)";
        } else {
            $select = "$fields";
        }

        $sql = "SELECT $select
                   FROM {tool_cohortroles} uca
                   JOIN {user} u ON u.id = uca.userid
                   JOIN {role} r ON r.id = uca.roleid
                   JOIN {cohort} c ON c.id = uca.cohortid";

        // Check if any additional filtering is required.
        [$sqlwhere, $params] = $this->get_sql_where();
        if ($sqlwhere) {
            $sql .= " WHERE {$sqlwhere}";
        }

        // Add order by if needed.
        if (!$count && $sqlsort = $this->get_sql_sort()) {
            $sql .= " ORDER BY " . $sqlsort;
        }

        return array($sql, $params);
    }

    /**
     * Override the default implementation to set a notification.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->notification(get_string('nothingtodisplay'), 'info', false);
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
