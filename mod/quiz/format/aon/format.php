<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// Academy of Nursing format
///
/// Based on missingword.php
///
/// This Moodle class provides all functions necessary to import and export 
/// one-correct-answer multiple choice questions in this format:
///
///    As soon as we begin to explore our body parts as infants
///    we become students of {=anatomy and physiology ~reflexology 
///    ~science ~experiment}, and in a sense we remain students for life.
/// 
/// Each answer is separated with a tilde ~, and the correct answer is 
/// prefixed with an equals sign =
///
/// Afterwards, all short-answer questions are randomly packed into 
/// 4-answer matching questions.
///
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

class quiz_file_format extends quiz_default_format {

    function readquestion($lines) {
    /// Given an array of lines known to define a question in 
    /// this format, this function converts it into a question 
    /// object suitable for processing and insertion into Moodle.

        $question = NULL;

        $text = implode($lines, " ");

        /// Find answer section

        $answerstart = strpos($text, "{");
        if ($answerstart === false) {
            if ($this->displayerrors) {
                echo "<P>$text<P>Could not find a {";
            }
            return false;
        }

        $answerfinish = strpos($text, "}");
        if ($answerfinish === false) {
            if ($this->displayerrors) {
                echo "<P>$text<P>Could not find a }";
            }
            return false;
        }

        $answerlength = $answerfinish - $answerstart;
        $answertext = substr($text, $answerstart + 1, $answerlength - 1);

        /// Save the new question text
        $question->questiontext = addslashes(substr_replace($text, "_____", $answerstart, $answerlength+1));
        $question->name = substr($question->questiontext, 0, 60)." ...";


        /// Parse the answers
        $answers = explode("~", $answertext);

        $countanswers = count($answers);

        switch ($countanswers) {
            case 0:  // invalid question
                if ($this->displayerrors) {
                    echo "<P>No answers found in $answertext";
                }
                return false;

            case 1:
                $question->qtype = SHORTANSWER;

                $answer = trim($answers[0]);
                if ($answer[0] == "=") {
                    $answer = substr($answer, 1);
                }
                $question->answer[]   = addslashes($answer);
                $question->fraction[] = 1;
                $question->feedback[] = "";
    
                $question->usecase = 0;  // Ignore case
                $question->defaultgrade = 1; 
                $question->image = "";   // No images with this format
                return $question;

            default:
                $question->qtype = MULTICHOICE;

                $answers = swapshuffle($answers);
                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);
                    if ($answer[0] == "=") {
                        $question->fraction[$key] = 1;
                        $answer = substr($answer, 1);
                    } else {
                        $question->fraction[$key] = 0;
                    }
                    $question->answer[$key]   = addslashes($answer);
                    $question->feedback[$key] = "";
                }
    
                $question->defaultgrade = 1; 
                $question->single = 1;   // Only one answer is allowed
                $question->image = "";   // No images with this format
                return $question;
        }
    }

    function importpostprocess() {
    /// Goes through the questionids, looking for shortanswer questions
    /// and converting random groups of 4 into matching questions.

    /// Doesn't handle shortanswer questions with more than one answer

        global $CFG;

        print_heading(count($this->questionids)." ".get_string("questions", "quiz"));

        $questionids = implode(',', $this->questionids);

        if (!$shortanswers = get_records_select("quiz_questions", 
                                                "id IN ($questionids) AND qtype = ".SHORTANSWER,
                                                "", "id,qtype")) {
            return true;
        }


        $shortanswerids = array();
        foreach ($shortanswers as $key => $shortanswer) {
            $shortanswerids[] = $key;
        }
        
        $strmatch = get_string("match", "quiz")." (".$this->category->name.")";

        $shortanswerids = swapshuffle($shortanswerids);
        $count = $shortanswercount = count($shortanswerids);
        $i = 1;
        $matchcount = 0;

        $question->category = $this->category->id;
        $question->qtype    = MATCH;
        $question->questiontext = get_string("randomsamatchintro", "quiz");
        $question->image  = "";

        while ($count > 4) {
             $matchcount++;
             $question->name         = "$strmatch $i";
             $question->subquestions = array();
             $question->subanswers   = array();

             $extractids = implode(',', array_splice($shortanswerids, -4));
             $count = count($shortanswerids);

             $extracts = get_records_sql("SELECT q.questiontext, a.answer
                                            FROM {$CFG->prefix}quiz_questions q,
                                                 {$CFG->prefix}quiz_shortanswer sa,
                                                 {$CFG->prefix}quiz_answers a
                                           WHERE q.id in ($extractids) 
                                             AND sa.question = q.id
                                             AND a.id = sa.answers");

             if (count($extracts) != 4) {
                 print_object($extracts);
                 notify("Could not find exactly four shortanswer questions with ids: $extractids");
                 continue;
             }

             $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
             $question->version = 1;                    // Original version of this question

             if (!$question->id = insert_record("quiz_questions", $question)) {
                 error("Could not insert new question!");
             }

             foreach ($extracts as $shortanswer) {
                 $question->subquestions[] = addslashes($shortanswer->questiontext);
                 $question->subanswers[] = addslashes($shortanswer->answer);
             }

             $result = quiz_save_question_options($question);

             if (!empty($result->error)) {
                 notify("Error: $result->error");
             }

             if (!empty($result->notice)) {
                 notify($result->notice);
             }

             /// Delete the old short-answer questions

             execute_sql("DELETE FROM {$CFG->prefix}quiz_questions WHERE id IN ($extractids)", false);
             execute_sql("DELETE FROM {$CFG->prefix}quiz_shortanswer WHERE question IN ($extractids)", false);
             execute_sql("DELETE FROM {$CFG->prefix}quiz_answers WHERE question IN ($extractids)", false);
             
        }

        if ($count) {    /// Delete the remaining ones
            foreach ($shortanswerids as $shortanswerid) {
                delete_records("quiz_questions", "id", $shortanswerid);
                delete_records("quiz_shortanswer", "question", $shortanswerid);
                delete_records("quiz_answers", "question", $shortanswerid);
            }
        }
        $info = "$shortanswercount ".get_string("shortanswer", "quiz").
                " => $matchcount ".get_string("match", "quiz");

        print_heading($info);

        $options['category'] = $this->category->id;
        echo "<center>";
        print_single_button("multiple.php", $options, get_string("randomcreate", "quiz"));
        echo "</center>";

        return true;
    }

}

?>
