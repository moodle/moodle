<?php  // $Id$
/**
 * @version $Id$
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
                                "    AND   a.id = n.answer " .
                                "ORDER BY a.id ASC")) {
            notify('Error: Missing question answer for numerical question ' . $question->id . '!');
            return false;
        }
        $this->get_numerical_units($question);

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

    function get_numerical_units(&$question) {
        if ($units = get_records('question_numerical_units',
                                         'question', $question->id, 'id ASC')) {
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
        // Get old versions of the objects
        if (!$oldanswers = get_records('question_answers', 'question', $question->id, 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = get_records('question_numerical', 'question', $question->id, 'answer ASC')) {
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
                if (! update_record("question_answers", $answer)) {
                    $result->error = "Could not update quiz answer! (id=$answer->id)";
                    return $result;
                }
            } else { // This is a completely new answer
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

        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }

        return true;
    }

    function save_numerical_units($question) {
        $result = new stdClass;

        // Delete the units previously saved for this question.
        delete_records('question_numerical_units', 'question', $question->id);

        // Nothing to do.
        if (!isset($question->multiplier)) {
            $result->units = array();
            return $result;
        }

        // Save the new units.
        $units = array();
        foreach ($question->multiplier as $i => $multiplier) {
            // Discard any unit which doesn't specify the unit or the multiplier
            if (!empty($question->multiplier[$i]) && !empty($question->unit[$i])) {
                $units[$i] = new stdClass;
                $units[$i]->question = $question->id;
                $units[$i]->multiplier = $this->apply_unit($question->multiplier[$i], array());
                $units[$i]->unit = $question->unit[$i];
                if (! insert_record('question_numerical_units', $units[$i])) {
                    $result->error = 'Unable to save unit ' . $units[$i]->unit . ' to the Databse';
                    return $result;
                }
            }
        }
        unset($question->multiplier, $question->unit);

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

    function compare_responses(&$question, $state, $teststate) {
        if (isset($state->responses['']) && isset($teststate->responses[''])) {
            return $state->responses[''] == $teststate->responses[''];
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

        $response = $this->apply_unit(stripslashes($state->responses['']), $question->options->units);

        if ($response === false) {
            return false; // The student did not type a number.
        }

        // The student did type a number, so check it with tolerances.
        $this->get_tolerance_interval($answer);
        return ($answer->min <= $response && $response <= $answer->max);
    }

    function get_correct_responses(&$question, &$state) {
        $correct = parent::get_correct_responses($question, $state);
        $unit = $this->get_default_numerical_unit($question);
        if (isset($correct['']) && $correct[''] != '*' && $unit) {
            $correct[''] .= ' '.$unit->unit;
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

    /// BACKUP FUNCTIONS ////////////////////////////

    /**
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

    /**
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {

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
            $newid = insert_record ("question_numerical", $numerical);

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

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
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
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }

}

// INITIATION - Without this line the question type is not in use.
question_register_questiontype(new question_numerical_qtype());
?>
