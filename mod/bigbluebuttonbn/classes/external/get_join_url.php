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

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\exceptions\meeting_join_exception;
use mod_bigbluebuttonbn\meeting;
use restricted_context_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External service to create the meeting (if needed), check user limit, and return the join URL when we can join.
 *
 * This is mainly used by the mobile application.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_join_url extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
            'groupid' => new external_value(PARAM_INT, 'bigbluebuttonbn group id', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Updates a recording
     *
     * @param int $cmid the bigbluebuttonbn course module id
     * @param null|int $groupid
     * @return array (empty array for now)
     */
    public static function execute(
        int $cmid,
        ?int $groupid = 0
    ): array {
        // Validate the cmid ID.
        [
            'cmid' => $cmid,
            'groupid' => $groupid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'groupid' => $groupid,
        ]);
        $result = ['warnings' => []];

        $instance = instance::get_from_cmid($cmid);
        if (empty($instance)) {
            throw new \moodle_exception('nosuchinstance', 'mod_bigbluebuttonbn', null,
                ['entity' => get_string('module', 'course'), 'id' => $cmid]);
        }
        // Validate the groupid.
        if (!groups_group_visible($groupid, $instance->get_course(), $instance->get_cm())) {
            throw new restricted_context_exception();
        }
        $instance->set_group_id($groupid);

        self::validate_context($instance->get_context());

        try {
            $result['join_url'] = meeting::join_meeting($instance);
        } catch (meeting_join_exception $e) {
            $result['warnings'][] = [
                'item' => 'mod_bigbluebuttonbn',
                'itemid' => $instance->get_instance_id(),
                'warningcode' => $e->errorcode,
                'message' => $e->getMessage()
            ];
        }
        return $result;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'join_url' => new external_value(PARAM_RAW, 'Can join session', VALUE_OPTIONAL),
            'warnings' => new \external_warnings()
        ]);
    }
}
