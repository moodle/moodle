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

require_once("$CFG->dirroot/mod/quiz/questiontypes/shortanswer/questiontype.php");

class quiz_numerical_qtype extends quiz_shortanswer_qtype {

    function name() {
        return 'numerical';
    }

    function get_question_options(&$question) {
        if (!$question->options->answers = get_records('quiz_answers',
         'question', $question->id)) {
            notify('Error: Missing question answer!');
            return false;
        }

        if (false === ($question->options->tolerance = get_field('quiz_numerical',
         'tolerance', 'question', $question->id))) {
            $question->options->tolerance = 0;
        }
        $this->get_numerical_units($question);
        return true;
    }

    function get_numerical_units(&$question) {
        if ($question->options->units = get_records('quiz_numerical_units',
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
        if (!isset($question->options->units[0])) {
            return '';
        } else if (1.0 === (float)$question->options->units[0]->multiplier) {
            return $question->options->units[0];
        } else {
            foreach ($question->options->units as $unit) {
                if (1.0 === (float)$unit->multiplier) {
                    return $unit;
                }
            }
        }
    }

    function save_question_options($question) {
        // save_question_options supports the definition of multiple answers
        // for numerical questions. This is not currently used by the editing
        // interface, but the GIFT format supports it. The multianswer qtype,
        // for example can make use of this feature.

        // Get old versions of the objects
        if (!$oldanswers = get_records("quiz_answers", "question", $question->id)) {
            $oldanswers = array();
        }

        if (!$oldoptions = get_record("quiz_numerical", "question", $question->id)) {
            $oldoptions = new stdClass;
        }

        $result = $this->save_numerical_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = &$result->units;
        }
        $answerids = array();
        // Insert all the new answers
        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                $answer = new stdClass;
                $answer->question = $question->id;
                $answer->answer   = trim($dataanswer);
                $answer->fraction = $question->fraction[$key];
                $answer->feedback = trim($question->feedback[$key]);
                if (!is_numeric($answer->answer)) {
                    $result->error = "The answer '$answer->answer' is not numeric!";
                    return $result;
                }

                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->id = $oldanswer->id;
                    if (! update_record("quiz_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    if (! $answer->id = insert_record("quiz_answers", $answer)) {
                        $result->error = "Could not insert quiz answer!";
                        return $result;
                    }
                }
                $answerids[] = $answer->id;
            }
        }

        // Set up the options object
        $options = clone($oldoptions);
        $options->question  = $question->id;
        $options->answers   = implode(',', $answerids);
        $options->tolerance = $this->apply_unit($question->tolerance, $units);

        // Save options
        if (isset($oldoptions->id)) { // options have changed
            $options->id = $oldoptions->id;
            if (! update_record('quiz_numerical', $options)) {
                $result->error = "Could not update quiz numerical options! (id=$options->id)";
                return $result;
            }
        } else { // new options
            if (! insert_record('quiz_numerical', $options)) {
                $result->error = "Could not insert quiz numerical options!";
                return $result;
            }
        }
    }

    function save_numerical_units($question) {
        if (!$oldunits = get_records("quiz_numerical_units", "question", $question->id)) {
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
                delete_records('quiz_numerical_units', 'id', $oldunits[$i]->id);
            } else if ($oldunits != $units) { // answer has changed or is new
                if (isset($oldunits[$i]->id)) { // answer has changed
                    $units[$i]->id = $oldunits[$i]->id;
                    if (! update_record('quiz_numerical_units', $units[$i])) {
                        $result->error = "Could not update quiz_numerical_unit $units[$i]->unit";
                        return $result;
                    }
                } else if (isset($units[$i])) { // answer is new
                    if (! insert_record('quiz_numerical_units', $units[$i])) {
                        $result->error = "Unable to insert new unit $units[$i]->unit";
                        return $result;
                    }
                }
            }
        }
        $result->units = &$units;
        return $result;
    }

    function compare_responses(&$question, &$state, &$teststate) {
        if (isset($state->responses[''])) {
            $response = $this->apply_unit(stripslashes($state->responses['']),
             $question->options->units);
        } else {
            $response = '';
        }
        if (isset($teststate->responses[''])) {
            $testresponse =
             $this->apply_unit(stripslashes($teststate->responses['']),
             $question->options->units);
        } else {
            $testresponse = '';
        }

        $this->get_tolerance_interval($question, $teststate);
        // We need to add a tiny fraction (0.00000000000000001) to make the
        // comparison work correctly. Otherwise seemingly equal values can yield
        // false.
        $tolerance = (float)$question->options->tolerance + 0.00000000000000001;

        if (is_numeric($response) && is_numeric($testresponse)) {
            if ($teststate->options->min <= $response
             && $teststate->options->max >= $response) {
                return true;
            }
        }
        return false;
    }

    function get_tolerance_interval(&$question, &$state) {
        $answer = (float)$state->responses[''];
        if (empty($question->options->tolerance)) {
            $state->options->min = $state->options->max = $answer;
            return true;
        }

        if (!isset($question->options->tolerancetype)) {
            $question->options->tolerancetype = 2; // nominal
        }
        $tolerance = (float)$question->options->tolerance;
        switch ($question->options->tolerancetype) {
            case '1': case 'relative':
                /// Recalculate the tolerance and fall through
                /// to the nominal case:
                $tolerance = $answer * $tolerance;
                // Falls through to the nominal case -
            case '2': case 'nominal':
                $tolerance = abs($tolerance); // important - otherwise min and max are swapped
                $max = $answer + $tolerance;
                $min = $answer - $tolerance;
                break;
            case '3': case 'geometric':
                $quotient = 1 + abs($tolerance);
                $max = $answer * $quotient;
                $min = $answer / $quotient;
                break;
            default:
                error("Unknown tolerance type $question->options->tolerancetype");
        }

        $state->options->min = $min;
        $state->options->max = $max;
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
        if (!is_numeric($rawresponse)) {
            return $rawresponse;
        }

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
                return (float)$responseparts[1]*$tmpunits[$responseparts[5]];
            } else {
                return (float)$responseparts[1];
            }
        }
        return (float)$rawresponse;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[NUMERICAL]= new quiz_numerical_qtype();

?>
