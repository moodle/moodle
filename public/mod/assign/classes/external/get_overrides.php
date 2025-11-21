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

namespace mod_assign\external;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\util;
use mod_assign\override_manager;

/**
 * Webservice for getting assignment overrides.
 *
 * @package   mod_assign
 * @copyright 2025 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_overrides extends external_api {
    /**
     * Defines parameters for getting assignment overrides.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'assignid' => new external_value(PARAM_INT, 'ID of assignment to get overrides for'),
        ]);
    }

    /**
     * Executes webservice function, returning assignment overrides.
     *
     * @param int $assignid
     * @return array with overrides key which contains the overrides for the given assignment.
     */
    public static function execute($assignid): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['assignid' => $assignid]);

        // Get the assignment and course module.
        $assign = $DB->get_record('assign', ['id' => $params['assignid']], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $assign->course, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        self::validate_context($context);

        // Create override manager.
        $manager = new override_manager($assign, $context);
        $manager->require_manage_capability();

        // Get all overrides that the user can access.
        $filteredoverrides = $manager->get_accessible_overrides();

        // Format text fields for external output.
        $formattedoverrides = [];
        foreach ($filteredoverrides as $override) {
            $override = (array) $override;
            if (!empty($override['reason'])) {
                [$override['reason'], $override['reasonformat']] = util::format_text(
                    $override['reason'],
                    $override['reasonformat'] ?? FORMAT_MOODLE,
                    $context
                );
            }
            $formattedoverrides[] = $override;
        }

        return ['overrides' => $formattedoverrides];
    }

    /**
     * Defines return type.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $overridedatastructure = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Override ID'),
            'assignid' => new external_value(PARAM_INT, 'Assignment ID'),
            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, null),
            'groupid' => new external_value(PARAM_INT, 'Group ID', VALUE_DEFAULT, null),
            'sortorder' => new external_value(PARAM_INT, 'Sort order', VALUE_DEFAULT, null),
            'allowsubmissionsfromdate' => new external_value(PARAM_INT, 'Allow submissions from date', VALUE_DEFAULT, null),
            'duedate' => new external_value(PARAM_INT, 'Override due date', VALUE_DEFAULT, null),
            'cutoffdate' => new external_value(PARAM_INT, 'Override cutoff date', VALUE_DEFAULT, null),
            'timelimit' => new external_value(PARAM_INT, 'Override time limit', VALUE_DEFAULT, null),
            'reason' => new external_value(PARAM_RAW, 'Override reason', VALUE_DEFAULT, null),
            'reasonformat' => new external_value(PARAM_INT, 'Override reason format', VALUE_DEFAULT, 0),
        ]);

        return new external_single_structure([
            'overrides' => new external_multiple_structure($overridedatastructure),
        ]);
    }
}
