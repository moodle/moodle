<?php
/**
 * @author Martin Dougiamas and many others. Tim Hunt.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 *//** */

require_once("$CFG->dirroot/question/type/shortanswer/questiontype.php");

/**
 * NUMERICAL QUESTION TYPE CLASS
 *
 * This class contains some special features in order to make the
 * question type embeddable within a multianswer (cloze) question
 *
 * This question type behaves like shortanswer in most cases.
 * Therefore, it extends the shortanswer question type...
 * @package questionbank
 * @subpackage questiontypes
 */
class question_numerical_qtype extends question_shortanswer_qtype {

    public $virtualqtype = false;
    public $unitpenalty = 0;
    public $raw_unitgrade = 0 ;
    public $raw_unitpenalty = 0 ;
    public $valid_numerical_unit = true ;
    function name() {
        return 'numerical';
    }

    function has_wildcards_in_responses() {
        return true;
    }

    function requires_qtypes() {
        return array('shortanswer');
    }

    function get_question_options(&$question) {
        // Get the question answers and their respective tolerances
        // Note: question_numerical is an extension of the answer table rather than
        //       the question table as is usually the case for qtype
        //       specific tables.
        global $CFG, $DB, $OUTPUT;
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
    function get_numerical_options(&$question) {
        global $DB;
        if (!$options = $DB->get_record('question_numerical_options', array('question' => $question->id))) {
            $question->options->unitgradingtype = 0;
            $question->options->unitpenalty = 0;
            // the default 
            if ($defaultunit = $this->get_default_numerical_unit($question)) {
                // so units can be graded
                $question->options->showunits = 0 ;
            }else {
                // only numerical will be graded
                $question->options->showunits = 3 ;
            }
            $question->options->unitsleft = 0 ;
            $question->options->instructions = '' ;
        } else {
            $question->options->unitgradingtype = $options->unitgradingtype;
            $question->options->unitpenalty = $options->unitpenalty;
            $question->options->showunits = $options->showunits ;
            $question->options->unitsleft = $options->unitsleft ;
            $question->options->instructions = $options->instructions ;
        }


        return true;
    }
    function get_numerical_units(&$question) {
        global $DB;
        if ($units = $DB->get_records('question_numerical_units', array('question' => $question->id), 'id ASC')) {
            $units  = array_values($units);
        } else {
            $units = array();
        }
        foreach ($units as $key => $unit) {
            $units[$key]->multiplier = clean_param($unit->multiplier, PARAM_NUMBER);
        }
        $question->options->units = $units;
        return true;
    }

    function get_default_numerical_unit(&$question) {
        if (isset($question->options->units[0])) {
            foreach ($question->options->units as $unit) {
                if (abs($unit->multiplier - 1.0) < '1.0e-' . ini_get('precision')) {
                    return $unit;
                }
            }
        }
        return false;
    }

    /**
     * Save the units and the answers associated with this question.
     */
    function save_question_options($question) {
        global $DB;
        // Get old versions of the objects
        if (!$oldanswers = $DB->get_records('question_answers', array('question' =>  $question->id), 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = $DB->get_records('question_numerical', array('question' =>  $question->id), 'answer ASC')) {
            $oldoptions = array();
        }

        // Save the units.
        $result = $this->save_numerical_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = &$result->units;
        }

        // Insert all the new answers
        foreach ($question->answer as $key => $dataanswer) {
            // Check for, and ingore, completely blank answer from the form.
            if (trim($dataanswer) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key])) {
                continue;
            }

            $answer = new stdClass;
            $answer->question = $question->id;
            if (trim($dataanswer) === '*') {
                $answer->answer = '*';
            } else {
                $answer->answer = $this->apply_unit($dataanswer, $units);
                if ($answer->answer === false) {
                    $result->notice = get_string('invalidnumericanswer', 'quiz');
                }
            }
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = trim($question->feedback[$key]);

            if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                $answer->id = $oldanswer->id;
                $DB->update_record("question_answers", $answer);
            } else { // This is a completely new answer
                $answer->id = $DB->insert_record("question_answers", $answer);
            }

            // Set up the options object
            if (!$options = array_shift($oldoptions)) {
                $options = new stdClass;
            }
            $options->question  = $question->id;
            $options->answer    = $answer->id;
            if (trim($question->tolerance[$key]) == '') {
                $options->tolerance = '';
            } else {
                $options->tolerance = $this->apply_unit($question->tolerance[$key], $units);
                if ($options->tolerance === false) {
                    $result->notice = get_string('invalidnumerictolerance', 'quiz');
                }
            }

            // Save options
            if (isset($options->id)) { // reusing existing record
                $DB->update_record('question_numerical', $options);
            } else { // new options
                $DB->insert_record('question_numerical', $options);
            }
        }
        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                $DB->delete_records('question_answers', array('id' => $oa->id));
            }
        }

        // delete old answer records
        if (!empty($oldoptions)) {
            foreach($oldoptions as $oo) {
                $DB->delete_records('question_numerical', array('id' => $oo->id));
            }
        }
        $result = $this->save_numerical_options($question);
        if (isset($result->error)) {
            return $result;
        }
        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }
        return true;
    }

    function save_numerical_options(&$question) {
        global $DB;
        $result = new stdClass;
        // numerical options
        $update = true ;
        $options = $DB->get_record("question_numerical_options", array("question" => $question->id));
        if (!$options) {
            $update = false;
            $options = new stdClass;
            $options->question = $question->id;
        }
        if(isset($question->unitgradingtype)){
            $options->unitgradingtype = $question->unitgradingtype;
        }else {
            $options->unitgradingtype = 0 ;
        }
        if(isset($question->unitpenalty)){
            $options->unitpenalty = $question->unitpenalty;
        }else {
            $options->unitpenalty = 0 ;
        }
        // if we came from the form then 'unitrole' exists
        if(isset($question->unitrole)){
            if ($question->unitrole == 0 ){
                $options->showunits = $question->showunits0;
            }else {
                $options->showunits = $question->showunits1;
            }
        }else {                
            if(isset($question->showunits)){
                $options->showunits = $question->showunits;
            }else {
                if ($defaultunit = $this->get_default_numerical_unit($question)) {
                    // so units can be graded
                    $options->showunits = 0 ;
                }else {
                    // only numerical will be graded
                    $options->showunits = 3 ;
                }
            }
        }
        if(isset($question->unitsleft)){
            $options->unitsleft = $question->unitsleft;
        }else {
            $options->unitsleft = 0 ;
        }
        if(isset($question->instructions)){
            $options->instructions = trim($question->instructions);
        }else {
            $options->instructions = '' ;
        }
        if ($update) {
            if (!$DB->update_record("question_numerical_options", $options)) {
                $result->error = "Could not update numerical question options! (id=$options->id)";
                return $result;
            }
        } else {
            if (!$DB->insert_record("question_numerical_options", $options)) {
                $result->error = "Could not insert numerical question options!";
                return $result;
            }
        }
        return $result;
    }

    function save_numerical_units($question) {
        global $DB;
        $result = new stdClass;

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
            if (!empty($question->multiplier[$i]) && !empty($question->unit[$i])&& !array_key_exists(addslashes($question->unit[$i]),$unitalreadyinsert)) {
                $unitalreadyinsert[addslashes($question->unit[$i])] = 1 ;
                $units[$i] = new stdClass;
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

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        $state->responses = array();
        $state->responses['answer'] =  '';
        $state->responses['unit'] = '';
     /*   if ($question->options->showunits == 1){
            $state->responses['unit'] = '0';
        }*/

        return true;
    }
    function restore_session_and_responses(&$question, &$state) {
       if(false === strpos($state->responses[''], '|||||')){
        // temporary
             $state->responses['answer']= $state->responses[''];
             $state->responses['unit'] = '';
             $this->split_old_answer($state->responses[''], $question->options->units, $state->responses['answer'] ,$state->responses['unit'] );
       }else {
            $responses = explode('|||||', $state->responses['']);
            $state->responses['answer']= $responses[0];
            $state->responses['unit'] = $responses[1];
       }
      //                             echo "<p> restore response $responses ||   <pre>";print_r($state);echo "</pre></p>";

      /*
       if ($question->options->showunits == 1 && isset($question->options->units)){
            $state->responses['unit']=$this->find_unit_index($question,$state->responses['unit']);
       }*/
       return true;
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

    function split_old_answer($rawresponse, $units, &$answer ,&$unit ) {
        $answer = $rawresponse ;
        // remove spaces and normalise decimal places.
        $search  = array(' ', ',');
        $replace = array('', '.');
        $rawresponse = str_replace($search, $replace, trim($rawresponse));
        if (preg_match('~^([+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$~',
                $rawresponse, $responseparts)) {
            $unit = $responseparts[5] ;
            $answer = $responseparts[1] ;
        }
        return ;
    }


    function save_session_and_responses(&$question, &$state) {
        global $DB;
        //                            echo "<p> save session   <pre>";print_r($state);echo "</pre></p>";

        $responses = '';
        if(isset($state->responses['unit']) && isset($question->options->units[$state->responses['unit']])){
            $responses = $state->responses['answer'].'|||||'.$question->options->units[$state->responses['unit']]->unit;
        }else if(isset($state->responses['unit'])){
            $responses = $state->responses['answer'].'|||||'.$state->responses['unit'] ;
        }else {
            $responses = $state->responses['answer'].'|||||';
        }
        // Set the legacy answer field
        if (!$DB->set_field('question_states', 'answer', $responses, array('id' => $state->id))) {
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
        global $DB;
        $DB->delete_records("question_numerical", array("question" => $questionid));
        $DB->delete_records("question_numerical_options", array("question" => $questionid));
        $DB->delete_records("question_numerical_units", array("question" => $questionid));
        return true;
    }


    function compare_responses(&$question, $state, $teststate) {
               if ($question->options->showunits == 1 && isset($question->options->units) && isset($question->options->units[$state->responses['unit']] )){
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
     //    echo "<p> compare response $responses || $testresponses  <pre>";print_r($state);echo "</pre></p>";

        if ( isset($responses)  && isset($testresponses )) {

            return $responses == $testresponses ;
        }
        return false;
    }

    /**
     * Checks whether a response matches a given answer, taking the tolerance
     * and units into account. Returns a true for if a response matches the
     * answer, false if it doesn't.
     */
    function test_response(&$question, &$state, $answer) {
        // Deal with the match anything answer.
        if ($answer->answer === '*') {
            return true;
        }
         //       echo "<p> test response numerical state  <pre>";print_r($state);echo "</pre></p>";
        //        echo "<p> test response numerical question  <pre>";print_r($question);echo "</pre></p>";

       if( isset($state->responses['unit']) && isset($question->options->units[$state->responses['unit']])){
            $state->responses['']=$state->responses['answer'].$question->options->units[$state->responses['unit']]->unit;
           //  echo "<p>test responses valid unit </p>";
        }else if(isset($state->responses['unit'])){
            $state->responses['']= $state->responses['answer'].$state->responses['unit'] ;// why?
        }
         //       echo "<p> test response numerical state  before apply <pre>";print_r($state);echo "</pre></p>";

        $response = $this->apply_unit($state->responses[''], $question->options->units);
                     //  $this->valid_numerical_unit = $this->valid_unit($state->responses[''], $question->options->units);
     //  if ($this->valid_numerical_unit) echo "<p>test responses valid unit </p>";
     //  if (!$this->valid_numerical_unit) echo "<p>test responses not valid unit </p>";
       // echo "<p>state response test $response <pre>";print_r($state);echo "</pre></p>";
       // $this->raw_unitpenalty = 0.1 ;

        if ($response === false) {
            return false; // The student did not type a number.
        }

        // The student did type a number, so check it with tolerances.
        $this->get_tolerance_interval($answer);
        return ($answer->min <= $response && $response <= $answer->max);
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // The default implementation uses the test_response method to
        // compare what the student entered against each of the possible
        // answers stored in the question, and uses the grade from the
        // first one that matches. It also sets the marks and penalty.
        // This should be good enought for most simple question types.
          //     echo "<p>grade responses  <pre>";print_r($state->responses);echo "</pre></p>";

        //first the split response from unit choice display is converted as
        // standard numerical response value.unit
  /*      if (!empty($question->options->showunits) && isset($state->responses['unit'])){
            $state->responses[''] .= $question->options->units[$state->responses['unit']]->unit ;
        }
  */
        //to apply the unit penalty we need to analyse the response in a more complex way
        //the apply_unit() function analysis could be used to obtain the infos
        // however it is used to detect good or bad numbers but also
        // gives false
        $state->raw_grade = 0;
        foreach($question->options->answers as $answer) {
            if($this->test_response($question, $state, $answer)) {
                $state->raw_grade = $answer->fraction;
                $this->raw_unitgrade = $answer->fraction;
                if( $question->options->showunits == 3) {
                    $this->valid_numerical_unit == true ;
                }else {
                $this->valid_numerical_unit = $this->valid_unit($state->responses[''], $question->options->units);
            }
                break;
            }
        }
     //  if ($this->valid_numerical_unit) echo "<p>grade responses valid unit </p>";
     //  if (!$this->valid_numerical_unit) echo "<p>grade responses not valid unit </p>";
        // apply unit penalty
        $this->raw_unitpenalty = 0 ;
        if(!empty($question->options->unitpenalty)&& !$this->valid_numerical_unit ){
            if($question->options->unitgradingtype == 1){
                $this->raw_unitpenalty = $question->options->unitpenalty* $state->raw_grade ;
            }else {
                $this->raw_unitpenalty = $question->options->unitpenalty* $question->maxgrade;
            }
            $state->raw_grade -= $question->options->unitpenalty ;
        }
      //  echo "<p>grade responses  <pre>";print_r($state->responses);echo "</pre></p>";

       // $this->raw_unitpenalty = $question->options->unitpenalty ;
        // Make sure we don't assign negative or too high marks.
     //   $state->raw_grade -= $question->options->unitpenalty ;
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Update the penalty.
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }


    function get_correct_responses(&$question, &$state) {
        $correct = parent::get_correct_responses($question, $state);
        $unit = $this->get_default_numerical_unit($question);
        $correct['answer']= $correct[''];
        if (isset($correct['']) && $correct[''] != '*' && $unit) {
            $correct[''] .= ' '.$unit->unit;
            $correct['unit']= $unit->unit;
        }
        return $correct;
    }

    // ULPGC ecastro
    function get_all_responses(&$question, &$state) {
        $result = new stdClass;
        $answers = array();
        $unit = $this->get_default_numerical_unit($question);
        if (is_array($question->options->answers)) {
            foreach ($question->options->answers as $aid=>$answer) {
                $r = new stdClass;
                $r->answer = $answer->answer;
                $r->credit = $answer->fraction;
                $this->get_tolerance_interval($answer);
                if ($r->answer != '*' && $unit) {
                    $r->answer .= ' ' . $unit->unit;
                }
                if ($answer->max != $answer->min) {
                    $max = "$answer->max"; //format_float($answer->max, 2);
                    $min = "$answer->min"; //format_float($answer->max, 2);
                    $r->answer .= ' ('.$min.'..'.$max.')';
                }
                $answers[$aid] = $r;
            }
        }
        $result->id = $question->id;
        $result->responses = $answers;
        return $result;
    }
    function get_actual_response($question, $state) {
      //   echo "<p>state response numerical GET ACTUAL RESPONSE $question->id $question->qtype <pre>";print_r($state);echo "</pre></p>";
       if (!empty($state->responses) && !empty($state->responses[''])) {
           if(false === strpos($state->responses[''], '|||||')){
                $responses[] = $state->responses[''];
            }else {
                $resp = explode('|||||', $state->responses['']);
                $responses[] = $resp[0].$resp[1];
            }
       } else {
           $responses[] = '';
        }

       return $responses;
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
        if (preg_match('~^([+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$~',
                $rawresponse, $responseparts)) {

            if (!empty($responseparts[5])) {

                if (isset($tmpunits[$responseparts[5]])) {
                    // Valid number with unit.
                    return (float)$responseparts[1] / $tmpunits[$responseparts[5]];
                } else {
                    // Valid number with invalid unit.
                    return (float)$responseparts[1];
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
  * function used in in function definition_inner()
  * of edit_..._form.php for
  * numerical, calculated, calculatedsimple
  */    
    function add_units_options(&$mform, &$that){
        $mform->addElement('header', 'unithandling', get_string('unitshandling', 'qtype_numerical'));
        // Units are graded
        $mform->addElement('radio', 'unitrole', get_string('unitgraded1', 'qtype_numerical'), get_string('unitgraded', 'qtype_numerical'),0);
        $penaltygrp = array();
        $penaltygrp[] =& $mform->createElement('text', 'unitpenalty', get_string('unitpenalty', 'qtype_numerical') ,
                array('size' => 6));
        $unitgradingtypes = array('1' => get_string('decfractionofquestiongrade', 'qtype_numerical'), '2' => get_string('decfractionofresponsegrade', 'qtype_numerical'));
        $penaltygrp[] =& $mform->createElement('select', 'unitgradingtype', '' , $unitgradingtypes );
        $mform->addGroup($penaltygrp, 'penaltygrp', get_string('unitpenalty', 'qtype_numerical'),' ' , false);
        $showunits0grp = array();
        $showunits0grp[] =& $mform->createElement('radio', 'showunits0', get_string('unitedit', 'qtype_numerical'), get_string('editableunittext', 'qtype_numerical'),0);
        $showunits0grp[] =& $mform->createElement('radio', 'showunits0', get_string('selectunits', 'qtype_numerical') , get_string('unitchoice', 'qtype_numerical'),1);
        $mform->addGroup($showunits0grp, 'showunits0grp', get_string('studentunitanswer', 'qtype_numerical'),' OR ' , false);
        $mform->addElement('htmleditor', 'instructions', get_string('instructions', 'qtype_numerical'),
                array('rows' => 10, 'course' => $that->coursefilesid));
        $mform->addElement('static', 'separator1', '<HR/>', '<HR/>');
        // Units are not graded
        $mform->addElement('radio', 'unitrole', get_string('unitnotgraded', 'qtype_numerical'), get_string('onlynumerical', 'qtype_numerical'),1);
        $showunits1grp = array();
        $showunits1grp[] = & $mform->createElement('radio', 'showunits1', '', get_string('no', 'moodle'),3);
        $showunits1grp[] = & $mform->createElement('radio', 'showunits1', '', get_string('yes', 'moodle'),2);
        $mform->addGroup($showunits1grp, 'showunits1grp', get_string('unitdisplay', 'qtype_numerical'),' ' , false);
        $unitslefts = array('0' => get_string('rightexample', 'qtype_numerical'),'1' => get_string('leftexample', 'qtype_numerical'));
        $mform->addElement('static', 'separator2', '<HR/>', '<HR/>');
        $mform->addElement('select', 'unitsleft', get_string('unitposition', 'qtype_numerical') , $unitslefts );
         $currentgrp1 = array();

        $mform->setType('unitpenalty', PARAM_NUMBER);
        $mform->setDefault('unitpenalty', 0.1);
        $mform->setDefault('unitgradingtype', 1);
        $mform->setHelpButton('penaltygrp', array('penaltygrp', get_string('unitpenalty', 'qtype_numerical'), 'qtype_numerical'));
        $mform->setDefault('showunits0', 0);
        $mform->setDefault('showunits1', 3);
        $mform->setDefault('unitsleft', 0);
        $mform->setType('instructions', PARAM_RAW);
        $mform->setHelpButton('instructions', array('instructions', get_string('instructions', 'qtype_numerical'), 'quiz'));
        $mform->disabledIf('penaltygrp', 'unitrole','eq','1');
        $mform->disabledIf('unitgradingtype', 'unitrole','eq','1');
        $mform->disabledIf('instructions', 'unitrole','eq','1');
        $mform->disabledIf('unitsleft', 'showunits1','eq','3');
        $mform->disabledIf('showunits1','unitrole','eq','0');
        $mform->disabledIf('showunits0','unitrole','eq','1');
       

    }
/**
  * function used in in function definition_inner()
  * of edit_..._form.php for
  * numerical, calculated, calculatedsimple
  */    
    function add_units_elements(& $mform,& $that) { 
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
        $mform->setType('multiplier', PARAM_NUMBER);

        if (isset($this->question->options)){
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
        }
    }
/**
  * function use in in function validation()
  * of edit_..._form.php for
  * numerical, calculated, calculatedsimple
  */    

    function validate_numerical_options(& $data, & $errors){
        $units  = $data['unit'];
        if ($data['unitrole'] == 0 ){
            $showunits = $data['showunits0'];
        }else {
            $showunits = $data['showunits1'];
        }
        
        if (($showunits == 0) || ($showunits == 1) || ($showunits == 2)){
           if (trim($units[0]) == ''){
             $errors['unit[0]'] = 'You must set a valid unit name' ;
            }
        }
        if ($showunits == 3 ){
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

    function print_question_grading_details(&$question, &$state, $cmoptions, $options) {
      //           echo "<p>state uestion_grading_details $question->id $question->qtype <pre>";print_r($state);echo "</pre></p>";

        parent::print_question_grading_details($question, $state, $cmoptions, $options);

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

    /// BACKUP FUNCTIONS ////////////////////////////

    /**
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {
        global $DB;

        $status = true;

        $numericals = $DB->get_records('question_numerical', array('question' =>  $question), 'id ASC');
        //If there are numericals
        if ($numericals) {
            //Iterate over each numerical
            foreach ($numericals as $numerical) {
                $status = fwrite ($bf,start_tag("NUMERICAL",$level,true));
                //Print numerical contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$numerical->answer));
                fwrite ($bf,full_tag("TOLERANCE",$level+1,false,$numerical->tolerance));
                //Now backup numerical_units
                $status = question_backup_numerical_units($bf,$preferences,$question,7);
                $status = fwrite ($bf,end_tag("NUMERICAL",$level,true));
            }
            $status = question_backup_numerical_options($bf,$preferences,$question,$level);
            /*            $numerical_options = $DB->get_records("question_numerical_options",array("questionid" => $question),"id");
            if ($numerical_options) {
                //Iterate over each numerical_option
                foreach ($numerical_options as $numerical_option) {
                    $status = fwrite ($bf,start_tag("NUMERICAL_OPTIONS",$level,true));
                    //Print numerical_option contents
                    fwrite ($bf,full_tag("INSTRUCTIONS",$level+1,false,$numerical_option->instructions));
                    fwrite ($bf,full_tag("SHOWUNITS",$level+1,false,$numerical_option->showunits));
                    fwrite ($bf,full_tag("UNITSLEFT",$level+1,false,$numerical_option->unitsleft));
                    fwrite ($bf,full_tag("UNITGRADINGTYPE",$level+1,false,$numerical_option->unitgradingtype));
                    fwrite ($bf,full_tag("UNITPENALTY",$level+1,false,$numerical_option->unitpenalty));
                    $status = fwrite ($bf,end_tag("NUMERICAL_OPTIONS",$level,true));
                }
            }*/

            //Now print question_answers
            $status = question_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    /// RESTORE FUNCTIONS /////////////////

    /**
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {
        global $DB;

        $status = true;

        //Get the numerical array
        if (isset($info['#']['NUMERICAL'])) {
            $numericals = $info['#']['NUMERICAL'];
        } else {
            $numericals = array();
        }

        //Iterate over numericals
        for($i = 0; $i < sizeof($numericals); $i++) {
            $num_info = $numericals[$i];

            //Now, build the question_numerical record structure
            $numerical = new stdClass;
            $numerical->question = $new_question_id;
            $numerical->answer = backup_todb($num_info['#']['ANSWER']['0']['#']);
            $numerical->tolerance = backup_todb($num_info['#']['TOLERANCE']['0']['#']);

            //We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$numerical->answer);
            if ($answer) {
                $numerical->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_numerical
            $newid = $DB->insert_record ("question_numerical", $numerical);

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

            //Now restore numerical_units
            $status = question_restore_numerical_units ($old_question_id,$new_question_id,$num_info,$restore);

            //Now restore numerical_options
            $status = question_restore_numerical_options ($old_question_id,$new_question_id,$num_info,$restore);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = default_questiontype::generate_test($name, $courseid);
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

        return $this->save_question($question, $form, $course);
    }
}

// INITIATION - Without this line the question type is not in use.
question_register_questiontype(new question_numerical_qtype());

