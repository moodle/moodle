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

namespace core_reportbuilder\external\filters;

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_reportbuilder\manager;
use core_reportbuilder\permission;

/**
 * External method for setting report filter values
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'reportid' => new external_value(PARAM_INT, 'Report ID'),
            'parameters' => new external_value(PARAM_RAW, 'JSON encoded report parameters', VALUE_DEFAULT, ''),
            'values' => new external_value(PARAM_RAW, 'JSON encoded filter values'),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $reportid
     * @param string $parameters
     * @param string $values
     * @return bool
     */
    public static function execute(int $reportid, string $parameters, string $values): bool {
        [
            'reportid' => $reportid,
            'parameters' => $parameters,
            'values' => $values,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
            'parameters' => $parameters,
            'values' => $values,
        ]);

        $report = manager::get_report_from_id($reportid, (array) json_decode($parameters));
        self::validate_context($report->get_context());

        // System report permission is implicitly handled, we need to make sure custom report can be viewed.
        $persistent = $report->get_report_persistent();
        if ($persistent->get('type') === $report::TYPE_CUSTOM_REPORT) {
            permission::require_can_view_report($persistent);
        }

        return $report->set_filter_values((array) json_decode($values));
    }

    /**
     * External method return value
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Success');
    }
}
