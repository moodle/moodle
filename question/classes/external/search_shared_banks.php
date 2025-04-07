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

namespace core_question\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_question\local\bank\question_bank_helper;
use core\context;

/**
 * Return a filtered of the user's shared question banks
 *
 * For use with core_question/question_banks_datasource as a source for autocomplete suggestions.
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_shared_banks extends external_api {

    /**
     * @var int The maximum number of banks to return.
     */
    const MAX_RESULTS = 20;

    /**
     * Define parameters for external function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'contextid' => new external_value(PARAM_INT, 'The current context ID.'),
                'search' => new external_value(PARAM_TEXT, 'Search terms by which to filter the shared banks.', default: ''),
            ]
        );
    }

    /**
     * Return ID and formatted name of question banks accessible by the user, in courses other than the one $contextid is in.
     *
     * @param int $contextid Context ID of the current activity
     * @param string $search String to filter results by question bank name
     * @return array
     */
    public static function execute(int $contextid, string $search = ''): array {
        [
            'contextid' => $contextid,
            'search' => $search,
        ] = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'search' => $search,
        ]);

        $modulecontext = context::instance_by_id($contextid);
        $courseid = $modulecontext->get_parent_context()->instanceid;

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            notincourseids: [$courseid],
            havingcap: ['moodle/question:useall', 'moodle/question:usemine'],
            filtercontext: $modulecontext,
            search: $search,
            limit: self::MAX_RESULTS + 1, // Return up to 1 extra result, so we know there are more.
        );

        $suggestions = array_map(
            fn($sharedbank) => [
                'value' => $sharedbank->modid,
                'label' => $sharedbank->coursenamebankname,
            ],
            $sharedbanks,
        );

        if (count($suggestions) > self::MAX_RESULTS) {
            // If there are too many results, replace the last one with a placeholder.
            $suggestions[array_key_last($suggestions)] = [
                'value' => 0,
                'label' => get_string('otherquestionbankstoomany', 'question', self::MAX_RESULTS),
            ];
        }

        return [
            'sharedbanks' => $suggestions,
        ];
    }

    /**
     * Define return values.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'sharedbanks' => new external_multiple_structure(
                new external_single_structure([
                    'value' => new external_value(PARAM_INT, 'Module ID of the shared bank.'),
                    'label' => new external_value(PARAM_TEXT, 'Formatted bank name'),
                ]),
                'List of shared banks',
            ),
        ]);
    }
}
