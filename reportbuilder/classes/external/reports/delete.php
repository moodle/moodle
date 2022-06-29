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

use external_api;
use external_function_parameters;
use external_value;
use core_reportbuilder\permission;
use core_reportbuilder\local\helpers\report;
use core_reportbuilder\local\models\report as report_model;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * External method for deleting reports
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete extends external_api {

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
     * @return bool
     */
    public static function execute(int $reportid): bool {
        [
            'reportid' => $reportid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
        ]);

        // Load the report model for deletion. Note we don't use the manager class because it validates the report source,
        // and we want user to be able to delete report, even if it's no longer associated with a valid source.
        $report = new report_model($reportid);

        self::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        return report::delete_report($reportid);
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
