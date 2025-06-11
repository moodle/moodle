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

namespace core_table\external\dynamic;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * Core table external functions.
 *
 * @package    core_table
 * @category   external
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get extends external_api {

    /**
     * Describes the parameters for fetching the table html.
     *
     * @return external_function_parameters
     * @since Moodle 3.9
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'component' => new external_value(
                PARAM_COMPONENT,
                'Component',
                VALUE_REQUIRED
            ),
            'handler' => new external_value(
                // Note: We do not have a PARAM_CLASSNAME which would have been ideal.
                PARAM_ALPHANUMEXT,
                'Handler',
                VALUE_REQUIRED
            ),
            'uniqueid' => new external_value(
                PARAM_ALPHANUMEXT,
                'Unique ID for the container',
                VALUE_REQUIRED
            ),
            'sortdata' => new external_multiple_structure(
                new external_single_structure([
                    'sortby' => new external_value(
                        PARAM_ALPHANUMEXT,
                        'The name of a sortable column',
                        VALUE_REQUIRED
                    ),
                    'sortorder' => new external_value(
                        PARAM_ALPHANUMEXT,
                        'The direction that this column should be sorted by',
                        VALUE_REQUIRED
                    ),
                ]),
                'The combined sort order of the table. Multiple fields can be specified.',
                VALUE_OPTIONAL,
                []
            ),
            'filters' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_ALPHANUM, 'Name of the filter', VALUE_REQUIRED),
                    'jointype' => new external_value(PARAM_INT, 'Type of join for filter values', VALUE_REQUIRED),
                    'values' => new external_multiple_structure(
                        new external_value(PARAM_RAW, 'Filter value'),
                        'The value to filter on',
                        VALUE_REQUIRED
                    ),
                    'filteroptions' => new external_multiple_structure(
                        new external_single_structure([
                            'name' => new external_value(PARAM_ALPHANUM, 'Name of the filter option', VALUE_REQUIRED),
                            'value' => new external_value(PARAM_RAW, 'Value of the filter option', VALUE_REQUIRED),
                        ]),
                        'Additional options for this filter',
                        VALUE_OPTIONAL,
                    ),
                ]),
                'The filters that will be applied in the request',
                VALUE_OPTIONAL
            ),
            'jointype' => new external_value(PARAM_INT, 'Type of join to join all filters together', VALUE_REQUIRED),
            'firstinitial' => new external_value(
                PARAM_RAW,
                'The first initial to sort filter on',
                VALUE_REQUIRED,
                null
            ),
            'lastinitial' => new external_value(
                PARAM_RAW,
                'The last initial to sort filter on',
                VALUE_REQUIRED,
                null
            ),
            'pagenumber' => new external_value(
                PARAM_INT,
                'The page number',
                VALUE_REQUIRED,
                null
            ),
            'pagesize' => new external_value(
                PARAM_INT,
                'The number of records per page',
                VALUE_REQUIRED,
                null
            ),
            'hiddencolumns' => new external_multiple_structure(
                new external_value(
                    PARAM_ALPHANUMEXT,
                    'Name of column',
                    VALUE_REQUIRED,
                    null
                )
            ),
            'resetpreferences' => new external_value(
                PARAM_BOOL,
                'Whether the table preferences should be reset',
                VALUE_REQUIRED,
                null
            ),
        ]);
    }

    /**
     * External function to get the table view content.
     *
     * @param string $component The component.
     * @param string $handler Dynamic table class name.
     * @param string $uniqueid Unique ID for the container.
     * @param array $sortdata The columns and order to sort by
     * @param array $filters The filters that will be applied in the request.
     * @param string $jointype The join type.
     * @param string $firstinitial The first name initial to filter on
     * @param string $lastinitial The last name initial to filter on
     * @param int $pagenumber The page number.
     * @param int $pagesize The number of records.
     * @param string $jointype The join type.
     * @param bool $resetpreferences Whether it is resetting table preferences or not.
     *
     * @return array
     */
    public static function execute(
        string $component,
        string $handler,
        string $uniqueid,
        array $sortdata,
        ?array $filters = null,
        ?string $jointype = null,
        ?string $firstinitial = null,
        ?string $lastinitial = null,
        ?int $pagenumber = null,
        ?int $pagesize = null,
        ?array $hiddencolumns = null,
        ?bool $resetpreferences = null
    ) {
        global $PAGE;

        [
            'component' => $component,
            'handler' => $handler,
            'uniqueid' => $uniqueid,
            'sortdata' => $sortdata,
            'filters' => $filters,
            'jointype' => $jointype,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
            'pagenumber' => $pagenumber,
            'pagesize' => $pagesize,
            'hiddencolumns' => $hiddencolumns,
            'resetpreferences' => $resetpreferences,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'handler' => $handler,
            'uniqueid' => $uniqueid,
            'sortdata' => $sortdata,
            'filters' => $filters,
            'jointype' => $jointype,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
            'pagenumber' => $pagenumber,
            'pagesize' => $pagesize,
            'hiddencolumns' => $hiddencolumns,
            'resetpreferences' => $resetpreferences,
        ]);

        $tableclass = "\\{$component}\\table\\{$handler}";
        if (!class_exists($tableclass)) {
            throw new \UnexpectedValueException("Table handler class {$tableclass} not found. " .
                "Please make sure that your table handler class is under the \\{$component}\\table namespace.");
        }

        if (!is_subclass_of($tableclass, \core_table\dynamic::class)) {
            throw new \UnexpectedValueException("Table handler class {$tableclass} does not support dynamic updating.");
        }

        $filtersetclass = $tableclass::get_filterset_class();
        if (!class_exists($filtersetclass)) {
            throw new \UnexpectedValueException("The filter specified ({$filtersetclass}) is invalid.");
        }

        $filterset = new $filtersetclass();
        $filterset->set_join_type($jointype);
        foreach ($filters as $rawfilter) {
            $filterset->add_filter_from_params(
                $rawfilter['name'],
                $rawfilter['jointype'],
                $rawfilter['values']
            );
        }

        /** @var \core_table\dynamic $instance */
        $instance = new $tableclass($uniqueid);
        $instance->set_filterset($filterset);
        self::validate_context($instance->get_context());
        if (!$instance->has_capability()) {
            throw new \moodle_exception('nopermissiontoaccesspage');
        }

        $instance->set_sortdata($sortdata);
        $alphabet = get_string('alphabet', 'langconfig');

        if ($firstinitial !== null && ($firstinitial === '' || strpos($alphabet, $firstinitial) !== false)) {
            $instance->set_first_initial($firstinitial);
        }

        if ($lastinitial !== null && ($lastinitial === '' || strpos($alphabet, $lastinitial) !== false)) {
            $instance->set_last_initial($lastinitial);
        }

        if ($pagenumber !== null) {
            $instance->set_page_number($pagenumber);
        }

        if ($pagesize === null) {
            $pagesize = 20;
        }

        if ($hiddencolumns !== null) {
            $instance->set_hidden_columns($hiddencolumns);
        }

        if ($resetpreferences === true) {
            $instance->mark_table_to_reset();
        }

        $PAGE->set_url($instance->baseurl);

        ob_start();
        $instance->out($pagesize, true);
        $tablehtml = ob_get_contents();
        ob_end_clean();

        return [
            'html' => $tablehtml,
            'warnings' => []
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 3.9
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'The raw html of the requested table.'),
            'warnings' => new external_warnings()
        ]);
    }
}
