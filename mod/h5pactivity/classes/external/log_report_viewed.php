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
 * This is the external method for logging that the h5pactivity was viewed.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.11
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use mod_h5pactivity\local\manager;
use mod_h5pactivity\event\report_viewed;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_warnings;
use moodle_exception;
use context_module;
use stdClass;

/**
 * This is the external method for logging that the h5pactivity was viewed.
 *
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_report_viewed extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'h5pactivityid' => new external_value(PARAM_INT, 'h5p activity instance id'),
                'userid' => new external_value(
                    PARAM_INT,
                    'The user id to log attempt (null means only current user)',
                    VALUE_DEFAULT
                ),
                'attemptid' => new external_value(PARAM_INT, 'The attempt id', VALUE_DEFAULT),
            ]
        );
    }

    /**
     * Logs that the h5pactivity was viewed.
     *
     * @throws  moodle_exception if the user cannot see the report
     * @param  int $h5pactivityid The h5p activity id
     * @param  int|null $userid The user id
     * @param  int|null $attemptid The attempt id
     * @return array of warnings and status result
     */
    public static function execute(int $h5pactivityid, int $userid = null, int $attemptid = null): stdClass {
        $params = external_api::validate_parameters(self::execute_parameters(), [
            'h5pactivityid' => $h5pactivityid,
            'userid' => $userid,
            'attemptid' => $attemptid,
        ]);
        $h5pactivityid = $params['h5pactivityid'];
        $userid = $params['userid'];
        $attemptid = $params['attemptid'];

        // Request and permission validation.
        list ($course, $cm) = get_course_and_cm_from_instance($h5pactivityid, 'h5pactivity');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $manager = manager::create_from_coursemodule($cm);

        $instance = $manager->get_instance();

        // Trigger event.
        $other = [
            'instanceid' => $instance->id,
            'userid' => $userid,
            'attemptid' => $attemptid,
        ];
        $event = report_viewed::create([
            'objectid' => $instance->id,
            'context' => $context,
            'other' => $other,
        ]);
        $event->trigger();

        $result = (object)[
            'status' => true,
            'warnings' => [],
        ];

        return $result;
    }

    /**
     * Describes the report_viewed return value.
     *
     * @return external_single_structure
     * @since Moodle 3.11
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            ]
        );
    }
}
