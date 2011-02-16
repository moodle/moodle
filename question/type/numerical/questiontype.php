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
 * Question type class for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/question.php');


if ( ! defined ("NUMERICALQUESTIONUNITTEXTINPUTDISPLAY")) {
    define("NUMERICALQUESTIONUNITTEXTINPUTDISPLAY",   0);
}
if ( ! defined ("NUMERICALQUESTIONUNITMULTICHOICEDISPLAY")) {
    define("NUMERICALQUESTIONUNITMULTICHOICEDISPLAY",   1);
}
if ( ! defined ("NUMERICALQUESTIONUNITTEXTDISPLAY")) {
    define("NUMERICALQUESTIONUNITTEXTDISPLAY",   2);
}
if ( ! defined ("NUMERICALQUESTIONUNITNODISPLAY")) {
    define("NUMERICALQUESTIONUNITNODISPLAY",    3);
}


/**
 * The numerical question type class.
 *
 * This class contains some special features in order to make the
 * question type embeddable within a multianswer (cloze) question
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical extends question_type {
    public function has_wildcards_in_responses() {
        return true;
    }

    public function get_question_options($question) {
        global $CFG, $DB, $OUTPUT;
        // Get the question answers and their respective tolerances
        // Note: question_numerical is an extension of the answer table rather than
        //       the question table as is usually the case for qtype
        //       specific tables.
        if (!$question->options->answers = $DB->get_records_sql(
                                "SELECT a.*, n.tolerance " .
                                "FROM {question_answers} a, " .
                                "     {question_numerical} n " .
                                "WHERE a.question = ? " .
                                "    AND   a.id = n.answer " .
                                "ORDER BY a.id ASC", array($question->id))) {
            echo $OUTPUT->notification('Error: Missing question answer for numerical question ' . $question->id . '!');
            return false;
        }

        $question->hints = get_records('question_hints', 'questionid', $question->id, 'id ASC');

        $this->get_numerical_units($question);
        //get_numerical_options() need to know if there are units
        // to set correctly default values
        $this->get_numerical_options($question);

        // If units are defined we strip off the default unit from the answer, if
        // it is present. (Required for compatibility with the old code and DB).
        if ($defaultunit = $this->get_default_numerical_unit($question)) {
            foreach($question->options->answers as $key => $val) {
                $answer = trim($val->answer);
                $length = strlen($defaultunit->unit);
                if ($length && substr($answer, -$length) == $defaultunit->unit) {
                    $question->options->answers[$key]->answer =
                            substr($answer, 0, strlen($answer)-$length);
                }
            }
        }

        return true;
    }

    public function get_numerical_units(&$question) {
        global $DB;

        if ($units = $DB->get_records('question_numerical_units',
                array('question' => $question->id), 'id ASC')) {
            $units = array_values($units);
        } else {
            $units = array();
        }
        foreach ($units as $key => $unit) {
            $units[$key]->multiplier = clean_param($unit->multiplier, PARAM_NUMBER);
        }
        $question->options->units = $units;
        return true;
    }

    public function get_default_numerical_unit(&$question) {
        if (isset($question->options->units[0])) {
            foreach ($question->options->units as $unit) {
                if (abs($unit->multiplier - 1.0) < '1.0e-' . ini_get('precision')) {
                    return $unit;
                }
            }
        }
        return false;
    }

    public function get_numerical_options(&$question) {
        global $DB;
        if (!$options = $DB->get_record('question_numerical_options', array('question' => $question->id))) {
            $question->options->unitgradingtype = 0; // total grade
            $question->options->unitpenalty = 0.1; // default for old questions
            // the default
            if ($defaultunit = $this->get_default_numerical_unit($question)) {
                // so units can be graded
                $question->options->showunits = NUMERICALQUESTIONUNITTEXTINPUTDISPLAY ;
            }else {
                // only numerical will be graded
                $question->options->showunits = NUMERICALQUESTIONUNITNODISPLAY ;
            }
            $question->options->unitsleft = 0 ;
            $question->options->instructions = '';
            $question->options->instructionsformat = editors_get_preferred_format();
        } else {
            $question->options->unitgradingtype = $options->unitgradingtype;
            $question->options->unitpenalty = $options->unitpenalty;
            $question->options->showunits = $options->showunits;
            $question->options->unitsleft = $options->unitsleft;
            $question->options->instructions = $options->instructions;
            $question->options->instructionsformat = $options->instructionsformat; 
        }

        return true;
    }

    /**
     * Save the units and the answers associated with this question.
     */
    public function save_question_options($question) {
        global $DB;
        $context = $question->context;

        // Get old versions of the objects
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');
        $oldoptions = $DB->get_records('question_numerical',
                array('question' => $question->id), 'answer ASC');

        // Save the units.
        $result = $this->save_numerical_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = $result->units;
        }

        // Insert all the new answers
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ingore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            if (trim($answerdata) === '*') {
                $answer->answer = '*';
            } else {
                $answer->answer = $this->apply_unit($answerdata, $units);
                if ($answer->answer === false) {
                    $result->notice = get_string('invalidnumericanswer', 'quiz');
                }
            }
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];
            $DB->update_record('question_answers', $answer);

            // Set up the options object
            if (!$options = array_shift($oldoptions)) {
                $options = new stdClass();
            }
            $options->question = $question->id;
            $options->answer   = $answer->id;
            if (trim($question->tolerance[$key]) == '') {
                $options->tolerance = '';
            } else {
                $options->tolerance = $this->apply_unit($question->tolerance[$key], $units);
                if ($options->tolerance === false) {
                    $result->notice = get_string('invalidnumerictolerance', 'quiz');
                }
            }
            if (isset($options->id)) {
                $DB->update_record('question_numerical', $options);
            } else {
                $DB->insert_record('question_numerical', $options);
            }
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }
        foreach($oldoptions as $oldoption) {
            $DB->delete_records('question_numerical', array('id' => $oldoption->id));
        }

        $result = $this->save_numerical_options($question);
        if (!empty($result->error) || !empty($result->notice)) {
            return $result;
        }

        return true;
    }

    /**
     * The numerical options control the display and the grading of the unit
     * part of the numerical question and related types (calculateds)
     * Questions previous to 2,0 do not have this table as multianswer questions
     * in all versions including 2,0. The default values are set to give the same grade
     * as old question.
     *
     */
    function save_numerical_options($question) {
        global $DB;

        $result = new stdClass();

        $update = true ;
        $options = $DB->get_record('question_numerical_options', array('question' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->question = $question->id;
            $options->instructions = '';
            $options->id = $DB->insert_record('question_numerical_options', $options);
        }

        if (isset($question->options->unitgradingtype)) {
            $options->unitgradingtype = $question->options->unitgradingtype;
        } else {
            $options->unitgradingtype = 0 ;
        }
        if (isset($question->unitpenalty)){
            $options->unitpenalty = $question->unitpenalty;
        } else { //so this is either an old question or a close question type
            $options->unitpenalty = 1 ;
        }
        // if we came from the form then 'unitrole' exists
        if (isset($question->unitrole)){
            switch ($question->unitrole){
                case '0' : $options->showunits = NUMERICALQUESTIONUNITNODISPLAY ;
                break ;
                case '1' : $options->showunits = NUMERICALQUESTIONUNITTEXTDISPLAY ;
                break ;
                case '2' : $options->showunits = NUMERICALQUESTIONUNITTEXTINPUTDISPLAY ;
                           $options->unitgradingtype = 0 ;
                break ;
                case '3' : $options->showunits = $question->multichoicedisplay ;
                           $options->unitgradingtype = $question->unitgradingtypes ;
                break ;
            }
        } else {
            if (isset($question->showunits)){
                $options->showunits = $question->showunits;
            } else {
                if ($defaultunit = $this->get_default_numerical_unit($question)) {
                    // so units can be used
                    $options->showunits = NUMERICALQUESTIONUNITTEXTINPUTDISPLAY ;
                } else {
                    // only numerical will be graded
                    $options->showunits = NUMERICALQUESTIONUNITNODISPLAY ;
                }
            }
        }

        if (isset($question->unitsleft)) {
            $options->unitsleft = $question->unitsleft;
        } else {
            $options->unitsleft = 0 ;
        }

        $options->instructions = $this->import_or_save_files($question->instructions,
                    $question->context, 'qtype_'.$question->qtype , 'instruction', $question->id);
        $options->instructionsformat = $question->instructions['format'];

        $DB->update_record('question_numerical_options', $options);

        $this->save_hints($question);

        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }

        return true;
    }

    public function save_numerical_units($question) {
        global $DB;
        $result = new stdClass();

        // Delete the units previously saved for this question.
        $DB->delete_records('question_numerical_units', array('question' => $question->id));

        // Nothing to do.
        if (!isset($question->multiplier)) {
            $result->units = array();
            return $result;
        }

        // Save the new units.
        $units = array();
        $unitalreadyinsert = array();
        foreach ($question->multiplier as $i => $multiplier) {
            // Discard any unit which doesn't specify the unit or the multiplier
            if (!empty($question->multiplier[$i]) && !empty($question->unit[$i])&& !array_key_exists($question->unit[$i],$unitalreadyinsert)) {
                $unitalreadyinsert[$question->unit[$i]] = 1 ;
                $units[$i] = new stdClass();
                $units[$i]->question = $question->id;
                $units[$i]->multiplier = $this->apply_unit($question->multiplier[$i], array());
                $units[$i]->unit = $question->unit[$i];
                $DB->insert_record('question_numerical_units', $units[$i]);
            }
        }
        unset($question->multiplier, $question->unit);

        $result->units = &$units;
        return $result;
    }

    function find_unit_index(&$question,$value){
            $length = 0;
            $goodkey = 0 ;
            foreach ($question->options->units as $key => $unit){
                    if($unit->unit ==$value ) {
                    return $key ;
                }
            }
        return 0 ;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_numerical_answers($question, $questiondata);
        $this->initialise_numerical_units($question, $questiondata);
    }

    protected function initialise_numerical_answers(question_definition $question, $questiondata) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new qtype_numerical_answer($a->answer,
                    $a->fraction, $a->feedback, $a->tolerance);
        }
    }

    protected function initialise_numerical_units(question_definition $question, $questiondata) {
        if (empty($questiondata->options->units)) {
            $question->ap = new qtype_numerical_answer_processor(array());
            return;
        }
        $units = array();
        foreach ($questiondata->options->units as $unit) {
            $units[$unit->unit] = $unit->multiplier;
        }
        $question->ap = new qtype_numerical_answer_processor($units);
    }

    function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_numerical', array('question' => $questionid));
        $DB->delete_records('question_numerical_options', array('question' => $questionid));
        $DB->delete_records('question_numerical_units', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    /**
    * This function has been reinserted in numerical/questiontype.php to simplify
    * the separate rendering of number and unit
    */
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG, $OUTPUT;

        $context = $this->get_context_by_category_id($question->category);
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;
        $component = 'qtype_' . $question->qtype;        
        // rewrite instructions text 
        $question->options->instructions = quiz_rewrite_question_urls($question->options->instructions, 'pluginfile.php', $context->id, $component, 'instruction', array($state->attempt, $state->question), $question->id);
        /// Print question text and media

        $questiontext = format_text($question->questiontext,
                $question->questiontextformat,
                $formatoptions, $cmoptions->course);

        /// Print input controls
        // as the entry is controlled the question type here is numerical
        // In all cases there is a text input for the number
        // If $question->options->showunits == NUMERICALQUESTIONUNITTEXTDISPLAY
        // there is an additional text input for the unit
        // If $question->options->showunits == NUMERICALQUESTIONUNITMULTICHOICEDISPLAY"
        // radio elements display the defined unit
        // The code allows the input number elememt to be displayed
        // before i.e. at left or after at rigth of the unit variants.
        $nameanswer = "name=\"".$question->name_prefix."answer\"";
        $nameunit   = "name=\"".$question->name_prefix."unit\"";
        // put old answer data in $state->responses['answer'] and $state->responses['unit']
        if (isset($state->responses['']) && $state->responses[''] != '' && !isset($state->responses['answer'])){
              $this->split_old_answer($state->responses[''], $question->options->units, $state->responses['answer'] ,$state->responses['unit'] );
        }
        // prepare the values of the input elements to be dispalyed answer i.e. number  and unit
        if (isset($state->responses['answer']) && $state->responses['answer']!='') {
            $valueanswer = ' value="'.s($state->responses['answer']).'" ';
        } else {
            $valueanswer = ' value="" ';
        }
        if (isset($state->responses['unit']) && $state->responses['unit']!='') {
            $valueunit = ' value="'.s($state->responses['unit']).'" ';
        } else {
            $valueunit = ' value="" ';
            if ($question->options->showunits == NUMERICALQUESTIONUNITTEXTDISPLAY ){
              $valueunit = ' value="'.s($question->options->units[0]->unit).'" ';
            }
        }

        $feedback = '';
        $class = '';
        $classunit = '' ;
        $classunitvalue = '' ;
        $feedbackimg = '';
        $feedbackimgunit = '' ;
        $answerasterisk = false ;
        $response = '' ;
        $valid_numerical_unit = false ;
        $valid_numerical_unit_index = -1 ;
        $unit_in_numerical_answer = false ;
        $rawgrade = 0 ;
        if ($options->feedback) {
            $class = question_get_feedback_class(0);
            $classunit = question_get_feedback_class(0);
            $feedbackimg = question_get_feedback_image(0);
            $feedbackimgunit = question_get_feedback_image(0);
            $classunitvalue = 0 ;
            $valid_numerical_unit_index = -1 ;
            // if there is unit in answer and unitgradingtype = 0
            // the grade is 0
            //this is OK for the first answer with a good response
            // having to test for * so response as long as not empty
           // $response = $this->extract_numerical_response($state->responses['answer']);
            // test for a greater than 0 grade
            foreach($question->options->answers as $answer) {
                if ($this->test_response($question, $state, $answer)) {
                    // Answer was correct or partially correct.
                    if ( $answer->answer === '*'){
                        $answerasterisk = true ;
                    }
                    // in all cases
                    $class = question_get_feedback_class($answer->fraction);
                    $feedbackimg = question_get_feedback_image($answer->fraction);
                    if ($question->options->unitgradingtype == 0 || ($question->options->unitgradingtype == 0 && $answer->answer === '*')){
                        // if * then unit has the $answer->fraction value
                        // if $question->options->unitgradingtype == 0 everything has been checked
                        // if $question->options->showunits == NUMERICALQUESTIONUNITTEXTINPUTDISPLAY
                        // then number - unit combination has been used to test response
                        // so the unit should have same color
                        $classunit = question_get_feedback_class($answer->fraction);
                        $feedbackimgunit = question_get_feedback_image($answer->fraction);
                        $rawgrade = $answer->fraction ;


                    }else {
                        /* so we need to apply unit grading i.e. to check if the number-unit combination
                        * was the rigth one
                        * on NUMERICALQUESTIONUNITTEXTINPUTDISPLAY we need only to ckeck if applyunit will test OK
                        * with the $state->responses['unit'] value which cannot be empty
                        * if $state->responses['unit']
                        * if apply-unit is true with a specific unit as long as the unit as been written either in the
                        * we need the numerical response and test it with the available units
                        * if the unit used is good then it should be set OK
                        * however the unit could have been put in the number element in this case
                        * the unit penalty should be apllied.
                        * testing apply_unit with no units will get us a false response if there is any text in it
                        * testing apply_unit with a given unit will get a good value if the number is good with this unit
                        * apply unit will return the numerical if
                        * we need to know which conditions let to a good numerical value that were done in the
                        */
                        $valid_numerical_unit = false ;
                        $rawgrade = $answer->fraction ;
                        $valid_numerical_unit_index = -1 ;
                        $invalid_unit_in_numerical_answer = false ;
                        if ( $answerasterisk ) {
                            $classunit = question_get_feedback_class($answer->fraction);
                            $feedbackimgunit = question_get_feedback_image($answer->fraction);
                            $valid_numerical_unit = true ;//everything is true with *
                        } else {
                          //  if( isset($state->responses['unit']) && $state->responses['unit'] != '' ){// unit should be written in the unit input or checked in multichoice
                            // we need to see if something was written in the answer field that was not in the number
                            // although we cannot actually detect units put before the number which will cause bad numerical.
                            // use extract response
                            $response = $this->extract_numerical_response($state->responses['answer']);
                            if(isset($response->unit ) && $response->unit != ''){
                                $unit_in_numerical_answer = true ;
                            }else {
                                $unit_in_numerical_answer = false ;
                            }

                            // the we let the testing to the two cases either
                            // NUMERICALQUESTIONUNITTEXTINPUTDISPLAY or
                            // NUMERICALQUESTIONUNITMULTICHOICEDISPLAY
                            if( !isset($state->responses['unit']) || $state->responses['unit'] == '' ){
                                // unit should be written in the unit input or checked in multichoice
                                $valid_numerical_unit = false ;
                                $classunit = question_get_feedback_class(0);
                                $feedbackimgunit = question_get_feedback_image(0);
                                $empty_unit = true ;
                            } else {
                               // echo"<p> some unit answer <pre>";print_r($answer) ;echo"</pre></p>";
                               // echo"<p> some unit answer <pre>";print_r($answer) ;echo"</pre></p>";
                                $empty_unit = false ;
                                $valid_numerical_unit = false ;

                                foreach ($question->options->units as $key => $unit) {
                                    if ($unit->unit == $state->responses['unit']){
                                    //    $response = $this->apply_unit($state->responses['answer'].$unit->unit, array($question->options->units[$key])) ;
                                //       echo "<p> avant false valid_numerical_unit_index $valid_numerical_unit_index  ".$state->responses['answer']."</p>";
                                        $invalid_unit_found = 0 ;
                                        if ($response->number !== false) {
                                //echo "<p> avanr get valid_numerical_unit_index $valid_numerical_unit_index  </p>";
                                       //     $this->get_tolerance_interval($answer);
                                       $testresponse = $response->number /$unit->multiplier ;
                                            if($answer->min <= $testresponse && $testresponse <= $answer->max){
                                //echo "<p> apres min max  valid_numerical_unit_index $valid_numerical_unit_index  </p>";
                                                $classunit = question_get_feedback_class($answer->fraction) ; //question_get_feedback_class(1);
                                                $feedbackimgunit = question_get_feedback_image($rawgrade);
                                                $valid_numerical_unit = true ;
                                                $valid_numerical_unit_index = $key ;
                                                break ;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($answer->feedback) {
                        $answer->feedback = quiz_rewrite_question_urls($answer->feedback, 'pluginfile.php', $context->id, 'question', 'answerfeedback', array($state->attempt, $state->question), $answer->id);
                        $feedback = format_text($answer->feedback, $answer->feedbackformat, $formatoptions, $cmoptions->course);
                    }

                    break;
                }
            }
    }
        $state->options->raw_unitpenalty = 0 ;
        $raw_unitpenalty = 0 ;
        if( $question->options->showunits == NUMERICALQUESTIONUNITNODISPLAY ||
                $question->options->showunits == NUMERICALQUESTIONUNITTEXTDISPLAY ) {
                    $classunitvalue = 1 ;
        }

        if(! $answerasterisk  && $question->options->unitgradingtype != 0 && (! $valid_numerical_unit || $unit_in_numerical_answer)){
            if($question->options->unitgradingtype == 1){
                $raw_unitpenalty = $question->options->unitpenalty * $rawgrade ;
            }else {
                $raw_unitpenalty = $question->options->unitpenalty ;
            }
            $state->options->raw_unitpenalty = $raw_unitpenalty ;
        }

        /// Removed correct answer, to be displayed later MDL-7496
        include("$CFG->dirroot/question/type/numerical/display.html");
    }


    function compare_responses(&$question, $state, $teststate) {

               if ($question->options->showunits == NUMERICALQUESTIONUNITMULTICHOICEDISPLAY && isset($question->options->units) && isset($state->responses['unit']) && isset($question->options->units[$state->responses['unit']] )){
            $state->responses['unit']=$question->options->units[$state->responses['unit']]->unit;
        };


        $responses = '';
        $testresponses = '';
        if (isset($state->responses['answer'])){
            $responses = $state->responses['answer'];
        }
        if (isset($state->responses['unit'])){
            $responses .= $state->responses['unit'];
        }
        if (isset($teststate->responses['answer'])){
            $testresponses = $teststate->responses['answer'];
        }
        if (isset($teststate->responses['unit'])){
            $testresponses .= $teststate->responses['unit'];
        }

        if ( isset($responses)  && isset($testresponses )) {

            return $responses == $testresponses ;
        }
        return false;
    }

    /**
     * Checks whether a response matches a given answer, taking the tolerance
     * and but NOT the unit into account. Returns a true for if a response matches the
     * answer or in one of the unit , false if it doesn't.
     * the total grading will see if the unit match.
     * if unit != -1 then the test is done only on this unit
     */
    function test_response(&$question, &$state, $answer ) {
        // Deal with the match anything answer.
        if ($answer->answer === '*') {
            return true;
        }
        // using old grading process if $question->unitgradingtype == 0
        // and adding unit1 for the new option NUMERICALQUESTIONUNITTEXTDISPLAY
        if ($question->options->unitgradingtype == 0 ){
            // values coming form old question stored in attempts
            if (!isset($state->responses['answer']) && isset($state->responses[''])){
               $state->responses['answer'] =  $state->responses[''];
            }
            $answertotest = $state->responses['answer'];
            // values coming from  NUMERICALQUESTIONUNITTEXTINPUTDISPLAY
            // or NUMERICALQUESTIONUNITTEXTDISPLAY as unit hidden HTML element

            if($question->options->showunits == NUMERICALQUESTIONUNITTEXTINPUTDISPLAY ){

                $testresponse = $this->extract_numerical_response($state->responses['answer']);
                if($testresponse->unit != '' || $testresponse->number === false){
                   return false;
                }
                $answertotest = $testresponse->number ;
            }
            if(isset($state->responses['unit'])) {
                $answertotest .= $state->responses['unit'] ;
            }
          //  if ($question->options->showunits == NUMERICALQUESTIONUNITTEXTDISPLAY && isset($question->options->units[0])){
           //     $answertotest .= $question->options->units[0]->unit ;
           // }
           // test OK if only numerical or numerical with known unit names with the unit mltiplier applied
            $response = $this->apply_unit($answertotest, $question->options->units);

            if ($response === false) {
                return false; // The student did not type a number.
            }

            // The student did type a number, so check it with tolerances.
            $this->get_tolerance_interval($answer);
            return ($answer->min <= $response && $response <= $answer->max);
        } else { // $question->options->unitgradingtype > 0
            /* testing with unitgradingtype $question->options->unitgradingtype > 0
            * if the response is at least patially true
            * if the numerical value agree in the interval
            * if so the only non valid case will be a bad unit and a unity penalty.

             To be able to test (old) questions that do not have an unit
            * input element the test is done using the $state->responses['']
            * which contains the response which is analyzed by extract_numerical_response()
            * If the data comes from the numerical or calculated display
            * the $state->responses['unit'] comes from either
            * a multichoice radio element NUMERICALQUESTIONUNITMULTICHOICEDISPLAY
            * where the $state->responses['unit'] value is the key => unit object
            * in the  the $question->options->units array
            * or an input text element NUMERICALQUESTIONUNITTEXTINPUTDISPLAY
            * which contains the student response
            * for NUMERICALQUESTIONUNITTEXTDISPLAY and NUMERICALQUESTIONUNITNODISPLAY
            *
            */

            $response = $this->extract_numerical_response($state->responses['answer']);


            if ($response->number === false ) {
                return false; // The student did not type a number.
            }

            // The student did type a number, so check it with tolerances.
            $this->get_tolerance_interval($answer);
            if ($answer->min <= $response->number && $response->number <= $answer->max){
               return true;
            }
            // testing for other units
            if ( isset($question->options->units) && count($question->options->units) > 0) {
                foreach($question->options->units as $key =>$unit){
                    $testresponse = $response->number /$unit->multiplier ;
                    if($answer->min <= $testresponse && $testresponse<= $answer->max) {
                        return true;
                    }
                }
            }
            return false;
        }
        return false;
    }

    /**
    * Performs response processing and grading
    * The function was redefined for handling correctly the two parts
    * number and unit of numerical or calculated questions
    * The code handles also the case when there no unit defined by the user or
    * when used in a multianswer (Cloze) question.
    * This function performs response processing and grading and updates
    * the state accordingly.
    * @return bool         Indicates success or failure.
    * @param object $question The question to be graded. Question type
    *                         specific information is included.
    * @param object $state    The state of the question to grade. The current
    *                         responses are in ->responses. The last graded state
    *                         is in ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included. The ->raw_grade and ->penalty fields
    *                         must be updated. The method is able to
    *                         close the question session (preventing any further
    *                         attempts at this question) by setting
    *                         $state->event to QUESTION_EVENTCLOSEANDGRADE
    * @param object $cmoptions
    */
    function grade_responses(&$question, &$state, $cmoptions) {
        if ( isset($state->responses['']) && $state->responses[''] != '' && !isset($state->responses['answer'])){
              $this->split_old_answer($state->responses[''], $question->options->units, $state->responses['answer'] ,$state->responses['unit'] );
        }

        $state->raw_grade = 0;
        $valid_numerical_unit = false ;
        $break = 0 ;
        $unittested = '';
        $hasunits = 0 ;
        $answerasterisk = false ;

        $break = 0 ;
        foreach($question->options->answers as $answer) {
            if ($this->test_response($question, $state, $answer)) {
                // Answer was correct or partially correct.
                $state->raw_grade = $answer->fraction ;
                if ($question->options->unitgradingtype == 0 || $answer->answer === '*'){
                    // if * then unit has the $answer->fraction value
                    // if $question->options->unitgradingtype == 0 everything has been checked
                    // if $question->options->showunits == NUMERICALQUESTIONUNITTEXTINPUTDISPLAY
                    // then number - unit combination has been used to test response
                    // so the unit should have same color

                }else {
                    // so we need to apply unit grading i.e. to check if the number-unit combination
                    // was the rigth one
                    $valid_numerical_unit = false ;
                    $class = question_get_feedback_class($answer->fraction);
                    $feedbackimg = question_get_feedback_image($answer->fraction);
                    if(isset($state->responses['unit']) && $state->responses['unit'] != '' ){
                        foreach ($question->options->units as $key => $unit) {
                            if ($unit->unit == $state->responses['unit']){

                                $response = $this->apply_unit($state->responses['answer'].$state->responses['unit'], array($question->options->units[$key])) ;
                                if ($response !== false) {
                                    $this->get_tolerance_interval($answer);
                                    if($answer->min <= $response && $response <= $answer->max){
                                        $valid_numerical_unit = true ;
                                    }
                                }
                                break ;
                            }
                        }
                    }
                }
                break ;
            }
        }
        // apply unit penalty
        $raw_unitpenalty = 0 ;
        if($question->options->unitgradingtype != 0 && !empty($question->options->unitpenalty)&& $valid_numerical_unit != true ){
            if($question->options->unitgradingtype == 1){
                $raw_unitpenalty = $question->options->unitpenalty * $state->raw_grade ;
            }else {
                $raw_unitpenalty = $question->options->unitpenalty ;
            }
            $state->raw_grade -= $raw_unitpenalty ;
        }

        // Make sure we don't assign negative or too high marks.
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Update the penalty.
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    public function get_correct_responses($question, $state) {
        $correct = parent::get_correct_responses($question, $state);
        $unit = $this->get_default_numerical_unit($question);
        if (isset($correct['']) && $correct[''] != '*' && $unit) {
            $correct[''] .= ' '.$unit->unit;
        }
        return $correct;
    }

    function get_tolerance_interval(&$answer) {
        // No tolerance
        if (empty($answer->tolerance)) {
            $answer->tolerance = 0;
        }

        // Calculate the interval of correct responses (min/max)
        if (!isset($answer->tolerancetype)) {
            $answer->tolerancetype = 2; // nominal
        }

        // We need to add a tiny fraction depending on the set precision to make the
        // comparison work correctly. Otherwise seemingly equal values can yield
        // false. (fixes bug #3225)
        $tolerance = (float)$answer->tolerance + ("1.0e-".ini_get('precision'));
        switch ($answer->tolerancetype) {
            case '1': case 'relative':
                /// Recalculate the tolerance and fall through
                /// to the nominal case:
                $tolerance = $answer->answer * $tolerance;
                // Do not fall through to the nominal case because the tiny fraction is a factor of the answer
                 $tolerance = abs($tolerance); // important - otherwise min and max are swapped
                $max = $answer->answer + $tolerance;
                $min = $answer->answer - $tolerance;
                break;
            case '2': case 'nominal':
                $tolerance = abs($tolerance); // important - otherwise min and max are swapped
                // $answer->tolerance 0 or something else
                if ((float)$answer->tolerance == 0.0  &&  abs((float)$answer->answer) <= $tolerance ){
                    $tolerance = (float) ("1.0e-".ini_get('precision')) * abs((float)$answer->answer) ; //tiny fraction
                } else if ((float)$answer->tolerance != 0.0 && abs((float)$answer->tolerance) < abs((float)$answer->answer) &&  abs((float)$answer->answer) <= $tolerance){
                    $tolerance = (1+("1.0e-".ini_get('precision')) )* abs((float) $answer->tolerance) ;//tiny fraction
               }

                $max = $answer->answer + $tolerance;
                $min = $answer->answer - $tolerance;
                break;
            case '3': case 'geometric':
                $quotient = 1 + abs($tolerance);
                $max = $answer->answer * $quotient;
                $min = $answer->answer / $quotient;
                break;
            default:
                print_error('unknowntolerance', 'question', '', $answer->tolerancetype);
        }

        $answer->min = $min;
        $answer->max = $max;
        return true;
    }

    /**
     * Checks if the $rawresponse has a unit and applys it if appropriate.
     *
     * @param string $rawresponse  The response string to be converted to a float.
     * @param array $units         An array with the defined units, where the
     *                             unit is the key and the multiplier the value.
     * @return float               The rawresponse with the unit taken into
     *                             account as a float.
     */
    function extract_numerical_response($rawresponse) {
        $extractedresponse = new stdClass() ;
        $rawresponse = trim($rawresponse) ;
        $search  = array(' ', ',');
        // test if a . is present or there are multiple , (i.e. 2,456,789 ) so that we don't need spaces and ,
        if ( strpos($rawresponse,'.' ) !== false || substr_count($rawresponse,',') > 1 ) {
            $replace = array('', '');
        }else { // remove spaces and normalise , to a . .
            $replace = array('', '.');
        }
        $rawresponse = str_replace($search, $replace, $rawresponse);

         if (preg_match('~^([+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$~',
                $rawresponse, $responseparts)) {
        //return (float)$responseparts[1] ;
            $extractedresponse->number = (float)$responseparts[1] ;
        }else {
            $extractedresponse->number = false ;
        }
        if (!empty($responseparts[5])) {
            $extractedresponse->unit = $responseparts[5] ;
        }else {
            $extractedresponse->unit = '';
        }

        // Invalid number. Must be wrong.
        return clone($extractedresponse) ;
    }
    /**
     * Checks if the $rawresponse has a unit and applys it if appropriate.
     *
     * @param string $rawresponse  The response string to be converted to a float.
     * @param array $units         An array with the defined units, where the
     *                             unit is the key and the multiplier the value.
     * @return float               The rawresponse with the unit taken into
     *                             account as a float.
     */
    function apply_unit($rawresponse, $units) {

        // Make units more useful
        $tmpunits = array();
        foreach ($units as $unit) {
            $tmpunits[$unit->unit] = $unit->multiplier;
        }
        // remove spaces and normalise decimal places.
        $rawresponse = trim($rawresponse) ;
        $search  = array(' ', ',');
        // test if a . is present or there are multiple , (i.e. 2,456,789 ) so that we don't need spaces and ,
        if ( strpos($rawresponse,'.' ) !== false || substr_count($rawresponse,',') > 1 ) {
            $replace = array('', '');
        }else { // remove spaces and normalise , to a . .
            $replace = array('', '.');
        }
        $rawresponse = str_replace($search, $replace, $rawresponse);


        // Apply any unit that is present.
        if (ereg('^([+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$',
                $rawresponse, $responseparts)) {
           //     echo"<p> responseparts <pre>";print_r($responseparts) ;echo"</pre></p>";

            if (!empty($responseparts[5])) {

                if (isset($tmpunits[$responseparts[5]])) {
                    // Valid number with unit.
                    return (float)$responseparts[1] / $tmpunits[$responseparts[5]];
                } else {
                    // Valid number with invalid unit. Must be wrong.
                    return false;
                }

            } else {
                // Valid number without unit.
                return (float)$responseparts[1];
            }
        }
        // Invalid number. Must be wrong.
        return false;
    }

    /**
     * function used in function definition_inner()
     * of edit_..._form.php for
     * numerical, calculated, calculatedsimple
     */
    protected function add_units_options(&$mform, &$that){
        // Units are graded
        $mform->addElement('header', 'unithandling', get_string('unitshandling', 'qtype_numerical'));
        $mform->addElement('radio', 'unitrole', get_string('unitnotused', 'qtype_numerical'), get_string('onlynumerical', 'qtype_numerical'),0);
        $mform->addElement('radio', 'unitrole', get_string('unitdisplay', 'qtype_numerical'), get_string('oneunitshown', 'qtype_numerical'),1);
        $mform->addElement('radio', 'unitrole', get_string('unitsused', 'qtype_numerical'), get_string('manynumerical', 'qtype_numerical'),2);
        $mform->addElement('static', 'separator2', '', '<HR/>');
        $mform->addElement('radio', 'unitrole', get_string('unitgraded1', 'qtype_numerical'), get_string('unitgraded', 'qtype_numerical'),3);
        $penaltygrp = array();
        $penaltygrp[] =& $mform->createElement('text', 'unitpenalty', get_string('unitpenalty', 'qtype_numerical') ,
                array('size' => 6));
        $unitgradingtypes = array('1' => get_string('decfractionofresponsegrade', 'qtype_numerical'), '2' => get_string('decfractionofquestiongrade', 'qtype_numerical'));
        $penaltygrp[] =& $mform->createElement('select', 'unitgradingtypes', '' , $unitgradingtypes );
        $mform->addGroup($penaltygrp, 'penaltygrp', get_string('unitpenalty', 'qtype_numerical'),' ' , false);
        $multichoicedisplaygrp = array();
        $multichoicedisplaygrp[] =& $mform->createElement('radio', 'multichoicedisplay', get_string('unitedit', 'qtype_numerical'), get_string('editableunittext', 'qtype_numerical'),0);
        $multichoicedisplaygrp[] =& $mform->createElement('radio', 'multichoicedisplay', get_string('selectunits', 'qtype_numerical') , get_string('unitchoice', 'qtype_numerical'),1);
        $mform->addGroup($multichoicedisplaygrp, 'multichoicedisplaygrp', get_string('studentunitanswer', 'qtype_numerical'),' OR ' , false);
        $unitslefts = array('0' => get_string('rightexample', 'qtype_numerical'),'1' => get_string('leftexample', 'qtype_numerical'));
        $mform->addElement('select', 'unitsleft', get_string('unitposition', 'qtype_numerical') , $unitslefts );

        $mform->addElement('static', 'separator2', '<HR/>', '<HR/>');

        $mform->addElement('editor', 'instructions', get_string('instructions', 'qtype_numerical'), null, $that->editoroptions);
        $showunits1grp = array();
        $mform->addElement('static', 'separator2', '<HR/>', '<HR/>');

        $mform->setType('unitpenalty', PARAM_NUMBER);
        $mform->setDefault('unitpenalty', 0.1);
        $mform->setDefault('unitgradingtypes', 1);
        $mform->addHelpButton('penaltygrp', 'unitpenalty', 'qtype_numerical');
        $mform->setDefault('unitsleft', 0);
        $mform->setType('instructions', PARAM_RAW);
        $mform->addHelpButton('instructions', 'numericalinstructions', 'qtype_numerical');
        $mform->disabledIf('penaltygrp', 'unitrole','eq','0');
        $mform->disabledIf('penaltygrp', 'unitrole','eq','1');
        $mform->disabledIf('penaltygrp', 'unitrole','eq','2');
        $mform->disabledIf('unitsleft', 'unitrole','eq','0');
        $mform->disabledIf('multichoicedisplay','unitrole','eq','0');
        $mform->disabledIf('multichoicedisplay','unitrole','eq','1');
        $mform->disabledIf('multichoicedisplay','unitrole','eq','2');
    }

    /**
     * function used in in function definition_inner()
     * of edit_..._form.php for
     * numerical, calculated, calculatedsimple
     */
    protected function add_units_elements(& $mform,& $that) {
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
        $mform->setType('multiplier', PARAM_NUMBER);

        if (isset($that->question->options->units)){
            $countunits = count($that->question->options->units);
        } else {
            $countunits = 0;
        }
        if ($that->question->formoptions->repeatelements){
            $repeatsatstart = $countunits + 1;
        } else {
            $repeatsatstart = $countunits;
        }
        $that->repeat_elements($repeated, $repeatsatstart, array(), 'nounits', 'addunits', 2, get_string('addmoreunitblanks', 'qtype_calculated', '{no}'));

        if ($mform->elementExists('multiplier[0]')){
            $firstunit =& $mform->getElement('multiplier[0]');
            $firstunit->freeze();
            $firstunit->setValue('1.0');
            $firstunit->setPersistantFreeze(true);
            $mform->addHelpButton('multiplier[0]', 'numericalmultiplier', 'qtype_numerical');
        }
    }

    /**
      * function used in in function data_preprocessing() of edit_numerical_form.php for
      * numerical, calculated, calculatedsimple
      */
    function set_numerical_unit_data($mform, &$question, &$default_values){

        list($categoryid) = explode(',', $question->category);
        $context = $this->get_context_by_category_id($categoryid);

        if (isset($question->options)){
            if (isset($question->options->unitpenalty)){
                $default_values['unitpenalty'] = $question->options->unitpenalty ;
            }
            $default_values['unitgradingtypes'] = 1 ;
            if (isset($question->options->unitgradingtype )&& isset($question->options->showunits ) ){
                if ( $question->options->unitgradingtype == 2 ) {
                    $default_values['unitgradingtypes'] = 1 ;
                }
                if ( $question->options->unitgradingtype == 0 ) {
                    $default_values['unitgradingtypes'] = 0 ;
                }
                switch ($question->options->showunits){
                    case 0 :// NUMERICALQUESTIONUNITTEXTINPUTDISPLAY
                        if($question->options->unitgradingtype == 0 ){
                            $default_values['unitrole'] = 2 ;
                            $default_values['multichoicedisplay'] = 0 ;
                        }else { // 1 or 2
                            $default_values['unitrole'] = 3 ;
                            $default_values['multichoicedisplay'] = 0 ;
                            $default_values['unitgradingtypes'] = $question->options->unitgradingtype ;
                        }
                        break;
                    case 1 : // NUMERICALQUESTIONUNITMULTICHOICEDISPLAY
                        $default_values['unitrole'] = 3 ;
                        $default_values['multichoicedisplay'] = $question->options->unitgradingtype ;
                        $default_values['unitgradingtypes'] = $question->options->unitgradingtype ;
                        break;
                    case 2 : // NUMERICALQUESTIONUNITTEXTDISPLAY
                        $default_values['unitrole'] = 1 ;
                        break;
                    case 3 : // NUMERICALQUESTIONUNITNODISPLAY
                        $default_values['unitrole'] = 0 ;
                        //  $default_values['showunits1'] = $question->options->showunits ;
                        break;
                }
            }
            if (isset($question->options->unitsleft)){
                $default_values['unitsleft'] = $question->options->unitsleft ;
            }

            // processing files
            $component = 'qtype_' . $question->qtype;
            $draftid = file_get_submitted_draft_itemid('instruction');
            $default_values['instructions'] = array();
            if (isset($question->options->instructionsformat) && isset($question->options->instructions)){
                $default_values['instructions']['format'] = $question->options->instructionsformat;
                $default_values['instructions']['text'] = file_prepare_draft_area(
                    $draftid,       // draftid
                    $context->id,   // context
                    $component,     // component
                    'instruction',  // filarea
                    !empty($question->id)?(int)$question->id:null, // itemid
                    $mform->fileoptions,    // options
                    $question->options->instructions // text
                );
            }
            $default_values['instructions']['itemid'] = $draftid ;

            if (isset($question->options->units)) {
                $units  = array_values($question->options->units);
                if (!empty($units)) {
                    foreach ($units as $key => $unit){
                        $default_values['unit['.$key.']'] = $unit->unit;
                        $default_values['multiplier['.$key.']'] = $unit->multiplier;
                    }
                }
            }
        }
    }

    /**
      * function use in in function validation()
      * of edit_..._form.php for
      * numerical, calculated, calculatedsimple
      */

    function validate_numerical_options(& $data, & $errors){
        $units  = $data['unit'];
            switch ($data['unitrole']){
                case '0' : $showunits = NUMERICALQUESTIONUNITNODISPLAY ;
                break ;
                case '1' : $showunits = NUMERICALQUESTIONUNITTEXTDISPLAY ;
                break ;
                case '2' : $showunits = NUMERICALQUESTIONUNITTEXTINPUTDISPLAY ;
                break ;
                case '3' : $showunits = $data['multichoicedisplay'] ;
                break ;
            }

        if (($showunits == NUMERICALQUESTIONUNITTEXTINPUTDISPLAY) ||
                ($showunits == NUMERICALQUESTIONUNITMULTICHOICEDISPLAY ) ||
                ($showunits == NUMERICALQUESTIONUNITTEXTDISPLAY )){
           if (trim($units[0]) == ''){
             $errors['unit[0]'] = 'You must set a valid unit name' ;
            }
        }
        if ($showunits == NUMERICALQUESTIONUNITNODISPLAY ){
            if (count($units)) {
                foreach ($units as $key => $unit){
                    if ($units[$key] != ''){
                    $errors["unit[$key]"] = 'You must erase this unit name' ;
                    }
                }
            }
        }


        // Check double units.
        $alreadyseenunits = array();
        if (isset($data['unit'])) {
            foreach ($data['unit'] as $key => $unit) {
                $trimmedunit = trim($unit);
                if ($trimmedunit!='' && in_array($trimmedunit, $alreadyseenunits)) {
                    $errors["unit[$key]"] = get_string('errorrepeatedunit', 'qtype_numerical');
                    if (trim($data['multiplier'][$key]) == '') {
                        $errors["multiplier[$key]"] = get_string('errornomultiplier', 'qtype_numerical');
                    }
                } elseif($trimmedunit!='') {
                    $alreadyseenunits[] = $trimmedunit;
                }
            }
        }
             $units  = $data['unit'];
            if (count($units)) {
                foreach ($units as $key => $unit){
                    if (is_numeric($unit)){
                        $errors['unit['.$key.']'] = get_string('mustnotbenumeric', 'qtype_calculated');
                    }
                    $trimmedunit = trim($unit);
                    $trimmedmultiplier = trim($data['multiplier'][$key]);
                    if (!empty($trimmedunit)){
                        if (empty($trimmedmultiplier)){
                            $errors['multiplier['.$key.']'] = get_string('youmustenteramultiplierhere', 'qtype_calculated');
                        }
                        if (!is_numeric($trimmedmultiplier)){
                            $errors['multiplier['.$key.']'] = get_string('mustbenumeric', 'qtype_calculated');
                        }

                    }
                }
            }

    }


    function valid_unit($rawresponse, $units) {
        // Make units more useful
        $tmpunits = array();
        foreach ($units as $unit) {
            $tmpunits[$unit->unit] = $unit->multiplier;
        }
        // remove spaces and normalise decimal places.
        $search  = array(' ', ',');
        $replace = array('', '.');
        $rawresponse = str_replace($search, $replace, trim($rawresponse));

        // Apply any unit that is present.
        if (preg_match('~^([+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$~',
                $rawresponse, $responseparts)) {

            if (!empty($responseparts[5])) {

                if (isset($tmpunits[$responseparts[5]])) {
                    // Valid number with unit.
                    return true ; //(float)$responseparts[1] / $tmpunits[$responseparts[5]];
                } else {
                    // Valid number with invalid unit. Must be wrong.
                    return false;
                }

            } else {
                // Valid number without unit.
                return false ; //(float)$responseparts[1];
            }
        }
        // Invalid number. Must be wrong.
        return false;
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;

        $form->questiontext = "What is 674 * 36?";
        $form->generalfeedback = "Thank you";
        $form->penalty = 0.1;
        $form->defaultgrade = 1;
        $form->noanswers = 3;
        $form->answer = array('24264', '24264', '1');
        $form->tolerance = array(10, 100, 0);
        $form->fraction = array(1, 0.5, 0);
        $form->nounits = 2;
        $form->unit = array(0 => null, 1 => null);
        $form->multiplier = array(1, 0);
        $form->feedback = array('Very good', 'Close, but not quite there', 'Well at least you tried....');

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
    }

    function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);

        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'qtype_numerical', 'instruction', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $fs->delete_area_files($contextid, 'qtype_numerical', 'instruction', $questionid);
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        $itemid = reset($args);
        
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $result = $options->feedback && array_key_exists($itemid, $question->options->answers);
            if (!$result) {
                return false;
            }
            foreach($question->options->answers as $answer) {
                if ($this->test_response($question, $state, $answer)) {
                    return true;
                }
            }
            return false;
        } else if ($filearea == 'instruction') {
            if ($itemid != $question->id) {
                return false;
            } else {
                return true;
            }
        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }
}
