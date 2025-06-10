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

use core_reportbuilder\manager;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;
use core_reportbuilder\permission;
use core_reportbuilder\external\{custom_report_data_exporter, custom_report_details_exporter};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * External method for retrieving custom report content
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
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
            'reportid' => new external_value(PARAM_INT, 'Report ID'),
            'page' => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 0),
            'perpage' => new external_value(PARAM_INT, 'Reports per page', VALUE_DEFAULT, 10),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $reportid
     * @param int $page
     * @param int $perpage
     * @return array
     */
    public static function execute(int $reportid, int $page = 0, int $perpage = 10): array {
        global $PAGE;

        [
            'reportid' => $reportid,
            'page' => $page,
            'perpage' => $perpage,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
            'page' => $page,
            'perpage' => $perpage,
        ]);

        $report = manager::get_report_from_id($reportid);
        self::validate_context($report->get_context());

        $persistent = $report->get_report_persistent();
        permission::require_can_view_report($persistent);

        $output = $PAGE->get_renderer('core');

        return [
            'details' => (array) (new custom_report_details_exporter($persistent))->export($output),
            'data' => (array) (new custom_report_data_exporter(null, [
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
            'details' => custom_report_details_exporter::get_read_structure(),
            'data' => custom_report_data_exporter::get_read_structure(),
            'warnings' => new external_warnings(),
        ]);
    }
}
