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

use context_system;
use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_function_parameters;
use core_external\external_warnings;
use stdClass;
use core_reportbuilder\permission;
use core_reportbuilder\external\custom_report_details_exporter;
use core_reportbuilder\local\helpers\audience;
use core_reportbuilder\local\models\report;

/**
 * External method for listing users' custom reports
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class listing extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'page' => new external_value(PARAM_INT, 'Page number', VALUE_DEFAULT, 0),
            'perpage' => new external_value(PARAM_INT, 'Reports per page', VALUE_DEFAULT, 10),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $page
     * @param int $perpage
     * @return array
     */
    public static function execute(int $page = 0, int $perpage = 10): array {
        global $DB, $PAGE;

        [
            'page' => $page,
            'perpage' => $perpage,
        ] = self::validate_parameters(self::execute_parameters(), [
            'page' => $page,
            'perpage' => $perpage,
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        permission::require_can_view_reports_list(null, $context);

        // Filter list of reports by those the user can access.
        [$where, $params] = audience::user_reports_list_access_sql('r');
        $reports = $DB->get_records_sql("
            SELECT r.*
              FROM {" . report::TABLE . "} r
             WHERE r.type = 0 AND {$where}
          ORDER BY r.name, r.id", $params, $page * $perpage, $perpage);

        $output = $PAGE->get_renderer('core');

        return [
            'reports' => array_map(static function(stdClass $report) use ($output): array {
                $exporter = new custom_report_details_exporter(new report(0, $report));
                return (array) $exporter->export($output);
            }, $reports),
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
            'reports' => new external_multiple_structure(custom_report_details_exporter::get_read_structure()),
            'warnings' => new external_warnings(),
        ]);
    }
}
