<?PHP  // $Id$ 
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// WebCT FORMAT                                                          //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Part of Moodle - Modular Object-Oriented Dynamic Learning Environment //
//                  http://moodle.com                                    //
//                                                                       //
// Copyright (C) 2004 ASP Consulting   http://www.asp-consulting.net     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

function unhtmlentities($string){
    $search = array ("'<script[?>]*?>.*?</script>'si",  // remove javascript
                 "'<[\/\!]*?[^<?>]*?>'si",  // remove HTML tags
                 "'([\r\n])[\s]+'",  // remove spaces
                 "'&(quot|#34);'i",  // remove HTML entites
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");  // Evaluate like PHP
    $replace = array ("",
                  "",
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  "?>",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)");
    return preg_replace ($search, $replace, $string);
}

class quiz_file_format extends quiz_default_format {

    function readquestions ($lines) {

        $questions = array();
        $errors = array();
        $warnings = array();
        $webct_options = array();

        $ignore_rest_of_question = FALSE;

        $nLineCounter = 0;
        $nQuestionStartLine = 0;
        $bIsHTMLText = FALSE;
        $lines[] = ":EOF:";    // for an easiest processing of the last line

        foreach ($lines as $line) {
            $nLineCounter++;

            // Processing multiples lines strings

            if (is_string($questiontext)) {
                if (ereg("^:",$line)) {
                    $question->questiontext = addslashes(trim($questiontext));
                    unset($questiontext);
                }
                 else {
                    $questiontext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (is_string($answertext)) {
                if (ereg("^:",$line)) {
                    $answertext = addslashes(trim($answertext));
                    $question->answer[$currentchoice] = $answertext;
                    unset($answertext);
                }
                 else {
                    $answertext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (is_string($responstext)) {
                if (ereg("^:",$line)) {
                    $question->subquestions[$currentchoice] = addslashes(trim($responstext));
                    unset($responstext);
                }
                 else {
                    $responstext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (is_string($feedbacktext)) {
                if (ereg("^:",$line)) {
                    $question->feedback[$currentchoice] = addslashes(trim($feedbacktext));
                    unset($feedbacktext);
                }
                 else {
                    $feedbacktext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            $line = trim($line);

            if (eregi("^:(TYPE|EOF):",$line)) {
                // New Question or End of File
                if (isset($question)) {            // if previous question exists, complete, check and save it

                    // Setup default value of missing fields
                    if (!isset($question->name)) {
                        $question->name = $question->questiontext;
                    }
                    if (strlen($question->name) > 255) {
                        $question->name = substr($question->name,0,250)."...";
                        $warnings[] = get_string("questionnametoolong", "quiz", $nQuestionStartLine);
                    }
                    if (!isset($question->defaultgrade)) {
                        $question->defaultgrade = 1;
                    }
                    if (!isset($question->image)) {
                        $question->image = "";
                    }

                    // Perform sanity checks
                    $QuestionOK = TRUE;
                    if (strlen($question->questiontext) == 0) {
                        $errors[] = get_string("missingquestion", "quiz", $nQuestionStartLine);
                        $QuestionOK = FALSE;
                    }
                    if (sizeof($question->answer) < 1) {  // a question must have at least 1 answer
                       $errors[] = get_string("missinganswer", "quiz", $nQuestionStartLine);
                       $QuestionOK = FALSE;
                    }
                    else {
                        // Perform string length check
                        foreach ($question->answer as $key => $dataanswer) {
                            if (strlen($dataanswer) > 255) {
                                $question->answer[$key] = substr($dataanswer,0,250)."...";
                                $warnings[] = get_string("answertoolong", "quiz", $nQuestionStartLine);
                            }
                        }
                        $maxfraction = -1;
                        $totalfraction = 0;
                        foreach($question->fraction as $fraction) {
                            if ($fraction > 0) {
                                $totalfraction += $fraction;
                            }
                            if ($fraction > $maxfraction) {
                                $maxfraction = $fraction;
                            }
                        }
                        switch ($question->qtype) {
                            case SHORTANSWER:
                                if ($maxfraction != 1) {
                                    $maxfraction = $maxfraction * 100;
                                    $errors[] = get_string("wronggrade", "quiz", $nLineCounter).get_string("fractionsnomax", "quiz", $maxfraction);
                                    $QuestionOK = FALSE;
                                }
                                break;
                                
                            case MULTICHOICE:
                                if ($question->single) {
                                    if ($maxfraction != 1) {
                                        $maxfraction = $maxfraction * 100;
                                        $errors[] = get_string("wronggrade", "quiz", $nLineCounter).get_string("fractionsnomax", "quiz", $maxfraction);
                                        $QuestionOK = FALSE;
                                    }
                                }
                                else {
                                    $totalfraction = round($totalfraction,2);
                                    if ($totalfraction != 1) {
                                        $totalfraction = $totalfraction * 100;
                                        $errors[] = get_string("wronggrade", "quiz", $nLineCounter).get_string("fractionsaddwrong", "quiz", $totalfraction);
                                        $QuestionOK = FALSE;
                                    }
                                }
                        }
                    }

                    if ($QuestionOK) {
                        // a MULTIANSWER Question record use 'answers' variable instead of 'answer' (see lib.php)
                        foreach ($question->answer as $key => $dataanswer) {
                            $question->answers[$key] = $dataanswer;
                        }

                        $questions[] = $question;    // store it
                        unset($question);            // and prepare a new one
                    }
                }
                $nQuestionStartLine = $nLineCounter;
            }

            // Processing Question Header

            if (eregi("^:TYPE:MC:1(.*)",$line,$webct_options)) {
                // Multiple Choice Question with only one good answer
                $question->qtype = MULTICHOICE;
                $question->single = 1;        // Only one answer is allowed
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:MC:N(.*)",$line,$webct_options)) {
                // Multiple Choice Question with several good answers
                $question->qtype = MULTICHOICE;
                $question->single = 0;        // Many answers allowed
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:S",$line)) {
                // Short Answer Question
                $question->qtype = SHORTANSWER;
                $question->usecase = 0;       // Ignore case
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:M",$line)) {
                // Match Question
                $question->qtype = MATCH;
                $ignore_rest_of_question = TRUE;         // match question processing is not debugged
                continue;
            }

            if (eregi("^:TYPE:P",$line)) {
                // Paragraph Question
                $warnings[] = get_string("paragraphquestion", "quiz", $nLineCounter);
                $ignore_rest_of_question = TRUE;         // Question Type not handled by Moodle
                continue;
            }

            if (eregi("^:TYPE:C",$line)) {
                // Calculated Question
                $warnings[] = get_string("calculatedquestion", "quiz", $nLineCounter);
                $ignore_rest_of_question = TRUE;         // Question Type not handled by Moodle
                continue;
            }

            if (eregi("^:TYPE:",$line)) {
                // Unknow Question
                $warnings[] = get_string("unknowntype", "quiz", $nLineCounter);
                $ignore_rest_of_question = TRUE;         // Question Type not handled by Moodle
                continue;
            }

            if ($ignore_rest_of_question) {
                continue;
            }

            if (eregi("^:TITLE:(.*)",$line,$webct_options)) {
            	$name = trim($webct_options[1]);
                if (strlen($name) > 255) {
                    $name = substr($name,0,250)."...";
                    $warnings[] = get_string("questionnametoolong", "quiz", $nLineCounter);
                }
                $question->name = addslashes($name);
                continue;
            }

            if (eregi("^:IMAGE:(.*)",$line,$webct_options)) {
            	$filename = trim($webct_options[1]);
            	if (eregi("^http://",$filename)) {
	                $question->image = $filename;
            	}
                continue;
            }

            $bIsHTMLText = eregi(":H$",$line);	// True if next lines are coded in HTML
            if (eregi("^:QUESTION",$line)) {
                $questiontext="";               // Start gathering next lines
                continue;
            }

            if (eregi("^:ANSWER([0-9]+):([0-9\.]+)",$line,$webct_options)) {
                $answertext="";                 // Start gathering next lines
                $currentchoice=$webct_options[1];
                $question->fraction[$currentchoice]=($webct_options[2]/100);
                continue;
            }

            if (eregi("^:ANSWER([0-9]+):([^:]+):([0-9\.]+):(.*)",$line,$webct_options)) {      /// SHORTANSWER
                $currentchoice=$webct_options[1];
                $answertext=$webct_options[2];            // Start gathering next lines
                $question->fraction[$currentchoice]=($webct_options[3]/100);
                continue;
            }

            if (eregi("^:L([0-9]+)",$line,$webct_options)) {
                $answertext="";                 // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }

            if (eregi("^:R[0-9]+)",$line,$webct_options)) {
                $responstext="";                // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }

            if (eregi("^:REASON([0-9]+):?",$line,$webct_options)) {
                $feedbacktext="";               // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }
        }

        if (sizeof($errors) > 0) {
            echo "<p>".get_string("errorsdetected", "quiz", sizeof($errors))."</p><ul>";
            foreach($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            unset($questions);     // no questions imported
        }

        if (sizeof($warnings) > 0) {
            echo "<p>".get_string("warningsdetected", "quiz", sizeof($warnings))."</p><ul>";
            foreach($warnings as $warning) {
                echo "<li>$warning</li>";
            }
            echo "</ul>";
        }
        return $questions;
    }
}

?>
