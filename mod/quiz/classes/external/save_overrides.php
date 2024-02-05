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

namespace mod_quiz\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use mod_quiz\quiz_settings;

/**
 * Webservice for upserting quiz overrides.
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_overrides extends external_api {
    /**
     * Defines parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        $overridestructure = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'ID of existing override (if updating)', VALUE_DEFAULT, null),
            'groupid' => new external_value(PARAM_INT, 'ID of group', VALUE_DEFAULT, null),
            'userid' => new external_value(PARAM_INT, 'ID of user', VALUE_DEFAULT, null),
            'timeopen' => new external_value(PARAM_INT, 'Quiz override opening timestamp', VALUE_DEFAULT, null),
            'timeclose' => new external_value(PARAM_INT, 'Quiz override closing timestamp', VALUE_OPTIONAL, null),
            'timelimit' => new external_value(PARAM_INT, 'Quiz override time limit', VALUE_DEFAULT, null),
            'attempts' => new external_value(PARAM_INT, 'Quiz override attempt count', VALUE_DEFAULT, null),
            'password' => new external_value(PARAM_TEXT, 'Quiz override password', VALUE_DEFAULT, null),
        ]);

        return new external_function_parameters([
            // This must be nested in a single structure, because the overrides structure does not play nicely at the top level.
            'data' => new external_single_structure([
                'quizid' => new external_value(PARAM_INT, 'ID of quiz to save overrides to'),
                'overrides' => new external_multiple_structure($overridestructure),
            ]),
        ]);
    }

    /**
     * Executes webservice function, saving the requested overrides.
     *
     * @param array $data array with quizid key and overrides key containing list of overrides to save.
     * @return array with ids key which contains ids of created/updated overrides.
     */
    public static function execute($data): array {
        $params = self::validate_parameters(self::execute_parameters(), ['data' => $data])['data'];

        $quizsettings = quiz_settings::create($params['quizid']);
        $manager = $quizsettings->get_override_manager();
        self::validate_context($manager->context);
        $manager->require_manage_capability();

        // Iterate over and save all overrides.
        $ids = array_map(fn($override) => $manager->save_override($override), $params['overrides']);

        return ['ids' => $ids];
    }

    /**
     * Defines return type
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'ID of created/updated override')),
        ]);
    }
}
