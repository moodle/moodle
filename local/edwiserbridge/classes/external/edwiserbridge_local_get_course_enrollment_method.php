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
 * Provides edwiserbridge_local\external\course_progress_data trait.
 *
 * @package     edwiserbridge_local
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserbridge\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

require_once($CFG->dirroot.'/local/edwiserbridge/classes/class-settings-handler.php');

/**
 * Trait implementing the external function edwiserbridge_local_course_progress_data
 */
trait edwiserbridge_local_get_course_enrollment_method {

    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() parameters
     *
     * @return external_function_parameters
     */
    public static function edwiserbridge_local_get_course_enrollment_method_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get list of active course enrolment methods for current user.
     *
     * @param int $courseid
     * @return array of course enrolment methods
     * @throws moodle_exception
     */
    public static function edwiserbridge_local_get_course_enrollment_method() {
        global $DB,$CFG;

        // self::validate_context(context_system::instance());

        // Check if Moodle manual enrollment plugin is disabled.
        $enrolplugins = explode(',', $CFG->enrol_plugins_enabled);
        if (! in_array('manual', $enrolplugins)) {
            throw new \moodle_exception('plugininactive');
        }

        $response = array();
        $result = $DB->get_records('enrol', array('status'=> 0, 'enrol'=>'manual'), 'sortorder,id');

        foreach ($result as $instance) {
            $response[] = array(
                'courseid' => $instance->courseid,
                'enabled'  => 1
            );
        }

        return $response;
    }

    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() result value
     *
     * @return external_description
     */
    public static function edwiserbridge_local_get_course_enrollment_method_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'enabled' => new external_value(PARAM_INT, 'Returns 1 if manual enrolment is enabled and 0 if disabled.'),
                )
            )
        );
    }
}
