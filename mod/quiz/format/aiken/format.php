<?PHP  // $Id$ 

///
/// Written by Tom Robb <tom@robb.net> 2 November 2003
/// 

////////////////////////////////////////////////////////////////////////////
/// AIKEN FORMAT
///
/// This Moodle class provides all functions necessary to import and export 
/// one-correct-answer multiple choice questions in this format:
///
///  Question text
///  A) Choice #1
///  B) Choice #2
///  C) Choice #3
///  D) Choice #4
///  ANSWER: B
///    (blank line next not necessary since "AN" at the beginning of a line 
///     triggers the question input and causes input to start all over.
///
///Only ONE correct answer is allowed with no feedback responses.
///
///Be sure to reword "All of the above" type questions as "All of these" (etc.) so that choices can
///  be randomized
///
///This should work on WIN, Mac and Unix although only tested on Mac
///
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

class quiz_file_format extends quiz_default_format {

//will this override default function?
    function readquestions($lines){
        $questions = array();
        $endchar = chr(13); 
        foreach ($lines as $line) {
            $stp = strpos($line,$endchar,0);
            $newlines = explode($endchar,$line);
            $foundQ = 0;
            for ($i=0; $i < count($newlines);$i++){
                $nowline = addslashes($newlines[$i]);
                ///Go through the arrage and build an object called $question
                ///When done, add $question to $questions
                if (strlen($nowline)< 2) {
                    continue;
                }
                //                This will show everyline when file is being processed
                //                print("$nowline<br>");
                $leader = substr(ltrim($nowline),0,2);
                if (strpos(".A)B)C)D)E)F)G)H)I)J)A.B.C.D.E.F.G.H.I.J.",$leader)>0){
                    //trim off the label and space
                    $question->answer[] = substr($nowline,3);
                    $question->fraction[] = 0;
                    continue;
                }
                if ($leader == "AN"){
                    $ans =  trim(strstr($nowline,":"));
                    $ans = substr($ans,2,1);
                    //A becomes 0 since array starts from 0
                    $rightans = ord($ans) - 65;
                    $question->fraction[$rightans] = 1;
                    $questions[] = $question;
                    //clear array for next question set
                    $question = NULL;
                    continue;
                } else {
                    //Must be the first line since no leader
                    $question->qtype = MULTICHOICE;
                    $question->name = substr($nowline,0,50);
                    $question->questiontext = $nowline;
                    $question->single = 1;
                    $question->feedback[] = "";
                    $question->usecase = 0;  // Ignore case
                    $question->defaultgrade = 1; 
                    $question->image = "";   // No images with this format
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
