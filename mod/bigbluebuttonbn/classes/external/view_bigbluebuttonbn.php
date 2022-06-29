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

namespace mod_bigbluebuttonbn\external;

use context_module;
use external_api;
use external_description;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;
use mod_bigbluebuttonbn\instance;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External service to trigger the course module viewed event and update the module completion status
 *
 * This is mainly used by the mobile application.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_bigbluebuttonbn extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function execute_parameters() {
        return new external_function_parameters([
                'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbn instance id'),
            ]
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $instanceid the bigbluebuttonbn instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     */
    public static function execute($instanceid) {
        global $CFG;
        require_once($CFG->dirroot . "/mod/bigbluebuttonbn/lib.php");

        ['bigbluebuttonbnid' => $instanceid] = self::validate_parameters(
            self::execute_parameters(),
            ['bigbluebuttonbnid' => $instanceid]
        );

        $instance = instance::get_from_instanceid($instanceid);

        if (empty($instance)) {
            return [
                'status' => false,
                'warnings' => [
                    [
                        'item' => 'mod_bigbluebuttonbn',
                        'itemid' => 0,
                        'warningcode' => 'nosuchinstance',
                        'message' => get_string('nosuchinstance', 'mod_bigbluebuttonbn',
                            (object) ['id' => $instanceid, 'entity' => 'bigbluebuttonbn'])
                    ]
                ]
            ];
        }
        $context = context_module::instance($instance->get_cm_id());
        self::validate_context($context);

        require_capability('mod/bigbluebuttonbn:view', $context);
        // Call the bigbluebuttonbn/lib API.
        bigbluebuttonbn_view($instance->get_instance_data(), $instance->get_course(), $instance->get_cm(), $context);
        return [
            'status' => true,
            'warnings' => []
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function execute_returns() {
        return new external_single_structure([
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            ]
        );
    }
}
