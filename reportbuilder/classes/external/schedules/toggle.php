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

namespace core_reportbuilder\external\schedules;

use external_api;
use external_function_parameters;
use external_value;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\local\helpers\schedule;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * External method for toggling report schedules
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toggle extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'reportid' => new external_value(PARAM_INT, 'Report ID'),
            'scheduleid' => new external_value(PARAM_INT, 'Schedule ID'),
            'enabled' => new external_value(PARAM_BOOL, 'Schedule enabled'),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $reportid
     * @param int $scheduleid
     * @param bool $enabled
     * @return bool
     */
    public static function execute(int $reportid, int $scheduleid, bool $enabled): bool {
        [
            'reportid' => $reportid,
            'scheduleid' => $scheduleid,
            'enabled' => $enabled,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
            'scheduleid' => $scheduleid,
            'enabled' => $enabled,
        ]);

        $report = manager::get_report_from_id($reportid);

        self::validate_context($report->get_context());
        permission::require_can_edit_report($report->get_report_persistent());

        return schedule::toggle_schedule($reportid, $scheduleid, $enabled);
    }

    /**
     * External method return value
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL);
    }
}
