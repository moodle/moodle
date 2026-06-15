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
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use mod_assign\override_manager;

/**
 * Webservice for saving assignment overrides.
 *
 * @package   mod_assign
 * @copyright 2025 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_overrides extends external_api {
    /**
     * Defines parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        $overridestructure = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'ID of existing override (if updating)', VALUE_DEFAULT, null),
            'groupid' => new external_value(PARAM_INT, 'ID of group', VALUE_DEFAULT, null),
            'userid' => new external_value(PARAM_INT, 'ID of user', VALUE_DEFAULT, null),
            'allowsubmissionsfromdate' => new external_value(PARAM_INT, 'Allow submissions from date', VALUE_DEFAULT, null),
            'duedate' => new external_value(PARAM_INT, 'Assignment override due date', VALUE_DEFAULT, null),
            'cutoffdate' => new external_value(PARAM_INT, 'Assignment override cutoff date', VALUE_DEFAULT, null),
            'timelimit' => new external_value(PARAM_INT, 'Assignment override time limit', VALUE_DEFAULT, null),
            'reason' => new external_value(PARAM_RAW, 'Assignment override reason', VALUE_OPTIONAL),
            'reasonformat' => new external_format_value('reason', VALUE_DEFAULT, FORMAT_MOODLE),
        ]);

        return new external_function_parameters([
            // This must be nested in a single structure, because the overrides structure does not play nicely at the top level.
            'data' => new external_single_structure([
                'assignid' => new external_value(PARAM_INT, 'ID of assignment to save overrides to'),
                'overrides' => new external_multiple_structure($overridestructure),
                'recalculatepenalties' => new external_value(
                    PARAM_BOOL,
                    'Recalculate penalties after saving',
                    VALUE_DEFAULT,
                    false
                ),
            ]),
        ]);
    }

    /**
     * Executes webservice function, saving the requested overrides.
     *
     * @param array $data array with assignid key and overrides key containing list of overrides to save.
     * @return array with ids key which contains ids of created/updated overrides.
     */
    public static function execute(array $data): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['data' => $data])['data'];

        // Get the assignment and course module.
        $assign = $DB->get_record('assign', ['id' => $params['assignid']], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $assign->course, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        self::validate_context($context);

        // Create override manager.
        $manager = new override_manager($assign, $context);
        $manager->require_manage_capability();

        // Save all overrides with recalculate flag.
        $recalculatepenalties = $params['recalculatepenalties'] ?? false;
        $ids = $manager->save_overrides($params['overrides'], $recalculatepenalties);

        return ['ids' => $ids];
    }

    /**
     * Defines return type.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'ID of created/updated override')),
        ]);
    }
}
