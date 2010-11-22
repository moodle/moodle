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

/////////////
/// MATCH ///
/////////////

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class question_match_qtype extends default_questiontype {

    function name() {
        return 'match';
    }

    function get_question_options(&$question) {
        global $DB;
        $question->options = $DB->get_record('question_match', array('question' => $question->id));
        $question->options->subquestions = $DB->get_records('question_match_sub', array('question' => $question->id), 'id ASC');
        return true;
    }

    function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass;

        $oldsubquestions = $DB->get_records('question_match_sub',
                array('question' => $question->id), 'id ASC');

        // $subquestions will be an array with subquestion ids
        $subquestions = array();

        // Insert all the new question+answer pairs
        foreach ($question->subquestions as $key => $questiontext) {
            if ($questiontext['text'] == '' && trim($question->subanswers[$key]) == '') {
                continue;
            }
            if ($questiontext['text'] != '' && trim($question->subanswers[$key]) == '') {
                $result->notice = get_string('nomatchinganswer', 'quiz', $questiontext);
            }

            // Update an existing subquestion if possible.
            $subquestion = array_shift($oldsubquestions);
            if (!$subquestion) {
                $subquestion = new stdClass;
                // Determine a unique random code
                $subquestion->code = rand(1, 999999999);
                while ($DB->record_exists('question_match_sub', array('code' => $subquestion->code, 'question' => $question->id))) {
                    $subquestion->code = rand(1, 999999999);
                }
                $subquestion->question = $question->id;
                $subquestion->questiontext = '';
                $subquestion->answertext = '';
                $subquestion->id = $DB->insert_record('question_match_sub', $subquestion);
            }

            $subquestion->questiontext = $this->import_or_save_files($questiontext,
                    $context, 'qtype_match', 'subquestion', $subquestion->id);
            $subquestion->questiontextformat = $questiontext['format'];
            $subquestion->answertext = trim($question->subanswers[$key]);

            $DB->update_record('question_match_sub', $subquestion);

            $subquestions[] = $subquestion->id;
        }

        // Delete old subquestions records
        $fs = get_file_storage();
        foreach($oldsubquestions as $oldsub) {
            $fs->delete_area_files($context->id, 'qtype_match', 'subquestion', $oldsub->id);
            $DB->delete_records('question_match_sub', array('id' => $oldsub->id));
        }

        if ($options = $DB->get_record('question_match', array('question' => $question->id))) {
            $options->subquestions = implode(',', $subquestions);
            $options->shuffleanswers = $question->shuffleanswers;
            $DB->update_record('question_match', $options);
        } else {
            unset($options);
            $options->question = $question->id;
            $options->subquestions = implode(',', $subquestions);
            $options->shuffleanswers = $question->shuffleanswers;
            $DB->insert_record('question_match', $options);
        }

        if (!empty($result->notice)) {
            return $result;
        }

        if (count($subquestions) < 3) {
            $result->notice = get_string('notenoughanswers', 'quiz', 3);
            return $result;
        }

        return true;
    }

    function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_match', array('question' => $questionid));
        $DB->delete_records('question_match_sub', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        global $DB, $OUTPUT;
        if (!$state->options->subquestions = $DB->get_records('question_match_sub', array('question' => $question->id), 'id ASC')) {
            echo $OUTPUT->notification('Error: Missing subquestions!');
            return false;
        }

        foreach ($state->options->subquestions as $key => $subquestion) {
            // This seems rather over complicated, but it is useful for the
            // randomsamatch questiontype, which can then inherit the print
            // and grading functions. This way it is possible to define multiple
            // answers per question, each with different marks and feedback.
            $answer = new stdClass();
            $answer->id       = $subquestion->code;
            $answer->answer   = $subquestion->answertext;
            $answer->fraction = 1.0;
            $state->options->subquestions[$key]->options->answers[$subquestion->code] = clone($answer);

            $state->responses[$key] = '';
        }

        // Shuffle the answers if required
        if ($cmoptions->shuffleanswers and $question->options->shuffleanswers) {
           $state->options->subquestions = swapshuffle_assoc($state->options->subquestions);
        }

        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        global $DB, $OUTPUT;
        static $subquestions = array();
        if (!isset($subquestions[$question->id])){
            if (!$subquestions[$question->id] = $DB->get_records('question_match_sub', array('question' => $question->id), 'id ASC')) {
               echo $OUTPUT->notification('Error: Missing subquestions!');
               return false;
            }
        }

        // The serialized format for matching questions is a comma separated
        // list of question answer pairs (e.g. 1-1,2-3,3-2), where the ids of
        // both refer to the id in the table question_match_sub.
        $responses = explode(',', $state->responses['']);
        $responses = array_map(create_function('$val', 'return explode("-", $val);'), $responses);

        // Restore the previous responses and place the questions into the state options
        $state->responses = array();
        $state->options->subquestions = array();
        foreach ($responses as $response) {
            $state->responses[$response[0]] = $response[1];
            $state->options->subquestions[$response[0]] = clone($subquestions[$question->id][$response[0]]);
        }

        foreach ($state->options->subquestions as $key => $subquestion) {
            // This seems rather over complicated, but it is useful for the
            // randomsamatch questiontype, which can then inherit the print
            // and grading functions. This way it is possible to define multiple
            // answers per question, each with different marks and feedback.
            $answer = new stdClass();
            $answer->id       = $subquestion->code;
            $answer->answer   = $subquestion->answertext;
            $answer->fraction = 1.0;
            $state->options->subquestions[$key]->options->answers[$subquestion->code] = clone($answer);
        }

        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        global $DB;
         $subquestions = &$state->options->subquestions;

        // Prepare an array to help when disambiguating equal answers.
        $answertexts = array();
        foreach ($subquestions as $subquestion) {
            $ans = reset($subquestion->options->answers);
            $answertexts[$ans->id] = $ans->answer;
        }

        // Serialize responses
        $responses = array();
        foreach ($subquestions as $key => $subquestion) {
            $response = 0;
            if ($subquestion->questiontext !== '' && !is_null($subquestion->questiontext)) {
                if ($state->responses[$key]) {
                    $response = $state->responses[$key];
                    if (!array_key_exists($response, $subquestion->options->answers)) {
                        // If student's answer did not match by id, but there may be
                        // two answers with the same text, but different ids,
                        // so we need to try matching the answer text.
                        $expected_answer = reset($subquestion->options->answers);
                        if ($answertexts[$response] == $expected_answer->answer) {
                            $response = $expected_answer->id;
                            $state->responses[$key] = $response;
                        }
                    }
                }
            }
            $responses[] = $key.'-'.$response;
        }
        $responses = implode(',', $responses);

        // Set the legacy answer field
        $DB->set_field('question_states', 'answer', $responses, array('id' => $state->id));
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        $responses = array();
        foreach ($state->options->subquestions as $sub) {
            foreach ($sub->options->answers as $answer) {
                if (1 == $answer->fraction && $sub->questiontext != '' && !is_null($sub->questiontext)) {
                    $responses[$sub->id] = $answer->id;
                }
            }
        }
        return empty($responses) ? null : $responses;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG, $OUTPUT;
        $context        = $this->get_context_by_category_id($question->category);
        $subquestions   = $state->options->subquestions;
        $correctanswers = $this->get_correct_responses($question, $state);
        $nameprefix     = $question->name_prefix;
        $answers        = array(); // Answer choices formatted ready for output.
        $allanswers     = array(); // This and the next used to detect identical answers
        $answerids      = array(); // and adjust ids.
        $responses      = &$state->responses;

        // Prepare a list of answers, removing duplicates.
        foreach ($subquestions as $subquestion) {
            foreach ($subquestion->options->answers as $ans) {
                $allanswers[$ans->id] = $ans->answer;
                if (!in_array($ans->answer, $answers)) {
                    $answers[$ans->id] = strip_tags(format_string($ans->answer, false));
                    $answerids[$ans->answer] = $ans->id;
                }
            }
        }

        // Fix up the ids of any responses that point the the eliminated duplicates.
        foreach ($responses as $subquestionid => $ignored) {
            if ($responses[$subquestionid]) {
                $responses[$subquestionid] = $answerids[$allanswers[$responses[$subquestionid]]];
            }
        }
        foreach ($correctanswers as $subquestionid => $ignored) {
            $correctanswers[$subquestionid] = $answerids[$allanswers[$correctanswers[$subquestionid]]];
        }

        // Shuffle the answers
        $answers = draw_rand_array($answers, count($answers));

        // Print formulation
        $questiontext = $this->format_text($question->questiontext,
                $question->questiontextformat, $cmoptions);

        // Print the input controls
        foreach ($subquestions as $key => $subquestion) {
            if ($subquestion->questiontext !== '' && !is_null($subquestion->questiontext)) {
                // Subquestion text:
                $a = new stdClass;
                $text = quiz_rewrite_question_urls($subquestion->questiontext, 'pluginfile.php', $context->id, 'qtype_match', 'subquestion', array($state->attempt, $state->question), $subquestion->id);
                $a->text = $this->format_text($text, $subquestion->questiontextformat, $cmoptions);

                // Drop-down list:
                $menuname = $nameprefix.$subquestion->id;
                $response = isset($state->responses[$subquestion->id])
                            ? $state->responses[$subquestion->id] : '0';

                $a->class = ' ';
                $a->feedbackimg = ' ';

                if ($options->readonly and $options->correct_responses) {
                    if (isset($correctanswers[$subquestion->id])
                            and ($correctanswers[$subquestion->id] == $response)) {
                        $correctresponse = 1;
                    } else {
                        $correctresponse = 0;
                    }

                    if ($options->feedback && $response) {
                        $a->class = question_get_feedback_class($correctresponse);
                        $a->feedbackimg = question_get_feedback_image($correctresponse);
                    }
                }

                $attributes = array();
                $attributes['disabled'] = $options->readonly ? 'disabled' : null;
                $a->control = html_writer::select($answers, $menuname, $response, array(''=>'choosedots'), $attributes);

                // Neither the editing interface or the database allow to provide
                // fedback for this question type.
                // However (as was pointed out in bug bug 3294) the randomsamatch
                // type which reuses this method can have feedback defined for
                // the wrapped shortanswer questions.
                //if ($options->feedback
                // && !empty($subquestion->options->answers[$responses[$key]]->feedback)) {
                //    print_comment($subquestion->options->answers[$responses[$key]]->feedback);
                //}

                $anss[] = $a;
            }
        }
        include("$CFG->dirroot/question/type/match/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $subquestions = &$state->options->subquestions;
        $responses    = &$state->responses;

        // Prepare an array to help when disambiguating equal answers.
        $answertexts = array();
        foreach ($subquestions as $subquestion) {
            $ans = reset($subquestion->options->answers);
            $answertexts[$ans->id] = $ans->answer;
        }

        // Add up the grades from each subquestion.
        $sumgrade = 0;
        $totalgrade = 0;
        foreach ($subquestions as $key => $sub) {
            if ($sub->questiontext !== '' && !is_null($sub->questiontext)) {
                $totalgrade += 1;
                $response = $responses[$key];
                if ($response && !array_key_exists($response, $sub->options->answers)) {
                    // If studen's answer did not match by id, but there may be
                    // two answers with the same text, but different ids,
                    // so we need to try matching the answer text.
                    $expected_answer = reset($sub->options->answers);
                    if ($answertexts[$response] == $expected_answer->answer) {
                        $response = $expected_answer->id;
                    }
                }
                if (array_key_exists($response, $sub->options->answers)) {
                    $sumgrade += $sub->options->answers[$response]->fraction;
                }
            }
        }

        $state->raw_grade = $sumgrade/$totalgrade;
        if (empty($state->raw_grade)) {
            $state->raw_grade = 0;
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    function compare_responses($question, $state, $teststate) {
        foreach ($state->responses as $i=>$sr) {
            if (empty($teststate->responses[$i])) {
                if (!empty($state->responses[$i])) {
                    return false;
                }
            } else if ($state->responses[$i] != $teststate->responses[$i]) {
                return false;
            }
        }
        return true;
    }

    // ULPGC ecastro for stats report
    function get_all_responses($question, $state) {
        $answers = array();
        if (is_array($question->options->subquestions)) {
            foreach ($question->options->subquestions as $aid => $answer) {
                if ($answer->questiontext !== '' && !is_null($answer->questiontext)) {
                    $r = new stdClass;
                    $r->answer = $answer->questiontext . ": " . $answer->answertext;
                    $r->credit = 1;
                    $answers[$aid] = $r;
                }
            }
        }
        $result = new stdClass;
        $result->id = $question->id;
        $result->responses = $answers;
        return $result;
    }

    function get_possible_responses(&$question) {
        $answers = array();
        if (is_array($question->options->subquestions)) {
            foreach ($question->options->subquestions as $subqid => $answer) {
                if ($answer->questiontext) {
                    $r = new stdClass;
                    $r->answer = $answer->questiontext . ": " . $answer->answertext;
                    $r->credit = 1;
                    $answers[$subqid] = array($answer->id =>$r);
                }
            }
        }
        return $answers;
    }

    // ULPGC ecastro
    function get_actual_response($question, $state) {
        $subquestions = &$state->options->subquestions;
        $responses    = &$state->responses;
        $results=array();
        foreach ($responses as $ind => $code) {
            foreach ($subquestions as $key => $sub) {
                if (isset($sub->options->answers[$code])) {
                    $results[$ind] =  $subquestions[$ind]->questiontext . ": " . $sub->options->answers[$code]->answer;
                }
            }
        }
        return $results;
    }

   function get_actual_response_details($question, $state) {
        $responses = $this->get_actual_response($question, $state);
        $teacherresponses = $this->get_possible_responses($question, $state);
        //only one response
        $responsedetails =array();
        foreach ($responses as $tsubqid => $response){
            $responsedetail = new stdClass();
            $responsedetail->subqid = $tsubqid;
            $responsedetail->response = $response;
            foreach ($teacherresponses[$tsubqid] as $aid => $tresponse){
                if ($tresponse->answer == $response){
                    $responsedetail->aid = $aid;
                    break;
                }
            }
            if (isset($responsedetail->aid)){
                $responsedetail->credit = $teacherresponses[$tsubqid][$aid]->credit;
            } else {
                $responsedetail->aid = 0;
                $responsedetail->credit = 0;
            }
            $responsedetails[] = $responsedetail;
        }
        return $responsedetails;
    }


    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    function get_random_guess_score($question) {
        return 1 / count($question->options->subquestions);
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->shuffleanswers = 1;
        $form->noanswers = 3;
        $form->subquestions = array('cat', 'dog', 'cow');
        $form->subanswers = array('feline', 'canine', 'bovine');

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
    }

    function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);

        $subquestionids = $DB->get_records_menu('question_match_sub',
                array('question' => $questionid), 'id', 'id,1');
        foreach ($subquestionids as $subquestionid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid,
                    $newcontextid, 'qtype_match', 'subquestion', $subquestionid);
        }
    }

    protected function delete_files($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);

        $subquestionids = $DB->get_records_menu('question_match_sub',
                array('question' => $questionid), 'id', 'id,1');
        foreach ($subquestionids as $subquestionid => $notused) {
            $fs->delete_area_files($contextid, 'qtype_match', 'subquestion', $subquestionid);
        }
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {

        $itemid = reset($args);
        if ($filearea == 'subquestion') {
            // itemid is sub question id
            if (!array_key_exists($itemid, $question->options->subquestions)) {
                return false;
            }

            return true;
        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_match_qtype());
