<?php  // $Id$ 

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
////////////////////////////////////////////////////////////////////////////

class qformat_aiken extends qformat_default {

  function provide_import() {
    return true;
  }

    function readquestions($lines){
        $questions = array();
        $question = $this->defaultquestion();
        $endchar = chr(13); 
        foreach ($lines as $line) {
            $stp = strpos($line,$endchar,0);
            $newlines = explode($endchar,$line);
            $foundQ = 0;
            for ($i=0; $i < count($newlines);$i++){
                $nowline = addslashes(trim($newlines[$i]));
                ///Go through the array and build an object called $question
                ///When done, add $question to $questions
                if (strlen($nowline) < 2) {
                    continue;
                }
                //                This will show everyline when file is being processed
                //                print("$nowline<br />");
                $leader = substr($nowline,0,2);
                if (preg_match('/[A-Z][).]/',$leader)){
                    //trim off the label and space
                    $question->answer[] = htmlspecialchars(trim(substr($nowline,2)), ENT_NOQUOTES);
                    $question->fraction[] = 0;
                    $question->feedback[] = '';
                    continue;
                }
                if ($leader == "AN"){
                    $ans = trim(substr($nowline,strpos($nowline,':') + 1));
                    $ans = substr($ans,0,1);
                    //A becomes 0 since array starts from 0
                    $rightans = ord($ans) - ord('A');
                    $question->fraction[$rightans] = 1;
                    $questions[] = $question;
                    //clear array for next question set
                    $question = $this->defaultquestion();
                    continue;
                } else {
                    //Must be the first line since no leader
                    $question->qtype = MULTICHOICE;
                    $question->name = htmlspecialchars(substr($nowline,0,50));
                    $question->questiontext = htmlspecialchars($nowline);
                    $question->single = 1;
                    $question->feedback[] = "";
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
