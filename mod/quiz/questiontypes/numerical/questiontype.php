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

    function get_answers($question, $addedcondition='') {
        // The added condition is one addition that has been added
        // to the behaviour of this question type in order to make
        // it embeddable within a multianswer (embedded cloze) question

        global $CFG;

        // There can be multiple answers
        return get_records_sql("SELECT a.*, n.min, n.max
                                  FROM {$CFG->prefix}quiz_numerical n,
                                       {$CFG->prefix}quiz_answers a
                                 WHERE a.question = '$question->id'
                                   AND n.answer = a.id "
                                     . $addedcondition);
    }

    function name() {
        return 'numerical';
    }

    function save_question_options($question) {

        if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
            $oldanswers = array();
        }

        $answers = array();
        $maxfraction = -1;

        // Insert all the new answers
        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer = $oldanswer;
                    $answer->answer   = $dataanswer;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!update_record("quiz_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    unset($answer);
                    $answer->answer   = $dataanswer;
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("quiz_answers", $answer)) {
                        $result->error = "Could not insert quiz answer!";
                        return $result;
                    }
                }
                $answers[] = $answer->id;
                if ($question->fraction[$key] > $maxfraction) {
                    $maxfraction = $question->fraction[$key];
                }

                if ($options = get_record("quiz_numerical", "answer", $answer->id)) {
                    $options->min= $question->min[$key];
                    $options->max= $question->max[$key];
                    if (!update_record("quiz_numerical", $options)) {
                        $result->error = "Could not update quiz numerical options! (id=$options->id)";
                        return $result;
                    }
                } else { // completely new answer
                    unset($options);
                    $options->question = $question->id;
                    $options->answer = $answer->id;
                    $options->min = $question->min[$key];
                    $options->max = $question->max[$key];
                    if (!insert_record("quiz_numerical", $options)) {
                        $result->error = "Could not insert quiz numerical options!";
                        return $result;
                    }
                }
            }
        }

        /// Save units
        /// Make unit records
        $newunits = array();
        unset($tmpunit);
        $tmpunit->question = $question->id;
        foreach ($question->multiplier as $key => $multiplier) {
            if ($multiplier && is_numeric($multiplier)) {
                $tmpunit->multiplier = $multiplier;
                $tmpunit->unit = trim($question->unit[$key]);
                $newunits[] = $tmpunit;
            }
        }
        if (1 == count($newunits) && !$newunits[0]->unit) {
            /// Only default unit and it is empty, so drop it:
            $newunits = array();
        }
        if ($oldunits = get_records('quiz_numerical_units',
                                    'question', $question->id)) {
            foreach ($oldunits as $unit) {
                if ($newunit = array_shift($newunits)) {
                    $unit->multiplier = $newunit->multiplier;
                    $unit->unit = $newunit->unit;
                    if (!update_record('quiz_numerical_units', $unit)) {
                        $result->error = "Could not update quiz_numerical_unit $unit->unit";
                        return $result;
                    }
                } else {
                    delete_records('quiz_numerical_units', 'id', $unit->id);
                }
            }
        }
        foreach ($newunits as $newunit) {
            // Create new records for the remaining units:
            if (!insert_record('quiz_numerical_units', $newunit)) {
                $result->error = "Unable to insert new unit $newunit->unit";
                return $result;
            }
        }

        /// Perform sanity checks on fractional grades
        if ($maxfraction != 1) {
            $maxfraction = $maxfraction * 100;
            $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
            return $result;
        } else {
            return true;
        }
    }
    
    function grade_response($question, $nameprefix, $addedanswercondition='') {

        $result->answers = array();
        $units = get_records('quiz_numerical_units',
                             'question', $question->id);
        if (isset($question->response[$nameprefix])) {
            $response = trim(stripslashes($question->response[$nameprefix]));
            // Arrays with 'wild cards':
            $search = array(' ',',');
            $replace = array('','.');
            $responsenum = str_replace($search, $replace, $response);
            if (empty($units)) {
                if ('' !== $responsenum && is_numeric($responsenum)) {
                    $responsenum = (float)$responsenum;
                } else {
                    unset($responsenum); // Answer is not numeric
                }
            } else if (ereg(
                    '^(([0-9]+(\\.[0-9]*)?|[.][0-9]+)([eE][-+]?[0-9]+)?)([^0-9].*)?$',
                    $responsenum, $responseparts)) {
                $responsenum = (float)$responseparts[1];
                if ($responseparts[5]) {
                    $responseunit = $responseparts[5];
                } else {
                    $responseunit = '';
                }
                $unitidentified = false;
                foreach ($units as $unit) {
                    if (str_replace($search, $replace, $unit->unit)
                            == $responseunit) {
                        $unitidentified = true;
                        $responsenum /= $unit->multiplier;
                        break;
                    }
                }
                if (!$unitidentified) {
                    unset($responsenum); // No unit OK
                }
            } else {
                unset($responsenum); // Answer is not numeric
            }
       } else {
            $response = '';
        }
        $answers = $this->get_answers($question, $addedanswercondition);
        foreach ($answers as $answer) {

            /// Check if response matches answer...
            if ('' !== $response and empty($result->answers)
                    || $answer->fraction
                     > $result->answers[$nameprefix]->fraction
                    and strtolower($response) == strtolower($answer->answer)
                    || '' != trim($answer->min) && isset($responsenum)
                    && $responsenum >= (float)$answer->min
                    && $responsenum <= (float)$answer->max) {
                $result->answers[$nameprefix] = $answer;
            }
        }

        $result->grade = isset($result->answers[$nameprefix])
                ?   $result->answers[$nameprefix]->fraction
                :   0.0;
        $result->correctanswers = quiz_extract_correctanswers($answers,
                                                              $nameprefix);

        /////////////////////////////////////////////////
        // For numerical answer we have the policy to 
        // set feedback for any response, even if the
        // response does not entitle the student to it.
        /////////////////////////////////////////////////
        if ('' !== $response and empty($result->answers)
                || empty($result->answers[$nameprefix]->feedback)) {
            // Look for just any feedback:
            foreach ($result->correctanswers as $correctanswer) {
                if ($correctanswer->feedback) {
                    $result->answers[$nameprefix]->feedback = 
                            $correctanswer->feedback;
                    if (empty($result->answers[$nameprefix]->id)) {
                        // Better fake an answer as well:
                        $result->answers[$nameprefix]->id = 0;
                        $result->answers[$nameprefix]->answer = $response;
                        $result->answers[$nameprefix]->fraction = 0.0;
                        $result->answers[$nameprefix]->question = $question->id;
                    }
                    break;
                }
            }
        }

        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[NUMERICAL]= new quiz_numerical_qtype();

?>
