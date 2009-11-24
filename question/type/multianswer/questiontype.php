<?php  // $Id$

///////////////////
/// MULTIANSWER /// (Embedded - cloze)
///////////////////

///
/// The multianswer question type is special in that it
/// depends on a few other question types, i.e.
/// 'multichoice', 'shortanswer' and 'numerical'.
/// These question types have got a few special features that
/// makes them useable by the 'multianswer' question type
///

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class embedded_cloze_qtype extends default_questiontype {

    function name() {
        return 'multianswer';
    }

    function get_question_options(&$question) {
        global $QTYPES;

        // Get relevant data indexed by positionkey from the multianswers table
        if (!$sequence = get_field('question_multianswer', 'sequence', 'question', $question->id)) {
            notify(get_string('noquestions','qtype_multianswer',$question->name));
            $question->options->questions['1']= '';
            return true ;
        }

        $wrappedquestions = get_records_list('question', 'id', $sequence, 'id ASC');

        // We want an array with question ids as index and the positions as values
        $sequence = array_flip(explode(',', $sequence));
        array_walk($sequence, create_function('&$val', '$val++;'));
        //If a question is lost, the corresponding index is null
        // so this null convention is used to test $question->options->questions 
        // before using the values.
        // first all possible questions from sequence are nulled 
        // then filled with the data if available in  $wrappedquestions
        $nbvaliquestion = 0 ;
        foreach($sequence as $seq){
            $question->options->questions[$seq]= '';
        }
        if (isset($wrappedquestions) && is_array($wrappedquestions)){
            foreach ($wrappedquestions as $wrapped) {
                if (!$QTYPES[$wrapped->qtype]->get_question_options($wrapped)) {
                    notify("Unable to get options for questiontype {$wrapped->qtype} (id={$wrapped->id})");                
                }else {
                    // for wrapped questions the maxgrade is always equal to the defaultgrade,
                    // there is no entry in the question_instances table for them
                    $wrapped->maxgrade = $wrapped->defaultgrade;
                    $nbvaliquestion++ ;
                    $question->options->questions[$sequence[$wrapped->id]] = clone($wrapped); // ??? Why do we need a clone here?
                }
            }
        }
        if ($nbvaliquestion == 0 ) {
            notify(get_string('noquestions','qtype_multianswer',$question->name));
        }
        return true;
    }

    function save_question_options($question) {
        global $QTYPES;
        $result = new stdClass;

        // This function needs to be able to handle the case where the existing set of wrapped
        // questions does not match the new set of wrapped questions so that some need to be
        // created, some modified and some deleted
        // Unfortunately the code currently simply overwrites existing ones in sequence. This
        // will make re-marking after a re-ordering of wrapped questions impossible and
        // will also create difficulties if questiontype specific tables reference the id.

        // First we get all the existing wrapped questions
        if (!$oldwrappedids = get_field('question_multianswer', 'sequence', 'question', $question->id)) {
            $oldwrappedquestions = array();
        } else {
            $oldwrappedquestions = get_records_list('question', 'id', $oldwrappedids, 'id ASC');
        }
        $sequence = array();
        foreach($question->options->questions as $wrapped) {
            if (!empty($wrapped)){
            // if we still have some old wrapped question ids, reuse the next of them

                if (is_array($oldwrappedquestions) && $oldwrappedquestion = array_shift($oldwrappedquestions)) {
                    $wrapped->id = $oldwrappedquestion->id;
                    if($oldwrappedquestion->qtype != $wrapped->qtype ) {
                        switch ($oldwrappedquestion->qtype) {
                        case 'multichoice':
                                 delete_records('question_multichoice', 'question' , $oldwrappedquestion->id );
                            break;
                        case 'shortanswer':
                                 delete_records('question_shortanswer', 'question' , $oldwrappedquestion->id );
                            break;
                        case 'numerical':
                                 delete_records('question_numerical', 'question' , $oldwrappedquestion->id );
                            break;
                        default:
                                print_error('qtypenotrecognized', 'qtype_multianswer','',$oldwrappedquestion->qtype);
                                        $wrapped->id = 0 ;
                            }
                    }
                }else {
                    $wrapped->id = 0 ;
                }
            }
            $wrapped->name = $question->name;
            $wrapped->parent = $question->id;
            $previousid = $wrapped->id ;
            $wrapped->category = $question->category . ',1'; // save_question strips this extra bit off again.
            $wrapped = $QTYPES[$wrapped->qtype]->save_question($wrapped,
                    $wrapped, $question->course);
            $sequence[] = $wrapped->id;
            if ($previousid != 0 && $previousid != $wrapped->id ) { 
                // for some reasons a new question has been created
                // so delete the old one
                delete_question($previousid) ;
            }
        }

        // Delete redundant wrapped questions
        if(is_array($oldwrappedquestions) && count($oldwrappedquestions)){
            foreach ($oldwrappedquestions as $oldwrappedquestion) {
                delete_question($oldwrappedquestion->id) ;
            }
        }

        if (!empty($sequence)) {
            $multianswer = new stdClass;
            $multianswer->question = $question->id;
            $multianswer->sequence = implode(',', $sequence);
            if ($oldid = get_field('question_multianswer', 'id', 'question', $question->id)) {
                $multianswer->id = $oldid;
                if (!update_record("question_multianswer", $multianswer)) {
                    $result->error = "Could not update cloze question options! " .
                            "(id=$multianswer->id)";
                    return $result;
                }
            } else {
                if (!insert_record("question_multianswer", $multianswer)) {
                    $result->error = "Could not insert cloze question options!";
                    return $result;
                }
            }
        }
    }

    function save_question($authorizedquestion, $form, $course) {
        $question = qtype_multianswer_extract_question($form->questiontext);
        if (isset($authorizedquestion->id)) {
            $question->id = $authorizedquestion->id;
        }

        $question->category = $authorizedquestion->category;
        $form->course = $course; // To pass the course object to
                                 // save_question_options, where it is
                                 // needed to call type specific
                                 // save_question methods.
        $form->defaultgrade = $question->defaultgrade;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = 0;
        $form->options = clone($question->options);
        unset($question->options);
        return parent::save_question($question, $form, $course);
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        $state->responses = array();
        foreach ($question->options->questions as $key => $wrapped) {
            $state->responses[$key] = '';
        }
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        $responses = explode(',', $state->responses['']);
        $state->responses = array();
        foreach ($responses as $response) {
            $tmp = explode("-", $response);
            // restore encoded characters
            $state->responses[$tmp[0]] = str_replace(array("&#0044;", "&#0045;"),
                    array(",", "-"), $tmp[1]);
        }
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        $responses = $state->responses;
        // encode - (hyphen) and , (comma) to &#0045; because they are used as
        // delimiters
        array_walk($responses, create_function('&$val, $key',
                '$val = str_replace(array(",", "-"), array("&#0044;", "&#0045;"), $val);
                $val = "$key-$val";'));
        $responses = implode(',', $responses);

        // Set the legacy answer field
        if (!set_field('question_states', 'answer', $responses, 'id', $state->id)) {
            return false;
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
        delete_records("question_multianswer", "question", $questionid);
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        global $QTYPES;
        $responses = array();
        foreach($question->options->questions as $key => $wrapped) {
            if (  !empty($wrapped)){
                if ($correct = $QTYPES[$wrapped->qtype]->get_correct_responses($wrapped, $state)) {
                    $responses[$key] = $correct[''];
                } else {
                    // if there is no correct answer to this subquestion then there
                    // can not be a correct answer to the whole question either, so
                    // we have to return null.
                    return null;
                }
            }
        }
        return $responses;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {

        global $QTYPES, $CFG, $USER;
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $disabled = empty($options->readonly) ? '' : 'disabled="disabled"';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        // adding an icon with alt to warn user this is a fill in the gap question
        // MDL-7497
        if (!empty($USER->screenreader)) {
            echo "<img src=\"$CFG->wwwroot/question/type/$question->qtype/icon.gif\" ".
                "class=\"icon\" alt=\"".get_string('clozeaid','qtype_multichoice')."\" />  ";
        }

        echo '<div class="ablock clearfix">';
        // For this question type, we better print the image on top:
        if ($image = get_question_image($question)) {
            echo('<img class="qimage" src="' . $image . '" alt="" /><br />');
        }

        $qtextremaining = format_text($question->questiontext,
                $question->questiontextformat, $formatoptions, $cmoptions->course);

        $strfeedback = get_string('feedback', 'quiz');

        // The regex will recognize text snippets of type {#X}
        // where the X can be any text not containg } or white-space characters.

        while (ereg('\{#([^[:space:]}]*)}', $qtextremaining, $regs)) {
            $qtextsplits = explode($regs[0], $qtextremaining, 2);
            echo $qtextsplits[0];
            echo "<label>"; // MDL-7497
            $qtextremaining = $qtextsplits[1];

            $positionkey = $regs[1];
            if (isset($question->options->questions[$positionkey]) && $question->options->questions[$positionkey] != ''){
            $wrapped = &$question->options->questions[$positionkey];
            $answers = &$wrapped->options->answers;
           // $correctanswers = $QTYPES[$wrapped->qtype]->get_correct_responses($wrapped, $state);

            $inputname = $nameprefix.$positionkey;
            if (isset($state->responses[$positionkey])) {
                $response = $state->responses[$positionkey];
            } else {
                $response = null;
            }

            // Determine feedback popup if any
            $popup = '';
            $style = '';
            $feedbackimg = '';
            $feedback = '' ;
            $correctanswer = '';
            $strfeedbackwrapped  = $strfeedback;
                $testedstate = clone($state);
                if ($correctanswers =  $QTYPES[$wrapped->qtype]->get_correct_responses($wrapped, $testedstate)) {
                    if ($options->readonly && $options->correct_responses) {
                        $delimiter = '';
                        if ($correctanswers) {
                            foreach ($correctanswers as $ca) {
                                switch($wrapped->qtype){
                                    case 'numerical':
                                    case 'shortanswer':
                                        $correctanswer .= $delimiter.$ca;
                                        break ;
                                    case 'multichoice':
                                        if (isset($answers[$ca])){
                                            $correctanswer .= $delimiter.$answers[$ca]->answer;
                                        }
                                        break ;
                                }
                                $delimiter = ', ';
                            }
                        }
                    }
                    if ($correctanswer) {
                        $feedback = '<div class="correctness">';
                        $feedback .= get_string('correctansweris', 'quiz', s($correctanswer, true));
                        $feedback .= '</div>';
                    }
                }
            if ($options->feedback) {
                $chosenanswer = null;
                switch ($wrapped->qtype) {
                    case 'numerical':
                    case 'shortanswer':
                        $testedstate = clone($state);
                        $testedstate->responses[''] = $response;
                        foreach ($answers as $answer) {
                            if($QTYPES[$wrapped->qtype]
                                    ->test_response($wrapped, $testedstate, $answer)) {
                                $chosenanswer = clone($answer);
                                break;
                            }
                        }
                        break;
                    case 'multichoice':
                        if (isset($answers[$response])) {
                            $chosenanswer = clone($answers[$response]);
                        }
                        break;
                    default:
                        break;
                }

                // Set up a default chosenanswer so that all non-empty wrong
                // answers are highlighted red
                if (empty($chosenanswer) && $response != '') {
                    $chosenanswer = new stdClass;
                    $chosenanswer->fraction = 0.0;
                }

                if (!empty($chosenanswer->feedback)) {
                    $feedback = s(str_replace(array("\\", "'"), array("\\\\", "\\'"), $feedback.$chosenanswer->feedback));
                    if  ($options->readonly && $options->correct_responses) {
                        $strfeedbackwrapped = get_string('correctanswerandfeedback', 'qtype_multianswer');
                    }else {
                        $strfeedbackwrapped = get_string('feedback', 'quiz');
                    }
                    $popup = " onmouseover=\"return overlib('$feedback', STICKY, MOUSEOFF, CAPTION, '$strfeedbackwrapped', FGCOLOR, '#FFFFFF');\" ".
                             " onmouseout=\"return nd();\" ";
                }

                /// Determine style
                if ($options->feedback && $response != '') {
                    $style = 'class = "'.question_get_feedback_class($chosenanswer->fraction).'"';
                    $feedbackimg = question_get_feedback_image($chosenanswer->fraction);
                } else {
                    $style = '';
                    $feedbackimg = '';
                }
            }
            if ($feedback !='' && $popup == ''){
                $strfeedbackwrapped = get_string('correctanswer', 'qtype_multianswer');
                    $feedback = s(str_replace(array("\\", "'"), array("\\\\", "\\'"), $feedback));
                    $popup = " onmouseover=\"return overlib('$feedback', STICKY, MOUSEOFF, CAPTION, '$strfeedbackwrapped', FGCOLOR, '#FFFFFF');\" ".
                             " onmouseout=\"return nd();\" ";
            }

            // Print the input control
            switch ($wrapped->qtype) {
                case 'shortanswer':
                case 'numerical':
                    $size = 1 ;
                    foreach ($answers as $answer) {
                        if (strlen(trim($answer->answer)) > $size ){
                            $size = strlen(trim($answer->answer));
                        }
                    }
                    if (strlen(trim($response))> $size ){
                            $size = strlen(trim($response))+1;
                    }
                    $size = $size + rand(0,$size*0.15);
                    $size > 60 ? $size = 60 : $size = $size;
                    $styleinfo = "size=\"$size\"";
                    /**
                    * Uncomment the following lines if you want to limit for small sizes.
                    * Results may vary with browsers see MDL-3274
                    */
                    /*
                    if ($size < 2) {
                        $styleinfo = 'style="width: 1.1em;"';
                    }
                    if ($size == 2) {
                        $styleinfo = 'style="width: 1.9em;"';
                    }
                    if ($size == 3) {
                        $styleinfo = 'style="width: 2.3em;"';
                    }
                    if ($size == 4) {
                        $styleinfo = 'style="width: 2.8em;"';
                    }
                    */

                    echo "<input $style $readonly $popup name=\"$inputname\"";
                    echo "  type=\"text\" value=\"".s($response, true)."\" ".$styleinfo." /> ";
                    if (!empty($feedback) && !empty($USER->screenreader)) {
                        echo "<img src=\"$CFG->pixpath/i/feedback.gif\" alt=\"$feedback\" />";
                    }
                    echo $feedbackimg;
                    break;
                case 'multichoice':
                 if ($wrapped->options->layout == 0 ){
                    $outputoptions = '<option></option>'; // Default empty option
                    foreach ($answers as $mcanswer) {
                        $selected = '';
                        if ($response == $mcanswer->id) {
                            $selected = ' selected="selected"';
                        }
                        $outputoptions .= "<option value=\"$mcanswer->id\"$selected>" .
                                s($mcanswer->answer, true) . '</option>';
                    }
                    // In the next line, $readonly is invalid HTML, but it works in
                    // all browsers. $disabled would be valid, but then the JS for
                    // displaying the feedback does not work. Of course, we should
                    // not be relying on JS (for accessibility reasons), but that is
                    // a bigger problem.
                    //
                    // The span is used for safari, which does not allow styling of
                    // selects.
                    echo "<span $style><select $popup $readonly $style name=\"$inputname\">";
                    echo $outputoptions;
                    echo '</select></span>';
                    if (!empty($feedback) && !empty($USER->screenreader)) {
                        echo "<img src=\"$CFG->pixpath/i/feedback.gif\" alt=\"$feedback\" />";
                    }
                    echo $feedbackimg;
                    }else if ($wrapped->options->layout == 1 || $wrapped->options->layout == 2){
                        $ordernumber=0;
                        $anss =  Array();
                        foreach ($answers as $mcanswer) {
                            $ordernumber++;
                            $checked = '';
                            $chosen = false;
                            $type = 'type="radio"';
                            $name   = "name=\"{$inputname}\"";
                            if ($response == $mcanswer->id) {
                                $checked = 'checked="checked"';
                                $chosen = true;
                            }
                            $a = new stdClass;
                            $a->id   = $question->name_prefix . $mcanswer->id;
                            $a->class = '';
                            $a->feedbackimg = '';
        
                    // Print the control
                    $a->control = "<input $readonly id=\"$a->id\" $name $checked $type value=\"$mcanswer->id\" />";
                if ($options->correct_responses && $mcanswer->fraction > 0) {
                    $a->class = question_get_feedback_class(1);
                }
                if (($options->feedback && $chosen) || $options->correct_responses) {
                    if ($type == ' type="checkbox" ') {
                        $a->feedbackimg = question_get_feedback_image($mcanswer->fraction > 0 ? 1 : 0, $chosen && $options->feedback);
                    } else {
                        $a->feedbackimg = question_get_feedback_image($mcanswer->fraction, $chosen && $options->feedback);
                    }
                }
    
                // Print the answer text: no automatic numbering

                $a->text =format_text($mcanswer->answer, FORMAT_MOODLE, $formatoptions, $cmoptions->course);
    
                // Print feedback if feedback is on
                if (($options->feedback || $options->correct_responses) && ($checked )) { //|| $options->readonly
                    $a->feedback = format_text($mcanswer->feedback, true, $formatoptions, $cmoptions->course);
                } else {
                    $a->feedback = '';
                }
    
                    $anss[] = clone($a);
                }
                ?>
            <?php    if ($wrapped->options->layout == 1 ){
            ?>
                  <table class="answer">
                    <?php $row = 1; foreach ($anss as $answer) { ?>
                      <tr class="<?php echo 'r'.$row = $row ? 0 : 1; ?>">
                        <td class="c0 control">
                          <?php echo $answer->control; ?>
                        </td>
                        <td class="c1 text <?php echo $answer->class ?>">
                          <label for="<?php echo $answer->id ?>">
                            <?php echo $answer->text; ?>
                            <?php echo $answer->feedbackimg; ?>
                          </label>
                        </td>
                        <td class="c0 feedback">
                          <?php echo $answer->feedback; ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </table>
                  <?php }else  if ($wrapped->options->layout == 2 ){
                    ?>
           
                  <table class="answer">
                      <tr class="<?php echo 'r'.$row = $row ? 0 : 1; ?>">
                    <?php $row = 1; foreach ($anss as $answer) { ?>
                        <td class="c0 control">
                          <?php echo $answer->control; ?>
                        </td>
                        <td class="c1 text <?php echo $answer->class ?>">
                          <label for="<?php echo $answer->id ?>">
                            <?php echo $answer->text; ?>
                            <?php echo $answer->feedbackimg; ?>
                          </label>
                        </td>
                        <td class="c0 feedback">
                          <?php echo $answer->feedback; ?>
                        </td>
                    <?php } ?>
                      </tr>
                  </table>
                  <?php }  
                        
                    }else {
                        echo "no valid layout";
                    }
                    
                    break;
                default:
                    $a = new stdClass;
                    $a->type = $wrapped->qtype ; 
                    $a->sub = $positionkey;
                    print_error('unknownquestiontypeofsubquestion', 'qtype_multianswer','',$a);
                    break;
           }
           echo "</label>"; // MDL-7497
        }
        else {
            if(!  isset($question->options->questions[$positionkey])){
                echo $regs[0]."</label>";
            }else {
                echo '</label><div class="error" >'.get_string('questionnotfound','qtype_multianswer',$positionkey).'</div>';
            }
       }
    }

        // Print the final piece of question text:
        echo $qtextremaining;
        $this->print_question_submit_buttons($question, $state, $cmoptions, $options);
        echo '</div>';
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        global $QTYPES;
        $teststate = clone($state);
        $state->raw_grade = 0;
        foreach($question->options->questions as $key => $wrapped) {
            if (!empty($wrapped)){
            $state->responses[$key] = $state->responses[$key];
            $teststate->responses = array('' => $state->responses[$key]);
            $teststate->raw_grade = 0;
            if (false === $QTYPES[$wrapped->qtype]
             ->grade_responses($wrapped, $teststate, $cmoptions)) {
                return false;
            }
            $state->raw_grade += $teststate->raw_grade;
        }
        }
        $state->raw_grade /= $question->defaultgrade;
        $state->raw_grade = min(max((float) $state->raw_grade, 0.0), 1.0)
         * $question->maxgrade;

        if (empty($state->raw_grade)) {
            $state->raw_grade = 0.0;
        }
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    function get_actual_response($question, $state) {
        global $QTYPES;
        $teststate = clone($state);
        foreach($question->options->questions as $key => $wrapped) {
            $state->responses[$key] = html_entity_decode($state->responses[$key]);
            $teststate->responses = array('' => $state->responses[$key]);
            $correct = $QTYPES[$wrapped->qtype]
             ->get_actual_response($wrapped, $teststate);
            // change separator here if you want
            $responsesseparator = ',';
            $responses[$key] = implode($responsesseparator, $correct);
        }
        return $responses;
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $multianswers = get_records("question_multianswer","question",$question,"id");
        //If there are multianswers
        if ($multianswers) {
            //Print multianswers header
            $status = fwrite ($bf,start_tag("MULTIANSWERS",$level,true));
            //Iterate over each multianswer
            foreach ($multianswers as $multianswer) {
                $status = fwrite ($bf,start_tag("MULTIANSWER",$level+1,true));
                //Print multianswer contents
                fwrite ($bf,full_tag("ID",$level+2,false,$multianswer->id));
                fwrite ($bf,full_tag("QUESTION",$level+2,false,$multianswer->question));
                fwrite ($bf,full_tag("SEQUENCE",$level+2,false,$multianswer->sequence));
                $status = fwrite ($bf,end_tag("MULTIANSWER",$level+1,true));
            }
            //Print multianswers footer
            $status = fwrite ($bf,end_tag("MULTIANSWERS",$level,true));
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

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the question_multianswer record structure
            $multianswer = new stdClass;
            $multianswer->question = $new_question_id;
            $multianswer->sequence = backup_todb($mul_info['#']['SEQUENCE']['0']['#']);

            //We have to recode the sequence field (a list of question ids)
            //Extracts question id from sequence
            $sequence_field = "";
            $in_first = true;
            $tok = strtok($multianswer->sequence,",");
            while ($tok) {
                //Get the answer from backup_ids
                $question = backup_getid($restore->backup_unique_code,"question",$tok);
                if ($question) {
                    if ($in_first) {
                        $sequence_field .= $question->new_id;
                        $in_first = false;
                    } else {
                        $sequence_field .= ",".$question->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multianswer->sequence = $sequence_field;
            //The structure is equal to the db, so insert the question_multianswer
            $newid = insert_record("question_multianswer", $multianswer);

            //Save ids in backup_ids
            if ($newid) {
                backup_putid($restore->backup_unique_code,"question_multianswer",
                             $oldid, $newid);
            }

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
        }

        return $status;
    }

    function restore_map($old_question_id,$new_question_id,$info,$restore) {

        $status = true;

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the question_multianswer record structure
            $multianswer->question = $new_question_id;
            $multianswer->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multianswer->positionkey = backup_todb($mul_info['#']['POSITIONKEY']['0']['#']);
            $multianswer->answertype = backup_todb($mul_info['#']['ANSWERTYPE']['0']['#']);
            $multianswer->norm = backup_todb($mul_info['#']['NORM']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //multianswer must exist too.
            //Now, we are going to look for that multianswer in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the multianswer from DB (by question and positionkey)
            $db_multianswer = get_record ("question_multianswer","question",$new_question_id,
                                                      "positionkey",$multianswer->positionkey);
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

            //We have the database multianswer, so update backup_ids
            if ($db_multianswer) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_multianswer",$oldid,
                             $db_multianswer->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function restore_recode_answer($state, $restore) {
        //The answer is a comma separated list of hypen separated sequence number and answers. We may have to recode the answers
        $answer_field = "";
        $in_first = true;
        $tok = strtok($state->answer,",");
        while ($tok) {
            //Extract the multianswer_id and the answer
            $exploded = explode("-",$tok);
            $seqnum = $exploded[0];
            $answer = $exploded[1];
            // $sequence is an ordered array of the question ids.
            if (!$sequence = get_field('question_multianswer', 'sequence', 'question', $state->question)) {
                print_error('missingoption', 'question', '', $state->question);
            }
            $sequence = explode(',', $sequence);
            // The id of the current question.
            $wrappedquestionid = $sequence[$seqnum-1];
            // now we can find the question
            if (!$wrappedquestion = get_record('question', 'id', $wrappedquestionid)) {
                notify("Can't find the subquestion $wrappedquestionid that is used as part $seqnum in cloze question $state->question");
            }
            // For multichoice question we need to recode the answer
            if ($answer and $wrappedquestion->qtype == 'multichoice') {
                //The answer is an answer_id, look for it in backup_ids
                if (!$ans = backup_getid($restore->backup_unique_code,"question_answers",$answer)) {
                    echo 'Could not recode cloze multichoice answer '.$answer.'<br />';
                }
                $answer = $ans->new_id;
            }
            //build the new answer field for each pair
            if ($in_first) {
                $answer_field .= $seqnum."-".$answer;
                $in_first = false;
            } else {
                $answer_field .= ",".$seqnum."-".$answer;
            }
            //check for next
            $tok = strtok(",");
        }
        return $answer_field;
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;
        $form->questiontext = "This question consists of some text with an answer embedded right here {1:MULTICHOICE:Wrong answer#Feedback for this wrong answer~Another wrong answer#Feedback for the other wrong answer~=Correct answer#Feedback for correct answer~%50%Answer that gives half the credit#Feedback for half credit answer} and right after that you will have to deal with this short answer {1:SHORTANSWER:Wrong answer#Feedback for this wrong answer~=Correct answer#Feedback for correct answer~%50%Answer that gives half the credit#Feedback for half credit answer} and finally we have a floating point number {2:NUMERICAL:=23.8:0.1#Feedback for correct answer 23.8~%50%23.8:2#Feedback for half credit answer in the nearby region of the correct answer}.

Note that addresses like www.moodle.org and smileys :-) all work as normal:
 a) How good is this? {:MULTICHOICE:=Yes#Correct~No#We have a different opinion}
 b) What grade would you give it? {3:NUMERICAL:=3:2}

Good luck!
";
        $form->feedback = "feedback";
        $form->generalfeedback = "General feedback";
        $form->fraction = 0;
        $form->penalty = 0.1;
        $form->versioning = 0;

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
question_register_questiontype(new embedded_cloze_qtype());

/////////////////////////////////////////////////////////////
//// ADDITIONAL FUNCTIONS
//// The functions below deal exclusivly with editing
//// of questions with question type 'multianswer'.
//// Therefore they are kept in this file.
//// They are not in the class as they are not
//// likely to be subject for overriding.
/////////////////////////////////////////////////////////////

// ANSWER_ALTERNATIVE regexes
define("ANSWER_ALTERNATIVE_FRACTION_REGEX",
       '=|%(-?[0-9]+)%');
// for the syntax '(?<!' see http://www.perl.com/doc/manual/html/pod/perlre.html#item_C
define("ANSWER_ALTERNATIVE_ANSWER_REGEX",
        '.+?(?<!\\\\|&|&amp;)(?=[~#}]|$)');
define("ANSWER_ALTERNATIVE_FEEDBACK_REGEX",
        '.*?(?<!\\\\)(?=[~}]|$)');
define("ANSWER_ALTERNATIVE_REGEX",
       '(' . ANSWER_ALTERNATIVE_FRACTION_REGEX .')?' .
       '(' . ANSWER_ALTERNATIVE_ANSWER_REGEX . ')' .
       '(#(' . ANSWER_ALTERNATIVE_FEEDBACK_REGEX .'))?');

// Parenthesis positions for ANSWER_ALTERNATIVE_REGEX
define("ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION", 2);
define("ANSWER_ALTERNATIVE_REGEX_FRACTION", 1);
define("ANSWER_ALTERNATIVE_REGEX_ANSWER", 3);
define("ANSWER_ALTERNATIVE_REGEX_FEEDBACK", 5);

// NUMBER_FORMATED_ALTERNATIVE_ANSWER_REGEX is used
// for identifying numerical answers in ANSWER_ALTERNATIVE_REGEX_ANSWER
define("NUMBER_REGEX",
        '-?(([0-9]+[.,]?[0-9]*|[.,][0-9]+)([eE][-+]?[0-9]+)?)');
define("NUMERICAL_ALTERNATIVE_REGEX",
        '^(' . NUMBER_REGEX . ')(:' . NUMBER_REGEX . ')?$');

// Parenthesis positions for NUMERICAL_FORMATED_ALTERNATIVE_ANSWER_REGEX
define("NUMERICAL_CORRECT_ANSWER", 1);
define("NUMERICAL_ABS_ERROR_MARGIN", 6);

// Remaining ANSWER regexes
define("ANSWER_TYPE_DEF_REGEX",
       '(NUMERICAL|NM)|(MULTICHOICE|MC)|(MULTICHOICE_V|MCV)|(MULTICHOICE_H|MCH)|(SHORTANSWER|SA|MW)|(SHORTANSWER_C|SAC|MWC)');
define("ANSWER_START_REGEX",
       '\{([0-9]*):(' . ANSWER_TYPE_DEF_REGEX . '):');

define("ANSWER_REGEX",
        ANSWER_START_REGEX
        . '(' . ANSWER_ALTERNATIVE_REGEX
        . '(~'
        . ANSWER_ALTERNATIVE_REGEX
        . ')*)\}' );

// Parenthesis positions for singulars in ANSWER_REGEX
define("ANSWER_REGEX_NORM", 1);
define("ANSWER_REGEX_ANSWER_TYPE_NUMERICAL", 3);
define("ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE", 4);
define("ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR", 5);
define("ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL", 6);
define("ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER", 7);
define("ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER_C", 8);
define("ANSWER_REGEX_ALTERNATIVES", 9);

function qtype_multianswer_extract_question($text) {
    $question = new stdClass;
    $question->qtype = 'multianswer';
    $question->questiontext = $text;
    $question->options->questions = array();
    $question->defaultgrade = 0; // Will be increased for each answer norm

    for ($positionkey=1
        ; preg_match('/'.ANSWER_REGEX.'/', $question->questiontext, $answerregs)
        ; ++$positionkey ) {
        $wrapped = new stdClass;
        $wrapped->defaultgrade = $answerregs[ANSWER_REGEX_NORM]
            or $wrapped->defaultgrade = '1';
        if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])) {
            $wrapped->qtype = 'numerical';
            $wrapped->multiplier = array();
            $wrapped->units      = array();
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER])) {
            $wrapped->qtype = 'shortanswer';
            $wrapped->usecase = 0;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER_C])) {
            $wrapped->qtype = 'shortanswer';
            $wrapped->usecase = 1;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback = '';
            $wrapped->partiallycorrectfeedback = '';
            $wrapped->incorrectfeedback = '';
            $wrapped->layout = 0;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback = '';
            $wrapped->partiallycorrectfeedback = '';
            $wrapped->incorrectfeedback = '';
            $wrapped->layout = 1;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback = '';
            $wrapped->partiallycorrectfeedback = '';
            $wrapped->incorrectfeedback = '';
            $wrapped->layout = 2;
        } else {
            print_error('unknownquestiontype', 'question', '', $answerregs[2]);
            return false;
        }

        // Each $wrapped simulates a $form that can be processed by the
        // respective save_question and save_question_options methods of the
        // wrapped questiontypes
        $wrapped->answer   = array();
        $wrapped->fraction = array();
        $wrapped->feedback = array();
        $wrapped->shuffleanswers = 1;
        $wrapped->questiontext = $answerregs[0];
        $wrapped->questiontextformat = 0;

        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (preg_match('/~?'.ANSWER_ALTERNATIVE_REGEX.'/', $remainingalts, $altregs)) {
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $wrapped->fraction[] = '1';
            } else if ($percentile = $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]){
                $wrapped->fraction[] = .01 * $percentile;
            } else {
                $wrapped->fraction[] = '0';
            }
            if (isset($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK])) {
                $feedback = html_entity_decode($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK], ENT_QUOTES, 'UTF-8');
                $feedback = str_replace('\}', '}', $feedback);
                $wrapped->feedback[] = str_replace('\#', '#', $feedback);
            } else {
                $wrapped->feedback[] = '';
            }
            if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])
                    && ereg(NUMERICAL_ALTERNATIVE_REGEX, $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], $numregs)) {
                $wrapped->answer[] = $numregs[NUMERICAL_CORRECT_ANSWER];
                if ($numregs[NUMERICAL_ABS_ERROR_MARGIN]) {
                    $wrapped->tolerance[] =
                    $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $wrapped->tolerance[] = 0;
                }
            } else { // Tolerance can stay undefined for non numerical questions
                // Undo quoting done by the HTML editor.
                $answer = html_entity_decode($altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], ENT_QUOTES, 'UTF-8');
                $answer = str_replace('\}', '}', $answer);
                $wrapped->answer[] = str_replace('\#', '#', $answer);
            }
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
        }

        $question->defaultgrade += $wrapped->defaultgrade;
        $question->options->questions[$positionkey] = clone($wrapped);
        $question->questiontext = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext, 2));
    }
    $question->questiontext = $question->questiontext;
    return $question;
}
?>
