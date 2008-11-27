<?php  // $Id$
/**
 * The questiontype class for the multiple choice question type.
 *
 * Note, This class contains some special features in order to make the
 * question type embeddable within a multianswer (cloze) question
 *
 * @package questionbank
 * @subpackage questiontypes
 */
class question_multichoice_qtype extends default_questiontype {

    function name() {
        return 'multichoice';
    }

    function has_html_answers() {
        return true;
    }

    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('question_multichoice', 'question',
         $question->id)) {
            notify('Error: Missing question options for multichoice question'.$question->id.'!');
            return false;
        }

        if (!$question->options->answers = get_records_select('question_answers', 'id IN ('.$question->options->answers.')', 'id')) {
           notify('Error: Missing question answers for multichoice question'.$question->id.'!');
           return false;
        }

        return true;
    }

    function save_question_options($question) {
        $result = new stdClass;
        if (!$oldanswers = get_records("question_answers", "question",
                                       $question->id, "id ASC")) {
            $oldanswers = array();
        }

        // following hack to check at least two answers exist
        $answercount = 0;
        foreach ($question->answer as $key=>$dataanswer) {
            if ($dataanswer != "") {
                $answercount++;
            }
        }
        $answercount += count($oldanswers);
        if ($answercount < 2) { // check there are at lest 2 answers for multiple choice
            $result->notice = get_string("notenoughanswers", "qtype_multichoice", "2");
            return $result;
        }

        // Insert all the new answers

        $totalfraction = 0;
        $maxfraction = -1;

        $answers = array();

        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                if ($answer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->answer     = $dataanswer;
                    $answer->fraction   = $question->fraction[$key];
                    $answer->feedback   = $question->feedback[$key];
                    if (!update_record("question_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {
                    unset($answer);
                    $answer->answer   = $dataanswer;
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("question_answers", $answer)) {
                        $result->error = "Could not insert quiz answer! ";
                        return $result;
                    }
                }
                $answers[] = $answer->id;

                if ($question->fraction[$key] > 0) {                 // Sanity checks
                    $totalfraction += $question->fraction[$key];
                }
                if ($question->fraction[$key] > $maxfraction) {
                    $maxfraction = $question->fraction[$key];
                }
            }
        }

        $update = true;
        $options = get_record("question_multichoice", "question", $question->id);
        if (!$options) {
            $update = false;
            $options = new stdClass;
            $options->question = $question->id;

        }
        $options->answers = implode(",",$answers);
        $options->single = $question->single;
        if(isset($question->layout)){
             $options->layout = $question->layout;
        }
        $options->answernumbering = $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;
        $options->correctfeedback = trim($question->correctfeedback);
        $options->partiallycorrectfeedback = trim($question->partiallycorrectfeedback);
        $options->incorrectfeedback = trim($question->incorrectfeedback);
        if ($update) {
            if (!update_record("question_multichoice", $options)) {
                $result->error = "Could not update quiz multichoice options! (id=$options->id)";
                return $result;
            }
        } else {
            if (!insert_record("question_multichoice", $options)) {
                $result->error = "Could not insert quiz multichoice options!";
                return $result;
            }
        }

        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                delete_records('question_answers', 'id', $oa->id);
            }
        }

        /// Perform sanity checks on fractional grades
        if ($options->single) {
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->noticeyesno = get_string("fractionsnomax", "qtype_multichoice", $maxfraction);
                return $result;
            }
        } else {
            $totalfraction = round($totalfraction,2);
            if ($totalfraction != 1) {
                $totalfraction = $totalfraction * 100;
                $result->noticeyesno = get_string("fractionsaddwrong", "qtype_multichoice", $totalfraction);
                return $result;
            }
        }
        return true;
    }

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_multichoice", "question", $questionid);
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // create an array of answerids ??? why so complicated ???
        $answerids = array_values(array_map(create_function('$val',
         'return $val->id;'), $question->options->answers));
        // Shuffle the answers if required
        if (!empty($cmoptions->shuffleanswers) and !empty($question->options->shuffleanswers)) {
           $answerids = swapshuffle($answerids);
        }
        $state->options->order = $answerids;
        // Create empty responses
        if ($question->options->single) {
            $state->responses = array('' => '');
        } else {
            $state->responses = array();
        }
        return true;
    }


    function restore_session_and_responses(&$question, &$state) {
        // The serialized format for multiple choice quetsions
        // is an optional comma separated list of answer ids (the order of the
        // answers) followed by a colon, followed by another comma separated
        // list of answer ids, which are the radio/checkboxes that were
        // ticked.
        // E.g. 1,3,2,4:2,4 means that the answers were shown in the order
        // 1, 3, 2 and then 4 and the answers 2 and 4 were checked.

        $pos = strpos($state->responses[''], ':');
        if (false === $pos) { // No order of answers is given, so use the default
            $state->options->order = array_keys($question->options->answers);
        } else { // Restore the order of the answers
            $state->options->order = explode(',', substr($state->responses[''], 0, $pos));
            $state->responses[''] = substr($state->responses[''], $pos + 1);
        }
        // Restore the responses
        // This is done in different ways if only a single answer is allowed or
        // if multiple answers are allowed. For single answers the answer id is
        // saved in $state->responses[''], whereas for the multiple answers case
        // the $state->responses array is indexed by the answer ids and the
        // values are also the answer ids (i.e. key = value).
        if (empty($state->responses[''])) { // No previous responses
            $state->responses = array('' => '');
        } else {
            if ($question->options->single) {
                $state->responses = array('' => $state->responses['']);
            } else {
                // Get array of answer ids
                $state->responses = explode(',', $state->responses['']);
                // Create an array indexed by these answer ids
                $state->responses = array_flip($state->responses);
                // Set the value of each element to be equal to the index
                array_walk($state->responses, create_function('&$a, $b',
                 '$a = $b;'));
            }
        }
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        // Bundle the answer order and the responses into the legacy answer
        // field.
        // The serialized format for multiple choice quetsions
        // is (optionally) a comma separated list of answer ids
        // followed by a colon, followed by another comma separated
        // list of answer ids, which are the radio/checkboxes that were
        // ticked.
        // E.g. 1,3,2,4:2,4 means that the answers were shown in the order
        // 1, 3, 2 and then 4 and the answers 2 and 4 were checked.
        $responses  = implode(',', $state->options->order) . ':';
        $responses .= implode(',', $state->responses);

        // Set the legacy answer field
        if (!set_field('question_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        if ($question->options->single) {
            foreach ($question->options->answers as $answer) {
                if (((int) $answer->fraction) === 1) {
                    return array('' => $answer->id);
                }
            }
            return null;
        } else {
            $responses = array();
            foreach ($question->options->answers as $answer) {
                if (((float) $answer->fraction) > 0.0) {
                    $responses[$answer->id] = (string) $answer->id;
                }
            }
            return empty($responses) ? null : $responses;
        }
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $answers = &$question->options->answers;
        $correctanswers = $this->get_correct_responses($question, $state);
        $readonly = empty($options->readonly) ? '' : 'disabled="disabled"';

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;

        // Print formulation
        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
        $image = get_question_image($question);
        $answerprompt = ($question->options->single) ? get_string('singleanswer', 'quiz') :
            get_string('multipleanswers', 'quiz');

        // Print each answer in a separate row
        foreach ($state->options->order as $key => $aid) {
            $answer = &$answers[$aid];
            $checked = '';
            $chosen = false;

            if ($question->options->single) {
                $type = 'type="radio"';
                $name   = "name=\"{$question->name_prefix}\"";
                if (isset($state->responses['']) and $aid == $state->responses['']) {
                    $checked = 'checked="checked"';
                    $chosen = true;
                }
            } else {
                $type = ' type="checkbox" ';
                $name   = "name=\"{$question->name_prefix}{$aid}\"";
                if (isset($state->responses[$aid])) {
                    $checked = 'checked="checked"';
                    $chosen = true;
                }
            }

            $a = new stdClass;
            $a->id   = $question->name_prefix . $aid;
            $a->class = '';
            $a->feedbackimg = '';

            // Print the control
            $a->control = "<input $readonly id=\"$a->id\" $name $checked $type value=\"$aid\" />";

            if ($options->correct_responses && $answer->fraction > 0) {
                $a->class = question_get_feedback_class(1);
            }
            if (($options->feedback && $chosen) || $options->correct_responses) {
                if ($type == ' type="checkbox" ') {
                    $a->feedbackimg = question_get_feedback_image($answer->fraction > 0 ? 1 : 0, $chosen && $options->feedback);
                } else {
                    $a->feedbackimg = question_get_feedback_image($answer->fraction, $chosen && $options->feedback);
                }
            }

            // Print the answer text
            $a->text = $this->number_in_style($key, $question->options->answernumbering) .
                    format_text($answer->answer, FORMAT_MOODLE, $formatoptions, $cmoptions->course);

            // Print feedback if feedback is on
            if (($options->feedback || $options->correct_responses) && $checked) {
                $a->feedback = format_text($answer->feedback, true, $formatoptions, $cmoptions->course);
            } else {
                $a->feedback = '';
            }

            $anss[] = clone($a);
        }

        $feedback = '';
        if ($options->feedback) {
            if ($state->raw_grade >= $question->maxgrade/1.01) {
                $feedback = $question->options->correctfeedback;
            } else if ($state->raw_grade > 0) {
                $feedback = $question->options->partiallycorrectfeedback;
            } else {
                $feedback = $question->options->incorrectfeedback;
            }
            $feedback = format_text($feedback,
                    $question->questiontextformat,
                    $formatoptions, $cmoptions->course);
        }

        include("$CFG->dirroot/question/type/multichoice/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $state->raw_grade = 0;
        if($question->options->single) {
            $response = reset($state->responses);
            if ($response) {
                $state->raw_grade = $question->options->answers[$response]->fraction;
            }
        } else {
            foreach ($state->responses as $response) {
                if ($response) {
                    $state->raw_grade += $question->options->answers[$response]->fraction;
                }
            }
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Apply the penalty for this attempt
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    // ULPGC ecastro
    function get_actual_response($question, $state) {
        $answers = $question->options->answers;
        $responses = array();
        if (!empty($state->responses)) {
            foreach ($state->responses as $aid =>$rid){
                if (!empty($answers[$rid])) {
                    $responses[] = $this->format_text($answers[$rid]->answer, $question->questiontextformat);
                }
            }
        } else {
            $responses[] = '';
        }
        return $responses;
    }

    function response_summary($question, $state, $length = 80) {
        return implode(',', $this->get_actual_response($question, $state));
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $multichoices = get_records("question_multichoice","question",$question,"id");
        //If there are multichoices
        if ($multichoices) {
            //Iterate over each multichoice
            foreach ($multichoices as $multichoice) {
                $status = fwrite ($bf,start_tag("MULTICHOICE",$level,true));
                //Print multichoice contents
                fwrite ($bf,full_tag("LAYOUT",$level+1,false,$multichoice->layout));
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$multichoice->answers));
                fwrite ($bf,full_tag("SINGLE",$level+1,false,$multichoice->single));
                fwrite ($bf,full_tag("SHUFFLEANSWERS",$level+1,false,$multichoice->shuffleanswers));
                fwrite ($bf,full_tag("CORRECTFEEDBACK",$level+1,false,$multichoice->correctfeedback));
                fwrite ($bf,full_tag("PARTIALLYCORRECTFEEDBACK",$level+1,false,$multichoice->partiallycorrectfeedback));
                fwrite ($bf,full_tag("INCORRECTFEEDBACK",$level+1,false,$multichoice->incorrectfeedback));
                fwrite ($bf,full_tag("ANSWERNUMBERING",$level+1,false,$multichoice->answernumbering));
                $status = fwrite ($bf,end_tag("MULTICHOICE",$level,true));
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

        //Get the multichoices array
        $multichoices = $info['#']['MULTICHOICE'];

        //Iterate over multichoices
        for($i = 0; $i < sizeof($multichoices); $i++) {
            $mul_info = $multichoices[$i];

            //Now, build the question_multichoice record structure
            $multichoice = new stdClass;
            $multichoice->question = $new_question_id;
            $multichoice->layout = backup_todb($mul_info['#']['LAYOUT']['0']['#']);
            $multichoice->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multichoice->single = backup_todb($mul_info['#']['SINGLE']['0']['#']);
            $multichoice->shuffleanswers = isset($mul_info['#']['SHUFFLEANSWERS']['0']['#'])?backup_todb($mul_info['#']['SHUFFLEANSWERS']['0']['#']):'';
            if (array_key_exists("CORRECTFEEDBACK", $mul_info['#'])) {
                $multichoice->correctfeedback = backup_todb($mul_info['#']['CORRECTFEEDBACK']['0']['#']);
            } else {
                $multichoice->correctfeedback = '';
            }
            if (array_key_exists("PARTIALLYCORRECTFEEDBACK", $mul_info['#'])) {
                $multichoice->partiallycorrectfeedback = backup_todb($mul_info['#']['PARTIALLYCORRECTFEEDBACK']['0']['#']);
            } else {
                $multichoice->partiallycorrectfeedback = '';
            }
            if (array_key_exists("INCORRECTFEEDBACK", $mul_info['#'])) {
                $multichoice->incorrectfeedback = backup_todb($mul_info['#']['INCORRECTFEEDBACK']['0']['#']);
            } else {
                $multichoice->incorrectfeedback = '';
            }
            if (array_key_exists("ANSWERNUMBERING", $mul_info['#'])) {
                $multichoice->answernumbering = backup_todb($mul_info['#']['ANSWERNUMBERING']['0']['#']);
            } else {
                $multichoice->answernumbering = 'abc';
            }

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($multichoice->answers,",");
            while ($tok) {
                //Get the answer from backup_ids
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
                if ($answer) {
                    if ($in_first) {
                        $answers_field .= $answer->new_id;
                        $in_first = false;
                    } else {
                        $answers_field .= ",".$answer->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multichoice->answers = $answers_field;

            //The structure is equal to the db, so insert the question_shortanswer
            $newid = insert_record ("question_multichoice",$multichoice);

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

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function restore_recode_answer($state, $restore) {
        $pos = strpos($state->answer, ':');
        $order = array();
        $responses = array();
        if (false === $pos) { // No order of answers is given, so use the default
            if ($state->answer) {
                $responses = explode(',', $state->answer);
            }
        } else {
            $order = explode(',', substr($state->answer, 0, $pos));
            if ($responsestring = substr($state->answer, $pos + 1)) {
                $responses = explode(',', $responsestring);
            }
        }
        if ($order) {
            foreach ($order as $key => $oldansid) {
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$oldansid);
                if ($answer) {
                    $order[$key] = $answer->new_id;
                } else {
                    echo 'Could not recode multichoice answer id '.$oldansid.' for state '.$state->oldid.'<br />';
                }
            }
        }
        if ($responses) {
            foreach ($responses as $key => $oldansid) {
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$oldansid);
                if ($answer) {
                    $responses[$key] = $answer->new_id;
                } else {
                    echo 'Could not recode multichoice response answer id '.$oldansid.' for state '.$state->oldid.'<br />';
                }
            }
        }
        return implode(',', $order).':'.implode(',', $responses);
    }

    /**
     * Decode links in question type specific tables.
     * @return bool success or failure.
     */
    function decode_content_links_caller($questionids, $restore, &$i) {
        $status = true;

        // Decode links in the question_multichoice table.
        if ($multichoices = get_records_list('question_multichoice', 'question',
                implode(',',  $questionids), '', 'id, correctfeedback, partiallycorrectfeedback, incorrectfeedback')) {

            foreach ($multichoices as $multichoice) {
                $correctfeedback = restore_decode_content_links_worker($multichoice->correctfeedback, $restore);
                $partiallycorrectfeedback = restore_decode_content_links_worker($multichoice->partiallycorrectfeedback, $restore);
                $incorrectfeedback = restore_decode_content_links_worker($multichoice->incorrectfeedback, $restore);
                if ($correctfeedback != $multichoice->correctfeedback ||
                        $partiallycorrectfeedback != $multichoice->partiallycorrectfeedback ||
                        $incorrectfeedback != $multichoice->incorrectfeedback) {
                    $subquestion->correctfeedback = addslashes($correctfeedback);
                    $subquestion->partiallycorrectfeedback = addslashes($partiallycorrectfeedback);
                    $subquestion->incorrectfeedback = addslashes($incorrectfeedback);
                    if (!update_record('question_multichoice', $multichoice)) {
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

    /**
     * @return array of the numbering styles supported. For each one, there
     *      should be a lang string answernumberingxxx in teh qtype_multichoice
     *      language file, and a case in the switch statement in number_in_style,
     *      and it should be listed in the definition of this column in install.xml.
     */
    function get_numbering_styles() {
        return array('abc', 'ABCD', '123', 'none');
    }

    function number_html($qnum) {
        return '<span class="anun">' . $qnum . '<span class="anumsep">.</span></span> ';
    }

    /**
     * @param int $num The number, starting at 0.
     * @param string $style The style to render the number in. One of the ones returned by $numberingoptions.
     * @return string the number $num in the requested style.
     */
    function number_in_style($num, $style) {
        switch($style) {
            case 'abc':
                return $this->number_html(chr(ord('a') + $num));
            case 'ABCD':
                return $this->number_html(chr(ord('A') + $num));
            case '123':
                return $this->number_html(($num + 1));
            case 'none':
                return '';
            default:
                return 'ERR';
        }
    }

    function find_file_links($question, $courseid){
        $urls = array();
        // find links in the answers table.
        $urls +=  question_find_file_links_from_html($question->options->correctfeedback, $courseid);
        $urls +=  question_find_file_links_from_html($question->options->partiallycorrectfeedback, $courseid);
        $urls +=  question_find_file_links_from_html($question->options->incorrectfeedback, $courseid);
        foreach ($question->options->answers as $answer) {
            $urls += question_find_file_links_from_html($answer->answer, $courseid);
        }
        //set all the values of the array to the question id
        if ($urls){
            $urls = array_combine(array_keys($urls), array_fill(0, count($urls), array($question->id)));
        }
        $urls = array_merge_recursive($urls, parent::find_file_links($question, $courseid));
        return $urls;
    }

    function replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination){
        parent::replace_file_links($question, $fromcourseid, $tocourseid, $url, $destination);
        // replace links in the question_match_sub table.
        // We need to use a separate object, because in load_question_options, $question->options->answers
        // is changed from a comma-separated list of ids to an array, so calling update_record on
        // $question->options stores 'Array' in that column, breaking the question.
        $optionschanged = false;
        $newoptions = new stdClass;
        $newoptions->id = $question->options->id;
        $newoptions->correctfeedback = question_replace_file_links_in_html($question->options->correctfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        $newoptions->partiallycorrectfeedback  = question_replace_file_links_in_html($question->options->partiallycorrectfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        $newoptions->incorrectfeedback = question_replace_file_links_in_html($question->options->incorrectfeedback, $fromcourseid, $tocourseid, $url, $destination, $optionschanged);
        if ($optionschanged){
            if (!update_record('question_multichoice', addslashes_recursive($newoptions))) {
                error('Couldn\'t update \'question_multichoice\' record '.$newoptions->id);
            }
        }
        $answerchanged = false;
        foreach ($question->options->answers as $answer) {
            $answer->answer = question_replace_file_links_in_html($answer->answer, $fromcourseid, $tocourseid, $url, $destination, $answerchanged);
            if ($answerchanged){
                if (!update_record('question_answers', addslashes_recursive($answer))){
                    error('Couldn\'t update \'question_answers\' record '.$answer->id);
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
        $question->category = $form->category;
        $form->questiontext = "How old is the sun?";
        $form->generalfeedback = "General feedback";
        $form->penalty = 0.1;
        $form->single = 1;
        $form->shuffleanswers = 1;
        $form->answernumbering = 'abc';
        $form->noanswers = 3;
        $form->answer = array('Ancient', '5 billion years old', '4.5 billion years old');
        $form->fraction = array(0.3, 0.9, 1);
        $form->feedback = array('True, but lacking in accuracy', 'Close, but no cigar!', 'Yep, that is it!');
        $form->correctfeedback = 'Excellent!';
        $form->incorrectfeedback = 'Nope!';
        $form->partiallycorrectfeedback = 'Not bad';

        if ($courseid) {
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }
}

// Register this question type with the question bank.
question_register_questiontype(new question_multichoice_qtype());
?>
