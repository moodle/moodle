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
// Copyright (C) 2003 ASP Consulting   http://www.asp-consulting.net     //
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

class quiz_file_format extends quiz_default_format {

    function readquestions ($lines) {

        $questions = array();
        $errors = array();
        $warnings = array();
        $webct_options = array();

        $ignore_lines = FALSE;

        $nLine = 0;
        $nQuestionStartLine = 0;
        $bRawText = TRUE;
        $lines[] = ":EOF:";    // for an easiest processing of the last line

        foreach ($lines as $line) {
            $nLine++;
            if (is_string($questiontext)) {
                if (ereg("^:",$line)) {
                    if ($bRawText) {
                    	$questiontext = htmlentities($questiontext);
                    }
                    $question->questiontext = trim($questiontext);
                    unset($questiontext);
                }
                 else {
                    $questiontext .= $line;
                    continue;
                }
            }

            if (is_string($answertext)) {
                if (ereg("^:",$line)) {
                    if ($bRawText) {
                    	$answertext = htmlentities($answertext);
                    }
                    $answertext = trim($answertext);
                    $question->answer[$currentchoice] = $answertext;
                    $question->answers[$currentchoice] = $answertext;	// for question MULTIANSWER (see lib.php)
                    $question->subanswers[$currentchoice] = $answertext;
                    unset($answertext);
                }
                 else {
                    $answertext .= $line;
                    continue;
                }
            }

            if (is_string($responstext)) {
                if (ereg("^:",$line)) {
                    if ($bRawText) {
                    	$responstext = htmlentities($responstext);
                    }
                    $question->subquestions[$currentchoice] = trim($responstext);
                    unset($responstext);
                }
                 else {
                    $responstext .= $line;
                    continue;
                }
            }

            if (is_string($feedbacktext)) {
                if (ereg("^:",$line)) {
                    if ($bRawText) {
                    	$feedbacktext = htmlentities($feedbacktext);
                    }
                    $question->feedback[$currentchoice] = trim($feedbacktext);
                    unset($feedbacktext);
                }
                 else {
                    $feedbacktext .= $line;
                    continue;
                }
            }

            $line = trim($line);

            if (eregi("^:(TYPE|EOF):",$line)) {
                // New Question or End of File
                if (isset($question)) {            // if previous question exists, save it
                    $QuestionOK = TRUE;
                    if (strlen($question->name) == 0) {
                        $question->name = $question->questiontext;
                    }
                    if (strlen($question->name) > 255) {
                        $question->name = substr($question->name,0,250)."...";
                        $warnings[] = get_string("questionnametoolong", "importwebcbt", $nQuestionStartLine);
                    }
                    // Perform sanity checks
                    if (strlen($question->questiontext) == 0) {
                        $errors[] = get_string("missingquestion", "importwebcbt", $nQuestionStartLine);
                        $QuestionOK = FALSE;
                    }

                    if (sizeof($question->answer) <= 1) {
                        $errors[] = get_string("missinganswer", "importwebcbt", $nQuestionStartLine);
                        $QuestionOK = FALSE;
                    }
                    else {
                        // Perform string length check
                        foreach ($question->answer as $key => $dataanswer) {
                            if (strlen($dataanswer) > 255) {
                                $question->answer[$key] = substr($dataanswer,0,250)."...";
                                $warnings[] = get_string("answertoolong", "importwebcbt", $nQuestionStartLine);
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
                                    $errors[] = get_string("wronggrade", "importwebcbt", $nLine).get_string("fractionsnomax", "quiz", $maxfraction);
                                    $QuestionOK = FALSE;
                                }
                                break;
                                
                            case MULTICHOICE:
                                if ($question->single) {
                                    if ($maxfraction != 1) {
                                        $maxfraction = $maxfraction * 100;
                                        $errors[] = get_string("wronggrade", "importwebcbt", $nLine).get_string("fractionsnomax", "quiz", $maxfraction);
                                        $QuestionOK = FALSE;
                                    }
                                }
                                else {
                                    $totalfraction = round($totalfraction,2);
                                    if ($totalfraction != 1) {
                                        $totalfraction = $totalfraction * 100;
                                        $errors[] = get_string("wronggrade", "importwebcbt", $nLine).get_string("fractionsaddwrong", "quiz", $totalfraction);
                                        $QuestionOK = FALSE;
                                    }
                                }
                        }
                    }

                    if ($QuestionOK) {
                        $questions[] = $question;    // store it
                        unset($question);            // and prepare a new one
                    }
                }
                $nQuestionStartLine = $nLine;
            }

            if (eregi("^:TYPE:MC:1(.*)",$line,$webct_options)) {
                // Multiple Choice Question with only one good answer
                $question->qtype = MULTICHOICE;
                $question->name = "";
                $question->defaultgrade = 1;
                $question->single = 1;        // Only one answer is allowed
                $question->image = "";        // No images with this format
                $ignore_lines = FALSE;
                continue;
            }

            if (eregi("^:TYPE:MC:N(.*)",$line,$webct_options)) {
                // Multiple Choice Question with many good answers
                $question->qtype = MULTICHOICE;
                $question->name = "";
                $question->defaultgrade = 1;
                $question->single = 0;        // Many answers allowed
                $question->image = "";        // No images with this format
                $ignore_lines = FALSE;
                continue;
            }

            if (eregi("^:TYPE:S",$line)) {
                // Short Answer Question
                $question->qtype = SHORTANSWER;
                $question->name = "";
                $question->defaultgrade = 1;
                $question->usecase = 0;       // Ignore case
                $question->image = "";        // No images with this format
                $ignore_lines = FALSE;
                continue;
            }

            if (eregi("^:TYPE:M",$line)) {
                // Match Question
                $question->qtype = MATCH;
                $question->name = "";
                $question->defaultgrade = 1;
                $question->image = "";        // No images with this format
                $ignore_lines = TRUE;         // match question processing is not debugged
                continue;
            }

            if (eregi("^:TYPE:P",$line)) {
                // Paragraph Question
                $warnings[] = get_string("paragraphquestion", "importwebcbt", $nLine);
                $ignore_lines = TRUE;         // do not process lines until next question
                continue;
            }

            if (eregi("^:TYPE:C",$line)) {
                // Calculated Question
                $warnings[] = get_string("calculatedquestion", "importwebcbt", $nLine);
                $ignore_lines = TRUE;         // do not process lines until next question
                continue;
            }

            if (eregi("^:TYPE:",$line)) {
                // Unknow Question
                $warnings[] = get_string("unknowtype", "importwebcbt", $nLine);
                $ignore_lines = TRUE;         // do not process lines until next question
                continue;
            }

            if (eregi("^:CAT:",$line)) {
                // Category ignored
                continue;
            }

            if ($ignore_lines) {
                continue;
            }

            if (eregi("^:TITLE:(.*)",$line,$webct_options)) {
            	$name = trim($webct_options[1]);
                if (strlen($name) > 255) {
                    $name = substr($name,0,250)."...";
                    $warnings[] = get_string("questionnametoolong", "importwebcbt", $nLine);
                }
                $question->name = $name;
                continue;
            }

            if (eregi("^:IMAGE:(.*)",$line,$webct_options)) {
            	$filename = trim($webct_options[1]);
            	if (file_exists("$CFG->dataroot\\$filename")) {
	                $question->image = $filename;
            	}
            	else {
                    $warnings[] = get_string("imagemissing", "importwebcbt", $nLine);
            	}
                continue;
            }

            $bRawText = !eregi(":H$",$line);	// false if next lines are coded in HTML

            if (eregi("^:QUESTION",$line)) {
                $questiontext="";               // Grab next lines
                continue;
            }

            if (eregi("^:ANSWER([0-9]+):([0-9\.]+)",$line,$webct_options)) {
                $answertext="";                 // Grab next lines
                $currentchoice=$webct_options[1];
                $question->fraction[$currentchoice]=1.0*$webct_options[2];
                continue;
            }

            if (eregi("^:L([0-9]+)",$line,$webct_options)) {
                $answertext="";                 // Grab next lines
                $currentchoice=$webct_options[1];
                continue;
            }

            if (eregi("^:R[0-9]+)",$line,$webct_options)) {
                $responstext="";                // Grab next lines
                $currentchoice=$webct_options[1];
                continue;
            }

            if (eregi("^:REASON([0-9]+):?",$line,$webct_options)) {
                $feedbacktext="";               // Grab next lines
                $currentchoice=$webct_options[1];
                continue;
            }
        }

        if (sizeof($errors) > 0) {
            echo "<p>".get_string("errorsdetected", "importwebcbt", sizeof($errors))."</p><ul>";
            foreach($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }

        if (sizeof($warnings) > 0) {
            echo "<p>".get_string("warningsdetected", "importwebcbt", sizeof($warnings))."</p><ul>";
            foreach($warnings as $warning) {
                echo "<li>$warning</li>";
            }
            echo "</ul>";
        }
        return $questions;
    }
}

?>
