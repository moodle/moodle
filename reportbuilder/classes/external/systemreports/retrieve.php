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

namespace core_reportbuilder\external\systemreports;

use core_external\external_api;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_warnings;
use core_reportbuilder\system_report_factory;
use core_reportbuilder\external\system_report_data_exporter;

/**
 * External method for retrieving system report content
 *
 * @package     core_reportbuilder
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class retrieve extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'source' => new external_value(PARAM_RAW, 'Report class path'),
            'context' => self::get_context_parameters(),
            'component' => new external_value(PARAM_COMPONENT, 'Report component', VALUE_DEFAULT, ''),
            'area' => new external_value(PARAM_AREA, 'Report area', VALUE_DEFAULT, ''),
            'itemid' => new external_value(PARAM_INT, 'Report item ID', VALUE_DEFAULT, 0),
            'parameters' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_RAW),
                    'value' => new external_value(PARAM_RAW),
                ]),
                'Report parameters', VALUE_DEFAULT, []
            ),
            'page' => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 0),
            'perpage' => new external_value(PARAM_INT, 'Reports per page', VALUE_DEFAULT, 10),
        ]);
    }

    /**
     * External method execution
     *
     * @param string $source
     * @param array $context
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @param array[] $parameters
     * @param int $page
     * @param int $perpage
     * @return array[]
     */
    public static function execute(
        string $source,
        array $context,
        string $component = '',
        string $area = '',
        int $itemid = 0,
        array $parameters = [],
        int $page = 0,
        int $perpage = 10,
    ): array {
        global $PAGE;

        [
            'source' => $source,
            'context' => $context,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
            'parameters' => $parameters,
            'page' => $page,
            'perpage' => $perpage,
        ] = self::validate_parameters(self::execute_parameters(), [
            'source' => $source,
            'context' => $context,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
            'parameters' => $parameters,
            'page' => $page,
            'perpage' => $perpage,
        ]);

        $context = self::get_context_from_params($context);
        self::validate_context($context);

        // Flatten the report parameters.
        $parameters = array_combine(array_column($parameters, 'name'), array_column($parameters, 'value'));

        $report = system_report_factory::create($source, $context, $component, $area, $itemid, $parameters);
        $report->require_can_view();

        $output = $PAGE->get_renderer('core');

        return [
            'data' => (array) (new system_report_data_exporter(null, [
                'report' => $report, 'page' => $page, 'perpage' => $perpage,
            ]))->export($output),
            'warnings' => [],
        ];
    }

    /**
     * External method return value
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'data' => system_report_data_exporter::get_read_structure(),
            'warnings' => new external_warnings(),
        ]);
    }
}
