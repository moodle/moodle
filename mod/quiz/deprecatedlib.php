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

/**
 * List of deprecated mod_quiz functions.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\access_manager;
use mod_quiz\quiz_settings;
use mod_quiz\task\update_overdue_attempts;

/**
 * @deprecated since Moodle 3.11
 */
function quiz_get_completion_state() {
    $completionclass = \mod_quiz\completion\custom_completion::class;
    throw new coding_exception(__FUNCTION__ . "() has been removed, please use the '{$completionclass}' class instead");
}

/**
 * Retrieves tag information for the given list of quiz slot ids.
 * Currently the only slots that have tags are random question slots.
 *
 * Example:
 * If we have 3 slots with id 1, 2, and 3. The first slot has two tags, the second
 * has one tag, and the third has zero tags. The return structure will look like:
 * [
 *      1 => [
 *          quiz_slot_tags.id => { ...tag data... },
 *          quiz_slot_tags.id => { ...tag data... },
 *      ],
 *      2 => [
 *          quiz_slot_tags.id => { ...tag data... },
 *      ],
 *      3 => [],
 * ]
 *
 * @param int[] $slotids The list of id for the quiz slots.
 * @return array[] List of quiz_slot_tags records indexed by slot id.
 * @deprecated since Moodle 4.0
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function quiz_retrieve_tags_for_slot_ids($slotids) {
    debugging('Method quiz_retrieve_tags_for_slot_ids() is deprecated, ' .
        'see filtercondition->tags from the question_set_reference table.', DEBUG_DEVELOPER);
    global $DB;
    if (empty($slotids)) {
        return [];
    }

    $slottags = $DB->get_records_list('quiz_slot_tags', 'slotid', $slotids);
    $tagsbyid = core_tag_tag::get_bulk(array_filter(array_column($slottags, 'tagid')), 'id, name');
    $tagsbyname = false; // It will be loaded later if required.
    $emptytagids = array_reduce($slotids, function($carry, $slotid) {
        $carry[$slotid] = [];
        return $carry;
    }, []);

    return array_reduce(
        $slottags,
        function($carry, $slottag) use ($slottags, $tagsbyid, $tagsbyname) {
            if (isset($tagsbyid[$slottag->tagid])) {
                // Make sure that we're returning the most updated tag name.
                $slottag->tagname = $tagsbyid[$slottag->tagid]->name;
            } else {
                if ($tagsbyname === false) {
                    // We were hoping that this query could be avoided, but life
                    // showed its other side to us!
                    $tagcollid = core_tag_area::get_collection('core', 'question');
                    $tagsbyname = core_tag_tag::get_by_name_bulk(
                        $tagcollid,
                        array_column($slottags, 'tagname'),
                        'id, name'
                    );
                }
                if (isset($tagsbyname[$slottag->tagname])) {
                    // Make sure that we're returning the current tag id that matches
                    // the given tag name.
                    $slottag->tagid = $tagsbyname[$slottag->tagname]->id;
                } else {
                    // The tag does not exist anymore (neither the tag id nor the tag name
                    // matches an existing tag).
                    // We still need to include this row in the result as some callers might
                    // be interested in these rows. An example is the editing forms that still
                    // need to display tag names even if they don't exist anymore.
                    $slottag->tagid = null;
                }
            }

            $carry[$slottag->slotid][$slottag->id] = $slottag;
            return $carry;
        },
        $emptytagids
    );
}

/**
 * Verify that the question exists, and the user has permission to use it.
 *
 * @deprecated in 4.1 use mod_quiz\structure::has_use_capability(...) instead.
 *
 * @param stdClass $quiz the quiz settings.
 * @param int $slot which question in the quiz to test.
 * @return bool whether the user can use this question.
 */
function quiz_has_question_use($quiz, $slot) {
    global $DB;

    debugging('Deprecated. Please use mod_quiz\structure::has_use_capability instead.');

    $sql = 'SELECT q.*
              FROM {quiz_slots} slot
              JOIN {question_references} qre ON qre.itemid = slot.id
              JOIN {question_bank_entries} qbe ON qbe.id = qre.questionbankentryid
              JOIN {question_versions} qve ON qve.questionbankentryid = qbe.id
              JOIN {question} q ON q.id = qve.questionid
             WHERE slot.quizid = ?
               AND slot.slot = ?
               AND qre.component = ?
               AND qre.questionarea = ?';

    $question = $DB->get_record_sql($sql, [$quiz->id, $slot, 'mod_quiz', 'slot']);

    if (!$question) {
        return false;
    }
    return question_has_capability_on($question, 'use');
}

/**
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
class mod_quiz_overdue_attempt_updater {

    /**
     * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts. that was.
     */
    public function update_overdue_attempts($timenow, $processto) {
        debugging('mod_quiz_overdue_attempt_updater has been deprecated. The code wsa moved to ' .
                'mod_quiz\task\update_overdue_attempts.');
        return (new update_overdue_attempts())->update_all_overdue_attempts((int) $timenow, (int) $processto);
    }

    /**
     * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts.
     */
    public function get_list_of_overdue_attempts($processto) {
        debugging('mod_quiz_overdue_attempt_updater has been deprecated. The code wsa moved to ' .
                'mod_quiz\task\update_overdue_attempts.');
        return (new update_overdue_attempts())->get_list_of_overdue_attempts((int) $processto);
    }
}

/**
 * Class for quiz exceptions. Just saves a couple of arguments on the
 * constructor for a moodle_exception.
 *
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 * @deprecated since Moodle 4.2. Please just use moodle_exception.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
class moodle_quiz_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param quiz_settings $quizobj the quiz the error relates to.
     * @param string $errorcode The name of the string from error.php to print.
     * @param mixed $a Extra words and phrases that might be required in the error string.
     * @param string $link The url where the user will be prompted to continue.
     *      If no url is provided the user will be directed to the site index page.
     * @param string|null $debuginfo optional debugging information.
     * @deprecated since Moodle 4.2. Please just use moodle_exception.
     */
    public function __construct($quizobj, $errorcode, $a = null, $link = '', $debuginfo = null) {
        debugging('Class moodle_quiz_exception is deprecated. ' .
                'Please use a standard moodle_exception instead.', DEBUG_DEVELOPER);
        if (!$link) {
            $link = $quizobj->view_url();
        }
        parent::__construct($errorcode, 'quiz', $link, $a, $debuginfo);
    }
}

/**
 * Update the sumgrades field of the quiz. This needs to be called whenever
 * the grading structure of the quiz is changed. For example if a question is
 * added or removed, or a question weight is changed.
 *
 * You should call {@see quiz_delete_previews()} before you call this function.
 *
 * @param stdClass $quiz a quiz.
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_quiz_sumgrades.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_update_sumgrades($quiz) {
    debugging('quiz_update_sumgrades is deprecated. ' .
        'Please use a standard grade_calculator::recompute_quiz_sumgrades instead.', DEBUG_DEVELOPER);
    quiz_settings::create($quiz->id)->get_grade_calculator()->recompute_quiz_sumgrades();
}

/**
 * Update the sumgrades field of the attempts at a quiz.
 *
 * @param stdClass $quiz a quiz.
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_all_attempt_sumgrades.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_update_all_attempt_sumgrades($quiz) {
    debugging('quiz_update_all_attempt_sumgrades is deprecated. ' .
        'Please use a standard grade_calculator::recompute_all_attempt_sumgrades instead.', DEBUG_DEVELOPER);
    quiz_settings::create($quiz->id)->get_grade_calculator()->recompute_all_attempt_sumgrades();
}

/**
 * Update the final grade at this quiz for all students.
 *
 * This function is equivalent to calling quiz_save_best_grade for all
 * users, but much more efficient.
 *
 * @param stdClass $quiz the quiz settings.
 * @deprecated since Moodle 4.2. Please use grade_calculator::recompute_all_final_grades.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_update_all_final_grades($quiz) {
    debugging('quiz_update_all_final_grades is deprecated. ' .
        'Please use a standard grade_calculator::recompute_all_final_grades instead.', DEBUG_DEVELOPER);
    quiz_settings::create($quiz->id)->get_grade_calculator()->recompute_all_final_grades();
}

/**
 * The quiz grade is the maximum that student's results are marked out of. When it
 * changes, the corresponding data in quiz_grades and quiz_feedback needs to be
 * rescaled. After calling this function, you probably need to call
 * quiz_update_all_attempt_sumgrades, grade_calculator::recompute_all_final_grades();
 * quiz_update_grades. (At least, that is what this comment has said for years, but
 * it seems to call recompute_all_final_grades itself.)
 *
 * @param float $newgrade the new maximum grade for the quiz.
 * @param stdClass $quiz the quiz we are updating. Passed by reference so its
 *      grade field can be updated too.
 * @return bool indicating success or failure.
 * @deprecated since Moodle 4.2. Please use grade_calculator::update_quiz_maximum_grade.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_set_grade($newgrade, $quiz) {
    debugging('quiz_set_grade is deprecated. ' .
        'Please use a standard grade_calculator::update_quiz_maximum_grade instead.', DEBUG_DEVELOPER);
    quiz_settings::create($quiz->id)->get_grade_calculator()->update_quiz_maximum_grade($newgrade);
    return true;
}

/**
 * Save the overall grade for a user at a quiz in the quiz_grades table
 *
 * @param stdClass $quiz The quiz for which the best grade is to be calculated and then saved.
 * @param int $userid The userid to calculate the grade for. Defaults to the current user.
 * @param array $attempts The attempts of this user. Useful if you are
 * looping through many users. Attempts can be fetched in one master query to
 * avoid repeated querying.
 * @return bool Indicates success or failure.
 * @deprecated since Moodle 4.2. Please use grade_calculator::update_quiz_maximum_grade.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_save_best_grade($quiz, $userid = null, $attempts = []) {
    debugging('quiz_save_best_grade is deprecated. ' .
        'Please use a standard grade_calculator::recompute_final_grade instead.', DEBUG_DEVELOPER);
    quiz_settings::create($quiz->id)->get_grade_calculator()->recompute_final_grade($userid, $attempts);
    return true;
}

/**
 * Calculate the overall grade for a quiz given a number of attempts by a particular user.
 *
 * @param stdClass $quiz    the quiz settings object.
 * @param array $attempts an array of all the user's attempts at this quiz in order.
 * @return float          the overall grade
 * @deprecated since Moodle 4.2. No direct replacement.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_calculate_best_grade($quiz, $attempts) {
    debugging('quiz_calculate_best_grade is deprecated with no direct replacement. It was only used ' .
        'in one place in the quiz code so this logic is now private to grade_calculator.', DEBUG_DEVELOPER);

    switch ($quiz->grademethod) {

        case QUIZ_ATTEMPTFIRST:
            $firstattempt = reset($attempts);
            return $firstattempt->sumgrades;

        case QUIZ_ATTEMPTLAST:
            $lastattempt = end($attempts);
            return $lastattempt->sumgrades;

        case QUIZ_GRADEAVERAGE:
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                if (!is_null($attempt->sumgrades)) {
                    $sum += $attempt->sumgrades;
                    $count++;
                }
            }
            if ($count == 0) {
                return null;
            }
            return $sum / $count;

        case QUIZ_GRADEHIGHEST:
        default:
            $max = null;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                }
            }
            return $max;
    }
}

/**
 * Return the attempt with the best grade for a quiz
 *
 * Which attempt is the best depends on $quiz->grademethod. If the grade
 * method is GRADEAVERAGE then this function simply returns the last attempt.
 * @return stdClass         The attempt with the best grade
 * @param stdClass $quiz    The quiz for which the best grade is to be calculated
 * @param array $attempts An array of all the attempts of the user at the quiz
 * @deprecated since Moodle 4.2. No direct replacement.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
function quiz_calculate_best_attempt($quiz, $attempts) {
    debugging('quiz_calculate_best_attempt is deprecated with no direct replacement. ' .
        'It was not used anywhere!', DEBUG_DEVELOPER);

    switch ($quiz->grademethod) {

        case QUIZ_ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt;
            }
            break;

        case QUIZ_GRADEAVERAGE: // We need to do something with it.
        case QUIZ_ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt;
            }
            return $final;

        default:
        case QUIZ_GRADEHIGHEST:
            $max = -1;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                    $maxattempt = $attempt;
                }
            }
            return $maxattempt;
    }
}
