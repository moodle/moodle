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

namespace gradepenalty_duedate\table;

use context;
use context_system;
use core_table\sql_table;

/**
 * Table for penalty rule.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_rule_table extends sql_table {
    /** @var context context */
    protected context $context;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of form.
     * @param string $contextid context id.
     */
    public function __construct($uniqueid, $contextid) {
        parent::__construct($uniqueid);

        $this->context = context::instance_by_id($contextid);

        // Add columns.
        $this->define_columns([
            'overdueby',
            'penalty',
        ]);

        // Add columns header.
        $this->define_headers([
            get_string('overdueby', 'gradepenalty_duedate'),
            get_string('penalty', 'gradepenalty_duedate'),
        ]);

        // Disable sorting.
        $this->sortable(false);

        // Disable hiding columns.
        $this->collapsible(false);
    }

    #[\Override]
    public function query_db($pagesize, $useinitialsbar = true): void {
        global $DB;
        // Contexts to find the penalty rules.
        $contextlevel = $this->context->contextlevel;
        $contextids = [];
        if ($contextlevel == CONTEXT_MODULE) {
            // Module context.
            $contextids[] = $this->context->id;
            // Course context.
            $contextids[] = $this->context->get_parent_context()->id;
            // System context.
            $contextids[] = context_system::instance()->id;
        } else if ($contextlevel == CONTEXT_COURSE) {
            // Course context.
            $contextids[] = $this->context->id;
            // System context.
            $contextids[] = context_system::instance()->id;
        } else if ($contextlevel == CONTEXT_SYSTEM) {
            // System context.
            $contextids[] = $this->context->id;
        }

        // Find the penalty rules.
        foreach ($contextids as $contextid) {
            $params = ['contextid' => $contextid];
            $total = $DB->count_records_sql($this->get_sql(true), $params);
            if ($total > 0) {
                break;
            }
        }

        $this->pagesize($pagesize, $total);
        $this->rawdata = $DB->get_records_sql($this->get_sql(), $params, $this->get_page_start(), $this->get_page_size());
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return string the SQL query.
     */
    protected function get_sql($count = false): string {
        if ($count) {
            $select = "COUNT(1)";
            $order = "";
        } else {
            $select = "r.overdueby, r.penalty, r.sortorder";
            $order = " order by r.sortorder ASC";
        }

        $sql = "SELECT $select
                  FROM {gradepenalty_duedate_rule} r
                 WHERE r.contextid = :contextid";

        return $sql . $order;
    }

    /**
     * Overdue column.
     *
     * @param object $row row object.
     */
    public function col_overdueby($row): string {
        // If this is the last rule, show the last row.
        if (count($this->rawdata) === ($row->sortorder + 1)) {
            // Find the previous rule.
            foreach ($this->rawdata as $rule) {
                if ($rule->sortorder == $row->sortorder - 1) {
                    return get_string('overdueby_lastrow', 'gradepenalty_duedate', format_time($rule->overdueby));
                }
            }
            // There is only one row.
            return get_string('overdueby_onerow', 'gradepenalty_duedate');
        }

        // This is not the last rule.
        return get_string('overdueby_row', 'gradepenalty_duedate', format_time($row->overdueby));
    }

    /**
     * Penalty column.
     *
     * @param object $row row object.
     */
    public function col_penalty($row): string {
        return get_string('percents', 'moodle', format_float($row->penalty, -1));
    }
}
