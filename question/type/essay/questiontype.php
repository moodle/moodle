<?php  // $Id$

//////////////////
///   ESSAY   ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
class question_essay_qtype extends default_questiontype {

    function name() {
        return 'essay';
    }

    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('question_essay', 'question', $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }

        if (!$question->options->answers = get_records('question_answers', 'question',
            $question->id)) {
           notify('Error: Missing question answers!');
           return false;
        }
        return true;
    }

    function save_question_options($question) {
        if ($answer = get_record("question_answers", "question", $question->id)) {
            // Existing answer, so reuse it
            $answer->answer = $question->feedback;
            $answer->feedback = $question->feedback;
            $answer->fraction = $question->fraction;
            if (!update_record("question_answers", $answer)) {
                $result->error = "Could not update quiz answer!";
                return $result;
            }
        } else {
            unset($answer);
            $answer->question = $question->id;
            $answer->answer = $question->feedback;
            $answer->feedback = $question->feedback;
            $answer->fraction = $question->fraction;
            if (!$answer->id = insert_record("question_answers", $answer)) {
                $result->error = "Could not insert quiz answer!";
                return $result;
            }
        }
        if ($options = get_record("question_essay", "question", $question->id)) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->answer  = $answer->id;
            if (!update_record("question_essay", $options)) {
                $result->error = "Could not update quiz essay options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->answer  = $answer->id;
            if (!insert_record("question_essay", $options)) {
                $result->error = "Could not insert quiz essay options!";
                return $result;
            }
        }
        return true;
    }

    /**
    * Deletes states from the question-type specific tables
    *
    * @param string $stateslist  Comma separated list of state ids to be deleted
    */
    function delete_states($stateslist) {
        delete_records_select("question_essay_states", "stateid IN ($stateslist)");
        return true;
    }

    /**
    * Deletes a question from the question-type specific tables
    *
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_essay", "question", $questionid);
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {

        $answers = &$question->options->answers;
        //$correctanswers = $this->get_correct_responses($question, $state);  // no correct answers ;)
        $readonly = empty($options->readonly) ? '' : 'disabled="disabled"';
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;
        
        /// Print question text and media
       echo format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
                         
        echo get_question_image($question, $cmoptions->course);

        /// Print input controls
        $stranswer = get_string("answer", "quiz");
        $strcomment = get_string("comments", "quiz");
        $usehtmleditor = can_use_html_editor();
        
        // this prints out the student response box
        if (isset($state->responses[''])) { 
            // security problem. responses[''] is never cleaned before it is sent to the db (I think)
            $value = clean_param(addslashes($state->responses['']), PARAM_CLEANHTML);
        } else {
            $value = "";
        }

        $inputname = $nameprefix;
        
        echo "<p>$stranswer: </p>".
            '<div style="padding-left: 30px;">';   
        if (empty($options->readonly)) {    
            // the student needs to type in their answer so print out a text editor
            print_textarea($usehtmleditor, 18, 80, 630, 400, $inputname, $value);
            use_html_editor($inputname);
        } else {
            // it is read only, so just format the students answer and output it
            echo format_text($value, $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
            echo '<input type="hidden" name="'.$inputname.'" value="'.htmlSpecialChars($value).'" />'; // need hidden one for grading
        }    
        echo '</div>';
                    
        if (isset($state->responses['response'])) {
            $value = $state->responses['response'];
        } else {
            $value = "";
        }

        $inputname = $nameprefix.'response';
        if(!empty($options->regrade)) {  // special option set in regrade.php.  This means we want to grade the essay question
            // print out a field for the teacher to add a comment
            echo "<p>$strcomment: ".
                '<div style="padding-left: 30px;">';
            print_textarea($usehtmleditor, 18, 80, 630, 400, $inputname, $value);
            use_html_editor($inputname);
            echo '</div></p><br />';
            
            // dropdown for score... for the fraction
            $grades = array(1,0.9,0.8,0.75,0.70,0.66666,0.60,0.50,0.40,0.33333,0.30,0.25,0.20,0.16666,0.142857,0.10,0.05,0);
            foreach ($grades as $grade) {
                $percentage = 100 * $grade;
                $neggrade = -$grade;
                $gradeoptions["$grade"] = "$percentage %";
                $gradeoptionsfull["$grade"] = "$percentage %";
                $gradeoptionsfull["$neggrade"] = -$percentage." %";
            }
            $gradeoptionsfull["0"] = $gradeoptions["0"] = get_string("none");
            print_string("grade");
            echo ":&nbsp;";
            choose_from_menu($gradeoptions, $nameprefix."fraction", $state->responses['fraction'],"");
        } else if (!empty($options->readonly)) {
            //read only so format the comment and print it out
            echo "<p>$strcomment: </p>".
                '<div style="padding-left: 30px;">';
            if (empty($value)) {  // no comment yet
                echo format_text(get_string('nocommentsyet', 'quiz'));
            } else {
                echo format_text($value, '', $formatoptions, $cmoptions->course);    
            }
            echo '</div>';
        }

        // feedback
        if ($options->feedback) {
            foreach ($answers as $answer) {
                format_text("<p align=\"right\">$answer->feedback</p>", '', $formatoptions, $cmoptions->course);
            }
        }
        $this->print_question_submit_buttons($question, $state, $cmoptions, $options);
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $state->raw_grade = 0;
        // if a fraction is submitted, then we use it, otherwise the raw grade is 0
        if (isset($state->responses['fraction']) and $state->responses['fraction']) {
            $state->raw_grade = $state->responses['fraction'];
            $state->options->graded = 1;
            // mark the state as graded
            $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;
        } else {
            $state->raw_grade = 0;
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        $state->penalty = $question->penalty * $question->maxgrade;  // should be no penalty for essays

        return true;
    }
    
    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        $state->options->graded = 0;  // our flag to indicated wheather an essay has been graded or not
        $state->responses['fraction'] = 0;   // this fraction is used for grading.  The teacher sets it while grading
        $state->responses['response'] = '';  // this is teacher response to the students essay
        
        return true;
    }
    
    function restore_session_and_responses(&$question, &$state) {
        if (!$options = get_record('question_essay_states', 'stateid', $state->id)) {
            return false;
        }
        $state->options->graded = $options->graded;
        $state->responses['fraction'] = $options->fraction;
        $state->responses['response'] = $options->response;

        return true;
    }
    
    function save_session_and_responses(&$question, &$state) {
        $options->stateid = $state->id;
        
        if (isset($state->options->graded)) {
            $options->graded = $state->options->graded;
        } else {
            $options->graded = 0;
        }
        
        if (isset($state->responses['fraction'])) {
            $options->fraction = $state->responses['fraction'];
        }
        if (isset($state->responses['response'])) {
            $options->response = addslashes( clean_param($state->responses['response'], PARAM_CLEANHTML) );
        }
        if (isset($state->update)) {
            if (!$options->id = get_field('question_essay_states', 'id', 'stateid', $state->id)) {
                return false;
            }
            if (!update_record('question_essay_states', $options)) {
                return false;
            }
        } else {
            if (!insert_record('question_essay_states', $options)) {
                return false;
            }
        }     

        return true;
    }
    
    /**
    * Utility function to count the number of ungraded essays
    * and the number of students those essays belong to.
    *
    * @param object $cmoptions  Course module options
    * @param mixed $users string/array Specify a specific user or a set of users as an array with ids as keys or as a comma separated list.  Defaults to current user.
    * @param string $attemptid Specify a specific attempt
    * @return array First item in array is the number of ungraded essays and the second is the number of students
    * @todo make this function quiz independent
    */
    function get_ungraded_count($cmoptions, $users=0, $attemptid=0) {
        global $USER, $CFG;
        
        // prep the userids variable
        if (empty($users)) {
            $userids = $USER->id;
        }else if (is_array($users)) {
            $userids = implode(', ', array_keys($users));
        } else {
            $userids = $users;
        }
        
        $ungradedcount = 0;     // holds the number of ungraded essays
        $usercount = array();   // holds the number of users of ungraded essays
        
        // get the essay questions
        $questionlist = quiz_questions_in_quiz($cmoptions->questions);
        $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
               "  FROM {$CFG->prefix}question q,".
               "       {$CFG->prefix}quiz_question_instances i".
               " WHERE i.quiz = '$cmoptions->id' AND q.id = i.question".
               "   AND q.id IN ($questionlist)".
               "   AND q.qtype = '".'essay'."'".
               "   ORDER BY q.name";
               
        if (empty($attemptid)) {
            $attemptsql = "quiz = $cmoptions->id and timefinish > 0 and userid IN ($userids)";
        } else {
            $attemptsql = "id = $attemptid";
        }
        if ($questions = get_records_sql($sql)) {
            // get all the finished attempts by the users
            if ($attempts = get_records_select('quiz_attempts', $attemptsql, 'userid, attempt')) {
                foreach($questions as $question) {
                    // determine the number of ungraded attempts essays
                    foreach ($attempts as $attempt) {
                        // grab the state then check if it is graded
                        if (!$neweststate = get_record('question_sessions', 'attemptid', $attempt->uniqueid, 'questionid', $question->id)) {
                            error("Can not find newest states for attempt $attempt->uniqueid for question $question->id");
                        }
                        if (!$questionstate = get_record('question_essay_states', 'stateid', $neweststate->newest)) {
                            error('Could not find question state');
                        }
                        if (!$questionstate->graded) {
                            $ungradedcount++;
                        }
                        // keep track of users
                        $usercount[$attempt->userid] = 1;
                    }
                }
            }
        }
    
        return array($ungradedcount, count($usercount));
    }
    
/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in a truefalse question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $essays = get_records('question_essay', 'question', $question, "id");
        //If there are essays
        if ($essays) {
            //Iterate over each essay
            foreach ($essays as $essay) {
                $status = fwrite ($bf,start_tag("ESSAY",$level,true));
                //Print essay contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$essay->answer));                
                $status = fwrite ($bf,end_tag("ESSAY",$level,true));
            }
            //Now print question_answers
            $status = question_backup_answers($bf,$preferences,$question);
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

        //Get the truefalse array
        $essays = $info['#']['ESSAY'];

        //Iterate over truefalse
        for($i = 0; $i < sizeof($essays); $i++) {
            $essay_info = $essays[$i];

            //Now, build the question_essay record structure
            $essay->question = $new_question_id;
            $essay->answer = backup_todb($essay_info['#']['ANSWER']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$essay->answer);
            if ($answer) {
                $essay->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_essay
            $newid = insert_record ("question_essay",$essay);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }


    //This function restores the question_essay_states
    function restore_state($state_id,$info,$restore) {

        $status = true;

        //Get the question_essay_state
        $essay_state = $info['#']['ESSAY_STATE']['0'];
        if ($essay_state) {

            //Now, build the ESSAY_STATES record structure
            $state->stateid = $state_id;
            $state->graded = backup_todb($essay_state['#']['GRADED']['0']['#']);
            $state->fraction = backup_todb($essay_state['#']['FRACTION']['0']['#']);
            $state->response = backup_todb($essay_state['#']['RESPONSE']['0']['#']);

            //The structure is equal to the db, so insert the question_states
            $newid = insert_record ("question_essay_states",$state);
        }

        return $status;
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES['essay'] = new question_essay_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU['essay'] = get_string("essay", "quiz");

?>
