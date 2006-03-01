<?php  // $Id$

/////////////////
/// TRUEFALSE ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
class quiz_truefalse_qtype extends quiz_default_questiontype {

    function name() {
        return 'truefalse';
    }

    function save_question_options($question) {
        
        // fetch old answer ids so that we can reuse them
        if (!$oldanswers = get_records("question_answers", "question", $question->id, "id ASC")) {
            $oldanswers = array();
        }

        // Save answer 'True'
        if ($true = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $true->answer   = get_string("true", "quiz");
            $true->fraction = $question->answer;
            $true->feedback = $question->feedbacktrue;
            if (!update_record("question_answers", $true)) {
                $result->error = "Could not update quiz answer \"true\")!";
                return $result;
            }
        } else {
            unset($true);
            $true->answer   = get_string("true", "quiz");
            $true->question = $question->id;
            $true->fraction = $question->answer;
            $true->feedback = $question->feedbacktrue;
            if (!$true->id = insert_record("question_answers", $true)) {
                $result->error = "Could not insert quiz answer \"true\")!";
                return $result;
            }
        }

        // Save answer 'False'
        if ($false = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $false->answer   = get_string("false", "quiz");
            $false->fraction = 1 - (int)$question->answer;
            $false->feedback = $question->feedbackfalse;
            if (!update_record("question_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        } else {
            unset($false);
            $false->answer   = get_string("false", "quiz");
            $false->question = $question->id;
            $false->fraction = 1 - (int)$question->answer;
            $false->feedback = $question->feedbackfalse;
            if (!$false->id = insert_record("question_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        }

        // delete any leftover old answer records (there couldn't really be any, but who knows)
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                delete_records('question_answers', 'id', $oa->id);
            }
        }

        // Save question options in quiz_truefalse table
        if ($options = get_record("quiz_truefalse", "question", $question->id)) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->trueanswer  = $true->id;
            $options->falseanswer = $false->id;
            if (!update_record("quiz_truefalse", $options)) {
                $result->error = "Could not update quiz truefalse options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question    = $question->id;
            $options->trueanswer  = $true->id;
            $options->falseanswer = $false->id;
            if (!insert_record("quiz_truefalse", $options)) {
                $result->error = "Could not insert quiz truefalse options!";
                return $result;
            }
        }
        return true;
    }

    /**
    * Loads the question type specific options for the question.
    */
    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('quiz_truefalse', 'question', $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }
        // Load the answers
        if (!$question->options->answers = get_records('question_answers', 'question', $question->id)) {
           notify('Error: Missing question answers!');
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
    function delete_question($question) {
        delete_records("quiz_truefalse", "question", $question->id);
        return true;
    }

    function get_correct_responses(&$question, &$state) {
    	// The correct answer is the one which gives full marks
        foreach ($question->options->answers as $answer) {
            if (((int) $answer->fraction) === 1) {
                return array('' => $answer->id);
            }
        }
        return null;
    }

    /**
    * Prints the main content of the question including any interactions
    */
    function print_question_formulation_and_controls(&$question, &$state,
            $cmoptions, $options) {
        global $CFG;

        $readonly = $options->readonly ? ' readonly="readonly"' : '';

        // Print question formulation
        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $cmoptions->course);
        $image = get_question_image($question, $cmoptions->course);

        $answers = &$question->options->answers;
        $trueanswer = &$answers[$question->options->trueanswer];
        $falseanswer = &$answers[$question->options->falseanswer];
        $correctanswer = ($trueanswer->fraction == 1) ? $trueanswer : $falseanswer;

        // Work out which radio button to select (if any)
        $truechecked = ($state->responses[''] == $trueanswer->id) ? ' checked="checked"' : '';
        $falsechecked = ($state->responses[''] == $falseanswer->id) ? ' checked="checked"' : '';

        // Work out which answer is correct if we need to highlight it
        if ($options->correct_responses) {
            $trueclass = ($trueanswer->fraction) ? ' class="highlight"' : '';
            $falseclass = ($falseanswer->fraction) ? ' class="highlight"' : '';
        } else {
            $trueclass = '';
            $falseclass = '';
        }

        $inputname = ' name="'.$question->name_prefix.'" ';
        $trueid    = $question->name_prefix.'true';
        $falseid   = $question->name_prefix.'false';

        $radiotrue = '<input type="radio"' . $truechecked . $readonly . $inputname
            . 'id="'.$trueid . '" value="' . $trueanswer->id . '" alt="'
            . s($trueanswer->answer) . '" /><label for="'.$trueid . '">'
            . s($trueanswer->answer) . '</label>';
        $radiofalse = '<input type="radio"' . $falsechecked . $readonly . $inputname
            . 'id="'.$falseid . '" value="' . $falseanswer->id . '" alt="'
            . s($falseanswer->answer) . '" /><label for="'.$falseid . '">'
            . s($falseanswer->answer) . '</label>';

        $feedback = '';
        if ($options->feedback and isset($answers[$state->responses['']])) {
            $chosenanswer = $answers[$state->responses['']];
            $feedback = format_text($chosenanswer->feedback, true, false);
        }
        
        include("$CFG->dirroot/question/questiontypes/truefalse/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        if (isset($question->options->answers[$state->responses['']])) {
            $state->raw_grade = $question->options->answers[$state->responses['']]->fraction * $question->maxgrade;
        } else {
            $state->raw_grade = 0;
        }
        // Only allow one attempt at the question
        $state->penalty = 1;

        return true;
    }

    function get_actual_response($question, $state) {
        if (isset($question->options->answers[$state->responses['']])) {
            $responses[] = $question->options->answers[$state->responses['']]->answer;
        } else {
            $responses[] = '';
        }
        return $responses;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES[TRUEFALSE]= new quiz_truefalse_qtype();

?>
