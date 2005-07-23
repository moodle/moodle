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
        if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
            $oldanswers = array();
        }

        if ($true = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $true->answer   = get_string("true", "quiz");
            $true->fraction = $question->answer;
            $true->feedback = $question->feedbacktrue;
            if (!update_record("quiz_answers", $true)) {
                $result->error = "Could not update quiz answer \"true\")!";
                return $result;
            }
        } else {
            unset($true);
            $true->answer   = get_string("true", "quiz");
            $true->question = $question->id;
            $true->fraction = $question->answer;
            $true->feedback = $question->feedbacktrue;
            if (!$true->id = insert_record("quiz_answers", $true)) {
                $result->error = "Could not insert quiz answer \"true\")!";
                return $result;
            }
        }

        if ($false = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $false->answer   = get_string("false", "quiz");
            $false->fraction = 1 - (int)$question->answer;
            $false->feedback = $question->feedbackfalse;
            if (!update_record("quiz_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        } else {
            unset($false);
            $false->answer   = get_string("false", "quiz");
            $false->question = $question->id;
            $false->fraction = 1 - (int)$question->answer;
            $false->feedback = $question->feedbackfalse;
            if (!$false->id = insert_record("quiz_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        }

        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                delete_records('quiz_answers', 'id', $oa->id);
            }
        }

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
        if (!$question->options = get_record('quiz_truefalse', 'question',
         $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }
        // Load possible answers
        if (!$answers = get_records('quiz_answers', 'question',
         $question->id)) {
           notify('Error: Missing question answers!');
           return false;
        }

        $question->options->answers = array(
         'true' => $answers[$question->options->trueanswer],
         'false' => $answers[$question->options->falseanswer]);

        return true;
    }

    function get_correct_responses(&$question, &$state) {
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
            $quiz, $options) {

        $answers = &$question->options->answers;
        $correctanswers = $this->get_correct_responses($question, $state);
        $readonly = $options->readonly ? ' readonly="readonly"' : '';

        // Print question formulation
        echo format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);

        // Update the answer strings
        $stranswer = get_string('answer', 'quiz');
        $strlastanswer = get_string('lastanswer', 'quiz');

        // Work out the selected answer and last marked answer
        $selected = '';
        $marked = '';
        $teststate = clone($state);
        $teststate->responses = array('' => $answers['true']->id);
        if ($this->compare_responses($question, $state, $teststate)) {
            $selected = 'true';
        }
        if ($this->compare_responses($question, $state->last_graded,
         $teststate)) {
            $marked = 'true';
        }
        $teststate->responses = array('' => $answers['false']->id);
        if ($this->compare_responses($question, $state, $teststate)) {
            $selected = 'false';
        }
        if ($this->compare_responses($question, $state->last_graded,
         $teststate)) {
            $marked = 'false';
        }

        // Work out the correct answer if feedback or correct responses are
        // requested.
        if ($options->feedback || $options->correct_responses) {
            $correctstate = clone($state);
            $correctstate->responses = array('' => $correctanswers['']);
            $correct = '';
            if (!is_null($correctstate->responses)) {
                $teststate->responses = array('' => $answers['true']->id);
                if ($this->compare_responses($question, $correctstate,
                 $teststate)) {
                    $correct = 'true';
                }
                $teststate->responses = array('' => $answers['false']->id);
                if ($this->compare_responses($question, $correctstate,
                 $teststate)) {
                    $correct = 'false';
                }
            }
        }

        // Work out which radio button to select (if either)
        $truechecked = ('true' === $selected) ? ' checked="checked"' : '';
        $falsechecked = ('false' === $selected) ? ' checked="checked"' : '';

        // Work out which answer is correct if we need to highlight it
        if ($options->correct_responses) {
            $truecorrect = ('true' === $correct) ? ' class="highlight"' : '';
            $falsecorrect = ('false' === $correct) ? ' class="highlight"' : '';
        } else {
            $truecorrect = '';
            $falsecorrect = '';
        }

        // Replace these with the appropriate language
        $answers['true']->answer  = get_string('true', 'quiz');
        $answers['false']->answer = get_string('false', 'quiz');

        // Print the controls
        $inputname = ' name="'.$question->name_prefix.'" ';
        $trueid    = $question->name_prefix.'true';
        $falseid   = $question->name_prefix.'false';
        echo '<table align="right" cellpadding="5"><tr><td align="right">';
        echo $stranswer . ':&nbsp;&nbsp;</td>';
        echo '<td' . $truecorrect . '>';
        echo '<input type="radio"' . $truechecked . $readonly . $inputname;
        echo 'id="'.$trueid . '" value="' . $answers['true']->id . '" alt="';
        echo s($answers['true']->answer) . '" /><label for="'.$trueid . '">';
        echo s($answers['true']->answer) . '</label>';
        echo '</td><td' . $falsecorrect . '>';
        echo '<input type="radio"' . $falsechecked . $readonly . $inputname;
        echo 'id="'.$falseid . '" value="' . $answers['false']->id . '" alt="';
        echo s($answers['false']->answer) . '" /><label for="'.$falseid . '">';
        echo s($answers['false']->answer) . '</label>';
        if (!empty($marked) && (!$options->readonly || $marked !== $selected)) {
            /* This should never happen but it is here both for robustness and
            to serve as an example for question type authors */
            echo '</td></tr><tr><td><font size="1">';
            echo $strlastanswer . ':&nbsp;&nbsp;</font></td>';
            echo '</td>';
            if ('true' === $marked) {
                echo '<td><font size="1">' . $answers['true']->answer;
                echo '</font></td><td></td>';
            } else {
                echo '<td></td><td><font size="1">';
                echo $answers['false']->answer . '</font></td>';
            }
        }
        echo '</td></tr></table><br clear="all" />';

        if ($options->feedback && !empty($marked)) {
            quiz_print_comment($answers[$marked]->feedback);
        }
    }

    function grade_responses(&$question, &$state, $quiz) {
        $teststate = clone($state);
	    $teststate->raw_grade = 0;
        foreach($question->options->answers as $answer) {
            $teststate->responses[''] = $answer->id;

            if($this->compare_responses($question, $state, $teststate)) {
                $state->raw_grade = min(max((float) $answer->fraction,
                 0.0), 1.0) * $question->maxgrade;
                break;
            }
        }
        if (empty($state->raw_grade)) {
            $state->raw_grade = 0.0;
        }
        // Only allow one attempt at the question
        $state->penalty = 1;

        return true;
    }

    function get_actual_response($question, $state) {
        $answers = $question->options->answers;
        if (!empty($state->responses)) {
                $responses[] = ($answers['true']->id == $state->responses['']) ? get_string("true", "quiz") : get_string("false", "quiz");
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
$QUIZ_QTYPES[TRUEFALSE]= new quiz_truefalse_qtype();

?>
