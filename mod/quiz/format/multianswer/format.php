<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// MULTIANSWER FORMAT
///
/// Created by Henrik Kaipe
///
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

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


function extractMultiAnswerQuestion($text) {
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

class quiz_file_format extends quiz_default_format {

    function readquestions($lines) {
    /// Parses an array of lines into an array of questions.
    /// For this class the method has been simplified as
    /// there can never be more than one question for a
    /// multianswer import

        $questions= array();
        $thequestion= extractMultiAnswerQuestion(addslashes(implode('',$lines)));

        if (!empty($thequestion)) {
            $thequestion->name = $lines[0];
            
            $questions[] = $thequestion;
        }

        return $questions;
    }
}

?>
