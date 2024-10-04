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

namespace enrol_guest\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_system;
use moodle_exception;
use core_text;
use stdClass;

/**
 * This is the external method validating a guest password.
 *
 * @package    enrol_guest
 * @since      Moodle 4.3
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class validate_password extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'instanceid' => new external_value(PARAM_INT, 'instance id of guest enrolment plugin'),
                'password' => new external_value(PARAM_RAW, 'the course password'),
            ]
        );
    }

    /**
     * Perform password validation.
     *
     * If password is correct: keep it as user preference.
     * If password is not correct: remove existing user preference (if any)
     *
     * @throws moodle_exception
     * @param  int $instanceid instance id of guest enrolment plugin
     * @param  string $password the course password
     * @return stdClass validation result info
     */
    public static function execute(int $instanceid, string $password): stdClass {
        global $CFG, $DB;
        require_once($CFG->libdir . '/enrollib.php');

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'instanceid' => $instanceid,
            'password' => $password,
        ]);
        $warnings = [];
        $validated = false;
        $hint = '';

        // Retrieve guest enrolment plugin.
        $enrolplugin = enrol_get_plugin('guest');
        if (empty($enrolplugin)) {
            throw new moodle_exception('invaliddata', 'error');
        }

        self::validate_context(context_system::instance());
        $enrolinstance = $DB->get_record('enrol',
            ['id' => $params['instanceid'], 'status' => ENROL_INSTANCE_ENABLED], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $enrolinstance->courseid], '*', MUST_EXIST);

        if (!\core_course_category::can_view_course_info($course) && !can_access_course($course)) {
            throw new moodle_exception('coursehidden');
        }

        if ($enrolinstance->password) {
            if ($params['password'] === $enrolinstance->password) {
                $validated = true;
                set_user_preference('enrol_guest_ws_password_' . $enrolinstance->id, $params['password']);
            } else {
                // Always unset in case there was something stored.
                unset_user_preference('enrol_guest_ws_password_' . $enrolinstance->id);

                if ($enrolplugin->get_config('showhint')) {
                    $hint = core_text::substr($enrolinstance->password, 0, 1);
                    $hint = get_string('passwordinvalidhint', 'enrol_guest', $hint);
                }
            }
        }

        $result = (object)[
            'validated' => $validated,
            'hint' => $hint,
            'warnings' => $warnings,
        ];

        return $result;
    }

    /**
     * Describes the return information.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'validated' => new external_value(PARAM_BOOL, 'Whether the password was successfully validated'),
            'hint' => new external_value(PARAM_RAW, 'Password hint (if enabled)', VALUE_OPTIONAL),
            'warnings' => new external_warnings(),
        ]);
    }
}
