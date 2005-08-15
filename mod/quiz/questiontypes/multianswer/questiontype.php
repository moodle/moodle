<?php  // $Id$

///////////////////
/// MULTIANSWER /// (Embedded - cloze)
///////////////////

///
/// The multianswer question type is special in that it
/// depends on a few other question types, i.e.
/// MULTICHOICE, SHORTANSWER and NUMERICAL.
/// These question types have got a few special features that
/// makes them useable by the MULTIANSWER question type
///

/// QUESTION TYPE CLASS //////////////////
class quiz_embedded_cloze_qtype extends quiz_default_questiontype {

    function name() {
        return 'multianswer';
    }

    function get_question_options(&$question) {
        // Get relevant data indexed by positionkey from the multianswers table
        if (!$sequence = get_field('quiz_multianswers', 'sequence', 'question',
         $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }

        global $QUIZ_QTYPES;
        $wrappedquestions = get_records_list('quiz_questions', 'id', $sequence);

        // We want an array with question ids as index and the positions as values
        $sequence = array_flip(explode(',', $sequence));
        array_walk($sequence, create_function('&$val', '$val++;'));

        foreach ($wrappedquestions as $wrapped) {
            if (!$QUIZ_QTYPES[$wrapped->qtype]
             ->get_question_options($wrapped)) {
                notify("Unable to get options for questiontype
                {$wrapped->qtype} (id={$wrapped->id})");
            }
            $wrapped->maxgrade = $wrapped->defaultgrade;
            $question->options->questions[$sequence[$wrapped->id]] = clone($wrapped);
        }

        return true;
    }

    function save_question_options($question) {
        global $QUIZ_QTYPES;
        if (!$oldwrappedids =
         get_records('quiz_questions', 'parent', $question->id, '', 'id, id')) {
         // We need to select 'id, id' because the first one is consumed by
         // get_records.
            $oldwrappedids = array();
        }
        $oldwrappedids = array_keys($oldwrappedids);
        $sequence = array();
        foreach($question->options->questions as $wrapped) {
            if ($oldwrappedid = array_shift($oldwrappedids)) {
                $wrapped->id = $oldwrappedid;
            }
            $wrapped->name     = $question->name;
            $wrapped->parent   = $question->id;
            $wrapped->category = $question->category;
            $wrapped->version  = $question->version;
            $wrapped = $QUIZ_QTYPES[$wrapped->qtype]->save_question($wrapped,
             $wrapped, $question->course);
            $sequence[] = $wrapped->id;
        }

        // Delete redundant wrapped questions
        $oldwrappedids = implode(',', $oldwrappedids);
        delete_records_select('quiz_questions', "id IN ($oldwrappedids)");

        if (!empty($sequence)) {
            $multianswer = new stdClass;
            $multianswer->question = $question->id;
            $multianswer->sequence = implode(',', $sequence);
            if ($oldid =
             get_field('quiz_multianswers', 'id', 'question', $question->id)) {
                $multianswer->id = $oldid;
                if (!update_record("quiz_multianswers", $multianswer)) {
                    $result->error = "Could not update quiz multianswer! " .
                     "(id=$multianswer->id)";
                    return $result;
                }
            } else {
                if (!insert_record("quiz_multianswers", $multianswer)) {
                    $result->error = "Could not insert quiz multianswer!";
                    return $result;
                }
            }
        }
    }

    function save_question($authorizedquestion, $form, $course) {
        $question =
         quiz_qtype_multianswer_extract_question ($form->questiontext);
        if (isset($authorizedquestion->id)) {
            $question->id = $authorizedquestion->id;
            $question->version = $form->version = $authorizedquestion->version;
        } else {
            $question->version = $form->version = 1;
        }


        $question->category = $authorizedquestion->category;
        $form->course = $course; // To pass the course object to
                                 // save_question_options, where it is
                                 // needed to call type specific
                                 // save_question methods.
        $form->defaultgrade = $question->defaultgrade;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = 0;
        $form->options      = clone($question->options);
        unset($question->options);
        return parent::save_question($question, $form, $course);
    }

    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
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
            $state->responses[$tmp[0]] =
             str_replace(array("&#0044;", "&#0045;"), array(",", "-"), $tmp[1]);
        }
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        $responses = $state->responses;
        array_walk($responses, create_function('&$val, $key',
         // encode - (hyphen) and , (comma) to &#0045; because they are used as
         // delimiters
         '$val = str_replace(array(",", "-"), array("&#0044;", "&#0045;"), $val);
          $val = "$key-$val";'));
        $responses = implode(',', $responses);

        // Set the legacy answer field
        if (!set_field('quiz_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        global $QUIZ_QTYPES;
        $responses = array();
        foreach($question->options->questions as $key => $wrapped) {
            $correct = $QUIZ_QTYPES[$wrapped->qtype]
             ->get_correct_responses($wrapped, $state);
            $responses[$key] = $correct[''];
        }
        return $responses;
    }

    function print_question_formulation_and_controls(&$question, &$state, $quiz,
     $options) {
        global $QUIZ_QTYPES;
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $nameprefix = $question->name_prefix;

        // For this question type, we better print the image on top:
        quiz_print_possible_question_image($quiz->id, $question);

        $qtextremaining = format_text($question->questiontext,
                                      $question->questiontextformat,
                                      NULL, $quiz->course);

        $strfeedback = get_string('feedback', 'quiz');

        // The regex will recognize text snippets of type {#X}
        // where the X can be any text not containg } or white-space characters.

        while (ereg('\{#([^[:space:]}]*)}', $qtextremaining, $regs)) {
            $qtextsplits = explode($regs[0], $qtextremaining, 2);
            echo $qtextsplits[0];
            $qtextremaining = $qtextsplits[1];

            $positionkey = $regs[1];
            $wrapped = &$question->options->questions[$positionkey];
            $answers = &$wrapped->options->answers;
            $correctanswers = $QUIZ_QTYPES[$wrapped->qtype]
             ->get_correct_responses($wrapped, $state);

            $inputname = $nameprefix.$positionkey;
            $response = isset($state->responses[$positionkey])
                    ? $state->responses[$positionkey] : null;


            // Determine feedback popup if any
            $popup = '';
            $style = '';
            if ($options->feedback) {
                $chosenanswer = null;
                switch ($wrapped->qtype) {
                    case NUMERICAL:
                        $testedstate = clone($state);
                        $testedstate->responses[''] = $response;
                        $raw_grade   = 0;
                        foreach ($answers as $answer) {
                            if($QUIZ_QTYPES[$wrapped->qtype]
                             ->test_response($wrapped, $testedstate, $answer)) {
                                if (empty($raw_grade) || $raw_grade < $answer->fraction) {
                                    $chosenanswer = clone($answer);
                                    $raw_grade = $answer->fraction;
                                }
                            }
                        }
                        break;
                    case SHORTANSWER:
                        $testedstate = clone($state);
                        $testedstate->responses[''] = $response;
                        $teststate   = clone($state);
                        $raw_grade   = 0;
                        foreach ($answers as $answer) {
                            $teststate->responses[''] = trim($answer->answer);
                            if($QUIZ_QTYPES[$wrapped->qtype]
                             ->compare_responses($wrapped, $testedstate, $teststate)) {
                                if (empty($raw_grade) || $raw_grade < $answer->fraction) {
                                    $chosenanswer = clone($answer);
                                    $raw_grade = $answer->fraction;
                                }
                            }
                        }
                        break;
                    case MULTICHOICE:
                        if (isset($answers[$response])) {
                            $chosenanswer = clone($answers[$response]);
                        }
                        break;
                    default:
                        break;
                }

                // Set up a default chosenanswer so that all non-empty wrong
                // answers are highlighted red
                if (empty($chosenanswer) && !empty($response)) {
                    $chosenanswer = new stdClass;
                    $chosenanswer->fraction = 0.0;
                }

                if (!empty($chosenanswer->feedback)) {
                    $feedback = str_replace("'", "\\'", $chosenanswer->feedback);
                    $popup = " onmouseover=\"return overlib('$feedback', CAPTION, '$strfeedback', FGCOLOR, '#FFFFFF');\" ".
                             " onmouseout=\"return nd();\" ";
                }

                /// Determine style
                if (!empty($chosenanswer) && $options->correct_responses) {
                    if (!isset($chosenanswer->fraction)
                            || $chosenanswer->fraction <= 0.0) {
                        // The response must have been totally wrong:
                        $style = 'style="background-color:red"';

                    } else if ($chosenanswer->fraction >= 1.0) {
                        // The response was correct!!
                        $style = 'style="background-color:lime"';

                    } else {
                        // This response did at least give some credit:
                        $style = 'style="background-color:yellow"';
                    }
                } else {
                    $style = '';
                }
            }

            // Print the input control
            switch ($wrapped->qtype) {
                case SHORTANSWER:
                case NUMERICAL:
                    echo " <input $style $readonly $popup name=\"$inputname\"
                            type=\"text\" value=\"$response\" size=\"12\" /> ";
                    break;
                case MULTICHOICE:
                    $outputoptions = '<option></option>'; // Default empty option
                    foreach ($answers as $mcanswer) {
                        $selected = $response == $mcanswer->id
                                ? ' selected="selected" ' : '';
                        $outputoptions .= "<option value=\"$mcanswer->id\" $selected>$mcanswer->answer</option>";
                    }
                   echo "<select $popup $readonly $style name=\"$inputname\">";
                   echo $outputoptions;
                   echo '</select>';
                   break;
               default:
                   error("Unable to recognized questiontype ($wrapped->qtype) of
                          question part $positionkey.");
                   break;
           }
        }

        // Print the final piece of question text:
        echo $qtextremaining;
    }

    function grade_responses(&$question, &$state, $quiz) {
        global $QUIZ_QTYPES;
        $teststate = clone($state);
        $state->raw_grade = 0;
        foreach($question->options->questions as $key => $wrapped) {
            $teststate->responses = array('' => $state->responses[$key]);
            $teststate->raw_grade = 0;
            if (false === $QUIZ_QTYPES[$wrapped->qtype]
             ->grade_responses($wrapped, $teststate, $quiz)) {
                return false;
            }
            $state->raw_grade += $teststate->raw_grade;
        }
        $state->raw_grade /= $question->defaultgrade;
        $state->raw_grade = min(max((float) $state->raw_grade, 0.0), 1.0)
         * $question->maxgrade;

        if (empty($state->raw_grade)) {
            $state->raw_grade = 0.0;
        }
        $state->penalty = $question->penalty * $question->maxgrade;

        return true;
    }
}
//// END OF CLASS ////


//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[MULTIANSWER]= new quiz_embedded_cloze_qtype();


/////////////////////////////////////////////////////////////
//// ADDITIONAL FUNCTIONS
//// The functions below deal exclusivly with editing
//// of questions with question type MULTIANSWER.
//// Therefore they are kept in this file.
//// They are not in the class as they are not
//// likely to be subject for overriding.
/////////////////////////////////////////////////////////////


function quiz_qtype_multianswer_extract_question($text) {

////////////////////////////////////////////////
//// Define some constants first. It is not the
//// pattern commonly used in quiz/questiontypes.
//// The reason is that it has been moved here from
//// quiz/format/multianswer/format.php
////////////////////////////////////////////////

    // REGULAR EXPRESSION CONSTANTS
    // I do not know any way to make this easier
    // Regexes are always awkard when defined but more comprehensible
    // when used as constants in the executive code

    // Handle the entity encoded ampersand in entities (e.g. &amp;lt; -> &lt;)
    $text = preg_replace('/&amp;(.{2,9}?;)/', '&${1}', $text);
    $text = stripslashes($text);

    // ANSWER_ALTERNATIVE regexes
    define("ANSWER_ALTERNATIVE_FRACTION_REGEX",
           '=|%(-?[0-9]+)%');
    define("ANSWER_ALTERNATIVE_ANSWER_REGEX",
            '.+?(?<!\\\\)(?=[~#}]|$)');
            //'[^~#}]+');
    define("ANSWER_ALTERNATIVE_FEEDBACK_REGEX",
            '.*?(?<!\\\\)(?=[~}]|$)');
            //'[//^~}]*');
    define("ANSWER_ALTERNATIVE_REGEX",
           '(' . ANSWER_ALTERNATIVE_FRACTION_REGEX .')?'
           . '(' . ANSWER_ALTERNATIVE_ANSWER_REGEX . ')'
           . '(#(' . ANSWER_ALTERNATIVE_FEEDBACK_REGEX .'))?');

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
           '(NUMERICAL|NM)|(MULTICHOICE|MC)|(SHORTANSWER|SA|MW)');
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
    define("ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER", 5);
    define("ANSWER_REGEX_ALTERNATIVES", 6);

////////////////////////////////////////
//// Start of the actual function
////////////////////////////////////////

    $question = new stdClass;
    $question->qtype = MULTIANSWER;
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
            $wrapped->qtype = NUMERICAL;
            $wrapped->multiplier = array();
            $wrapped->units      = array();
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER])) {
            $wrapped->qtype = SHORTANSWER;
            $wrapped->usecase = 0;
        } else if(!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE])) {
            $wrapped->qtype = MULTICHOICE;
            $wrapped->single = 1;
        } else {
            error("Cannot identify qtype $answerregs[2]");
            return false;
        }

        // Each $wrapped simulates a $form that can be processed by the
        // respective save_question and save_question_options methods of the
        // wrapped questiontypes
        $wrapped->answer   = array();
        $wrapped->fraction = array();
        $wrapped->feedback = array();
        $wrapped->questiontext = addslashes(str_replace('&\#', '&#',
         $answerregs[0]));
        $wrapped->questiontextformat = 0;

        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (preg_match('/~?'.ANSWER_ALTERNATIVE_REGEX.'/', $remainingalts, $altregs)) {
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $wrapped->fraction[] = '1';
            } else if ($percentile =
             $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]){
                $wrapped->fraction[] = .01 * $percentile;
            } else {
                $wrapped->fraction[] = '0';
            }
            $wrapped->feedback[] = addslashes(str_replace('&\#', '&#',
             isset($altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK])
             ? $altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK] : ''));
            if (!empty($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL])
                    && ereg(NUMERICAL_ALTERNATIVE_REGEX,
                            $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER],
                            $numregs) )
            {
                $wrapped->answer[] =
                 addslashes($numregs[NUMERICAL_CORRECT_ANSWER]);
                if ($numregs[NUMERICAL_ABS_ERROR_MARGIN]) {
                    $wrapped->tolerance[] =
                     $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $wrapped->tolerance[] = 0;
                }
            } else { // Tolerance can stay undefined for non numerical questions
                $wrapped->answer[] = addslashes(str_replace('&\#', '&#',
                 $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER]));
            }
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
        }

        $question->defaultgrade += $wrapped->defaultgrade;
        $question->options->questions[$positionkey] = clone($wrapped);
        $question->questiontext = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext, 2));
    }
    $question->questiontext = addslashes(str_replace('&\#', '&#',
     $question->questiontext));
    return $question;
}

?>
