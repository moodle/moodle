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

use core\output\notification;
use html_writer;
use moodle_exception;
use moodle_url;
use stdClass;
use core_reportbuilder\manager;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\column;
use core_reportbuilder\output\column_aggregation_editable;
use core_reportbuilder\output\column_heading_editable;

/**
 * Custom report dynamic table class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_table extends base_report_table {

    /** @var string Unique ID prefix for the table */
    private const UNIQUEID_PREFIX = 'custom-report-table-';

    /** @var bool Whether report is being edited (we don't want user filters/sorting to be applied when editing) */
    protected const REPORT_EDITING = true;

    /**
     * Table constructor. Note that the passed unique ID value must match the pattern "custom-report-table-(\d+)" so that
     * dynamic updates continue to load the same report
     *
     * @param string $uniqueid
     * @param string $download
     * @throws moodle_exception For invalid unique ID
     */
    public function __construct(string $uniqueid, string $download = '') {
        if (!preg_match('/^' . self::UNIQUEID_PREFIX . '(?<id>\d+)$/', $uniqueid, $matches)) {
            throw new moodle_exception('invalidcustomreportid', 'core_reportbuilder', '', null, $uniqueid);
        }

        parent::__construct($uniqueid);

        $this->define_baseurl(new moodle_url('/reportbuilder/edit.php', ['id' => $matches['id']]));

        // Load the report persistent, and accompanying report instance.
        $this->persistent = new report($matches['id']);
        $this->report = manager::get_report_from_persistent($this->persistent);

        $fields = $groupby = [];
        $maintable = $this->report->get_main_table();
        $maintablealias = $this->report->get_main_table_alias();
        $joins = $this->report->get_joins();
        [$where, $params] = $this->report->get_base_condition();

        $this->set_attribute('data-region', 'reportbuilder-table');
        $this->set_attribute('class', $this->attributes['class'] . ' reportbuilder-table');

        // Download options.
        $this->showdownloadbuttonsat = [TABLE_P_BOTTOM];
        $this->is_downloading($download ?? null, $this->persistent->get_formatted_name());

        // Retrieve all report columns, exit early if there are none.
        $columns = $this->get_active_columns();
        if (empty($columns)) {
            $this->init_sql("{$maintablealias}.*", "{{$maintable}} {$maintablealias}", $joins, '1=0', []);
            return;
        }

        // If we are aggregating any columns, we should group by the remaining ones.
        $aggregatedcolumns = array_filter($columns, static function(column $column): bool {
            return !empty($column->get_aggregation());
        });

        // Also take account of the report setting to show unique rows (only if no columns are being aggregated).
        $hasaggregatedcolumns = !empty($aggregatedcolumns);
        $showuniquerows = !$hasaggregatedcolumns && $this->persistent->get('uniquerows');

        $columnheaders = $columnsattributes = [];
        foreach ($columns as $column) {
            $columnheading = $column->get_persistent()->get_formatted_heading($this->report->get_context());
            $columnheaders[$column->get_column_alias()] = $columnheading !== '' ? $columnheading : $column->get_title();

            // We need to determine for each column whether we should group by it's fields, to support aggregation.
            $columnaggregation = $column->get_aggregation();
            if ($showuniquerows || ($hasaggregatedcolumns && empty($columnaggregation))) {
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

            // Add column attributes needed for card view.
            $settings = $this->report->get_settings_values();
            $showfirsttitle = $settings['cardview_showfirsttitle'] ?? false;
            $visiblecolumns = max($settings['cardview_visiblecolumns'] ?? 1, count($this->columns));
            if ($showfirsttitle || $column->get_persistent()->get('columnorder') > 1) {
                $column->add_attributes(['data-cardtitle' => $columnheaders[$column->get_column_alias()]]);
            }
            if ($column->get_persistent()->get('columnorder') > $visiblecolumns) {
                $column->add_attributes(['data-cardviewhidden' => '']);
            }

            // Generate column attributes to be included in each cell.
            $columnsattributes[$column->get_column_alias()] = $column->get_attributes();
        }

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));

        // Add column attributes to the table.
        $this->set_columnsattributes($columnsattributes);

        // Table configuration.
        $this->initialbars(false);
        $this->collapsible(false);
        $this->pageable(true);

        // Initialise table SQL properties.
        $this->set_report_editing(static::REPORT_EDITING);

        $fieldsql = implode(', ', $fields);
        $this->init_sql($fieldsql, "{{$maintable}} {$maintablealias}", $joins, $where, $params, $groupby);
    }

    /**
     * Return a new instance of the class for given report ID
     *
     * @param int $reportid
     * @param string $download
     * @return static
     */
    public static function create(int $reportid, string $download = ''): self {
        return new static(self::UNIQUEID_PREFIX . $reportid, $download);
    }

    /**
     * Get user preferred sort columns, overriding those of parent. If user has no preferences then use report defaults
     *
     * @return array
     */
    public function get_sort_columns(): array {
        $sortcolumns = parent::get_sort_columns();

        if ($this->editing || empty($sortcolumns)) {
            $sortcolumns = [];
            $columns = $this->get_active_columns();

            // We need to sort the columns by the configured sorting order.
            usort($columns, static function(column $a, column $b): int {
                return ($a->get_persistent()->get('sortorder') < $b->get_persistent()->get('sortorder')) ? -1 : 1;
            });

            foreach ($columns as $column) {
                $persistent = $column->get_persistent();
                if ($column->get_is_sortable() && $persistent->get('sortenabled')) {
                    $sortcolumns[$column->get_column_alias()] = $persistent->get('sortdirection');
                }
            }
        }

        return $sortcolumns;
    }

    /**
     * Format each row of returned data, executing defined callbacks for the row and each column
     *
     * @param array|stdClass $row
     * @return array
     */
    public function format_row($row) {
        $columns = $this->get_active_columns();

        $formattedrow = [];
        foreach ($columns as $column) {
            $formattedrow[$column->get_column_alias()] = $column->format_value((array) $row);
        }

        return $formattedrow;
    }

    /**
     * Download is disabled when editing the report
     *
     * @return string
     */
    public function download_buttons(): string {
        return '';
    }

    /**
     * Get the columns of the custom report, returned instances being valid and available for the user
     *
     * @return column[]
     */
    protected function get_active_columns(): array {
        return $this->report->get_active_columns();
    }

    /**
     * Override parent method for printing headers so we can render our custom controls in each cell
     */
    public function print_headers() {
        global $OUTPUT, $PAGE;

        $columns = $this->get_active_columns();
        if (empty($columns)) {
            return;
        }

        $columns = array_values($columns);
        $renderer = $PAGE->get_renderer('core');

        echo html_writer::start_tag('thead');
        echo html_writer::start_tag('tr');

        foreach ($this->headers as $index => $title) {
            $column = $columns[$index];

            $headingeditable = new column_heading_editable(0, $column->get_persistent());
            $aggregationeditable = new column_aggregation_editable(0, $column->get_persistent());

            // Render table header cell, with all editing controls.
            $headercell = $OUTPUT->render_from_template('core_reportbuilder/table_header_cell', [
                'entityname' => $this->report->get_entity_title($column->get_entity_name()),
                'name' => $column->get_title(),
                'headingeditable' => $headingeditable->render($renderer),
                'aggregationeditable' => $aggregationeditable->render($renderer),
                'movetitle' => get_string('movecolumn', 'core_reportbuilder', $column->get_title()),
            ]);

            echo html_writer::tag('th', $headercell, [
                'class' => 'border-right border-left',
                'scope' => 'col',
                'data-region' => 'column-header',
                'data-column-id' => $column->get_persistent()->get('id'),
                'data-column-name' => $column->get_title(),
                'data-column-position' => $index + 1,
            ]);
        }

        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');
    }

    /**
     * Override print_nothing_to_display to ensure that column headers are always added.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        $this->start_html();
        $this->print_headers();
        echo html_writer::end_tag('table');
        echo html_writer::end_tag('div');
        $this->wrap_html_finish();

        $notification = (new notification(get_string('nothingtodisplay'), notification::NOTIFY_INFO, false))
            ->set_extra_classes(['mt-3']);
        echo $OUTPUT->render($notification);

        echo $this->get_dynamic_table_html_end();
    }

    /**
     * Override get_row_cells_html to add an extra cell with the toggle button for card view.
     *
     * @param string $rowid
     * @param array $row
     * @param array|null $suppresslastrow
     * @return string
     */
    public function get_row_cells_html(string $rowid, array $row, ?array $suppresslastrow): string {
        $html = parent::get_row_cells_html($rowid, $row, $suppresslastrow);

        // Add extra 'td' in the row with card toggle button (only visible in card view).
        $visiblecolumns = $this->report->get_settings_values()['cardview_visiblecolumns'] ?? 1;
        if ($visiblecolumns < count($this->columns)) {
            $buttonicon = html_writer::tag('i', '', ['class' => 'fa fa-angle-down']);

            // We need a cleaned version (without tags/entities) of the first row column to use as toggle button.
            $rowfirstcolumn = strip_tags((string) reset($row));
            $buttontitle = $rowfirstcolumn !== ''
                ? get_string('showhide', 'core_reportbuilder', html_entity_decode($rowfirstcolumn))
                : get_string('showhidecard', 'core_reportbuilder');

            $button = html_writer::tag('button', $buttonicon, [
                'type' => 'button',
                'class' => 'btn collapsed',
                'title' => $buttontitle,
                'data-toggle' => 'collapse',
                'data-action' => 'toggle-card'
            ]);
            $html .= html_writer::tag('td', $button, ['class' => 'card-toggle d-none']);
        }
        return $html;
    }
}
