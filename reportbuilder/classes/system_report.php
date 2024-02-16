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

namespace core_reportbuilder;

use action_menu_filler;
use coding_exception;
use html_writer;
use stdClass;
use core\output\checkbox_toggleall;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\base;
use core_reportbuilder\local\report\column;

/**
 * Base class for system reports
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class system_report extends base {

    /** @var array $parameters */
    private $parameters;

    /** @var string[] $basefields List of base fields */
    private $basefields = [];

    /** @var callable $checkboxcallback */
    private $checkboxcallback = null;

    /** @var bool $filterformdefault Whether to use the default filters form */
    private $filterformdefault = true;

    /** @var action|action_menu_filler[] $actions */
    private $actions = [];

    /** @var column $initialsortcolumn */
    private $initialsortcolumn;

    /** @var int $initialsortdirection */
    private $initialsortdirection;

    /**
     * System report constructor.
     *
     * @param report $report
     * @param array $parameters
     */
    final public function __construct(report $report, array $parameters) {
        $this->parameters = $parameters;

        parent::__construct($report);
    }

    /**
     * Provide default implementation of the report name. Extending classes can implement this method to provide their own name
     *
     * @return string
     */
    public static function get_name(): string {
        $classparts = explode('\\', get_called_class());
        $classname = end($classparts);

        // Try to make human readable, capitalized and with spaces.
        return ucfirst(str_replace('_', ' ', $classname));
    }

    /**
     * Validates access to view this report
     *
     * This is necessary to implement independently of the page that would typically embed the report because
     * subsequent pages are requested via AJAX requests, and access should be validated each time
     *
     * @return bool
     */
    abstract protected function can_view(): bool;

    /**
     * Validate access to the report
     *
     * @throws report_access_exception
     */
    final public function require_can_view(): void {
        if (!$this->can_view()) {
            throw new report_access_exception();
        }
    }

    /**
     * Report validation
     *
     * @throws report_access_exception If user cannot access the report
     * @throws coding_exception If no default column are specified
     */
    protected function validate(): void {
        parent::validate();

        $this->require_can_view();

        // Ensure the report has some default columns specified.
        if (empty($this->get_columns())) {
            throw new coding_exception('No columns added');
        }
    }

    /**
     * Add list of fields that have to be always included in SQL query for actions and row classes
     *
     * Base fields are only available in system reports because they are not compatible with aggregation
     *
     * @param string $sql SQL clause for the list of fields that only uses main table or base joins
     */
    final protected function add_base_fields(string $sql): void {
        $this->basefields[] = $sql;
    }

    /**
     * Return report base fields
     *
     * @return array
     */
    final public function get_base_fields(): array {
        return $this->basefields;
    }

    /**
     * Define toggle all checkbox for the report, required row data should be defined by calling {@see add_base_fields}
     *
     * @param callable $callback Callback to return value/label for each checkbox, implementing the following signature:
     *      function(stdClass $row): array containing value/label pair
     */
    final protected function set_checkbox_toggleall(callable $callback): void {
        $this->checkboxcallback = $callback;
    }

    /**
     * Return instance of toggle all checkbox, if previously defined by {@see set_checkbox_toggleall}
     *
     * @param bool $ismaster
     * @param stdClass|null $row
     * @return checkbox_toggleall|null
     */
    final public function get_checkbox_toggleall(bool $ismaster, ?stdClass $row = null): ?checkbox_toggleall {
        if (!is_callable($this->checkboxcallback)) {
            return null;
        }

        // Generic content for the master checkbox, execute callback for those belonging to each row.
        if ($ismaster) {
            $value = '';
            $label = get_string('selectall');
        } else {
            [$value, $label] = ($this->checkboxcallback)($row);
        }

        return new checkbox_toggleall('report-select-all', $ismaster, [
            'id' => html_writer::random_id(),
            'name' => 'report-select-row[]',
            'value' => $value,
            'label' => $label,
            'labelclasses' => 'accesshide',
        ]);
    }

    /**
     * Override whether to use the default system report filters form, for instance this can be disabled if the UI requires
     * it's own custom filter management form for a specific report
     *
     * @param bool $filterformdefault
     */
    final public function set_filter_form_default(bool $filterformdefault = true): void {
        $this->filterformdefault = $filterformdefault;
    }

    /**
     * Whether to use the default filters form
     *
     * @return bool
     */
    final public function get_filter_form_default(): bool {
        return $this->filterformdefault;
    }

    /**
     * Adds an action to the report
     *
     * @param action $action
     */
    final public function add_action(action $action): void {
        $this->actions[] = $action;
    }

    /**
     * Adds action divider to the report
     *
     */
    final public function add_action_divider(): void {
        $divider = new action_menu_filler();
        // We need to set as not primary action because we just need add an action divider, not a new action item.
        $divider->primary = false;
        $this->actions[] = $divider;
    }

    /**
     * Whether report has any actions
     *
     * @return bool
     */
    final public function has_actions(): bool {
        return !empty($this->actions);
    }

    /**
     * Return report actions
     *
     * @return action|action_menu_filler[]
     */
    final public function get_actions(): array {
        return $this->actions;
    }

    /**
     * Set all report parameters
     *
     * @param array $parameters
     */
    final public function set_parameters(array $parameters): void {
        $this->parameters = $parameters;
    }

    /**
     * Return all report parameters
     *
     * @return array
     */
    final public function get_parameters(): array {
        return $this->parameters;
    }

    /**
     * Return specific report parameter
     *
     * @param string $param
     * @param mixed $default
     * @param string $type
     * @return mixed
     */
    final public function get_parameter(string $param, $default, string $type) {
        if (!array_key_exists($param, $this->parameters)) {
            return $default;
        }

        return clean_param($this->parameters[$param], $type);
    }

    /**
     * Output the report
     *
     * @uses \core_reportbuilder\output\renderer::render_system_report()
     *
     * @return string
     */
    final public function output(): string {
        global $PAGE;

        /** @var \core_reportbuilder\output\renderer $renderer */
        $renderer = $PAGE->get_renderer('core_reportbuilder');
        $report = new \core_reportbuilder\output\system_report($this->get_report_persistent(), $this, $this->parameters);

        return $renderer->render($report);
    }

    /**
     * CSS classes to add to the row. Can be overridden by system reports do define class to be added to output according to
     * content of each row
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return '';
    }

    /**
     * Called before rendering each row. Can be overridden to pre-fetch/create objects and store them in the class, which can
     * later be used in column and action callbacks
     *
     * @param stdClass $row
     */
    public function row_callback(stdClass $row): void {
        return;
    }

    /**
     * Validates access to download this report.
     *
     * @return bool
     */
    final public function can_be_downloaded(): bool {
        return $this->can_view() && $this->is_downloadable();
    }

    /**
     * Return list of column names that will be excluded when table is downloaded. Extending classes should override this method
     * as appropriate
     *
     * @return string[] Array of column unique identifiers
     */
    public function get_exclude_columns_for_download(): array {
        return [];
    }

    /**
     * Set initial sort column and sort direction for the report
     *
     * @param string $uniqueidentifier
     * @param int $sortdirection One of SORT_ASC or SORT_DESC
     * @throws coding_exception
     */
    public function set_initial_sort_column(string $uniqueidentifier, int $sortdirection): void {
        if (!$sortcolumn = $this->get_column($uniqueidentifier)) {
            throw new coding_exception('Unknown column identifier', $uniqueidentifier);
        }

        $this->initialsortcolumn = $sortcolumn;
        $this->initialsortdirection = $sortdirection;
    }

    /**
     * Get initial sort column
     *
     * @return column|null
     */
    public function get_initial_sort_column(): ?column {
        return $this->initialsortcolumn;
    }

    /**
     * Get initial sort column direction
     *
     * @return int
     */
    public function get_initial_sort_direction(): int {
        return $this->initialsortdirection;
    }
}
