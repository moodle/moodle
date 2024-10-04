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

use core\exception\coding_exception;
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
                'contextid' => new external_value(PARAM_INT, 'The current context ID for applying text filters to bank names.'),
                'search' => new external_value(PARAM_TEXT, 'Search terms by which to filter the shared banks.', default: ''),
                'requiredcapabilities' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Capability'),
                    'Array of abbreviated "moodle/question:" capabilities that the user must have at least one of in the context ' .
                        'of each matching question bank. Valid options are "add", "managecategory", "flag", "config", "edit", ' .
                        '"view", "use", "move" or "tag". For "edit", "view", "use", "move" and "tag", the -all and -mine ' .
                        "suffixed versions of the capabilty will both be checked.",
                    VALUE_DEFAULT,
                    ['use'],
                ),
            ]
        );
    }

    /**
     * Expand a list of abbreviated capaibilities into an array of full capability strings.
     *
     * Each abbreviation must match a capability with the 'moodle/question:' prefix. Capabilities that have an "all" and "mine"
     * variant will have both variants included in the returned array.
     *
     * These abbreviations are copied from {@see question_has_capability_on()}
     *
     * @param array $abbreviations Abbreviated capabilities. Must match capabilities with the 'moodle/question:' prefix.
     * @return array The expanded capabilities
     */
    protected static function expand_capabilities(array $abbreviations): array {
        $capabilitieswithallandmine = ['edit', 'view', 'use', 'move', 'tag'];
        $prefix = 'moodle/question:';
        $suffixes = ['all', 'mine'];
        $capabilities = [];
        foreach ($abbreviations as $abbreviation) {
            if (in_array($abbreviation, $capabilitieswithallandmine)) {
                foreach ($suffixes as $suffix) {
                    $capability = $prefix . $abbreviation . $suffix;
                    if (is_null(get_capability_info($capability))) {
                        throw new coding_exception("Capability {$capability} does not exist.");
                    }
                    $capabilities[] = $capability;
                }
            } else {
                $capability = $prefix . $abbreviation;
                if (is_null(get_capability_info($capability))) {
                    throw new coding_exception("Capability {$capability} does not exist.");
                }
                $capabilities[] = $capability;
            }
        }
        return $capabilities;
    }

    /**
     * Return ID and formatted name of question banks accessible by the user, in courses other than the one $contextid is in.
     *
     * @param int $contextid Context ID of the current activity
     * @param string $search String to filter results by question bank name
     * @param array $requiredcapabilities List of abbreviated capabilities to check, {@see self::expand_capabilities()}
     * @return array
     */
    public static function execute(
        int $contextid,
        string $search = '',
        array $requiredcapabilities = ['use'],
    ): array {
        [
            'contextid' => $contextid,
            'search' => $search,
            'requiredcapabilities' => $requiredcapabilities,
        ] = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'search' => $search,
            'requiredcapabilities' => $requiredcapabilities,
        ]);

        $modulecontext = context::instance_by_id($contextid);
        self::validate_context($modulecontext);

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            havingcap: self::expand_capabilities($requiredcapabilities),
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
                    'value' => new external_value(PARAM_INT, 'Course Module ID of the shared bank.'),
                    'label' => new external_value(PARAM_TEXT, 'Formatted bank name'),
                ]),
                'List of shared banks',
            ),
        ]);
    }
}
