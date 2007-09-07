<?php  // $Id$
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
/**
 * @package questionbank
 * @subpackage importexport
 */

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



function qformat_webct_convert_formula($formula) {

    // Remove empty space, as it would cause problems otherwise:
    $formula = str_replace(' ', '', $formula);

    // Remove paranthesis after e,E and *10**:
    while (ereg('[0-9.](e|E|\\*10\\*\\*)\\([+-]?[0-9]+\\)', $formula, $regs)) {
        $formula = str_replace(
                $regs[0], ereg_replace('[)(]', '', $regs[0]), $formula);
    }

    // Replace *10** with e where possible
    while (ereg(
            '(^[+-]?|[^eE][+-]|[^0-9eE+-])[0-9.]+\\*10\\*\\*[+-]?[0-9]+([^0-9.eE]|$)',
            $formula, $regs)) {
        $formula = str_replace(
                $regs[0], str_replace('*10**', 'e', $regs[0]), $formula);
    }

    // Replace other 10** with 1e where possible
    while (ereg('(^|[^0-9.eE])10\\*\\*[+-]?[0-9]+([^0-9.eE]|$)', $formula, $regs)) {
        $formula = str_replace(
                $regs[0], str_replace('10**', '1e', $regs[0]), $formula);
    }

    // Replace all other base**exp with the PHP equivalent function pow(base,exp)
    // (Pretty tricky to exchange an operator with a function)
    while (2 == count($splits = explode('**', $formula, 2))) {

        // Find $base
        if (ereg('^(.*[^0-9.eE])?(([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][+-]?[0-9]+)?|\\{[^}]*\\})$',
                $splits[0], $regs)) {
            // The simple cases
            $base = $regs[2];
            $splits[0] = $regs[1];

        } else if (ereg('\\)$', $splits[0])) {
            // Find the start of this parenthesis
            $deep = 1;
            for ($i = 1 ; $deep ; ++$i) {
                if (!ereg('^(.*[^[:alnum:]_])?([[:alnum:]_]*([)(])([^)(]*[)(]){'.$i.'})$',
                        $splits[0], $regs)) {
                    error("Parenthesis before ** is not properly started in $splits[0]**");
                }
                if ('(' == $regs[3]) {
                    --$deep;
                } else if (')' == $regs[3]) {
                    ++$deep;
                } else {
                    error("Impossible character $regs[3] detected as parenthesis character");
                }
            }
            $base = $regs[2];
            $splits[0] = $regs[1];

        } else {
            error("Bad base before **: $splits[0]**");
        }

        // Find $exp (similar to above but a little easier)
        if (ereg('^([+-]?(\\{[^}]\\}|([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][+-]?[0-9]+)?))(.*)',
                $splits[1], $regs)) {
            // The simple case
            $exp = $regs[1];
            $splits[1] = $regs[6];

        } else if (ereg('^[+-]?[[:alnum:]_]*\\(', $splits[1])) {
            // Find the end of the parenthesis
            $deep = 1;
            for ($i = 1 ; $deep ; ++$i) {
                if (!ereg('^([+-]?[[:alnum:]_]*([)(][^)(]*){'.$i.'}([)(]))(.*)',
                        $splits[1], $regs)) {
                    error("Parenthesis after ** is not properly closed in **$splits[1]");
                }
                if (')' == $regs[3]) {
                    --$deep;
                } else if ('(' == $regs[3]) {
                    ++$deep;
                } else {
                    error("Impossible character $regs[3] detected as parenthesis character");
                }
            }
            $exp = $regs[1];
            $splits[1] = $regs[4];
        }

        // Replace it!
        $formula = "$splits[0]pow($base,$exp)$splits[1]";
    }

    // Nothing more is known to need to be converted

    return $formula;
}

class qformat_webct extends qformat_default {

    function provide_import() {
      return true;
    }

    function readquestions ($lines) {
        global $QTYPES ;
        //  $qtypecalculated = new qformat_webct_modified_calculated_qtype();
        $webctnumberregex =
                '[+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)((e|E|\\*10\\*\\*)([+-]?[0-9]+|\\([+-]?[0-9]+\\)))?';

        $questions = array();
        $errors = array();
        $warnings = array();
        $webct_options = array();

        $ignore_rest_of_question = FALSE;

        $nLineCounter = 0;
        $nQuestionStartLine = 0;
        $bIsHTMLText = FALSE;
        $lines[] = ":EOF:";    // for an easiest processing of the last line
    //    $question = $this->defaultquestion();

        foreach ($lines as $line) {
            $nLineCounter++;
            $line = iconv("Windows-1252","UTF-8",$line);
            // Processing multiples lines strings

            if (isset($questiontext) and is_string($questiontext)) {
                if (ereg("^:",$line)) {
                    $question->questiontext = addslashes(trim($questiontext));
                    unset($questiontext);
                }
                 else {
                    $questiontext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($answertext) and is_string($answertext)) {
                if (ereg("^:",$line)) {
                    $answertext = addslashes(trim($answertext));
                    $question->answer[$currentchoice] = $answertext;
                    $question->subanswers[$currentchoice] = $answertext;
                    unset($answertext);
                }
                 else {
                    $answertext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($responsetext) and is_string($responsetext)) {
                if (ereg("^:",$line)) {
                    $question->subquestions[$currentchoice] = addslashes(trim($responsetext));
                    unset($responsetext);
                }
                 else {
                    $responsetext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($feedbacktext) and is_string($feedbacktext)) {
                if (ereg("^:",$line)) {
                   $question->feedback[$currentchoice] = addslashes(trim($feedbacktext));
                    unset($feedbacktext);
                }
                 else {
                    $feedbacktext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($generalfeedbacktext) and is_string($generalfeedbacktext)) {
                if (ereg("^:",$line)) {
                   $question->tempgeneralfeedback= addslashes(trim($generalfeedbacktext));
                    unset($generalfeedbacktext);
                }
                 else {
                    $generalfeedbacktext .= str_replace('\:', ':', $line);
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
                        $warnings[] = get_string("missingquestion", "quiz", $nQuestionStartLine);
                        $QuestionOK = FALSE;
                    }
                    if (sizeof($question->answer) < 1) {  // a question must have at least 1 answer
                       $errors[] = get_string("missinganswer", "quiz", $nQuestionStartLine);
                       $QuestionOK = FALSE;
                    }
                    else {
                        // Create empty feedback array                      
                        foreach ($question->answer as $key => $dataanswer) {
                            if(!isset( $question->feedback[$key])){
                                $question->feedback[$key] = '';
                            }
                        }
                        // this tempgeneralfeedback allows the code to work with versions from 1.6 to 1.9
                        // when question->generalfeedback is undefined, the webct feedback is added to each answer feedback
                        if (isset($question->tempgeneralfeedback)){
                            if (isset($question->generalfeedback)) {
                                $question->generalfeedback = $question->tempgeneralfeedback;
                            } else {  
                                foreach ($question->answer as $key => $dataanswer) {
                                    if ($question->tempgeneralfeedback !=''){
                                        $question->feedback[$key] = $question->tempgeneralfeedback.'<br/>'.$question->feedback[$key];
                                    }
                                }
                            }
                            unset($question->tempgeneralfeedback);   
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
                                    $errors[] = "'$question->name': ".get_string("wronggrade", "quiz", $nLineCounter).' '.get_string("fractionsnomax", "quiz", $maxfraction);
                                    $QuestionOK = FALSE;
                                }
                                break;

                            case MULTICHOICE:
                                if ($question->single) {
                                    if ($maxfraction != 1) {
                                        $maxfraction = $maxfraction * 100;
                                        $errors[] = "'$question->name': ".get_string("wronggrade", "quiz", $nLineCounter).' '.get_string("fractionsnomax", "quiz", $maxfraction);
                                        $QuestionOK = FALSE;
                                    }
                                } else {
                                    $totalfraction = round($totalfraction,2);
                                    if ($totalfraction != 1) {                               
                                        $totalfraction = $totalfraction * 100;
                                        $errors[] = "'$question->name': ".get_string("wronggrade", "quiz", $nLineCounter).' '.get_string("fractionsaddwrong", "quiz", $totalfraction);
                                        $QuestionOK = FALSE;
                                    }
                                }
                                break;

                            case CALCULATED:
                                foreach ($question->answers as $answer) {
                                    if ($formulaerror =qtype_calculated_find_formula_errors($answer)) { //$QTYPES['calculated']->
                                        $warnings[] = "'$question->name': ". $formulaerror;
                                        $QuestionOK = FALSE;
                                    }
                                }
                                foreach ($question->dataset as $dataset) {
                                    $dataset->itemcount=count($dataset->datasetitem);
                                }
                                $question->import_process=TRUE ;
                                unset($question->answer); //not used in calculated question
                                break;
                            case MATCH:
                                // MDL-10680:
                                // switch subquestions and subanswers
                                foreach ($question->subquestions as $id=>$subquestion) {
                                    $temp = $question->subquestions[$id];
                                    $question->subquestions[$id] = $question->subanswers[$id];
                                    $question->subanswers[$id] = $temp; 
                                }
                                if (count($question->answer) < 3){
                                    // add a dummy missing question
                                    $question->name = 'Dummy question added '.$question->name ;
                                    $question->answer[] = 'dummy';
                                    $question->subanswers[] = 'dummy';
                                    $question->subquestions[] = 'dummy';                                    
                                    $question->fraction[] = '0.0';
                                    $question->feedback[] = '';
                                 }   
                                 break;   
                            default:
                                // No problemo
                        }
                    }

                    if ($QuestionOK) {                        
                       // echo "<pre>"; print_r ($question);
                        $questions[] = $question;    // store it
                        unset($question);            // and prepare a new one
                        $question = $this->defaultquestion();
                    }
                }
                $nQuestionStartLine = $nLineCounter;
            }

            // Processing Question Header

            if (eregi("^:TYPE:MC:1(.*)",$line,$webct_options)) {
                // Multiple Choice Question with only one good answer
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = MULTICHOICE;
                $question->single = 1;        // Only one answer is allowed
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:MC:N(.*)",$line,$webct_options)) {
                // Multiple Choice Question with several good answers
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = MULTICHOICE;
                $question->single = 0;        // Many answers allowed
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:S",$line)) {
                // Short Answer Question
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = SHORTANSWER;
                $question->usecase = 0;       // Ignore case
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:C",$line)) {
                // Calculated Question
           /*     $warnings[] = get_string("calculatedquestion", "quiz", $nLineCounter);
                unset($question);
                $ignore_rest_of_question = TRUE;         // Question Type not handled by Moodle
             */
                $question = $this->defaultquestion();
                $question->qtype = CALCULATED;
                $question->answers = array(); // No problem as they go as :FORMULA: from webct
                $question->units = array();
                $question->dataset = array();

                // To make us pass the end-of-question sanity checks
                $question->answer = array('dummy');
                $question->fraction = array('1.0');
                $question->feedback = array();

                $currentchoice = -1;
                $ignore_rest_of_question = FALSE;
                continue;
            }

            if (eregi("^:TYPE:M",$line)) {
                // Match Question
                $question = $this->defaultquestion();
                $question->qtype = MATCH;
                $question->feedback = array();
                $ignore_rest_of_question = FALSE;         // match question processing is not debugged
                continue;
            }

            if (eregi("^:TYPE:P",$line)) {
                // Paragraph Question
                $warnings[] = get_string("paragraphquestion", "quiz", $nLineCounter);
                unset($question);
                $ignore_rest_of_question = TRUE;         // Question Type not handled by Moodle
                continue;
            }

            if (eregi("^:TYPE:",$line)) {
                // Unknow Question
                $warnings[] = get_string("unknowntype", "quiz", $nLineCounter);
                unset($question);
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

            // Need to put the parsing of calculated items here to avoid ambitiuosness:
            // if question isn't defined yet there is nothing to do here (avoid notices)
            if (!isset($question)) {
                continue;
            } 
            if (isset($question->qtype ) && CALCULATED == $question->qtype && ereg(
                    "^:([[:lower:]].*|::.*)-(MIN|MAX|DEC|VAL([0-9]+))::?:?($webctnumberregex)", $line, $webct_options)) {
                $datasetname = ereg_replace('^::', '', $webct_options[1]);
                $datasetvalue = qformat_webct_convert_formula($webct_options[4]);
                switch ($webct_options[2]) {
                    case 'MIN':
                        $question->dataset[$datasetname]->min = $datasetvalue;
                        break;
                    case 'MAX':
                        $question->dataset[$datasetname]->max = $datasetvalue;
                        break;
                    case 'DEC':
                        $datasetvalue = floor($datasetvalue); // int only!
                        $question->dataset[$datasetname]->length = max(0, $datasetvalue);
                        break;
                    default:
                        // The VAL case:
                        $question->dataset[$datasetname]->datasetitem[$webct_options[3]] = new stdClass();
                        $question->dataset[$datasetname]->datasetitem[$webct_options[3]]->itemnumber = $webct_options[3];
                        $question->dataset[$datasetname]->datasetitem[$webct_options[3]]->value  = $datasetvalue;
                        break;
                }
                continue;
            }


            $bIsHTMLText = eregi(":H$",$line);  // True if next lines are coded in HTML
            if (eregi("^:QUESTION",$line)) {
                $questiontext="";               // Start gathering next lines
                continue;
            }

            if (eregi("^:ANSWER([0-9]+):([^:]+):([0-9\.\-]+):(.*)",$line,$webct_options)) {      /// SHORTANSWER
                $currentchoice=$webct_options[1];
                $answertext=$webct_options[2];            // Start gathering next lines
                $question->fraction[$currentchoice]=($webct_options[3]/100);
                continue;
            }

            if (eregi("^:ANSWER([0-9]+):([0-9\.\-]+)",$line,$webct_options)) {
                $answertext="";                 // Start gathering next lines
                $currentchoice=$webct_options[1];
                $question->fraction[$currentchoice]=($webct_options[2]/100);
                continue;
            }

            if (eregi('^:FORMULA:(.*)', $line, $webct_options)) {
                // Answer for a CALCULATED question
                ++$currentchoice;
                $question->answers[$currentchoice] =
                        qformat_webct_convert_formula($webct_options[1]);

                // Default settings:
                $question->fraction[$currentchoice] = 1.0;
                $question->tolerance[$currentchoice] = 0.0;
                $question->tolerancetype[$currentchoice] = 2; // nominal (units in webct)
                $question->feedback[$currentchoice] = '';
                $question->correctanswerlength[$currentchoice] = 4;

                $datasetnames = $QTYPES[CALCULATED]->find_dataset_names($webct_options[1]);
                foreach ($datasetnames as $datasetname) {
                    $question->dataset[$datasetname] = new stdClass();
                    $question->dataset[$datasetname]->datasetitem = array();
                    $question->dataset[$datasetname]->name = $datasetname ; 
                    $question->dataset[$datasetname]->distribution = 'uniform'; 
                    $question->dataset[$datasetname]->status ='private';
                }
                continue;
            }

            if (eregi("^:L([0-9]+)",$line,$webct_options)) {
                $answertext="";                 // Start gathering next lines
                $currentchoice=$webct_options[1];
                $question->fraction[$currentchoice]=1; 
                continue;
            }

            if (eregi("^:R([0-9]+)",$line,$webct_options)) {
                $responsetext="";                // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }

            if (eregi("^:REASON([0-9]+):?",$line,$webct_options)) {
                $feedbacktext="";               // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }
            if (eregi("^:FEEDBACK([0-9]+):?",$line,$webct_options)) {
                $generalfeedbacktext="";               // Start gathering next lines
                $currentchoice=$webct_options[1];
                continue;
            }
            if (eregi('^:FEEDBACK:(.*)',$line,$webct_options)) {
                $generalfeedbacktext="";               // Start gathering next lines
                continue;
            }
            if (eregi('^:LAYOUT:(.*)',$line,$webct_options)) {
            //    ignore  since layout in question_multichoice  is no more used in moodle       
            //    $webct_options[1] contains either vertical or horizontal ;
                continue;
            }

            if (isset($question->qtype ) && CALCULATED == $question->qtype && eregi('^:ANS-DEC:([1-9][0-9]*)', $line, $webct_options)) {
                // We can but hope that this always appear before the ANSTYPE property
                $question->correctanswerlength[$currentchoice] = $webct_options[1];
                continue;
            }

            if (isset($question->qtype )&& CALCULATED == $question->qtype && eregi("^:TOL:($webctnumberregex)", $line, $webct_options)) {
                // We can but hope that this always appear before the TOL property
                $question->tolerance[$currentchoice] =
                        qformat_webct_convert_formula($webct_options[1]);
                continue;
            }

            if (isset($question->qtype )&& CALCULATED == $question->qtype && eregi('^:TOLTYPE:percent', $line)) {
                // Percentage case is handled as relative in Moodle:
                $question->tolerance[$currentchoice]  /= 100;
                $question->tolerancetype[$currentchoice] = 1; // Relative
                continue;
            }

            if (eregi('^:UNITS:(.+)', $line, $webct_options)
                    and $webctunits = trim($webct_options[1])) {
                // This is a guess - I really do not know how different webct units are separated...
                $webctunits = explode(':', $webctunits);
                $unitrec->multiplier = 1.0; // Webct does not seem to support this
                foreach ($webctunits as $webctunit) {
                    $unitrec->unit = trim($webctunit);
                    $question->units[] = $unitrec;
                }
                continue;
            }

            if (!empty($question->units) && eregi('^:UNITREQ:(.*)', $line, $webct_options)
                    && !$webct_options[1]) {
                // There are units but units are not required so add the no unit alternative
                // We can but hope that the UNITS property always appear before this property
                $unitrec->unit = '';
                $unitrec->multiplier = 1.0;
                $question->units[] = $unitrec;
                continue;
            }

            if (!empty($question->units) && eregi('^:UNITCASE:', $line)) {
                // This could be important but I was not able to figure out how
                // it works so I ignore it for now
                continue;
            }

            if (isset($question->qtype )&& CALCULATED == $question->qtype && eregi('^:ANSTYPE:dec', $line)) {
                $question->correctanswerformat[$currentchoice]='1';
                continue;
            }
            if (isset($question->qtype )&& CALCULATED == $question->qtype && eregi('^:ANSTYPE:sig', $line)) {
                $question->correctanswerformat[$currentchoice]='2';
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
