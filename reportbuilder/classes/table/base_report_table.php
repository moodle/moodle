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

declare(strict_types=1);

namespace core_reportbuilder\table;

use context;
use moodle_url;
use renderable;
use table_sql;
use html_writer;
use core_table\dynamic;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\filters\base;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base as base_report;
use core_reportbuilder\local\report\filter;
use core\output\notification;

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->libdir}/tablelib.php");

/**
 * Base report dynamic table class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_report_table extends table_sql implements dynamic, renderable {

    /** @var report $persistent */
    protected $persistent;

    /** @var base_report $report */
    protected $report;

    /** @var string $groupbysql */
    protected $groupbysql = '';

    /** @var bool $editing */
    protected $editing = false;

    /**
     * Initialises table SQL properties
     *
     * @param string $fields
     * @param string $from
     * @param array $joins
     * @param string $where
     * @param array $params
     * @param array $groupby
     */
    protected function init_sql(string $fields, string $from, array $joins, string $where, array $params,
            array $groupby = []): void {

        $wheres = [];
        if ($where !== '') {
            $wheres[] = $where;
        }

        // Track the index of conditions/filters as we iterate over them.
        $conditionindex = $filterindex = 0;

        // For each condition, we need to ensure their values are always accounted for in the report.
        $conditionvalues = $this->report->get_condition_values();
        foreach ($this->report->get_active_conditions() as $condition) {
            [$conditionsql, $conditionparams] = $this->get_filter_sql($condition, $conditionvalues, 'c' . $conditionindex++);
            if ($conditionsql !== '') {
                $joins = array_merge($joins, $condition->get_joins());
                $wheres[] = "({$conditionsql})";
                $params = array_merge($params, $conditionparams);
            }
        }

        // For each filter, we also need to apply their values (will differ according to user viewing the report).
        if (!$this->editing) {
            $filtervalues = $this->report->get_filter_values();
            foreach ($this->report->get_active_filters() as $filter) {
                [$filtersql, $filterparams] = $this->get_filter_sql($filter, $filtervalues, 'f' . $filterindex++);
                if ($filtersql !== '') {
                    $joins = array_merge($joins, $filter->get_joins());
                    $wheres[] = "({$filtersql})";
                    $params = array_merge($params, $filterparams);
                }
            }
        }

        // Join all the filters into a SQL WHERE clause, falling back to all records.
        if (!empty($wheres)) {
            $wheresql = implode(' AND ', $wheres);
        } else {
            $wheresql = '1=1';
        }

        if (!empty($groupby)) {
            $this->groupbysql = 'GROUP BY ' . implode(', ', $groupby);
        }

        // Add unique table joins.
        $from .= ' ' . implode(' ', array_unique($joins));

        $this->set_sql($fields, $from, $wheresql, $params);

        $counttablealias = database::generate_alias();
        $this->set_count_sql("
            SELECT COUNT(1)
              FROM (SELECT {$fields}
                      FROM {$from}
                     WHERE {$wheresql}
                           {$this->groupbysql}
                   ) {$counttablealias}", $params);
    }

    /**
     * Whether the current report table is being edited, in which case certain actions are not applied to it, e.g. user filtering
     * and sorting. Default class value is false
     *
     * @param bool $editing
     */
    public function set_report_editing(bool $editing): void {
        $this->editing = $editing;
    }

    /**
     * Return SQL fragments from given filter instance suitable for inclusion in table SQL
     *
     * @param filter $filter
     * @param array $filtervalues
     * @param string $paramprefix
     * @return array [$sql, $params]
     */
    private function get_filter_sql(filter $filter, array $filtervalues, string $paramprefix): array {
        /** @var base $filterclass */
        $filterclass = $filter->get_filter_class();

        // Retrieve SQL fragments from the filter instance, process parameters if required.
        [$sql, $params] = $filterclass::create($filter)->get_sql_filter($filtervalues);
        if ($paramprefix !== '' && count($params) > 0) {
            return database::sql_replace_parameters(
                $sql,
                $params,
                fn(string $param) => "{$paramprefix}_{$param}",
            );
        }

        return [$sql, $params];
    }

    /**
     * Generate suitable SQL for the table
     *
     * @return string
     */
    protected function get_table_sql(): string {
        $sql = "SELECT {$this->sql->fields} FROM {$this->sql->from} WHERE {$this->sql->where} {$this->groupbysql}";

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql .= " ORDER BY {$sort}";
        }

        return $sql;
    }

    /**
     * Override parent method of the same, to make use of a recordset and avoid issues with duplicate values in the first column
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     */
    public function query_db($pagesize, $useinitialsbar = true): void {
        global $DB;

        if (!$this->is_downloading()) {
            $this->pagesize($pagesize, $DB->count_records_sql($this->countsql, $this->countparams));

            $this->rawdata = $DB->get_recordset_sql($this->get_table_sql(), $this->sql->params, $this->get_page_start(),
                $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_recordset_sql($this->get_table_sql(), $this->sql->params);
        }
    }

    /**
     * Override parent method of the same, to ensure that any columns with custom sort fields are accounted for
     *
     * Because the base table_sql has "special" handling of fullname columns {@see table_sql::contains_fullname_columns}, we need
     * to handle that here to ensure that any that are being sorted take priority over reportbuilders own aliases of the same
     * columns. This prevents them appearing multiple times in a query, which SQL Server really doesn't like
     *
     * @return string
     */
    public function get_sql_sort() {
        $columnsbyalias = $this->report->get_active_columns_by_alias();
        $columnsortby = [];

        // First pass over sorted columns, to extract all the fullname fields from table_sql.
        $sortedcolumns = $this->get_sort_columns();
        $sortedcolumnsfullname = array_filter($sortedcolumns, static function(string $alias): bool {
            return !preg_match('/^c[\d]+_/', $alias);
        }, ARRAY_FILTER_USE_KEY);

        // Iterate over all sorted report columns, replace with columns own fields if applicable.
        foreach ($sortedcolumns as $alias => $order) {
            $column = $columnsbyalias[$alias] ?? null;

            // If the column is not being aggregated and defines custom sort fields, then use them.
            if ($column && !$column->get_aggregation() &&
                    ($sortfields = $column->get_sort_fields())) {

                foreach ($sortfields as $sortfield) {
                    $columnsortby[$sortfield] = $order;
                }
            } else {
                $columnsortby[$alias] = $order;
            }
        }

        // Now ensure that any fullname sorted columns have duplicated aliases removed.
        $columnsortby = array_filter($columnsortby, static function(string $alias) use ($sortedcolumnsfullname): bool {
            if (preg_match('/^c[\d]+_(?<column>.*)$/', $alias, $matches)) {
                return !array_key_exists($matches['column'], $sortedcolumnsfullname);
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);

        return static::construct_order_by($columnsortby);
    }

    /**
     * Get the context for the table (that of the report persistent)
     *
     * @return context
     */
    public function get_context(): context {
        return $this->persistent->get_context();
    }

    /**
     * Set the base URL of the table to the current page URL
     */
    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/');
    }

    /**
     * Override print_nothing_to_display to modity the output styles.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        echo $this->get_dynamic_table_html_start();
        echo $this->render_reset_button();

        if ($notice = $this->report->get_default_no_results_notice()) {
            echo $OUTPUT->render(new notification($notice->out(), notification::NOTIFY_INFO, false));
        }

        echo $this->get_dynamic_table_html_end();
    }

    /**
     * Override start of HTML to remove top pagination.
     */
    public function start_html() {
        // Render the dynamic table header.
        echo $this->get_dynamic_table_html_start();

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->wrap_html_start();

        $this->set_caption($this->report::get_name(), ['class' => 'sr-only']);

        echo html_writer::start_tag('div');
        echo html_writer::start_tag('table', $this->attributes) . $this->render_caption();
    }
}
