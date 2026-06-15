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
use mod_assign\override_manager;

/**
 * Webservice for deleting assignment overrides.
 *
 * @package   mod_assign
 * @copyright 2025 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_overrides extends external_api {
    /**
     * Defines parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'data' => new external_single_structure([
                'assignid' => new external_value(PARAM_INT, "ID of assignment to delete overrides in"),
                'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'List of overrides to delete')),
                'recalculatepenalties' => new external_value(
                    PARAM_BOOL,
                    'Recalculate penalties after deleting',
                    VALUE_DEFAULT,
                    false
                ),
            ]),
        ]);
    }

    /**
     * Executes webservice function, deleting given overrides.
     *
     * @param array $params array of override parameters
     * @return array with ids key, which contains the ids of the overrides successfully deleted.
     */
    public static function execute($params): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['data' => $params])['data'];

        // Get the assignment and course module.
        $assign = $DB->get_record('assign', ['id' => $params['assignid']], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $assign->course, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        self::validate_context($context);

        // Create override manager.
        $manager = new override_manager($assign, $context);
        $manager->require_manage_capability();

        // Delete the overrides using the manager (handles recalculation internally).
        $recalculatepenalties = $params['recalculatepenalties'] ?? false;
        $manager->delete_overrides_by_id($params['ids'], true, $recalculatepenalties);

        return ['ids' => $params['ids']];
    }

    /**
     * Defines return type.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'ID of deleted override')),
        ]);
    }
}
