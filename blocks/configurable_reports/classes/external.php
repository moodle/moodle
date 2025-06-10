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
 * Configurable Reports - A Moodle block for creating customizable reports
 *
 * @package    block_configurable_reports
 * @copyright  Daniel Neis Araujo <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_configurable_reports;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use context_course;
use context_system;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

/**
 * This is the external API for this component.
 *
 * @copyright  Daniel Neis Araujo <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * get_report_data parameters.
     *
     * @return external_function_parameters
     */
    public static function get_report_data_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'The report id', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'The course id', VALUE_DEFAULT, 1),
            ]
        );
    }

    /**
     * Returns data of given report id.
     *
     * @param int $reportid the report id
     * @param int $courseid course id (default to site)
     * @return array An array with a 'data' JSON string and a 'warnings' string
     */
    public static function get_report_data($reportid, int $courseid = 1): array {
        global $CFG, $DB, $USER;

        $params = self::validate_parameters(
            self::get_report_data_parameters(),
            ['reportid' => $reportid, 'courseid' => $courseid]
        );

        if ($courseid === SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($courseid);
        }

        self::validate_context($context);

        $json = [];
        $warnings = '';
        if (!$report = $DB->get_record('block_configurable_reports', ['id' => $reportid])) {
            $warnings = get_string('reportdoesnotexists', 'block_configurable_reports');
        } else {

            require_once($CFG->dirroot . '/blocks/configurable_reports/locallib.php');
            require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
            require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

            $reportclassname = 'report_' . $report->type;
            $reportclass = new $reportclassname($report);
            if (!$reportclass->check_permissions($USER->id, $context)) {
                $warnings = get_string('badpermissions', 'block_configurable_reports');
            }

            $reportclass->create_report();
            $table = $reportclass->finalreport->table;
            $headers = $table->head;
            foreach ($table->data as $data) {
                $jsonobject = [];
                foreach ($data as $index => $value) {
                    $jsonobject[$headers[$index]] = $value;
                }
                $json[] = $jsonobject;
            }
        }

        return [
            'data' => json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'warnings' => $warnings,
        ];
    }

    /**
     * get_report_data return
     *
     * @return external_single_structure
     */
    public static function get_report_data_returns(): external_single_structure {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_RAW, 'JSON-formatted report data'),
                'warnings' => new external_value(PARAM_TEXT, 'Warning message'),
            ]
        );
    }

}
