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
use html_writer;
use moodle_exception;
use moodle_url;
use renderable;
use table_sql;
use stdClass;
use core_table\dynamic;
use core_reportbuilder\manager;
use core_reportbuilder\system_report;
use core_reportbuilder\local\filters\base;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\column;
use core_table\local\filter\filterset;

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->libdir}/tablelib.php");

/**
 * System report dynamic table class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_table extends table_sql implements dynamic, renderable {

    /** @var string Unique ID prefix for the table */
    private const UNIQUEID_PREFIX = 'system-report-table-';

    /** @var report $report */
    protected $report;

    /** @var system_report $systemreport */
    protected $systemreport;

    /**
     * Table constructor. Note that the passed unique ID value must match the pattern "system-report-table-(\d+)" so that
     * dynamic updates continue to load the same report
     *
     * @param string $uniqueid
     * @param array $parameters
     * @throws moodle_exception For invalid unique ID
     */
    public function __construct(string $uniqueid, array $parameters = []) {
        if (!preg_match('/^' . self::UNIQUEID_PREFIX . '(?<id>\d+)$/', $uniqueid, $matches)) {
            throw new moodle_exception('invalidsystemreportid', 'core_reportbuilder', '', null, $uniqueid);
        }

        parent::__construct($uniqueid);

        // Load the report persistent, and accompanying system report instance.
        $this->report = new report($matches['id']);
        $this->systemreport = manager::get_report_from_persistent($this->report, $parameters);

        $fields = $this->systemreport->get_base_fields();
        $maintable = $this->systemreport->get_main_table();
        $maintablealias = $this->systemreport->get_main_table_alias();
        $joins = $this->systemreport->get_joins();
        [$where, $params] = $this->systemreport->get_base_condition();

        $this->set_attribute('data-region', 'reportbuilder-table');
        $this->set_attribute('class', $this->attributes['class'] . ' reportbuilder-table');

        // Download options.
        $this->showdownloadbuttonsat = [TABLE_P_BOTTOM];
        $this->is_downloading($parameters['download'] ?? null, $this->systemreport->get_downloadfilename());

        // Retrieve all report columns. If we are downloading the report, remove as required.
        $columns = $this->systemreport->get_columns();
        if ($this->is_downloading()) {
            $columns = array_diff_key($columns,
                array_flip($this->systemreport->get_exclude_columns_for_download()));
        }

        $columnheaders = [];
        $columnindex = 1;
        foreach ($columns as $identifier => $column) {
            $column->set_index($columnindex++);

            $columnheaders[$column->get_column_alias()] = $column->get_title();

            // Add each columns fields, joins and params to our report.
            $fields = array_merge($fields, $column->get_fields());
            $joins = array_merge($joins, $column->get_joins());
            $params = array_merge($params, $column->get_params());

            // Disable sorting for some columns.
            if (!$column->get_is_sortable()) {
                $this->no_sorting($column->get_column_alias());
            }
        }

        // If the report has any actions then append appropriate column, note that actions are excluded during download.
        if ($this->systemreport->has_actions() && !$this->is_downloading()) {
            $columnheaders['actions'] = html_writer::tag('span', get_string('actions', 'core_reportbuilder'), [
                'class' => 'sr-only',
            ]);
            $this->no_sorting('actions');
        }

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Initial table sort column.
        if ($sortcolumn = $this->systemreport->get_initial_sort_column()) {
            $this->sortable(true, $sortcolumn->get_column_alias(), $this->systemreport->get_initial_sort_direction());
        }

        // Table configuration.
        $this->initialbars(false);
        $this->collapsible(false);
        $this->pageable(true);
        $this->set_default_per_page($this->systemreport->get_default_per_page());

        // Initialise table SQL properties.
        $fieldsql = implode(', ', $fields);
        $this->init_sql($fieldsql, "{{$maintable}} {$maintablealias}", $joins, $where, $params);
    }

    /**
     * Return a new instance of the class for given report ID. We include report parameters here so they are present during
     * initialisation
     *
     * @param int $reportid
     * @param array $parameters
     * @return static
     */
    public static function create(int $reportid, array $parameters): self {
        return new static(self::UNIQUEID_PREFIX . $reportid, $parameters);
    }

    /**
     * Set the filterset in the table class. We set the report parameters here so that they are persisted while paging
     *
     * @param filterset $filterset
     */
    public function set_filterset(filterset $filterset): void {
        $parameters = $filterset->get_filter('parameters')->current();
        $this->systemreport->set_parameters((array) json_decode($parameters, true));

        parent::set_filterset($filterset);
    }

    /**
     * Initialises table SQL properties
     *
     * @param string $fields
     * @param string $from
     * @param array $joins
     * @param string $where
     * @param array $params
     */
    protected function init_sql(string $fields, string $from, array $joins, string $where, array $params): void {
        $wheres = [];
        if ($where !== '') {
            $wheres[] = $where;
        }

        $filtervalues = $this->systemreport->get_filter_values();
        foreach ($this->systemreport->get_filters() as $filter) {
            /** @var base $filterclass */
            $filterclass = $filter->get_filter_class();
            $filterinstance = $filterclass::create($filter);

            [$filtersql, $filterparams] = $filterinstance->get_sql_filter($filtervalues);
            if ($filtersql !== '') {
                $wheres[] = "({$filtersql})";
                $params = array_merge($params, $filterparams);

                $joins = array_merge($joins, $filter->get_joins());
            }
        }

        $wheresql = '1=1';
        if (!empty($wheres)) {
            $wheresql = implode(' AND ', $wheres);
        }

        // Add unique table joins.
        $from .= ' ' . implode(' ', array_unique($joins));

        $this->set_sql($fields, $from, $wheresql, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM {$from} WHERE {$wheresql}", $params);
    }

    /**
     * Override parent method of the same, to make use of a recordset and avoid issues with duplicate values in the first column
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $sql = "SELECT {$this->sql->fields} FROM {$this->sql->from} WHERE {$this->sql->where}";

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql .= " ORDER BY {$sort}";
        }

        if (!$this->is_downloading()) {
            $this->pagesize($pagesize, $DB->count_records_sql($this->countsql, $this->countparams));

            $this->rawdata = $DB->get_recordset_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_recordset_sql($sql, $this->sql->params);
        }
    }

    /**
     * Override parent method for retrieving row class with that defined by the system report
     *
     * @param array|stdClass $row
     * @return string
     */
    public function get_row_class($row) {
        return $this->systemreport->get_row_class((object) $row);
    }

    /**
     * Format each row of returned data, executing defined callbacks for the row and each column
     *
     * @param array|stdClass $row
     * @return array
     */
    public function format_row($row) {
        $this->systemreport->row_callback((object) $row);

        /** @var column[] $columnsbyalias */
        $columnsbyalias = [];

        // Create a lookup for convenience, indexed by column alias.
        $columns = $this->systemreport->get_columns();
        foreach ($columns as $column) {
            $columnsbyalias[$column->get_column_alias()] = $column;
        }

        // Walk over the row, and for any key that matches one of our column aliases, call that columns format method.
        $row = (array) $row;
        array_walk($row, static function(&$value, $key) use ($row, $columnsbyalias): void {
            if (array_key_exists($key, $columnsbyalias)) {
                $value = $columnsbyalias[$key]->format_value($row);
            }
        });

        if ($this->systemreport->has_actions()) {
            $row['actions'] = $this->format_row_actions((object) $row);
        }

        return $row;
    }

    /**
     * Return formatted actions column for the row
     *
     * @param stdClass $row
     * @return string
     */
    private function format_row_actions(stdClass $row): string {
        $actions = array_map(static function(action $action) use ($row): string {
            return (string) $action->get_action_link($row);
        }, $this->systemreport->get_actions());

        return implode('', $actions);
    }

    /**
     * Get the context for the table (that of the report persistent)
     *
     * @return context
     */
    public function get_context(): context {
        return $this->report->get_context();
    }

    /**
     * Set the base URL of the table to the current page URL
     */
    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/');
    }

    /**
     * Get the html for the download buttons
     *
     * @return string
     */
    public function download_buttons(): string {
        global $OUTPUT;

        if ($this->systemreport->can_be_downloaded() && !$this->is_downloading()) {
            return $OUTPUT->download_dataformat_selector(
                get_string('downloadas', 'table'),
                new \moodle_url('/reportbuilder/download.php'),
                'download',
                [
                    'id' => $this->report->get('id'),
                    'parameters' => json_encode($this->systemreport->get_parameters()),
                ]
            );
        }

        return '';
    }
}
