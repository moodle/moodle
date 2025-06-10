<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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

namespace local_honorlockproctoring\external;

// @codeCoverageIgnoreStart
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
// @codeCoverageIgnoreEnd

use external_function_parameters;
use external_value;
use external_single_structure;
use moodle_exception;

/**
 * Honorlock proctoring external lib.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_quiz extends \external_api {

    /**
     * @var string[]
     */
    private static $allowedquizupdates = [
        'password' => "password",
    ];

    /**
     * Update the quiz value parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'quizid' => new external_value(PARAM_INT, 'Quiz Id', VALUE_REQUIRED),
                'overwritevalues' => new external_single_structure(
                    [
                        'password' => new external_value(PARAM_TEXT, 'password value'),
                    ],
                    'values to overwrite',
                    VALUE_REQUIRED
                ),
            ]
        );
    }

    /**
     * Update quiz values returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Was operation successful? '),
                'quizid' => new external_value(PARAM_INT, 'Quiz id modified'),
                'updatedvalues' => new external_single_structure(
                    [
                        'password' => new external_value(PARAM_BOOL, 'changed?'),
                    ]
                ),
            ],
        );
    }

    /**
     * Update quiz values.
     *
     * @param int $quizid
     * @param array $overwritevalues
     * @return array
     */
    public static function execute(int $quizid, array $overwritevalues): array {
        global $DB;
        $updatedvalues = [];
        try {
            $quiz = $DB->get_record('quiz', ['id' => $quizid], '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course);
            $context = \context_module::instance($cm->id);
            self::validate_context($context);
        } catch (moodle_exception $e) {
            return [
                'success' => false,
                'quizid' => $quizid,
                'updatedvalues' => $updatedvalues,
            ];
        }
        foreach ($overwritevalues as $key => $value) {
            if (!isset(self::$allowedquizupdates[$key])) {
                $updatedvalues[$key] = false;
                continue;
            }

            $DB->set_field('quiz', self::$allowedquizupdates[$key], $value, [
               'id' => $quizid,
            ]);

            $updatedvalues[$key] = true;
        }

        return [
            'success' => true,
            'quizid' => $quizid,
            'updatedvalues' => $updatedvalues,
        ];
    }

}
