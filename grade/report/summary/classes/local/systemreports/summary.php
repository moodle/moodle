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

namespace gradereport_summary\local\systemreports;

use gradereport_summary\local\entities\grade_items;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\system_report;

/**
 * Grade summary system report class implementation
 *
 * @package    gradereport_summary
 * @copyright  2022 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        global $PAGE;

        // We need to ensure page context is always set, as required by output and string formatting.
        $course = get_course($this->get_context()->instanceid);
        $PAGE->set_context($this->get_context());

        // Our main entity, it contains all of the column definitions that we need.
        $entitymain = new grade_items($course);
        $entitymainalias = $entitymain->get_table_alias('grade_items');

        $this->set_main_table('grade_items', $entitymainalias);
        $this->add_entity($entitymain);

        $param1 = database::generate_param_name();
        $param2 = database::generate_param_name();
        $param3 = database::generate_param_name();

        // Exclude grade categories.
        // For now exclude course total as well.
        $wheresql = "$entitymainalias.courseid = :$param1";
        $wheresql .= " AND $entitymainalias.itemtype <> 'course'";

        // Not showing category items.
        $wheresql .= " AND $entitymainalias.itemtype <> 'category'";

        // Only value and scale grade types may be aggregated.
        $wheresql .= " AND ($entitymainalias.gradetype = :$param2 OR $entitymainalias.gradetype = :$param3)";

        $this->add_base_condition_sql($wheresql,
            [$param1 => $course->id, $param2 => GRADE_TYPE_VALUE, $param3 => GRADE_TYPE_SCALE]);

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();

    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('gradereport/summary:view', $this->get_context());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    public function add_columns(): void {
        $columns = [
            'grade_items:name',
            'grade_items:average',
        ];

        $this->add_columns_from_entities($columns);

    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'grade_items:name',
        ];

        $this->add_filters_from_entities($filters);
    }
}
