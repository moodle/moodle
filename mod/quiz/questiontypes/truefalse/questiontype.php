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

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {

        // Get additional information from database

        if (!$options = get_record("quiz_truefalse", "question", $question->id)) {
           notify("Error: Missing question options!");
        }
        if (!$true = get_record("quiz_answers", "id", $options->trueanswer)) {
           notify("Error: Missing question answers!");
        }
        if (!$false = get_record("quiz_answers", "id", $options->falseanswer)) {
           notify("Error: Missing question answers!");
        }

        // Print question formulation

        echo format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);

        // Print input controls

        $stranswer = get_string("answer", "quiz");

        if (!$true->answer) {
           $true->answer = get_string("true", "quiz");
        }
        if (!$false->answer) {
           $false->answer = get_string("false", "quiz");
        }

        $truechecked = "";
        $falsechecked = "";

        if (!isset($question->response[$nameprefix])) {
            $question->response[$nameprefix] = '';
        }
        if ($true->id == $question->response[$nameprefix]) {
           $truechecked = 'checked="checked"';
        } else if ($false->id == $question->response[$nameprefix]) {
           $falsechecked = 'checked="checked"';
        }
        if ($readonly) {
            $readonly = ' readonly="readonly" disabled="disabled" ';
        }

        $truecorrect = "";
        $falsecorrect = "";
        if ($readonly && $quiz->correctanswers) {
           if (!empty($correctanswers[$nameprefix.$true->id])) {
               $truecorrect = 'class="highlight"';
           }
           if (!empty($correctanswers[$nameprefix.$false->id])) {
               $falsecorrect = 'class="highlight"';
           }
        }
        $inputname = ' name="'.$nameprefix.'" ';
        echo "<table align=\"right\" cellpadding=\"5\"><tr><td align=\"right\">$stranswer:&nbsp;&nbsp;";
        echo "<td $truecorrect>";
        echo "<input $truechecked type=\"radio\" $readonly $inputname value=\"$true->id\" />$true->answer";
        echo "</td><td $falsecorrect>";
        echo "<input $falsechecked type=\"radio\"  $readonly $inputname value=\"$false->id\" />$false->answer";
        echo "</td></tr></table><br clear=\"all\">";// changed from clear=ALL jm
        if ($quiz->feedback && isset($answers[$nameprefix])
                && $feedback = $answers[$nameprefix]->feedback) {
           quiz_print_comment(
                    "<p align=\"right\">$feedback</p>");
        }
    }

    function grade_response($question, $nameprefix) {

        $answers = get_records("quiz_answers", "question", $question->id);
        if (isset($question->response[$nameprefix])
                && isset($answers[$question->response[$nameprefix]])) {
            $result->answers = array($nameprefix
                               => $answers[$question->response[$nameprefix]]);
            $result->grade = $result->answers[$nameprefix]->fraction;

        } else {
            $result->answers = array();
            $result->grade = 0.0;
        }
        $result->correctanswers = quiz_extract_correctanswers($answers,
                                                              $nameprefix);

        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[TRUEFALSE]= new quiz_truefalse_qtype();

?>
