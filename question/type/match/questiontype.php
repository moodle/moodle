<?php  // $Id$

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
        $question->options = get_record('question_match', 'question', $question->id);
        $question->options->subquestions = get_records('question_match_sub', 'question', $question->id, 'id ASC');
        return true;
    }

    function save_question_options($question) {
        $result = new stdClass;

        if (!$oldsubquestions = get_records("question_match_sub", "question", $question->id, "id ASC")) {
            $oldsubquestions = array();
        }

        // $subquestions will be an array with subquestion ids
        $subquestions = array();

        // Insert all the new question+answer pairs
        foreach ($question->subquestions as $key => $questiontext) {
            $questiontext = trim($questiontext);
            $answertext = trim($question->subanswers[$key]);
            if ($questiontext != '' || $answertext != '') {
                if ($subquestion = array_shift($oldsubquestions)) {  // Existing answer, so reuse it
                    $subquestion->questiontext = $questiontext;
                    $subquestion->answertext   = $answertext;
                    if (!update_record("question_match_sub", $subquestion)) {
                        $result->error = "Could not insert match subquestion! (id=$subquestion->id)";
                        return $result;
                    }
                } else {
                    $subquestion = new stdClass;
                    // Determine a unique random code
                    $subquestion->code = rand(1,999999999);
                    while (record_exists('question_match_sub', 'code', $subquestion->code, 'question', $question->id)) {
                        $subquestion->code = rand();
                    }
                    $subquestion->question = $question->id;
                    $subquestion->questiontext = $questiontext;
                    $subquestion->answertext   = $answertext;
                    if (!$subquestion->id = insert_record("question_match_sub", $subquestion)) {
                        $result->error = "Could not insert match subquestion!";
                        return $result;
                    }
                }
                $subquestions[] = $subquestion->id;
            }
            if ($questiontext != '' && $answertext == '') {
                $result->notice = get_string('nomatchinganswer', 'quiz', $questiontext);
            }
        }

        // delete old subquestions records
        if (!empty($oldsubquestions)) {
            foreach($oldsubquestions as $os) {
                delete_records('question_match_sub', 'id', $os->id);
            }
        }

        if ($options = get_record("question_match", "question", $question->id)) {
            $options->subquestions = implode(",",$subquestions);
            $options->shuffleanswers = $question->shuffleanswers;
            if (!update_record("question_match", $options)) {
                $result->error = "Could not update match options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->subquestions = implode(",",$subquestions);
            $options->shuffleanswers = $question->shuffleanswers;
            if (!insert_record("question_match", $options)) {
                $result->error = "Could not insert match options!";
                return $result;
            }
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

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param integer $question->id
    */
    function delete_question($questionid) {
        delete_records("question_match", "question", $questionid);
        delete_records("question_match_sub", "question", $questionid);
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        if (!$state->options->subquestions = get_records('question_match_sub', 'question', $question->id, 'id ASC')) {
            notify('Error: Missing subquestions!');
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
        // The serialized format for matching questions is a comma separated
        // list of question answer pairs (e.g. 1-1,2-3,3-2), where the ids of
        // both refer to the id in the table question_match_sub.
        $responses = explode(',', $state->responses['']);
        $responses = array_map(create_function('$val', 'return explode("-", $val);'), $responses);

        if (!$questions = get_records('question_match_sub', 'question', $question->id, 'id ASC')) {
           notify('Error: Missing subquestions!');
           return false;
        }

        // Restore the previous responses and place the questions into the state options
        $state->responses = array();
        $state->options->subquestions = array();
        foreach ($responses as $response) {
            $state->responses[$response[0]] = $response[1];
            $state->options->subquestions[$response[0]] = $questions[$response[0]];
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
        if (!set_field('question_states', 'answer', $responses, 'id', $state->id)) {
            return false;
        }
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
        global $CFG;
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
        $image = get_question_image($question);

        // Print the input controls
        foreach ($subquestions as $key => $subquestion) {
            if ($subquestion->questiontext !== '' && !is_null($subquestion->questiontext)) {
                // Subquestion text:
                $a = new stdClass;
                $a->text = $this->format_text($subquestion->questiontext,
                        $question->questiontextformat, $cmoptions);

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

                $a->control = choose_from_menu($answers, $menuname, $response, 'choose',
                                               '', 0, true, $options->readonly);

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

    // ULPGC ecastro
    function get_actual_response($question, $state) {
       $subquestions = &$state->options->subquestions;
       $responses    = &$state->responses;
       $results=array();
       foreach ($subquestions as $key => $sub) {
           foreach ($responses as $ind => $code) {
               if (isset($sub->options->answers[$code])) {
                   $results[$ind] =  $subquestions[$ind]->questiontext . ": " . $sub->options->answers[$code]->answer;
               }
           }
       }
       return $results;
   }

    function response_summary($question, $state, $length=80) {
        // This should almost certainly be overridden
        return shorten_text(implode(', ', $this->get_actual_response($question, $state)), $length);
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {
        $status = true;

        // Output the shuffleanswers setting.
        $matchoptions = get_record('question_match', 'question', $question);
        if ($matchoptions) {
            $status = fwrite ($bf,start_tag("MATCHOPTIONS",6,true));
            fwrite ($bf,full_tag("SHUFFLEANSWERS",7,false,$matchoptions->shuffleanswers));
            $status = fwrite ($bf,end_tag("MATCHOPTIONS",6,true));
        }

        $matchs = get_records('question_match_sub', 'question', $question, 'id ASC');
        //If there are matchs
        if ($matchs) {
            //Print match contents
            $status = fwrite ($bf,start_tag("MATCHS",6,true));
            //Iterate over each match
            foreach ($matchs as $match) {
                $status = fwrite ($bf,start_tag("MATCH",7,true));
                //Print match contents
                fwrite ($bf,full_tag("ID",8,false,$match->id));
                fwrite ($bf,full_tag("CODE",8,false,$match->code));
                fwrite ($bf,full_tag("QUESTIONTEXT",8,false,$match->questiontext));
                fwrite ($bf,full_tag("ANSWERTEXT",8,false,$match->answertext));
                $status = fwrite ($bf,end_tag("MATCH",7,true));
            }
            $status = fwrite ($bf,end_tag("MATCHS",6,true));
        }
        return $status;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {
        $status = true;

        //Get the matchs array
        $matchs = $info['#']['MATCHS']['0']['#']['MATCH'];

        //We have to build the subquestions field (a list of match_sub id)
        $subquestions_field = "";
        $in_first = true;

        //Iterate over matchs
        for($i = 0; $i < sizeof($matchs); $i++) {
            $mat_info = $matchs[$i];

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

            //Now, build the question_match_SUB record structure
            $match_sub = new stdClass;
            $match_sub->question = $new_question_id;
            $match_sub->code = isset($mat_info['#']['CODE']['0']['#'])?backup_todb($mat_info['#']['CODE']['0']['#']):'';
            if (!$match_sub->code) {
                $match_sub->code = $oldid;
            }
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //The structure is equal to the db, so insert the question_match_sub
            $newid = insert_record ("question_match_sub",$match_sub);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
                             $newid);
                //We have a new match_sub, append it to subquestions_field
                if ($in_first) {
                    $subquestions_field .= $newid;
                    $in_first = false;
                } else {
                    $subquestions_field .= ",".$newid;
                }
            } else {
                $status = false;
            }
        }

        //We have created every match_sub, now create the match
        $match = new stdClass;
        $match->question = $new_question_id;
        $match->subquestions = $subquestions_field;

        // Get the shuffleanswers option, if it is there.
        if (!empty($info['#']['MATCHOPTIONS']['0']['#']['SHUFFLEANSWERS'])) {
            $match->shuffleanswers = backup_todb($info['#']['MATCHOPTIONS']['0']['#']['SHUFFLEANSWERS']['0']['#']);
        } else {
            $match->shuffleanswers = 1;
        }

        //The structure is equal to the db, so insert the question_match_sub
        $newid = insert_record ("question_match",$match);

        if (!$newid) {
            $status = false;
        }

        return $status;
    }

    function restore_map($old_question_id,$new_question_id,$info,$restore) {

        $status = true;

        //Get the matchs array
        $matchs = $info['#']['MATCHS']['0']['#']['MATCH'];

        //We have to build the subquestions field (a list of match_sub id)
        $subquestions_field = "";
        $in_first = true;

        //Iterate over matchs
        for($i = 0; $i < sizeof($matchs); $i++) {
            $mat_info = $matchs[$i];

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

            //Now, build the question_match_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //match_sub must exist too.
            //Now, we are going to look for that match_sub in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the match_sub from DB (by question, questiontext and answertext)
            $db_match_sub = get_record ("question_match_sub","question",$new_question_id,
                                                      "questiontext",$match_sub->questiontext,
                                                      "answertext",$match_sub->answertext);
            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            //We have the database match_sub, so update backup_ids
            if ($db_match_sub) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
                             $db_match_sub->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function restore_recode_answer($state, $restore) {

        //The answer is a comma separated list of hypen separated math_subs (for question and answer)
        $answer_field = "";
        $in_first = true;
        $tok = strtok($state->answer,",");
        while ($tok) {
            //Extract the match_sub for the question and the answer
            $exploded = explode("-",$tok);
            $match_question_id = $exploded[0];
            $match_answer_id = $exploded[1];
            //Get the match_sub from backup_ids (for the question)
            if (!$match_que = backup_getid($restore->backup_unique_code,"question_match_sub",$match_question_id)) {
                echo 'Could not recode question in question_match_sub '.$match_question_id.'<br />';
            } else {
                if ($in_first) {
                    $in_first = false;
                } else {
                    $answer_field .= ',';
                }
                $answer_field .= $match_que->new_id.'-'.$match_answer_id;
            }
            //check for next
            $tok = strtok(",");
        }
        return $answer_field;
    }

    /**
     * Decode links in question type specific tables.
     * @return bool success or failure.
     */
    function decode_content_links_caller($questionids, $restore, &$i) {
        $status = true;

        // Decode links in the question_match_sub table.
        if ($subquestions = get_records_list('question_match_sub', 'question',
                implode(',',  $questionids), '', 'id, questiontext')) {

            foreach ($subquestions as $subquestion) {
                $questiontext = restore_decode_content_links_worker($subquestion->questiontext, $restore);
                if ($questiontext != $subquestion->questiontext) {
                    $subquestion->questiontext = addslashes($questiontext);
                    if (!update_record('question_match_sub', $subquestion)) {
                        $status = false;
                    }
                }

                // Do some output.
                if (++$i % 5 == 0 && !defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if ($i % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }

    function find_file_links($question, $courseid){
        // find links in the question_match_sub table.
        $urls = array();
        if (isset($question->options->subquestions)){
            foreach ($question->options->subquestions as $subquestion) {
                $urls += question_find_file_links_from_html($subquestion->questiontext, $courseid);
            }

            //set all the values of the array to the question object
            if ($urls){
                $urls = array_combine(array_keys($urls), array_fill(0, count($urls), array($question->id)));
            }
        }
        $urls = array_merge_recursive($urls, parent::find_file_links($question, $courseid));

        return $urls;
    }

    function replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination){
        parent::replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination);
        // replace links in the question_match_sub table.
        if (isset($question->options->subquestions)){
            foreach ($question->options->subquestions as $subquestion) {
                $subquestionchanged = false;
                $subquestion->questiontext = question_replace_file_links_in_html($subquestion->questiontext, $fromcourseid, $tocourseid, $url, $destination, $subquestionchanged);
                if ($subquestionchanged){//need to update rec in db
                    if (!update_record('question_match_sub', addslashes_recursive($subquestion))) {
                        error('Couldn\'t update \'question_match_sub\' record '.$subquestion->id);
                    }

                }
            }
        }
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->shuffleanswers = 1;
        $form->noanswers = 3;
        $form->subquestions = array('cat', 'dog', 'cow');
        $form->subanswers = array('feline', 'canine', 'bovine');

        if ($courseid) {
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_match_qtype());
?>
