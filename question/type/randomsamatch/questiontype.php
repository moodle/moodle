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
 * Question type class for the randomsamatch question type.
 *
 * @package    qtype
 * @subpackage randomsamatch
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * The randomsamatch question type class.
 *
 * TODO: Make sure short answer questions chosen by a randomsamatch question
 * can not also be used by a random question
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch extends question_type {
    const MAX_SUBQUESTIONS = 10;

    public function requires_qtypes() {
        return array('shortanswer', 'match');
    }

    public function is_usable_by_random() {
        return false;
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_randomsamatch',
                array('question' => $question->id), '*', MUST_EXIST);

        // This could be included as a flag in the database. It's already
        // supported by the code.
        // Recurse subcategories: 0 = no recursion, 1 = recursion .
        $question->options->subcats = 1;
        return true;

    }

    public function save_question_options($question) {
        global $DB;
        $options = new stdClass();
        $options->question = $question->id;
        $options->choose = $question->choose;

        if (2 > $question->choose) {
            $result = new stdClass();
            $result->error = "At least two shortanswer questions need to be chosen!";
            return $result;
        }

        if ($existing = $DB->get_record('question_randomsamatch',
                array('question' => $options->question))) {
            $options->id = $existing->id;
            $DB->update_record('question_randomsamatch', $options);
        } else {
            $DB->insert_record('question_randomsamatch', $options);
        }
        return true;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_randomsamatch', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // Choose a random shortanswer question from the category:
        // We need to make sure that no question is used more than once in the
        // quiz. Therfore the following need to be excluded:
        // 1. All questions that are explicitly assigned to the quiz
        // 2. All random questions
        // 3. All questions that are already chosen by an other random question.
        global $QTYPES, $OUTPUT, $USER;
        if (!isset($cmoptions->questionsinuse)) {
            $cmoptions->questionsinuse = $cmoptions->questions;
        }

        if ($question->options->subcats) {
            // Recurse into subcategories.
            $categorylist = question_categorylist($question->category);
        } else {
            $categorylist = array($question->category);
        }

        $saquestions = $this->get_sa_candidates($categorylist, $cmoptions->questionsinuse);

        $count  = count($saquestions);
        $wanted = $question->options->choose;

        if ($count < $wanted) {
            $question->questiontext = "Insufficient selection options are
                available for this question, therefore it is not available in  this
                quiz. Please inform your teacher.";
            // Treat this as a description from this point on.
            $question->qtype = 'description';
            return true;
        }

        $saquestions =
         draw_rand_array($saquestions, $question->options->choose); // From bug 1889.

        foreach ($saquestions as $key => $wrappedquestion) {
            if (!$QTYPES[$wrappedquestion->qtype]
             ->get_question_options($wrappedquestion)) {
                return false;
            }

            // Now we overwrite the $question->options->answers field to only
            // *one* (the first) correct answer. This loop can be deleted to
            // take all answers into account (i.e. put them all into the
            // drop-down menu.
            $foundcorrect = false;
            foreach ($wrappedquestion->options->answers as $answer) {
                if ($foundcorrect || $answer->fraction != 1.0) {
                    unset($wrappedquestion->options->answers[$answer->id]);
                } else if (!$foundcorrect) {
                    $foundcorrect = true;
                }
            }

            if (!$QTYPES[$wrappedquestion->qtype]
             ->create_session_and_responses($wrappedquestion, $state, $cmoptions,
             $attempt)) {
                return false;
            }
            $wrappedquestion->name_prefix = $question->name_prefix;
            $wrappedquestion->maxgrade    = $question->maxgrade;
            $cmoptions->questionsinuse .= ",$wrappedquestion->id";
            $state->options->subquestions[$key] = clone($wrappedquestion);
        }

        // Shuffle the answers (Do this always because this is a random question type).
        $subquestionids = array_values(array_map(create_function('$val',
         'return $val->id;'), $state->options->subquestions));
        $subquestionids = swapshuffle($subquestionids);

        // Create empty responses.
        foreach ($subquestionids as $val) {
            $state->responses[$val] = '';
        }
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        global $DB;
        global $QTYPES, $OUTPUT;
        static $wrappedquestions = array();
        if (empty($state->responses[''])) {
            $question->questiontext = "Insufficient selection options are
             available for this question, therefore it is not available in  this
             quiz. Please inform your teacher.";
            // Treat this as a description from this point on.
            $question->qtype = 'description';
        } else {
            $responses = explode(',', $state->responses['']);
            $responses = array_map(create_function('$val',
             'return explode("-", $val);'), $responses);

            // Restore the previous responses.
            $state->responses = array();
            foreach ($responses as $response) {
                $wqid = $response[0];
                $state->responses[$wqid] = $response[1];
                if (!isset($wrappedquestions[$wqid])) {
                    if (!$wrappedquestions[$wqid] = $DB->get_record('question', array('id' => $wqid))) {
                        echo $OUTPUT->notification("Couldn't get question (id=$wqid)!");
                        return false;
                    }
                    if (!$QTYPES[$wrappedquestions[$wqid]->qtype]
                     ->get_question_options($wrappedquestions[$wqid])) {
                        echo $OUTPUT->notification("Couldn't get question options (id=$response[0])!");
                        return false;
                    }

                    // Now we overwrite the $question->options->answers field to only
                    // *one* (the first) correct answer. This loop can be deleted to
                    // take all answers into account (i.e. put them all into the
                    // drop-down menu.
                    $foundcorrect = false;
                    foreach ($wrappedquestions[$wqid]->options->answers as $answer) {
                        if ($foundcorrect || $answer->fraction != 1.0) {
                            unset($wrappedquestions[$wqid]->options->answers[$answer->id]);
                        } else if (!$foundcorrect) {
                            $foundcorrect = true;
                        }
                    }
                }
                $wrappedquestion = clone($wrappedquestions[$wqid]);

                if (!$QTYPES[$wrappedquestion->qtype]
                 ->restore_session_and_responses($wrappedquestion, $state)) {
                    echo $OUTPUT->notification("Couldn't restore session of question (id=$response[0])!");
                    return false;
                }
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade    = $question->maxgrade;

                $state->options->subquestions[$wrappedquestion->id] =
                 clone($wrappedquestion);
            }
        }
        return true;
    }

    public function get_sa_candidates($categorylist, $questionsinuse = 0) {
        global $DB;
        list ($usql, $params) = $DB->get_in_or_equal($categorylist);
        list ($ques_usql, $ques_params) = $DB->get_in_or_equal(explode(',', $questionsinuse),
                SQL_PARAMS_QM, null, false);
        $params = array_merge($params, $ques_params);
        return $DB->get_records_select('question',
         "qtype = 'shortanswer' " .
         "AND category $usql " .
         "AND parent = '0' " .
         "AND hidden = '0'" .
         "AND id $ques_usql", $params);
    }

    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    public function get_random_guess_score($question) {
        return 1/$question->options->choose;
    }
}
