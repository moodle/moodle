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
 * This is the external method for getting access information for a h5p activity.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_warnings;
use context_module;

/**
 * This is the external method for getting access information for a h5p activity.
 *
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_h5pactivity_access_information extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'h5pactivityid' => new external_value(PARAM_INT, 'h5p activity instance id')
            ]
        );
    }

    /**
     * Return access information for a given h5p activity.
     *
     * @param  int $h5pactivityid The h5p activity id.
     * @return array of warnings and the access information
     * @since Moodle 3.9
     * @throws  moodle_exception
     */
    public static function execute(int $h5pactivityid): array {
        global $DB;

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'h5pactivityid' => $h5pactivityid
        ]);

        // Request and permission validation.
        $h5pactivity = $DB->get_record('h5pactivity', ['id' => $params['h5pactivityid']], '*', MUST_EXIST);

        list($course, $cm) = get_course_and_cm_from_instance($h5pactivity, 'h5pactivity');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $result = [];
        // Return all the available capabilities.
        $capabilities = load_capability_def('mod_h5pactivity');
        foreach ($capabilities as $capname => $capdata) {
            $field = 'can' . str_replace('mod/h5pactivity:', '', $capname);
            $result[$field] = has_capability($capname, $context);
        }

        $result['warnings'] = [];
        return $result;
    }

    /**
     * Describes the get_h5pactivity_access_information return value.
     *
     * @return external_single_structure
     * @since Moodle 3.9
     */
    public static function execute_returns() {

        $structure = [
            'warnings' => new external_warnings()
        ];

        $capabilities = load_capability_def('mod_h5pactivity');
        foreach ($capabilities as $capname => $capdata) {
            $field = 'can' . str_replace('mod/h5pactivity:', '', $capname);
            $structure[$field] = new external_value(PARAM_BOOL, 'Whether the user has the capability ' . $capname . ' allowed.',
                VALUE_OPTIONAL);
        }

        return new external_single_structure($structure);
    }
}