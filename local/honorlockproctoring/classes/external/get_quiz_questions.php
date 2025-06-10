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
use external_multiple_structure;
use moodle_exception;

/**
 * Honorlock proctoring external lib.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_quiz_questions extends \external_api {

    /**
     * Get quiz questions parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'quizid' => new external_value(PARAM_INT, 'Quiz Id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Get quiz questions returns
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Was operation successful? '),
                'quizid' => new external_value(PARAM_INT, 'Quiz id modified'),
                'questions' => new external_multiple_structure(
                    new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'Question ID'),
                        'title' => new external_value(PARAM_TEXT, 'Question Title'),
                        'intro' => new external_value(PARAM_RAW, 'Question Text'),
                    ])
                ),
            ],
        );
    }

    /**
     * Obtain the questions for a specific quiz
     *
     * @param int $quizid
     * @return array
     */
    public static function execute(int $quizid): array {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');
        require_once($CFG->dirroot . '/question/engine/bank.php');
        $params = self::validate_parameters(self::execute_parameters(), ['quizid' => $quizid]);
        try {
            $quiz = $DB->get_record('quiz', ['id' => $params['quizid']], '*', MUST_EXIST);
            // Check if the user has permission to view quiz questions.
            $cm = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course);
            $context = \context_module::instance($cm->id);
            self::validate_context($context);

            require_capability('moodle/question:viewall', $context);

            $result = [];
            // Fetch the questions based on the IDs.
            $quizobj = new \quiz($quiz, $cm, $quiz->course);
            $quizobj->preload_questions();
            $quizobj->load_questions();

            // Iterate over the questions.
            foreach ($quizobj->get_questions() as $question) {
                $result[] = [
                    'id' => $question->id,
                    'title' => $question->name,
                    'intro' => $question->questiontext,
                ];
            }
        } catch (moodle_exception $e) {
            return [
                'success' => false,
                'quizid' => $quizid,
                'questions' => [],
            ];
        }
        return ['success' => true, 'quizid' => $quizid, 'questions' => $result];
    }
}
