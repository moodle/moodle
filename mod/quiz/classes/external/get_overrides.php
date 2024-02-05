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
 * Webservice for searching overrides.
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_overrides extends external_api {
    /**
     * Defines parameters for getting quiz overrides.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'ID of quiz to get overrides for'),
        ]);
    }

    /**
     * Executes webservice function, returning quiz overrides.
     *
     * @param int $quizid
     * @return array with overrides key which contains the overrides for the given quiz.
     */
    public static function execute($quizid): array {
        $params = self::validate_parameters(self::execute_parameters(), ['quizid' => $quizid]);
        $manager = quiz_settings::create($params['quizid'])->get_override_manager();
        self::validate_context($manager->context);
        $manager->require_read_capability();
        $overrides = $manager->get_all_overrides();
        return ['overrides' => $overrides];
    }

    /**
     * Defines return type
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $overridedatastructure = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Override ID'),
            'quiz' => new external_value(PARAM_INT, 'Quiz ID'),
            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, null),
            'groupid' => new external_value(PARAM_INT, 'Group ID', VALUE_DEFAULT, null),
            'timeopen' => new external_value(PARAM_INT, 'Override time open value', VALUE_DEFAULT, null),
            'timeclose' => new external_value(PARAM_INT, 'Override time close value', VALUE_DEFAULT, null),
            'timelimit' => new external_value(PARAM_INT, 'Override time limit value', VALUE_DEFAULT, null),
            'attempts' => new external_value(PARAM_INT, 'Override attempts value', VALUE_DEFAULT, null),
            'password' => new external_value(PARAM_TEXT, 'Override password', VALUE_DEFAULT, null),
        ]);

        return new external_single_structure([
            'overrides' => new external_multiple_structure($overridedatastructure),
        ]);
    }
}
