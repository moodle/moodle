<?php  // $Id$ 

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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

/**
 * Aiken format - a simple format for creating multiple choice questions (with
 * only one correct choice, and no feedback).
 *
 * The format looks like this:
 *
 * Question text
 * A) Choice #1
 * B) Choice #2
 * C) Choice #3
 * D) Choice #4
 * ANSWER: B
 *
 * That is,
 *  + question text all one one line.
 *  + then a number of choices, one to a line. Each line must comprise a letter,
 *    then ')' or '.', then a space, then the choice text.
 *  + Then a line of the form 'ANSWER: X' to indicate the correct answer.
 *
 * Be sure to word "All of the above" type choices like "All of these" in
 * case choices are being shuffled.
 */
class qformat_aiken extends qformat_default {

  function provide_import() {
    return true;
  }

    function readquestions($lines) {
        $questions = array();
        $question = $this->defaultquestion();
        $endchar = chr(13); 
        foreach ($lines as $line) {
            $stp = strpos($line, $endchar, 0);
            $newlines = explode($endchar, $line);
            $foundQ = 0;
            $linescount = count($newlines);
            for ($i=0; $i < $linescount; $i++) {
                $nowline = addslashes(trim($newlines[$i]));
                // Go through the array and build an object called $question
                // When done, add $question to $questions
                if (strlen($nowline) < 2) {
                    continue;
                }
                if (preg_match('/^[A-Z][).][ \t]/', $nowline)) {
                    // A choice. Trim off the label and space, then save
                    $question->answer[] = htmlspecialchars(trim(substr($nowline, 2)), ENT_NOQUOTES);
                    $question->fraction[] = 0;
                    $question->feedback[] = '';
                    continue;
                }
                if (preg_match('/^ANSWER:/', $nowline)) {
                    // The line that indicates the correct answer. This question is finised.
                    $ans = trim(substr($nowline, strpos($nowline, ':') + 1));
                    $ans = substr($ans, 0, 1);
                    // We want to map A to 0, B to 1, etc.
                    $rightans = ord($ans) - ord('A');
                    $question->fraction[$rightans] = 1;
                    $questions[] = $question;

                    // Clear array for next question set
                    $question = $this->defaultquestion();
                    continue;
                } else {
                    // Must be the first line of a new question, since no recognised prefix.
                    $question->qtype = MULTICHOICE;
                    $question->name = htmlspecialchars(substr($nowline, 0, 50));
                    $question->questiontext = htmlspecialchars($nowline);
                    $question->single = 1;
                    $question->feedback[] = '';
                }
            }
        }
        return $questions;
    }

    function readquestion($lines) {
        //this is no longer needed but might still be called by default.php
        return;
    }
}

?>
