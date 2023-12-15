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

namespace core_reportbuilder\external\reports;

use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use core_external\external_warnings;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\event\report_viewed;

/**
 * External method to record the viewing of a report
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'reportid' => new external_value(PARAM_INT, 'Report ID'),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $reportid
     * @return array
     */
    public static function execute(int $reportid): array {
        [
            'reportid' => $reportid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
        ]);

        $report = manager::get_report_from_id($reportid);
        self::validate_context($report->get_context());

        $persistent = $report->get_report_persistent();
        permission::require_can_view_report($persistent);

        // Trigger the report viewed event.
        report_viewed::create_from_object($persistent)->trigger();

        return [
           'status' => true,
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
            'status' => new external_value(PARAM_BOOL, 'Success'),
            'warnings' => new external_warnings(),
        ]);
    }
}
