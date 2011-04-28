<?php

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

    function has_wildcards_in_responses($question, $subqid) {
        global $QTYPES, $OUTPUT;
        foreach ($question->options->questions as $subq){
            if ($subq->id == $subqid){
                return $QTYPES[$subq->qtype]->has_wildcards_in_responses($subq, $subqid);
            }
        }
        echo $OUTPUT->notification('Could not find sub question!');
        return true;
    }

    function requires_qtypes() {
        return array('shortanswer', 'numerical', 'multichoice');
    }

    function get_question_options(&$question) {
        global $QTYPES, $DB, $OUTPUT;

        // Get relevant data indexed by positionkey from the multianswers table
        if (!$sequence = $DB->get_field('question_multianswer', 'sequence', array('question' => $question->id))) {
            echo $OUTPUT->notification(get_string('noquestions','qtype_multianswer',$question->name));
            $question->options->questions['1']= '';
            return true ;
        }

        $wrappedquestions = $DB->get_records_list('question', 'id', explode(',', $sequence), 'id ASC');

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
                    echo $OUTPUT->notification("Unable to get options for questiontype {$wrapped->qtype} (id={$wrapped->id})");
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
            echo $OUTPUT->notification(get_string('noquestions','qtype_multianswer',$question->name));
        }

        return true;
    }

    function save_question_options($question) {
        global $QTYPES, $DB;
        $result = new stdClass;

        // This function needs to be able to handle the case where the existing set of wrapped
        // questions does not match the new set of wrapped questions so that some need to be
        // created, some modified and some deleted
        // Unfortunately the code currently simply overwrites existing ones in sequence. This
        // will make re-marking after a re-ordering of wrapped questions impossible and
        // will also create difficulties if questiontype specific tables reference the id.

        // First we get all the existing wrapped questions
        if (!$oldwrappedids = $DB->get_field('question_multianswer', 'sequence', array('question' => $question->id))) {
            $oldwrappedquestions = array();
        } else {
            $oldwrappedquestions = $DB->get_records_list('question', 'id', explode(',', $oldwrappedids), 'id ASC');
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
                                 $DB->delete_records('question_multichoice', array('question' => $oldwrappedquestion->id));
                                    break;
                                case 'shortanswer':
                                 $DB->delete_records('question_shortanswer', array('question' => $oldwrappedquestion->id));
                                    break;
                                case 'numerical':
                                 $DB->delete_records('question_numerical', array('question' => $oldwrappedquestion->id));
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
            $wrapped = $QTYPES[$wrapped->qtype]->save_question($wrapped, clone($wrapped));
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
            if ($oldid = $DB->get_field('question_multianswer', 'id', array('question' => $question->id))) {
                $multianswer->id = $oldid;
                $DB->update_record("question_multianswer", $multianswer);
            } else {
                $DB->insert_record("question_multianswer", $multianswer);
            }
        }
    }

    function save_question($authorizedquestion, $form) {
        $question = qtype_multianswer_extract_question($form->questiontext);
        if (isset($authorizedquestion->id)) {
            $question->id = $authorizedquestion->id;
        }

        $question->category = $authorizedquestion->category;
        $form->defaultgrade = $question->defaultgrade;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = 0;
        $form->options = clone($question->options);
        unset($question->options);
        return parent::save_question($question, $form);
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
        global $DB;
        $responses = $state->responses;
        // encode - (hyphen) and , (comma) to &#0045; because they are used as
        // delimiters
        array_walk($responses, create_function('&$val, $key',
                '$val = str_replace(array(",", "-"), array("&#0044;", "&#0045;"), $val);
                $val = "$key-$val";'));
        $responses = implode(',', $responses);

        // Set the legacy answer field
        $DB->set_field('question_states', 'answer', $responses, array('id' => $state->id));
        return true;
    }

    function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records("question_multianswer", array("question" => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    function get_correct_responses(&$question, &$state) {
        global $QTYPES;
        $responses = array();
        foreach($question->options->questions as $key => $wrapped) {
            if (!empty($wrapped)){
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

    function get_possible_responses(&$question) {
        global $QTYPES;
        $responses = array();
        foreach($question->options->questions as $key => $wrapped) {
            if (!empty($wrapped)){
                if ($correct = $QTYPES[$wrapped->qtype]->get_possible_responses($wrapped)) {
                    $responses += $correct;
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
    function get_actual_response_details($question, $state){
        global $QTYPES;
        $details = array();
        foreach($question->options->questions as $key => $wrapped) {
            if (!empty($wrapped)){
                $stateforquestion = clone($state);
                $stateforquestion->responses[''] = $state->responses[$key];
                $details = array_merge($details, $QTYPES[$wrapped->qtype]->get_actual_response_details($wrapped, $stateforquestion));
            }
        }
        return $details;
    }

    function get_html_head_contributions(&$question, &$state) {
        global $PAGE;
        parent::get_html_head_contributions($question, $state);
        $PAGE->requires->js('/lib/overlib/overlib.js', true);
        $PAGE->requires->js('/lib/overlib/overlib_cssstyle.js', true);
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $QTYPES, $CFG, $USER, $OUTPUT, $PAGE;

        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $disabled = empty($options->readonly) ? '' : 'disabled="disabled"';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        // adding an icon with alt to warn user this is a fill in the gap question
        // MDL-7497
        if (!empty($USER->screenreader)) {
            echo "<img src=\"".$OUTPUT->pix_url('icon', 'qtype_'.$question->qtype)."\" ".
                "class=\"icon\" alt=\"".get_string('clozeaid','qtype_multichoice')."\" />  ";
        }

        echo '<div class="ablock clearfix">';

        $qtextremaining = format_text($question->questiontext,
                $question->questiontextformat, $formatoptions, $cmoptions->course);

        $strfeedback = get_string('feedback', 'quiz');

        // The regex will recognize text snippets of type {#X}
        // where the X can be any text not containg } or white-space characters.
        while (preg_match('~\{#([^[:space:]}]*)}~', $qtextremaining, $regs)) {
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
            //   echo "<p> multianswer positionkey $positionkey response $response state  <pre>";print_r($state);echo "</pre></p>";

            // Determine feedback popup if any
            $popup = '';
            $style = '';
            $feedbackimg = '';
            $feedback = '' ;
            $correctanswer = '';
            $strfeedbackwrapped  = $strfeedback;
                $testedstate = clone($state);
                if ($correctanswers =  $QTYPES[$wrapped->qtype]->get_correct_responses($wrapped, $state)) {
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
                    if ($correctanswer != '' ) {
                        $feedback = '<div class="correctness">';
                        $feedback .= get_string('correctansweris', 'quiz', s($correctanswer));
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
                    echo "  type=\"text\" value=\"".s($response)."\" ".$styleinfo." /> ";
                    if (!empty($feedback) && !empty($USER->screenreader)) {
                        echo "<img src=\"" . $OUTPUT->pix_url('i/feedback') . "\" alt=\"$feedback\" />";
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
                                s($mcanswer->answer) . '</option>';
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
                            echo "<img src=\"" . $OUTPUT->pix_url('i/feedback') . "\" alt=\"$feedback\" />";
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

                $a->text = format_text($mcanswer->answer, $mcanswer->answerformat, $formatoptions, $cmoptions->course);

                // Print feedback if feedback is on
                if (($options->feedback || $options->correct_responses) && ($checked )) { //|| $options->readonly
                    $a->feedback = format_text($mcanswer->feedback, $mcanswer->feedbackformat, $formatoptions, $cmoptions->course);
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

    public function compare_responses($question, $state, $teststate) {
        global $QTYPES;

        foreach ($question->options->questions as $key => $wrapped) {
            if (empty($wrapped)) {
                continue;
            }

            $stateforquestion = clone($state);
            if (isset($state->responses[$key])) {
                $stateforquestion->responses[''] = $state->responses[$key];
            } else {
                $stateforquestion->responses[''] = '';
            }

            $teststateforquestion = clone($teststate);
            if (isset($teststate->responses[$key])) {
                $teststateforquestion->responses[''] = $teststate->responses[$key];
            } else {
                $teststateforquestion->responses[''] = '';
            }

            if ($wrapped->qtype == 'numerical') {
                // Use shortanswer
                if (!$QTYPES['shortanswer']->compare_responses($wrapped,
                        $stateforquestion, $teststateforquestion)) {
                    return false;
                }
            } else {
                if (!$QTYPES[$wrapped->qtype]->compare_responses($wrapped,
                        $stateforquestion, $teststateforquestion)) {
                    return false;
                }
            }
        }

        return true;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        global $QTYPES;
        $teststate = clone($state);
        $state->raw_grade = 0;
        foreach($question->options->questions as $key => $wrapped) {
            if (!empty($wrapped)){
                if(isset($state->responses[$key])){
                    $state->responses[$key] = $state->responses[$key];
                }else {
                    $state->responses[$key] = '' ;
                }
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
            $responses[$key] = implode(';', $correct);
        }
        return $responses;
    }

    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    function get_random_guess_score($question) {
        $totalfraction = 0;
        foreach (array_keys($question->options->questions) as $key){
            $totalfraction += question_get_random_guess_score($question->options->questions[$key]);
        }
        return $totalfraction / count($question->options->questions);
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
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
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
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
    // $text is an array [text][format][itemid]
    $question = new stdClass;
    $question->qtype = 'multianswer';
    $question->questiontext = $text;
    $question->generalfeedback['text'] = '';
    $question->generalfeedback['format'] = '1';
    $question->generalfeedback['itemid'] = '';
    
    $question->options->questions = array();    
    $question->defaultgrade = 0; // Will be increased for each answer norm

    for ($positionkey=1; preg_match('/'.ANSWER_REGEX.'/', $question->questiontext['text'], $answerregs); ++$positionkey ) {
        $wrapped = new stdClass;
        $wrapped->generalfeedback['text'] = '';
        $wrapped->generalfeedback['format'] = '1';
        $wrapped->generalfeedback['itemid'] = '';
        if (isset($answerregs[ANSWER_REGEX_NORM])&& $answerregs[ANSWER_REGEX_NORM]!== ''){
            $wrapped->defaultgrade = $answerregs[ANSWER_REGEX_NORM];
        } else {
            $wrapped->defaultgrade = '1';
        }
        if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])) {
            $wrapped->qtype = 'numerical';
            $wrapped->multiplier = array();
            $wrapped->units      = array();
            $wrapped->instructions['text'] = '';
            $wrapped->instructions['format'] = '1';
            $wrapped->instructions['itemid'] = '';
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
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = '1';
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = '1';
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = '1';
            $wrapped->incorrectfeedback['itemid'] = '';
            $wrapped->layout = 0;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_REGULAR])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = '1';
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = '1';
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = '1';
            $wrapped->incorrectfeedback['itemid'] = '';
            $wrapped->layout = 1;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE_HORIZONTAL])) {
            $wrapped->qtype = 'multichoice';
            $wrapped->single = 1;
            $wrapped->answernumbering = 0;
            $wrapped->correctfeedback['text'] = '';
            $wrapped->correctfeedback['format'] = '1';
            $wrapped->correctfeedback['itemid'] = '';
            $wrapped->partiallycorrectfeedback['text'] = '';
            $wrapped->partiallycorrectfeedback['format'] = '1';
            $wrapped->partiallycorrectfeedback['itemid'] = '';
            $wrapped->incorrectfeedback['text'] = '';
            $wrapped->incorrectfeedback['format'] = '1';
            $wrapped->incorrectfeedback['itemid'] = '';
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
        $wrapped->questiontext['text'] = $answerregs[0];
        $wrapped->questiontext['format'] = 0 ;
        $wrapped->questiontext['itemid'] = '' ;
        $answerindex = 0 ;

        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (preg_match('/~?'.ANSWER_ALTERNATIVE_REGEX.'/', $remainingalts, $altregs)) {
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $wrapped->fraction["$answerindex"] = '1';
            } else if ($percentile = $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]){
                $wrapped->fraction["$answerindex"] = .01 * $percentile;
            } else {
                $wrapped->fraction["$answerindex"] = '0';
            }
            if (isset($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK])) {
                $feedback = html_entity_decode($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK], ENT_QUOTES, 'UTF-8');
                $feedback = str_replace('\}', '}', $feedback);
                $wrapped->feedback["$answerindex"]['text'] = str_replace('\#', '#', $feedback);
                $wrapped->feedback["$answerindex"]['format'] = '1';
                $wrapped->feedback["$answerindex"]['itemid'] = '';
            } else {
                $wrapped->feedback["$answerindex"]['text'] = '';
                $wrapped->feedback["$answerindex"]['format'] = '1';
                $wrapped->feedback["$answerindex"]['itemid'] = '1';

            }
            if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])
                    && preg_match('~'.NUMERICAL_ALTERNATIVE_REGEX.'~', $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], $numregs)) {
                $wrapped->answer[] = $numregs[NUMERICAL_CORRECT_ANSWER];
                if ($numregs[NUMERICAL_ABS_ERROR_MARGIN]) {
                    $wrapped->tolerance["$answerindex"] =
                    $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $wrapped->tolerance["$answerindex"] = 0;
                }
            } else { // Tolerance can stay undefined for non numerical questions
                // Undo quoting done by the HTML editor.
                $answer = html_entity_decode($altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER], ENT_QUOTES, 'UTF-8');
                $answer = str_replace('\}', '}', $answer);
                $wrapped->answer["$answerindex"] = str_replace('\#', '#', $answer);
            }
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
            $answerindex++ ;
        }

        $question->defaultgrade += $wrapped->defaultgrade;
        $question->options->questions[$positionkey] = clone($wrapped);
        $question->questiontext['text'] = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext['text'], 2));
//    echo"<p>questiontext 2 <pre>";print_r($question->questiontext);echo"<pre></p>";
    }
//    echo"<p>questiontext<pre>";print_r($question->questiontext);echo"<pre></p>";
    $question->questiontext = $question->questiontext;
//    echo"<p>question<pre>";print_r($question);echo"<pre></p>";
    return $question;
}
