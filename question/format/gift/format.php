<?php // $Id$
//
///////////////////////////////////////////////////////////////
// The GIFT import filter was designed as an easy to use method 
// for teachers writing questions as a text file. It supports most
// question types and the missing word format.
//
// Multiple Choice / Missing Word
//     Who's buried in Grant's tomb?{~Grant ~Jefferson =no one}
//     Grant is {~buried =entombed ~living} in Grant's tomb.
// True-False:
//     Grant is buried in Grant's tomb.{FALSE}
// Short-Answer.
//     Who's buried in Grant's tomb?{=no one =nobody}
// Numerical
//     When was Ulysses S. Grant born?{#1822:5}
// Matching
//     Match the following countries with their corresponding
//     capitals.{=Canada->Ottawa =Italy->Rome =Japan->Tokyo}
//
// Comment lines start with a double backslash (//). 
// Optional question names are enclosed in double colon(::). 
// Answer feedback is indicated with hash mark (#).
// Percentage answer weights immediately follow the tilde (for
// multiple choice) or equal sign (for short answer and numerical),
// and are enclosed in percent signs (% %). See docs and examples.txt for more.
// 
// This filter was written through the collaboration of numerous 
// members of the Moodle community. It was originally based on 
// the missingword format, which included code from Thomas Robb
// and others. Paul Tsuchido Shew wrote this filter in December 2003.
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
class qformat_gift extends qformat_default {

    function provide_import() {
        return true;
    }

    function provide_export() {
        return true;
    }

    function answerweightparser(&$answer) {
        $answer = substr($answer, 1);                        // removes initial %
        $end_position  = strpos($answer, "%");
        $answer_weight = substr($answer, 0, $end_position);  // gets weight as integer
        $answer_weight = $answer_weight/100;                 // converts to percent
        $answer = substr($answer, $end_position+1);          // removes comment from answer
        return $answer_weight;
    }


    function commentparser(&$answer) {
        if (strpos($answer,"#") > 0){
            $hashpos = strpos($answer,"#");
            $comment = substr($answer, $hashpos+1);
            $comment = addslashes(trim($this->escapedchar_post($comment)));
            $answer  = substr($answer, 0, $hashpos);
        } else {
            $comment = " ";
        }
        return $comment;
    }

    function split_truefalse_comment($comment){
        // splits up comment around # marks
        // returns an array of true/false feedback
        $bits = explode('#',$comment);
        $feedback = array('wrong' => $bits[0]);
        if (count($bits) >= 2) {
            $feedback['right'] = $bits[1];
        } else {
            $feedback['right'] = '';
        }
        return $feedback;
    }
    
    function escapedchar_pre($string) {
        //Replaces escaped control characters with a placeholder BEFORE processing
        
        $escapedcharacters = array("\\:",    "\\#",    "\\=",    "\\{",    "\\}",    "\\~",    "\\n"   );  //dlnsk
        $placeholders      = array("&&058;", "&&035;", "&&061;", "&&123;", "&&125;", "&&126;", "&&010" );  //dlnsk

        $string = str_replace("\\\\", "&&092;", $string);
        $string = str_replace($escapedcharacters, $placeholders, $string);
        $string = str_replace("&&092;", "\\", $string);
        return $string;
    }

    function escapedchar_post($string) {
        //Replaces placeholders with corresponding character AFTER processing is done
        $placeholders = array("&&058;", "&&035;", "&&061;", "&&123;", "&&125;", "&&126;", "&&010"); //dlnsk
        $characters   = array(":",     "#",      "=",      "{",      "}",      "~",      "\n"   ); //dlnsk
        $string = str_replace($placeholders, $characters, $string);
        return $string;
    }

    function check_answer_count( $min, $answers, $text ) {
        $countanswers = count($answers);
        if ($countanswers < $min) {
            $importminerror = get_string( 'importminerror', 'quiz' );
            $this->error( $importminerror, $text );
            return false;
        }

        return true;
    }


    function readquestion($lines) {
    // Given an array of lines known to define a question in this format, this function
    // converts it into a question object suitable for processing and insertion into Moodle.

        $question = $this->defaultquestion();
        $comment = NULL;
        // define replaced by simple assignment, stop redefine notices
        $gift_answerweight_regex = "^%\-*([0-9]{1,2})\.?([0-9]*)%";        

        // REMOVED COMMENTED LINES and IMPLODE
        foreach ($lines as $key => $line) {
            $line = trim($line);
            if (substr($line, 0, 2) == "//") {
                $lines[$key] = " ";
            }
        }

        $text = trim(implode(" ", $lines));

        if ($text == "") {
            return false;
        }

        // Substitute escaped control characters with placeholders
        $text = $this->escapedchar_pre($text);

        // Look for category modifier
        if (ereg( '^\$CATEGORY:', $text)) {
            // $newcategory = $matches[1];
            $newcategory = trim(substr( $text, 10 ));

            // build fake question to contain category
            $question->qtype = 'category';
            $question->category = $newcategory;
            return $question;
        }
        
        // QUESTION NAME parser
        if (substr($text, 0, 2) == "::") {
            $text = substr($text, 2);

            $namefinish = strpos($text, "::");
            if ($namefinish === false) {
                $question->name = false;
                // name will be assigned after processing question text below
            } else {
                $questionname = substr($text, 0, $namefinish);
                $question->name = addslashes(trim($this->escapedchar_post($questionname)));
                $text = trim(substr($text, $namefinish+2)); // Remove name from text
            }
        } else {
            $question->name = false;
        }


        // FIND ANSWER section
        // no answer means its a description
        $answerstart = strpos($text, "{");
        $answerfinish = strpos($text, "}");

        $description = false;
        if (($answerstart === false) and ($answerfinish === false)) {
            $description = true;
            $answertext = '';
            $answerlength = 0;
        }
        elseif (!(($answerstart !== false) and ($answerfinish !== false))) {
            $this->error( get_string( 'braceerror', 'quiz' ), $text );
            return false;
        }
        else {
            $answerlength = $answerfinish - $answerstart;
            $answertext = trim(substr($text, $answerstart + 1, $answerlength - 1));
        }

        // Format QUESTION TEXT without answer, inserting "_____" as necessary
        if ($description) {
            $questiontext = $text;
        }
        elseif (substr($text, -1) == "}") {
            // no blank line if answers follow question, outside of closing punctuation
            $questiontext = substr_replace($text, "", $answerstart, $answerlength+1);
        } else {
            // inserts blank line for missing word format
            $questiontext = substr_replace($text, "_____", $answerstart, $answerlength+1);
        }

        // get questiontext format from questiontext
        $oldquestiontext = $questiontext;
        $questiontextformat = 0;
        if (substr($questiontext,0,1)=='[') {
            $questiontext = substr( $questiontext,1 );
            $rh_brace = strpos( $questiontext, ']' );
            $qtformat= substr( $questiontext, 0, $rh_brace );
            $questiontext = substr( $questiontext, $rh_brace+1 );
            if (!$questiontextformat = text_format_name( $qtformat )) {
                $questiontext = $oldquestiontext;
            }          
        }
        $question->questiontextformat = $questiontextformat;
        $question->questiontext = addslashes(trim($this->escapedchar_post($questiontext)));

        // set question name if not already set
        if ($question->name === false) {
            $question->name = $question->questiontext;
            }

        // ensure name is not longer than 250 characters
        $question->name = shorten_text( $question->name, 200 );
        $question->name = strip_tags(substr( $question->name, 0, 250 ));

        // determine QUESTION TYPE
        $question->qtype = NULL;

        // give plugins first try
        // plugins must promise not to intercept standard qtypes
        // MDL-12346, this could be called from lesson mod which has its own base class =(
        if (method_exists($this, 'try_importing_using_qtypes') && ($try_question = $this->try_importing_using_qtypes( $lines, $question, $answertext ))) {
            return $try_question;
        }

        if ($description) {
            $question->qtype = DESCRIPTION;
        }
        elseif ($answertext == '') {
            $question->qtype = ESSAY;
        }
        elseif ($answertext{0} == "#"){
            $question->qtype = NUMERICAL;

        } elseif (strpos($answertext, "~") !== false)  {
            // only Multiplechoice questions contain tilde ~
            $question->qtype = MULTICHOICE;
    
        } elseif (strpos($answertext, "=")  !== false 
                && strpos($answertext, "->") !== false) {
            // only Matching contains both = and ->
            $question->qtype = MATCH;

        } else { // either TRUEFALSE or SHORTANSWER
    
            // TRUEFALSE question check
            $truefalse_check = $answertext;
            if (strpos($answertext,"#") > 0){ 
                // strip comments to check for TrueFalse question
                $truefalse_check = trim(substr($answertext, 0, strpos($answertext,"#")));
            }

            $valid_tf_answers = array("T", "TRUE", "F", "FALSE");
            if (in_array($truefalse_check, $valid_tf_answers)) {
                $question->qtype = TRUEFALSE;

            } else { // Must be SHORTANSWER
                    $question->qtype = SHORTANSWER;
            }
        }

        if (!isset($question->qtype)) {
            $giftqtypenotset = get_string('giftqtypenotset','quiz');
            $this->error( $giftqtypenotset, $text );
            return false;
        }

        switch ($question->qtype) {
            case DESCRIPTION:
                $question->defaultgrade = 0;
                $question->length = 0;
                return $question;
                break;
            case ESSAY:
                $question->feedback = '';
                $question->fraction = 0;
                return $question;
                break;
            case MULTICHOICE:
                if (strpos($answertext,"=") === false) {
                    $question->single = 0;   // multiple answers are enabled if no single answer is 100% correct                        
                } else {
                    $question->single = 1;   // only one answer allowed (the default)
                }

                $answertext = str_replace("=", "~=", $answertext);
                $answers = explode("~", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }
    
                $countanswers = count($answers);
                
                if (!$this->check_answer_count( 2,$answers,$text )) {
                    return false;
                    break;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // determine answer weight
                    if ($answer[0] == "=") {
                        $answer_weight = 1;
                        $answer = substr($answer, 1);
    
                    } elseif (ereg($gift_answerweight_regex, $answer)) {    // check for properly formatted answer weight
                        $answer_weight = $this->answerweightparser($answer);
                    
                    } else {     //default, i.e., wrong anwer
                        $answer_weight = 0;
                    }
                    $question->fraction[$key] = $answer_weight;
                    $question->feedback[$key] = $this->commentparser($answer); // commentparser also removes comment from $answer
                    $question->answer[$key]   = addslashes($this->escapedchar_post($answer));
                    $question->correctfeedback = '';
                    $question->partiallycorrectfeedback = '';
                    $question->incorrectfeedback = '';
                }  // end foreach answer
    
                //$question->defaultgrade = 1;
                //$question->image = "";   // No images with this format
                return $question;
                break;

            case MATCH:
                $answers = explode("=", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }
    
                if (!$this->check_answer_count( 2,$answers,$text )) {
                    return false;
                    break;
                }
    
                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);
                    if (strpos($answer, "->") === false) {
                        $giftmatchingformat = get_string('giftmatchingformat','quiz');
                        $this->error($giftmatchingformat, $answer );
                        return false;
                        break 2;
                    }

                    $marker = strpos($answer,"->");
                    $question->subquestions[$key] = addslashes(trim($this->escapedchar_post(substr($answer, 0, $marker))));
                    $question->subanswers[$key]   = addslashes(trim($this->escapedchar_post(substr($answer, $marker+2))));

                }  // end foreach answer
    
                return $question;
                break;
            
            case TRUEFALSE:
                $answer = $answertext;
                $comment = $this->commentparser($answer); // commentparser also removes comment from $answer
                $feedback = $this->split_truefalse_comment($comment);

                if ($answer == "T" OR $answer == "TRUE") {
                    $question->answer = 1;
                    $question->feedbacktrue = $feedback['right'];
                    $question->feedbackfalse = $feedback['wrong'];
                } else {
                    $question->answer = 0;
                    $question->feedbackfalse = $feedback['right'];
                    $question->feedbacktrue = $feedback['wrong'];
                }

                $question->penalty = 1;
                $question->correctanswer = $question->answer;

                return $question;
                break;
                
            case SHORTANSWER:
                // SHORTANSWER Question
                $answers = explode("=", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }
    
                if (!$this->check_answer_count( 1,$answers,$text )) {
                    return false;
                    break;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Answer Weight
                    if (ereg($gift_answerweight_regex, $answer)) {    // check for properly formatted answer weight
                        $answer_weight = $this->answerweightparser($answer);
                    } else {     //default, i.e., full-credit anwer
                        $answer_weight = 1;
                    }
                    $question->fraction[$key] = $answer_weight;
                    $question->feedback[$key] = $this->commentparser($answer); //commentparser also removes comment from $answer
                    $question->answer[$key]   = addslashes($this->escapedchar_post($answer));
                }     // end foreach

                //$question->usecase = 0;  // Ignore case
                //$question->defaultgrade = 1;
                //$question->image = "";   // No images with this format
                return $question;
                break;

            case NUMERICAL:
                // Note similarities to ShortAnswer
                $answertext = substr($answertext, 1); // remove leading "#"

                // If there is feedback for a wrong answer, store it for now.
                if (($pos = strpos($answertext, '~')) !== false) {
                    $wrongfeedback = substr($answertext, $pos);
                    $answertext = substr($answertext, 0, $pos);
                } else {
                    $wrongfeedback = '';
                }

                $answers = explode("=", $answertext);
                if (isset($answers[0])) {
                    $answers[0] = trim($answers[0]);
                }
                if (empty($answers[0])) {
                    array_shift($answers);
                }
    
                if (count($answers) == 0) {
                    // invalid question
                    $giftnonumericalanswers = get_string('giftnonumericalanswers','quiz');
                    $this->error( $giftnonumericalanswers, $text );
                    return false;
                    break;
                }

                foreach ($answers as $key => $answer) {
                    $answer = trim($answer);

                    // Answer weight
                    if (ereg($gift_answerweight_regex, $answer)) {    // check for properly formatted answer weight
                        $answer_weight = $this->answerweightparser($answer);
                    } else {     //default, i.e., full-credit anwer
                        $answer_weight = 1;
                    }
                    $question->fraction[$key] = $answer_weight;
                    $question->feedback[$key] = $this->commentparser($answer); //commentparser also removes comment from $answer

                    //Calculate Answer and Min/Max values
                    if (strpos($answer,"..") > 0) { // optional [min]..[max] format
                        $marker = strpos($answer,"..");
                        $max = trim(substr($answer, $marker+2));
                        $min = trim(substr($answer, 0, $marker));
                        $ans = ($max + $min)/2;
                        $tol = $max - $ans;
                    } elseif (strpos($answer,":") > 0){ // standard [answer]:[errormargin] format
                        $marker = strpos($answer,":");
                        $tol = trim(substr($answer, $marker+1));
                        $ans = trim(substr($answer, 0, $marker));
                    } else { // only one valid answer (zero errormargin)
                        $tol = 0;
                        $ans = trim($answer);
                    }
    
                    if (!(is_numeric($ans) || $ans = '*') || !is_numeric($tol)) {
                            $errornotnumbers = get_string( 'errornotnumbers' );
                            $this->error( $errornotnumbers, $text );
                        return false;
                        break;
                    }
                    
                    // store results
                    $question->answer[$key] = $ans;
                    $question->tolerance[$key] = $tol;
                } // end foreach

                if ($wrongfeedback) {
                    $key += 1;
                    $question->fraction[$key] = 0;
                    $question->feedback[$key] = $this->commentparser($wrongfeedback);
                    $question->answer[$key] = '';
                    $question->tolerance[$key] = '';
                }

                return $question;
                break;

                default:
                    $giftnovalidquestion = get_string('giftnovalidquestion','quiz');
                    $this->error( $giftnovalidquestion, $text );
                return false;
                break;                
        
        } // end switch ($question->qtype)

    }    // end function readquestion($lines)

function repchar( $text, $format=0 ) {
    // escapes 'reserved' characters # = ~ { ) : and removes new lines
    // also pushes text through format routine
    $reserved = array( '#', '=', '~', '{', '}', ':', "\n","\r");
    $escaped =  array( '\#','\=','\~','\{','\}','\:','\n',''  ); //dlnsk

    $newtext = str_replace( $reserved, $escaped, $text ); 
    $format = 0; // turn this off for now
    if ($format) {
        $newtext = format_text( $format );
    }
    return $newtext;
    }

function writequestion( $question ) {
    // turns question into string
    // question reflects database fields for general question and specific to type

    global $QTYPES; 

    // initial string;
    $expout = "";

    // add comment
    $expout .= "// question: $question->id  name: $question->name \n";

    // get  question text format
    $textformat = $question->questiontextformat;
    $tfname = "";
    if ($textformat!=FORMAT_MOODLE) {
        $tfname = text_format_name( (int)$textformat );
        $tfname = "[$tfname]";
    }

    // output depends on question type
    switch($question->qtype) {
    case 'category':
        // not a real question, used to insert category switch
        $expout .= "\$CATEGORY: $question->category\n";    
        break;
    case DESCRIPTION:
        $expout .= '::'.$this->repchar($question->name).'::';
        $expout .= $tfname;
        $expout .= $this->repchar( $question->questiontext, $textformat);
        break;
    case ESSAY:
        $expout .= '::'.$this->repchar($question->name).'::';
        $expout .= $tfname;
        $expout .= $this->repchar( $question->questiontext, $textformat);
        $expout .= "{}\n";
        break;
    case TRUEFALSE:
        $trueanswer = $question->options->answers[$question->options->trueanswer];
        $falseanswer = $question->options->answers[$question->options->falseanswer];
        if ($trueanswer->fraction == 1) {
            $answertext = 'TRUE';
            $right_feedback = $trueanswer->feedback;
            $wrong_feedback = $falseanswer->feedback;
        } else {
            $answertext = 'FALSE';
            $right_feedback = $falseanswer->feedback;
            $wrong_feedback = $trueanswer->feedback;
        }

        $wrong_feedback = $this->repchar($wrong_feedback);
        $right_feedback = $this->repchar($right_feedback);
        $expout .= "::".$this->repchar($question->name)."::".$tfname.$this->repchar( $question->questiontext,$textformat )."{".$this->repchar( $answertext );
        if ($wrong_feedback) {
            $expout .= "#" . $wrong_feedback;
        } else if ($right_feedback) {
            $expout .= "#";
        }
        if ($right_feedback) {
            $expout .= "#" . $right_feedback;
        }
        $expout .= "}\n";
        break;
    case MULTICHOICE:
        $expout .= "::".$this->repchar($question->name)."::".$tfname.$this->repchar( $question->questiontext, $textformat )."{\n";
        foreach($question->options->answers as $answer) {
            if ($answer->fraction==1) {
                $answertext = '=';
            }
            elseif ($answer->fraction==0) {
                $answertext = '~';
            }
            else {
                $export_weight = $answer->fraction*100;
                $answertext = "~%$export_weight%";
            }
            $expout .= "\t".$answertext.$this->repchar( $answer->answer );
            if ($answer->feedback!="") {
                $expout .= "#".$this->repchar( $answer->feedback );
            }
            $expout .= "\n";
        }
        $expout .= "}\n";
        break;
    case SHORTANSWER:
        $expout .= "::".$this->repchar($question->name)."::".$tfname.$this->repchar( $question->questiontext, $textformat )."{\n";
        foreach($question->options->answers as $answer) {
            $weight = 100 * $answer->fraction;
            $expout .= "\t=%".$weight."%".$this->repchar( $answer->answer )."#".$this->repchar( $answer->feedback )."\n";
        }
        $expout .= "}\n";
        break;
    case NUMERICAL:
        $expout .= "::".$this->repchar($question->name)."::".$tfname.$this->repchar( $question->questiontext, $textformat )."{#\n";
        foreach ($question->options->answers as $answer) {
            if ($answer->answer != '') {
                $percentage = '';
                if ($answer->fraction < 1) {
                    $pval = $answer->fraction * 100;
                    $percentage = "%$pval%";
                }
                $expout .= "\t=$percentage".$answer->answer.":".(float)$answer->tolerance."#".$this->repchar( $answer->feedback )."\n";
            } else {
                $expout .= "\t~#".$this->repchar( $answer->feedback )."\n";
            }
        }
        $expout .= "}\n";
        break;
    case MATCH:
        $expout .= "::".$this->repchar($question->name)."::".$tfname.$this->repchar( $question->questiontext, $textformat )."{\n";
        foreach($question->options->subquestions as $subquestion) {
            $expout .= "\t=".$this->repchar( $subquestion->questiontext )." -> ".$this->repchar( $subquestion->answertext )."\n";
        }
        $expout .= "}\n";
        break;
    default:
        // check for plugins
        if ($out = $this->try_exporting_using_qtypes( $question->qtype, $question )) {
            $expout .= $out;
        }
        else {
            $expout .= "// $question->qtype is not supported by the GIFT format\n";
            $menuname = $QTYPES[$question->qtype]->menu_name(); 
            notify( get_string('nohandler','qformat_gift', $menuname ) );
        }
    }
    // add empty line to delimit questions
    $expout .= "\n";
    return $expout;
}
}
?>
