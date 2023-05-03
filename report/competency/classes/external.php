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

namespace report_competency;

use context_course;
use core_competency\external\user_competency_course_exporter;
use core_course\external\course_summary_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_user\external\user_summary_exporter;
use tool_lp\external\competency_summary_exporter;

/**
 * This is the external API for this report.
 *
 * @package    report_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of data_for_competency_frameworks_manage_page() parameters.
     *
     * @return external_function_parameters
     */
    public static function data_for_report_parameters() {
        $courseid = new external_value(
            PARAM_INT,
            'The course id',
            VALUE_REQUIRED
        );
        $userid = new external_value(
            PARAM_INT,
            'The user id',
            VALUE_REQUIRED
        );
        $moduleid = new external_value(
            PARAM_INT,
            'The module id',
            VALUE_REQUIRED
        );
        $params = array(
            'courseid' => $courseid,
            'userid' => $userid,
            'moduleid' => $moduleid,
        );
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the report.
     *
     * @param int $courseid The course id
     * @param int $userid The user id
     * @param int $moduleid The module id
     * @return \stdClass
     */
    public static function data_for_report($courseid, $userid, $moduleid) {
        global $PAGE;

        $params = self::validate_parameters(
            self::data_for_report_parameters(),
            array(
                'courseid' => $courseid,
                'userid' => $userid,
                'moduleid' => $moduleid
            )
        );
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        if (!is_enrolled($context, $params['userid'], 'moodle/competency:coursecompetencygradable')) {
            throw new coding_exception('invaliduser');
        }

        $renderable = new output\report($params['courseid'], $params['userid'], $params['moduleid']);
        $renderer = $PAGE->get_renderer('report_competency');

        $data = $renderable->export_for_template($renderer);

        return $data;
    }

    /**
     * Returns description of data_for_report() result value.
     *
     * @return external_description
     */
    public static function data_for_report_returns() {
        return new external_single_structure(array (
            'courseid' => new external_value(PARAM_INT, 'Course id'),
            'user' => user_summary_exporter::get_read_structure(),
            'course' => course_summary_exporter::get_read_structure(),
            'usercompetencies' => new external_multiple_structure(
                new external_single_structure(array(
                    'usercompetencycourse' => user_competency_course_exporter::get_read_structure(),
                    'competency' => competency_summary_exporter::get_read_structure()
                ))
            ),
            'pushratingstouserplans' => new external_value(PARAM_BOOL, 'True if rating is push to user plans')
        ));
    }

}
