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

use action_menu;
use action_menu_filler;
use core_table\local\filter\filterset;
use html_writer;
use moodle_exception;
use stdClass;
use core_reportbuilder\{manager, system_report};
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\column;

/**
 * System report dynamic table class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_table extends base_report_table {

    /** @var system_report $report */
    protected $report;

    /** @var string Unique ID prefix for the table */
    private const UNIQUEID_PREFIX = 'system-report-table-';

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

        // If we are loading via a dynamic table AJAX request, defer the report loading until the filterset is added to
        // the table, as it is used to populate the report $parameters during construction.
        $serviceinfo = optional_param('info', null, PARAM_RAW);
        if ($serviceinfo !== 'core_table_get_dynamic_table_content') {
            $this->load_report_instance((int) $matches['id'], $parameters);
        }
    }

    /**
     * Load the report persistent, and accompanying system report instance.
     *
     * @param int $reportid
     * @param array $parameters
     */
    private function load_report_instance(int $reportid, array $parameters): void {
        global $PAGE;

        $this->persistent = new report($reportid);
        $this->report = manager::get_report_from_persistent($this->persistent, $parameters);

        // TODO: can probably be removed pending MDL-72974.
        $PAGE->set_context($this->persistent->get_context());

        $fields = $this->report->get_base_fields();
        $groupby = [];
        $maintable = $this->report->get_main_table();
        $maintablealias = $this->report->get_main_table_alias();
        $joins = $this->report->get_joins();
        [$where, $params] = $this->report->get_base_condition();

        $this->set_attribute('data-region', 'reportbuilder-table');
        $this->set_attribute('class', $this->attributes['class'] . ' reportbuilder-table');

        // Download options.
        $this->showdownloadbuttonsat = [TABLE_P_BOTTOM];
        $this->is_downloading($parameters['download'] ?? null, $this->report->get_downloadfilename());

        // Retrieve all report columns. If we are downloading the report, remove as required.
        $columns = $this->report->get_active_columns();
        if ($this->is_downloading()) {
            $columns = array_diff_key($columns,
                array_flip($this->report->get_exclude_columns_for_download()));
        }

        // If we are aggregating any columns, we should group by the remaining ones.
        $aggregatedcolumns = array_filter($columns, static function(column $column): bool {
            return !empty($column->get_aggregation());
        });

        $hasaggregatedcolumns = !empty($aggregatedcolumns);
        if ($hasaggregatedcolumns) {
            $groupby = $fields;
        }

        $columnheaders = $columnsattributes = [];

        // Check whether report has checkbox toggle defined, note that select all is excluded during download.
        if (($checkbox = $this->report->get_checkbox_toggleall(true)) && !$this->is_downloading()) {
            $columnheaders['selectall'] = $PAGE->get_renderer('core')->render($checkbox);
            $this->no_sorting('selectall');
        }

        $columnindex = 1;
        foreach ($columns as $identifier => $column) {
            $column->set_index($columnindex++);

            $columnheaders[$column->get_column_alias()] = $column->get_title();

            // Specify whether column should behave as a user fullname column unless the column has a custom title set.
            if (preg_match('/^user:fullname.*$/', $column->get_unique_identifier()) && !$column->has_custom_title()) {
                $this->userfullnamecolumns[] = $column->get_column_alias();
            }

            // We need to determine for each column whether we should group by its fields, to support aggregation.
            if ($hasaggregatedcolumns && empty($column->get_aggregation())) {
                $groupby = array_merge($groupby, $column->get_groupby_sql());
            }

            // Add each columns fields, joins and params to our report.
            $fields = array_merge($fields, $column->get_fields());
            $joins = array_merge($joins, $column->get_joins());
            $params = array_merge($params, $column->get_params());

            // Disable sorting for some columns.
            if (!$column->get_is_sortable()) {
                $this->no_sorting($column->get_column_alias());
            }

            // Generate column attributes to be included in each cell.
            $columnsattributes[$column->get_column_alias()] = $column->get_attributes();
        }

        // If the report has any actions then append appropriate column, note that actions are excluded during download.
        if ($this->report->has_actions() && !$this->is_downloading()) {
            $columnheaders['actions'] = html_writer::tag('span', get_string('actions', 'core_reportbuilder'), [
                'class' => 'sr-only',
            ]);
            $this->no_sorting('actions');
        }

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Add column attributes to the table.
        $this->set_columnsattributes($columnsattributes);

        // Initial table sort column.
        if ($sortcolumn = $this->report->get_initial_sort_column()) {
            $this->sortable(true, $sortcolumn->get_column_alias(), $this->report->get_initial_sort_direction());
        }

        // Table configuration.
        $this->initialbars(false);
        $this->collapsible(false);
        $this->pageable(true);
        $this->set_default_per_page($this->report->get_default_per_page());

        // Initialise table SQL properties.
        $fieldsql = implode(', ', $fields);
        $this->init_sql($fieldsql, "{{$maintable}} {$maintablealias}", $joins, $where, $params, $groupby);
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
        $reportid = $filterset->get_filter('reportid')->current();
        $parameters = $filterset->get_filter('parameters')->current();

        $this->load_report_instance($reportid, json_decode($parameters, true));

        parent::set_filterset($filterset);
    }

    /**
     * Override parent method for retrieving row class with that defined by the system report
     *
     * @param array|stdClass $row
     * @return string
     */
    public function get_row_class($row) {
        return $this->report->get_row_class((object) $row);
    }

    /**
     * Format each row of returned data, executing defined callbacks for the row and each column
     *
     * @param array|stdClass $row
     * @return array
     */
    public function format_row($row) {
        global $PAGE;

        $this->report->row_callback((object) $row);

        // Walk over the row, and for any key that matches one of our column aliases, call that columns format method.
        $columnsbyalias = $this->report->get_active_columns_by_alias();
        $row = (array) $row;
        array_walk($row, static function(&$value, $key) use ($columnsbyalias, $row): void {
            if (array_key_exists($key, $columnsbyalias)) {
                $value = $columnsbyalias[$key]->format_value($row);
            }
        });

        // Check whether report has checkbox toggle defined.
        if ($checkbox = $this->report->get_checkbox_toggleall(false, (object) $row)) {
            $row['selectall'] = $PAGE->get_renderer('core')->render($checkbox);
        }

        // Now check for any actions.
        if ($this->report->has_actions()) {
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
        global $OUTPUT;

        $menu = new action_menu();
        $menu->set_menu_trigger(
            $OUTPUT->pix_icon('i/menu', get_string('actions', 'core_reportbuilder')),
            'btn btn-icon d-flex align-items-center justify-content-center no-caret',
        );

        $actions = array_filter($this->report->get_actions(), function($action) use ($row) {
            // Only return dividers and action items who can be displayed for current users.
            return $action instanceof action_menu_filler || $action->get_action_link($row);
        });

        $totalactions = count($actions);
        $actionvalues = array_values($actions);
        foreach ($actionvalues as $position => $action) {
            if ($action instanceof action_menu_filler) {
                $ispreviousdivider = array_key_exists($position - 1, $actionvalues) &&
                    ($actionvalues[$position - 1] instanceof action_menu_filler);
                $isnextdivider = array_key_exists($position + 1, $actionvalues) &&
                    ($actionvalues[$position + 1] instanceof action_menu_filler);
                $isfirstdivider = ($position === 0);
                $islastdivider = ($position === $totalactions - 1);

                // Avoid add divider at last/first position and having multiple fillers in a row.
                if ($ispreviousdivider || $isnextdivider || $isfirstdivider || $islastdivider) {
                    continue;
                }
                $actionlink = $action;
            } else {
                // Ensure the action link can be displayed for the current row.
                $actionlink = $action->get_action_link($row);
            }

            if ($actionlink) {
                $menu->add($actionlink);
            }
        }
        return $OUTPUT->render($menu);
    }

    /**
     * Get the html for the download buttons
     *
     * @return string
     */
    public function download_buttons(): string {
        global $OUTPUT;

        if ($this->report->can_be_downloaded() && !$this->is_downloading()) {
            return $OUTPUT->download_dataformat_selector(
                get_string('downloadas', 'table'),
                new \moodle_url('/reportbuilder/download.php'),
                'download',
                [
                    'id' => $this->persistent->get('id'),
                    'parameters' => json_encode($this->report->get_parameters()),
                ]
            );
        }

        return '';
    }

    /**
     * Check if the user has the capability to access this table.
     *
     * @return bool Return true if capability check passed.
     */
    public function has_capability(): bool {
        try {
            $this->report->require_can_view();
            return true;
        } catch (\core_reportbuilder\exception\report_access_exception $e) {
            return false;
        }
    }
}
