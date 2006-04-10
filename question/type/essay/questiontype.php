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
    * Deletes a question from the question-type specific tables
    *
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_essay", "question", $questionid);
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $answers       = &$question->options->answers;
        $readonly      = empty($options->readonly) ? '' : 'disabled="disabled"';
        $usehtmleditor = can_use_html_editor();
        
        $formatoptions          = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para    = false;
        
        $inputname = $question->name_prefix;
        $stranswer = get_string("answer", "quiz").': ';
        
        /// set question text and media
        $questiontext = format_text($question->questiontext,
                                   $question->questiontextformat,
                                   $formatoptions, $cmoptions->course);
                         
        $image = get_question_image($question, $cmoptions->course);

        // feedback handling
        $feedback = '';
        if ($options->feedback) {
            foreach ($answers as $answer) {
                $feedback = format_text($answer->feedback, '', $formatoptions, $cmoptions->course);
            }
        }
        
        // get response value
        if (isset($state->responses[''])) { 
            // security problem. responses[''] is never cleaned before it is sent to the db (I think)
            $value = $state->responses[''];            
        } else {
            $value = "";
        }

        // answer
        if (empty($options->readonly)) {    
            // the student needs to type in their answer so print out a text editor
            $answer = print_textarea($usehtmleditor, 18, 80, 630, 400, $inputname, $value, $cmoptions->course, true);
        } else {
            // it is read only, so just format the students answer and output it
            $answer = format_text($value, $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
        }
        
        include("$CFG->dirroot/question/type/essay/display.html");

        if ($usehtmleditor) {
            use_html_editor($inputname);
        }
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // All grading takes place in Manual Grading

        clean_param($state->responses[''], PARAM_CLEANHTML);
        
        $state->raw_grade = 0;
        $state->penalty = 0;

        return true;
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
// Add essay to the list of manually graded questions
$QTYPE_MANUAL = isset($QTYPE_MANUAL) ? $QTYPE_MANUAL.",'essay'" : "'essay'";

?>
