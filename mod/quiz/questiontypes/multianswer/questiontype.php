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

    function get_answers($question) {
    // This function is not used by any other function within this class.
    // It is possible that it is used by some report that uses the 
    // function quiz_get_answers in lib.php

        /// The returned answers includes subanswers...
        // As this question type embedds some other question types,
        // it is necessary to have access to those:
        global $QUIZ_QTYPES;

        $answers = array();

        $virtualquestion->id = $question->id;

        if ($multianswers = get_records('quiz_multianswers', 'question', $question->id)) {
            foreach ($multianswers as $multianswer) {
                $virtualquestion->qtype = $multianswer->answertype;
                // Call to other question type for subanswers
                $addedcondition = " AND a.id IN ($multianswer->answers) ";
                $multianswer->subanswers =
                    $QUIZ_QTYPES[$multianswer->answertype]
                    ->get_answers($virtualquestion, $addedcondition);
                $answers[] = $multianswer;
            }
        }
        return $answers;
    }

    function get_position_multianswer($questionid, $positionkey) {
    // As a separate function in order to make it overridable

        return get_record('quiz_multianswers', 'question', $questionid,
                                               'positionkey', $positionkey);
    }

    function get_multianswers($questionid) {
    // As a separate function in order to make it overridable
        return get_records('quiz_multianswers', 'question', $questionid);
    }

    function name() {
        return 'multianswer';
    }

    function save_question_options($question) {
        if (!$oldmultianswers = get_records("quiz_multianswers", "question", $question->id, "id ASC")) {
            $oldmultianswers = array();
        }

        // Insert all the new multi answers
        foreach ($question->answers as $dataanswer) {
            if ($oldmultianswer = array_shift($oldmultianswers)) {  // Existing answer, so reuse it
                $multianswer = $oldmultianswer;
                $multianswer->positionkey = $dataanswer->positionkey;
                $multianswer->norm = $dataanswer->norm;
                $multianswer->answertype = $dataanswer->answertype;

                if (! $multianswer->answers =
                        quiz_qtype_multianswer_save_alternatives
                        ($question->id, $dataanswer->answertype,
                         $dataanswer->alternatives, $oldmultianswer->answers))
                {
                    $result->error = "Could not update multianswer alternatives! (id=$multianswer->id)";
                    return $result;
                }
                if (!update_record("quiz_multianswers", $multianswer)) {
                    $result->error = "Could not update quiz multianswer! (id=$multianswer->id)";
                    return $result;
                }
            } else {    // This is a completely new answer
                unset($multianswer);
                $multianswer->question = $question->id;
                $multianswer->positionkey = $dataanswer->positionkey;
                $multianswer->norm = $dataanswer->norm;
                $multianswer->answertype = $dataanswer->answertype;

                if (! $multianswer->answers =
                        quiz_qtype_multianswer_save_alternatives
                        ($question->id, $dataanswer->answertype,
                         $dataanswer->alternatives))
                {
                    $result->error = "Could not insert multianswer alternatives! (questionid=$question->id)";
                    return $result;
                }
                if (!insert_record("quiz_multianswers", $multianswer)) {
                    $result->error = "Could not insert quiz multianswer!";
                    return $result;
                }
            }
        }
    }
    
    function save_question($authorizedquestion, $form, $course) {

        $question = quiz_qtype_multianswer_extract_question
                                     ($form->questiontext);
        $question->id = $authorizedquestion->id;
        $question->qtype = $authorizedquestion->qtype;
        $question->category = $authorizedquestion->category;

        $question->name = $form->name;
        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        // Formcheck
        $err = array();
        if (empty($question->name)) {
            $err["name"] = get_string("missingname", "quiz");
        }
        if (empty($question->questiontext)) {
            $err["questiontext"] = get_string("missingquestiontext", "quiz");
        }
        if ($err) { // Formcheck failed
            notify(get_string("someerrorswerefound"));

        } else {

            if (!empty($question->id)) { // Question already exists
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {         // Question is a new one
                $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                if (!$question->id = insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }
    
            // Now to save all the answers and type-specific options
            $result = $this->save_question_options($question);

            if (!empty($result->error)) {
                error($result->error);
            }

            if (!empty($result->notice)) {
                notice_yesno($result->notice, "question.php?id=$question->id", "edit.php");
                print_footer($course);
                exit;
            }
    
            redirect("edit.php");
        }
    }
    
    function convert_to_response_answer_field($questionresponse) {
    /// This method, together with extract_response, should be
    /// obsolete as soon as we get a better response storage

        $delimiter = '';
        $responseanswerfield = '';
        foreach ($questionresponse as $key => $value) {
            if ($multianswerid = $this->extract_response_id($key)) {
                $responseanswerfield .= "$delimiter$multianswerid-$value";
                $delimiter = ',';
            } else {
                notify("Error: Illegal match key $key detected");
            }
        }
        return $responseanswerfield;
    }

    function extract_response($rawresponse, $nameprefix) {
        /// A temporary fix for bug #647 has accidently been enforced here
        /// because of the odd circumstances during the refactoring

        $multianswers = get_records('quiz_multianswers',
                                    'question', $rawresponse->question);
        $response = array();
        foreach ($multianswers as $maid => $multianswer) {
            if (ereg("(^|,)$maid-(.*)", $rawresponse->answer, $regs)) {
                $splits = split(',[0-9]+-', $regs[2], 2);
                $response[$nameprefix.$maid] = $splits[0];
            } else {
                $response[$nameprefix.$maid] = '';
            }
        }
        return $response;
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
         global $THEME;

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

            $multianswer = $this->get_position_multianswer($question->id, $regs[1]);

            $inputname = $nameprefix.$multianswer->id;
            $response = isset($question->response[$inputname])
                    ? $question->response[$inputname] : '';

            /// Determine style
            if (!empty($correctanswers) && '' !== $response) {

                if (!isset($answers[$inputname])
                        || $answers[$inputname]->fraction <= 0.0) {
                    // The response must have been totally wrong:
                    $style = ' style="background-color:red" ';
                
                } else if ($answers[$inputname]->fraction >= 1.0) {
                    // The response must was correct!!
                    $style = 'style="background-color:lime"';

                } else {
                    // This response did at least give some credit:
                    $style = 'style="background-color:yellow"';
                }
            } else {
                // No colorish feedback is to be used
                $style = '';
            }

            // Determine feedback popup if any
            if ($quiz->feedback && isset($answers[$inputname])
                    && '' !== $answers[$inputname]->feedback) {
                $title = str_replace("'", "\\'", $answers[$inputname]->feedback);
                $popup = " onmouseover=\"return overlib('$title', CAPTION, '$strfeedback', FGCOLOR, '$THEME->cellcontent');\" ".
                         " onmouseout=\"return nd();\" ";
            } else {
                $popup = '';
            }

            // Print the input control
            switch ($multianswer->answertype) {
                case SHORTANSWER:
                case NUMERICAL:
                    echo " <input $style $readonly $popup name=\"$inputname\"
                            type=\"text\" value=\"$response\" size=\"12\" /> ";
                    break;
                case MULTICHOICE:
                    $outputoptions = '<option></option>'; // Default empty option
                    $mcanswers = get_records_list("quiz_answers", "id", $multianswer->answers);
                    foreach ($mcanswers as $mcanswer) {
                        $selected = $response == $mcanswer->id
                                ? ' selected="selected" ' : '';
                        $outputoptions .= "<option value=\"$mcanswer->id\" $selected>$mcanswer->answer</option>";
                    }
                   echo "<select $popup $style name=\"$inputname\" $readonly>";
                   echo $outputoptions;
                   echo '</select>';
                   break;
               default:
                   error("Unable to recognized answertype $answer->answertype");
                   break;
           }
        }

        // Print the final piece of question text:
        echo $qtextremaining;
    }

    function grade_response($question, $nameprefix) {

        global $QUIZ_QTYPES;

        $result->grade = 0.0;
        $result->answers = array();
        $result->correctanswers = array();

        $multianswers = $this->get_multianswers($question->id);
        // Default settings:
        $subquestion->id = $question->id;
        $normsum = 0;

        // Grade each multianswer
        foreach ($multianswers as $multianswer) {
            $name = $nameprefix.$multianswer->id;
            $subquestion->response[$nameprefix] =
                    isset($question->response[$name])
                    ?   $question->response[$name] : '';
            
            $subresult = $QUIZ_QTYPES[$multianswer->answertype]
                    ->grade_response($subquestion, $nameprefix,
                                           " AND a.id IN ($multianswer->answers) ");

            // Summarize subquestion results:
            
            if (isset($subresult->answers[$nameprefix])) {

                /// Answer was found:
                $result->answers[$name] = $subresult->answers[$nameprefix];

                if ($result->answers[$name]->fraction >= 1.0) {
                    // This is also the correct answer:
                    $result->correctanswers[$name] = $result->answers[$name];
                }
            }

            if (!isset($result->correctanswers[$name])) {
                // Pick the first correctanswer:
                foreach ($subresult->correctanswers as $correctanswer) {
                    $result->correctanswers[$name] = $correctanswer;
                    break;
                }
            }
            $result->grade += $multianswer->norm * $subresult->grade;
            $normsum += $multianswer->norm;
        }
        $result->grade /= $normsum;

        return $result;
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

    // ANSWER_ALTERNATIVE regexes

    define("ANSWER_ALTERNATIVE_FRACTION_REGEX",
           '=|%(-?[0-9]+)%');
    define("ANSWER_ALTERNATIVE_ANSWER_REGEX",
            '[^~#}]+');
    define("ANSWER_ALTERNATIVE_FEEDBACK_REGEX",
            '[^~}]*');
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
            . ')*)}' );

    // Parenthesis positions for singulars in ANSWER_REGEX
    define("ANSWER_REGEX_NORM", 1);
    define("ANSWER_REGEX_ANSWER_TYPE_NUMERICAL", 3);
    define("ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE", 4);
    define("ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER", 5);
    define("ANSWER_REGEX_ALTERNATIVES", 6);

////////////////////////////////////////
//// Start of the actual function
////////////////////////////////////////

    $question = NULL;
    $question->qtype= MULTIANSWER;
    $question->questiontext= $text;
    $question->answers= array();
    $question->defaultgrade = 0; // Will be increased for each answer norm

    for ($positionkey=1
        ; ereg(ANSWER_REGEX, $question->questiontext, $answerregs)
        ; ++$positionkey )
    {
        unset($multianswer);

        $multianswer->positionkey = $positionkey;
        $multianswer->norm = $answerregs[ANSWER_REGEX_NORM]
            or $multianswer->norm = '1';
        if ($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL]) {
            $multianswer->answertype = NUMERICAL;
        } else if($answerregs[ANSWER_REGEX_ANSWER_TYPE_SHORTANSWER]) {
            $multianswer->answertype = SHORTANSWER;
        } else if($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE]){
            $multianswer->answertype = MULTICHOICE;
        } else {
            error("Cannot identify answertype $answerregs[2]");
            return false;
        }

        $multianswer->alternatives= array();
        $remainingalts = $answerregs[ANSWER_REGEX_ALTERNATIVES];
        while (ereg(ANSWER_ALTERNATIVE_REGEX, $remainingalts, $altregs)) {
            unset($alternative);
            
            if ('=' == $altregs[ANSWER_ALTERNATIVE_REGEX_FRACTION]) {
                $alternative->fraction = '1';
            } else {
                $alternative->fraction = .01 *
                        $altregs[ANSWER_ALTERNATIVE_REGEX_PERCENTILE_FRACTION]
                    or $alternative->fraction = '0';
            }
            $alternative->feedback = $altregs[ANSWER_ALTERNATIVE_REGEX_FEEDBACK];
            if ($answerregs[ANSWER_REGEX_ANSWER_TYPE_NUMERICAL]
                    && ereg(NUMERICAL_ALTERNATIVE_REGEX,
                            $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER],
                            $numregs) )
            {
                $alternative->answer = $numregs[NUMERICAL_CORRECT_ANSWER];
                if ($numregs[NUMERICAL_ABS_ERROR_MARGIN]) {
                    $alternative->min = $numregs[NUMERICAL_CORRECT_ANSWER]
                                      - $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                    $alternative->max = $numregs[NUMERICAL_CORRECT_ANSWER]
                                      + $numregs[NUMERICAL_ABS_ERROR_MARGIN];
                } else {
                    $alternative->min = $numregs[NUMERICAL_CORRECT_ANSWER];
                    $alternative->max = $numregs[NUMERICAL_CORRECT_ANSWER];
                }
            } else { // Min and max must stay undefined...
                $alternative->answer =
                        $altregs[ANSWER_ALTERNATIVE_REGEX_ANSWER];
            }
            
            $multianswer->alternatives[] = $alternative;
            $tmp = explode($altregs[0], $remainingalts, 2);
            $remainingalts = $tmp[1];
        }

        $question->defaultgrade += $multianswer->norm;
        $question->answers[] = $multianswer;
        $question->questiontext = implode("{#$positionkey}",
                    explode($answerregs[0], $question->questiontext, 2));
    }
    return $question;
}

function quiz_qtype_multianswer_save_alternatives($questionid,
        $answertype, $alternatives, $oldalternativeids= NULL) {
// Returns false if something goes wrong,
// otherwise the ids of the answers.

    if (empty($oldalternativeids)
        or !($oldalternatives =
                get_records_list('quiz_answers', 'id', $oldalternativeids)))
    {
        $oldalternatives = array();
    }

    $alternativeids = array();

    foreach ($alternatives as $altdata) {

        if ($altold = array_shift($oldalternatives)) { // Use existing one...
            $alt = $altold;
            $alt->answer = $altdata->answer;
            $alt->fraction = $altdata->fraction;
            $alt->feedback = $altdata->feedback;
            if (!update_record("quiz_answers", $alt)) {
                return false;
            }

        } else { // Completely new one
            unset($alt);
            $alt->question= $questionid;
            $alt->answer = $altdata->answer;
            $alt->fraction = $altdata->fraction;
            $alt->feedback = $altdata->feedback;
            if (!($alt->id = insert_record("quiz_answers", $alt))) {
                return false;
            }
        }

        // For the answer type numerical, each alternative has individual options:
        if ($answertype == NUMERICAL) {
            if ($numericaloptions =
                    get_record('quiz_numerical', 'answer', $alt->id))
            {
                // Reuse existing numerical options
                $numericaloptions->min = $altdata->min;
                $numericaloptions->max = $altdata->max;
                if (!update_record('quiz_numerical', $numericaloptions)) {
                    return false;
                }
            } else {
                // New numerical options
                $numericaloptions->answer = $alt->id;
                $numericaloptions->question = $questionid;
                $numericaloptions->min = $altdata->min;
                $numericaloptions->max = $altdata->max;
                if (!insert_record("quiz_numerical", $numericaloptions)) {
                    return false;
                }
            }
        } else { // Delete obsolete numerical options
            delete_records('quiz_numerical', 'answer', $alt->id);
        } // end if NUMERICAL

        $alternativeids[] = $alt->id;
    } // end foreach $alternatives
    $answers = implode(',', $alternativeids);

    // Removal of obsolete alternatives from answers and quiz_numerical:
    while ($altobsolete = array_shift($oldalternatives)) {
        delete_records("quiz_answers", "id", $altobsolete->id);

        // Possibly obsolute numerical options are also to be deleted:
        delete_records("quiz_numerical", 'answer', $altobsolete->id);
    }

    // Common alternative options and removal of obsolete options
    switch ($answertype) {
        case NUMERICAL:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_shortanswer', 'answers',
$oldalternativeids);
                delete_records('quiz_multichoice', 'answers',
$oldalternativeids);
            }
            break;
        case SHORTANSWER:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_multichoice', 'answers',
$oldalternativeids);
                $options = get_record('quiz_shortanswer',
                                      'answers', $oldalternativeids);
            } else {
                unset($options);
            }
            if (empty($options)) {
                // Create new shortanswer options
                $options->question = $questionid;
                $options->usecase = 0;
                $options->answers = $answers;
                if (!insert_record('quiz_shortanswer', $options)) {
                    return false;
                }
            } else if ($answers != $oldalternativeids) {
                // Shortanswer options needs update:
                $options->answers = $answers;
                if (!update_record('quiz_shortanswer', $options)) {
                    return false;
                }
            }
            break;
        case MULTICHOICE:
            if (!empty($oldalternativeids)) {
                delete_records('quiz_shortanswer', 'answers',
$oldalternativeids);
                $options = get_record('quiz_multichoice',
                                      'answers', $oldalternativeids);
            } else {
                unset($options);
            }
            if (empty($options)) {
                // Create new multichoice options
                $options->question = $questionid;
                $options->layout = 0;
                $options->single = 1;
                $options->answers = $answers;
                if (!insert_record('quiz_multichoice', $options)) {
                    return false;
                }
            } else if ($answers != $oldalternativeids) {
                // Multichoice options needs update:
                $options->answers = $answers;
                if (!update_record('quiz_multichoice', $options)) {
                    return false;
                }
            }
            break;
        default:
            return false;
    }
    return $answers;
}

?>
