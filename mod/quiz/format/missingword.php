<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// MISSING WORD FORMAT
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
////////////////////////////////////////////////////////////////////////////

require("default.php");

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
        $question->name = $question->questiontext;


        /// Parse the answers
        $answertext = str_replace("=", "~=", $answertext);
        $answers = explode("~", $answertext);
        if (isset($answers[0])) {
            $answers[0] = trim($answers[0]);
        }
        if (empty($answers[0])) {
            array_shift($answers);
        }

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

}

?>
