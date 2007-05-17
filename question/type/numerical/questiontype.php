<?php  // $Id$

/////////////////
/// NUMERICAL ///
/////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

/// This question type behaves like shortanswer in most cases.
/// Therefore, it extends the shortanswer question type...

require_once("$CFG->dirroot/question/type/shortanswer/questiontype.php");

class question_numerical_qtype extends question_shortanswer_qtype {

    function name() {
        return 'numerical';
    }

    function get_question_options(&$question) {
        // Get the question answers and their respective tolerances
        // Note: question_numerical is an extension of the answer table rather than
        //       the question table as is usually the case for qtype
        //       specific tables.
        global $CFG;
        if (!$question->options->answers = get_records_sql(
                                "SELECT a.*, n.tolerance " .
                                "FROM {$CFG->prefix}question_answers a, " .
                                "     {$CFG->prefix}question_numerical n " .
                                "WHERE a.question = $question->id " .
                                "AND   a.id = n.answer " . 
                                "ORDER BY a.id ASC")) {
            notify('Error: Missing question answer!');
            return false;
        }
        $this->get_numerical_units($question);

        // If units are defined we strip off the defaultunit from the answer, if
        // it is present. (Required for compatibility with the old code and DB).
        if ($defaultunit = $this->get_default_numerical_unit($question)) {
            foreach($question->options->answers as $key => $val) {
                $answer = trim($val->answer);
                $length = strlen($defaultunit->unit);
                if (substr($answer, -$length) == $defaultunit->unit) {
                    $question->options->answers[$key]->answer =
                     substr($answer, 0, strlen($answer)-$length);
                }
            }
        }
        return true;
    }

    function get_numerical_units(&$question) {
        if ($question->options->units = get_records('question_numerical_units',
                                         'question', $question->id, 'id ASC')) {
            $question->options->units  = array_values($question->options->units);
            usort($question->options->units, create_function('$a, $b', // make sure the default unit is at index 0
             'if (1.0 === (float)$a->multiplier) { return -1; } else '.
             'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
            array_walk($question->options->units, create_function('$val',
             '$val->multiplier = (float)$val->multiplier;'));
        } else {
            $question->options->units = array();
        }
        return true;
    }

    function get_default_numerical_unit(&$question) {
        $unit = new stdClass;
        $unit->unit = '';
        $unit->multiplier = 1.0;
        if (!isset($question->options->units[0])) {
            // do nothing
        } else if (1.0 === (float)$question->options->units[0]->multiplier) {
            $unit->unit = $question->options->units[0]->unit;
        } else {
            foreach ($question->options->units as $u) {
                if (1.0 === (float)$unit->multiplier) {
                    $unit->unit = $u->unit;
                    break;
                }
            }
        }
        return $unit;
    }

    function save_question_options($question) {
        // save_question_options supports the definition of multiple answers
        // for numerical questions. This is not currently used by the editing
        // interface, but the GIFT format supports it. The multianswer qtype,
        // for example can make use of this feature.
        // Get old versions of the objects
        if (!$oldanswers = get_records("question_answers", "question", $question->id, 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = get_records("question_numerical", "question", $question->id, 'answer ASC')) {
            $oldoptions = array();
        }

        $result = $this->save_numerical_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = &$result->units;
        }

        // Insert all the new answers
        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                $answer = new stdClass;
                $answer->question = $question->id;
                $answer->answer   = trim($dataanswer);
                $answer->fraction = $question->fraction[$key];
                $answer->feedback = trim($question->feedback[$key]);

                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->id = $oldanswer->id;
                    if (! update_record("question_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    if (! $answer->id = insert_record("question_answers", $answer)) {
                        $result->error = "Could not insert quiz answer!";
                        return $result;
                    }
                }

                // Set up the options object
                if (!$options = array_shift($oldoptions)) {
                    $options = new stdClass;
                }
                $options->question  = $question->id;
                $options->answer    = $answer->id;
                $options->tolerance = $this->apply_unit($question->tolerance[$key], $units);

                // Save options
                if (isset($options->id)) { // reusing existing record
                    if (! update_record('question_numerical', $options)) {
                        $result->error = "Could not update quiz numerical options! (id=$options->id)";
                        return $result;
                    }
                } else { // new options
                    if (! insert_record('question_numerical', $options)) {
                        $result->error = "Could not insert quiz numerical options!";
                        return $result;
                    }
                }

                // delete old answer records
                if (!empty($oldanswers)) {
                    foreach($oldanswers as $oa) {
                        delete_records('question_answers', 'id', $oa->id);
                    }
                }

                // delete old answer records
                if (!empty($oldoptions)) {
                    foreach($oldoptions as $oo) {
                        delete_records('question_numerical', 'id', $oo->id);
                    }
                }

            }
        }
    }

    function save_numerical_units($question) {
        if (!$oldunits = get_records('question_numerical_units', 'question', $question->id, 'id ASC')) {
            $oldunits = array();
        }

        // Set the units
        $units = array();
        $keys  = array();
        $oldunits = array_values($oldunits);
        usort($oldunits, create_function('$a, $b', // make sure the default unit is at index 0
         'if (1.0 === (float)$a->multiplier) { return -1; } else '.
         'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
        foreach ($oldunits as $unit) {
            $units[] = clone($unit);
        }
        $n = isset($question->multiplier) ? count($question->multiplier) : 0;
        for ($i = 0; $i < $n; $i++) {
            // Discard any unit which doesn't specify the unit or the multiplier
            if (!empty($question->multiplier[$i]) && !empty($question->unit[$i])) {
                $units[$i]->question = $question->id;
                $units[$i]->multiplier =
                 $this->apply_unit($question->multiplier[$i], array());
                $units[$i]->unit = $question->unit[$i];
            } else {
                unset($units[$i]);
            }
        }
        unset($question->multiplier, $question->unit);

        /// Save units
        for ($i = 0; $i < $n; $i++) {
            if (!isset($units[$i]) && isset($oldunits[$i])) { // Delete if it hasn't been resubmitted
                delete_records('question_numerical_units', 'id', $oldunits[$i]->id);
            } else if ($oldunits != $units) { // answer has changed or is new
                if (isset($oldunits[$i]->id)) { // answer has changed
                    $units[$i]->id = $oldunits[$i]->id;
                    if (! update_record('question_numerical_units', $units[$i])) {
                        $result->error = "Could not update question_numerical_unit $units[$i]->unit";
                        return $result;
                    }
                } else if (isset($units[$i])) { // answer is new
                    if (! insert_record('question_numerical_units', $units[$i])) {
                        $result->error = "Unable to insert new unit $units[$i]->unit";
                        return $result;
                    }
                }
            }
        }
        $result->units = &$units;
        return $result;
    }

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_numerical", "question", $questionid);
        delete_records("question_numerical_units", "question", $questionid);
        return true;
    }

    function compare_responses(&$question, &$state, &$teststate) {
        $response = isset($state->responses['']) ? $state->responses[''] : '';
        $testresponse = isset($teststate->responses[''])
         ? $teststate->responses[''] : '';
        return ($response == $testresponse);
    }



    // Checks whether a response matches a given answer, taking the tolerance
    // into account. Returns a true for if a response matches the answer, false
    // if it doesn't.
    function test_response(&$question, &$state, $answer) {
        if (isset($state->responses[''])) {
            $response = $this->apply_unit(stripslashes($state->responses['']),
             $question->options->units);
        } else {
            $response = '';
        }

        if (is_numeric($response) && is_numeric($answer->answer)) {
            $this->get_tolerance_interval($answer);
            return ($answer->min <= $response && $answer->max >= $response);
        } else {
            return ($response == $answer->answer);
        }
    }

    // ULPGC ecastro
    function check_response(&$question, &$state){
        $answers = &$question->options->answers;
        foreach($answers as $aid => $answer) {
            if($this->test_response($question, $state, $answer)) {
                return $aid;
            }
        }
        return false;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $answers     = &$question->options->answers;
        $state->raw_grade = 0;
        foreach($answers as $answer) {
            if($this->test_response($question, $state, $answer)) {
                if ($state->raw_grade < $answer->fraction) {
                    $state->raw_grade = $answer->fraction;
                }
            }
        }
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

    function get_correct_responses(&$question, &$state) {
        $correct = parent::get_correct_responses($question, $state);
        if ($unit = $this->get_default_numerical_unit($question)) {
            $correct[''] .= ' '.$unit->unit;
        }
        return $correct;
    }

    // ULPGC ecastro
    function get_all_responses(&$question, &$state) {
        unset($answers);
        $unit = $this->get_default_numerical_unit($question);
        if (is_array($question->options->answers)) {
            foreach ($question->options->answers as $aid=>$answer) {
                unset ($r);
                $r->answer = $answer->answer;
                $r->credit = $answer->fraction;
                $this->get_tolerance_interval($answer);
                if ($unit) {
                    $r->answer .= ' '.$unit->unit;
                }
                if ($answer->max != $answer->min) {
                    $max = "$answer->max"; //format_float($answer->max, 2);
                    $min = "$answer->min"; //format_float($answer->max, 2);
                    $r->answer .= ' ('.$min.'..'.$max.')';
                }
                $answers[$aid] = $r;
            }
        } else {
            $answers[]="error"; // just for debugging, eliminate
        }
        $result->id = $question->id;
        $result->responses = $answers;
        return $result;
    }

    function get_tolerance_interval(&$answer) {
        // No tolerance
        if (empty($answer->tolerance)) {
            $answer->min = $answer->max = $answer->answer;
            return true;
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
                error("Unknown tolerance type $answer->tolerancetype");
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

        $search  = array(' ', ',');
        $replace = array('', '.');
        $rawresponse = str_replace($search, $replace, $rawresponse); // remove spaces
        if (ereg(
         '^([+-]?([0-9]+(\\.[0-9]*)?|[.][0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$',
         $rawresponse, $responseparts)) {
            $responsenum  = (float)$responseparts[1];
            if (isset($tmpunits[$responseparts[5]])) {
                return (float)$responseparts[1] / $tmpunits[$responseparts[5]];
            } else {
                return (float)$responseparts[1];
            }
        }
        return $rawresponse;
    }
    
/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $numericals = get_records('question_numerical', 'question', $question, 'id ASC');
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

        //Get the numerical array
        $numericals = $info['#']['NUMERICAL'];

        //Iterate over numericals
        for($i = 0; $i < sizeof($numericals); $i++) {
            $num_info = $numericals[$i];

            //Now, build the question_numerical record structure
            $numerical->question = $new_question_id;
            $numerical->answer = backup_todb($num_info['#']['ANSWER']['0']['#']);
            $numerical->tolerance = backup_todb($num_info['#']['TOLERANCE']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$numerical->answer);
            if ($answer) {
                $numerical->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_numerical
            $newid = insert_record ("question_numerical",$numerical);

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

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES['numerical']= new question_numerical_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU['numerical'] = get_string("numerical", "quiz");

?>
