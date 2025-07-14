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

namespace tool_brickfield;

use tool_brickfield\local\tool\filter;
use tool_brickfield\local\tool\tool;

/**
 * Provides the Brickfield Accessibility toolkit site data API.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @author     Mike Churchward (mike@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sitedata {

    /** @var array An array of SQL parts to build an SQL statement. */
    private $sqlparts = ['SELECT' => [], 'FROM' => '', 'WHERE' => '', 'GROUPBY' => '', 'ORDERBY' => ''];

    /** @var array An array of SQL parameters. */
    private $sqlparams = [];

    /** @var array An array of labels to be displayed. */
    private $grouplabels = [];

    /** @var array An array of the used check group numbers. */
    private $groupnumbers = [];

    /** @var int The count of group labels. */
    private $groupcount = 0;

    /**
     * Return the total number of courses that have been checked.
     * @return int
     * @throws \dml_exception
     */
    public static function get_total_courses_checked(): int {
        global $DB;
        return $DB->count_records_select(manager::DB_AREAS, '', [], 'COUNT(DISTINCT courseid)');
    }

    /**
     * Get records of component per course summary data.
     * @param local\tool\filter $filter
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_component_data(filter $filter): array {
        global $DB;

        $data = [];
        if ($filter->validate_filters()) {
            list($wheresql, $params) = $filter->get_course_sql('', true);
            $sql = 'SELECT component, SUM(errorcount) as errorsum, SUM(totalactivities) as total, ' .
                'SUM(failedactivities) as failed, SUM(passedactivities) as passed ' .
                'FROM {' . manager::DB_CACHEACTS . '} area ' .
                (empty($wheresql) ? '' : ('WHERE ' . $wheresql . ' ')) .
                'GROUP BY area.component ' .
                'ORDER BY area.component ASC';

            $data = $DB->get_records_sql($sql, $params);

            foreach ($data as $key => $componentsummary) {
                $data[$key]->componentlabel = tool::get_module_label($componentsummary->component);
            }
        }

        return $data;
    }

    /**
     * Get records of check group per course summary data.
     * @param local\tool\filter $filter
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_checkgroup_data(filter $filter): array {
        global $DB;

        $data = [];
        if ($filter->validate_filters()) {
            $this->get_base_checkgroup_sql($filter);

            $sql = $this->get_select_string() . ' ' .
                'FROM ' . $this->sqlparts['FROM'] . ' ' .
                (!empty($this->sqlparts['WHERE']) ? 'WHERE ' . $this->sqlparts['WHERE'] . ' ' : '') .
                (!empty($this->sqlparts['GROUPBY']) ? 'GROUP BY ' . $this->sqlparts['GROUPBY'] . ' ' : '') .
                (!empty($this->sqlparts['ORDERBY']) ? 'ORDER BY ' . $this->sqlparts['ORDERBY'] . ' ' : '');
            $data = array_values($DB->get_records_sql($sql, $this->sqlparams,
                ($filter->page * $filter->perpage), $filter->perpage));
            if (empty($data)) {
                $data[0] = (object)(array_values($this->grouplabels));
            } else {
                $data[0] = (object)(array_merge((array)$data[0], $this->grouplabels));
            }
            $data[0]->groupcount = $this->groupcount;
        }

        return $data;
    }

    /**
     * Get records of check group per course summary data.
     * @param local\tool\filter $filter
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_checkgroup_by_course_data(filter $filter): array {
        $this->sqlparts['GROUPBY'] = 'courseid';
        $this->add_select_item('courseid');
        return $this->get_checkgroup_data($filter);
    }

    /**
     * Get records of check group per course summary data.
     * @param local\tool\filter $filter
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_checkgroup_with_failed_data(filter $filter): array {
        $this->add_select_item('SUM(activitiesfailed) as failed');
        return $this->get_checkgroup_data($filter);
    }

    /**
     * Load up the base SQL parts.
     * @param filter $filter
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_base_checkgroup_sql(filter $filter) {
        if ($filter->validate_filters()) {
            $this->get_grouplabel_info();

            list($this->sqlparts['WHERE'], $this->sqlparams) = $filter->get_course_sql('summ', true);
            $this->add_select_item('SUM(activities) as activities');
            foreach ($this->groupnumbers as $lab) {
                $this->add_select_item("SUM(errorschecktype$lab) as errorsvalue$lab");
                $this->add_select_item("SUM(failedchecktype$lab) as failedvalue$lab");
                $this->add_select_item("AVG(percentchecktype$lab) as percentvalue$lab");
            }
            $this->sqlparts['FROM'] = '{' . manager::DB_SUMMARY . '} summ';
        }
    }

    /**
     * Load the group label information.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_grouplabel_info() {
        global $DB;

        // Don't need to do this more than once.
        if (empty($this->grouplabels)) {
            // Determine the checkgroups being used by this site.
            $labelsql = 'SELECT DISTINCT checkgroup, checkgroup as cg2 FROM {' . manager::DB_CHECKS . '} ' .
                'WHERE status = ? ORDER BY checkgroup ASC';
            $groupvals = $DB->get_records_sql_menu($labelsql, [1]);
            $grouplabels = array_intersect_key(area_base::CHECKGROUP_NAMES, $groupvals);

            foreach ($grouplabels as $lab => $label) {
                $this->grouplabels['componentlabel' . $lab] = get_string('checktype:' . $label, manager::PLUGINNAME);
                $this->groupnumbers[] = $lab;
                $this->groupcount++;
            }
        }
    }

    /**
     * Add a select item to the sqlparts array.
     * @param string $item
     */
    private function add_select_item(string $item): void {
        $this->sqlparts['SELECT'][] = $item;
    }

    /**
     * Assemble and return the select portion of the sql.
     * @return string
     */
    private function get_select_string(): string {
        return 'SELECT ' . implode(', ', $this->sqlparts['SELECT']);
    }
}
