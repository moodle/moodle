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
 * Table external API.
 *
 * @package    core_table
 * @category   external
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_table\external\dynamic;

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use moodle_url;

/**
 * Core table external functions.
 *
 * @package    core_table
 * @category   external
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch extends external_api {

    /**
     * Describes the parameters for fetching the table html.
     *
     * @return external_function_parameters
     * @since Moodle 3.9
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'handler' => new external_value(
                // Note: We do not have a PARAM_CLASSNAME which would have been ideal.
                PARAM_RAW,
                'Handler',
                VALUE_REQUIRED
            ),
            'uniqueid' => new external_value(
                PARAM_ALPHANUMEXT,
                'Unique ID for the container',
                VALUE_REQUIRED
            ),
            'sortby' => new external_value(
                PARAM_ALPHANUMEXT,
                'The name of a sortable column',
                VALUE_REQUIRED
            ),
            'sortorder' => new external_value(
                PARAM_ALPHANUMEXT,
                'The sort order',
                VALUE_REQUIRED
            ),
            'filters' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_ALPHANUM, 'Name of the filter', VALUE_REQUIRED),
                    'jointype' => new external_value(PARAM_INT, 'Type of join for filter values', VALUE_REQUIRED),
                    'values' => new external_multiple_structure(
                        new external_value(PARAM_RAW, 'Filter value'),
                        'The value to filter on',
                        VALUE_REQUIRED
                    )
                ]),
                'The filters that will be applied in the request',
                VALUE_OPTIONAL
            ),
            'jointype' => new external_value(PARAM_INT, 'Type of join to join all filters together', VALUE_REQUIRED),
            'firstinitial' => new external_value(
                PARAM_ALPHANUMEXT,
                'The first initial to sort filter on',
                VALUE_REQUIRED,
                null
            ),
            'lastinitial' => new external_value(
                PARAM_ALPHANUMEXT,
                'The last initial to sort filter on',
                VALUE_REQUIRED,
                null
            ),
        ]);
    }

    /**
     * External function to fetch a table view.
     *
     * @param string $handler Dynamic table class name.
     * @param string $uniqueid Unique ID for the container.
     * @param string $sortby The name of a sortable column.
     * @param string $sortorder The sort order.
     * @param array $filters The filters that will be applied in the request.
     * @param string $jointype The join type.
     * @param string $firstinitial The first name initial to filter on
     * @param string $lastinitial The last name initial to filter on
     *
     * @return array
     */
    public static function execute(
        string $handler,
        string $uniqueid,
        string $sortby,
        string $sortorder,
        ?array $filters = null,
        ?string $jointype = null,
        ?string $firstinitial = null,
        ?string $lastinitial = null
    ) {

        global $PAGE;

        if (!class_exists($handler) || !is_subclass_of($handler, \core_table\dynamic::class)) {
            throw new \UnexpectedValueException('Unknown table handler, or table handler does not support dynamic updating.');
        }

        [
            'handler' => $handler,
            'uniqueid' => $uniqueid,
            'sortby' => $sortby,
            'sortorder' => $sortorder,
            'filters' => $filters,
            'jointype' => $jointype,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
        ] = self::validate_parameters(self::execute_parameters(), [
            'handler' => $handler,
            'uniqueid' => $uniqueid,
            'sortby' => $sortby,
            'sortorder' => $sortorder,
            'filters' => $filters,
            'jointype' => $jointype,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
        ]);

        $filterset = new \core_user\table\participants_filterset();
        foreach ($filters as $rawfilter) {
            $filterset->add_filter_from_params(
                $rawfilter['name'],
                $rawfilter['jointype'],
                $rawfilter['values']
            );
        }

        $instance = new $handler($uniqueid);
        $instance->set_filterset($filterset);
        $instance->set_sorting($sortby, $sortorder);

        if ($firstinitial !== null) {
            $instance->set_first_initial($firstinitial);
        }

        if ($lastinitial !== null) {
            $instance->set_last_initial($lastinitial);
        }

        $context = $instance->get_context();

        self::validate_context($context);
        $PAGE->set_url($instance->get_base_url());

        ob_start();
        $instance->out(20, true);
        $participanttablehtml = ob_get_contents();
        ob_end_clean();

        return [
            'html' => $participanttablehtml,
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
