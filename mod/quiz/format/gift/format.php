<?PHP  // gift2
// version 1.0 BETA 2

////////////////////////////////////////////////////////////////////////////
/// GIFT: General Import Format Template
/// 
/// The GIFT format is a quick, easy to use method for teachers 
/// writing questions as a text file. It supports true-false, 
/// short answer and multiple-choice questions, as well as insertion 
/// of a blank line for the missing word format. Below are examples 
/// of the following question types: multiple choice, missing word, 
/// true-false and short-answer.
/// 
///     Who's buried in Grant's tomb?{~Grant ~Jefferson =no one}     
///     Grant is {~buried =entombed ~living} in Grant's tomb.
///     Grant is buried in Grant's tomb.{FALSE}
///     Who's buried in Grant's tomb?{=no one =nobody}
///
/// Optional question names are enclosed in double colon(::). 
/// Answer feedback is indicated with hash mark (#).
/// Percentage answer weights immediately follow the tilde (for multiple
/// choice) or equal sign (for short answer), and are enclosed
/// in percent signs (% %).
/// 
///     ::Grant's Tomb::Grant is {
///     ~buried#No one is buried there.
///     =entombed#Right answer!
///     ~living#We hope not!
///     } in Grant's tomb.
/// 
///     Difficult question.{~wrong answer#comment on wrong answer 
///     ~%50%half credit answer =full credit answer#well done!}
///     
///     ::Jesus' hometown:: Jesus Christ was from {
///     =Nazareth#Yes! That's right!
///     =%75%Nazereth#Right, but misspelled.
///     =%25%Bethlehem#He was born here, but not raised here.}
/// 
/// This filter was written through the collaboration of numerous 
/// members of the Moodle community. It was originally based on 
/// the missingword format. In July 2003, Thomas Robb wrote the 
/// original code for the percentage answer weight parser and comment 
/// insertion. Paul Tsuchido Shew rewrote the filter in December 2003 
/// incorporating community suggestions for a more robust question format, 
/// and adding the question name parser, additional question types
/// and other features.
//////////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

class quiz_file_format extends quiz_default_format {

	function answerweightparser(&$answer) {
		$answer = substr($answer, 1); 							// removes initial %
		$end_position  = strpos($answer, "%");
		$answer_weight = substr($answer, 0, $end_position);		// gets weight as integer
		$answer_weight = $answer_weight/100;					// converts to percent
		$answer = substr($answer, $end_position+1);				// cleans up answer
		///	To enable multiple answers (if fractional answer weights are assigned) 
		///	uncomment the following three lines.
		/// if ($answer_weight > 0 and $answer_weight <> 1){
		/// 	$question->single = 0; // ok many good answers
		/// }
		return $answer_weight;
	}


	function commentparser(&$answer) {
		//Answer Comment parser
		if (strpos($answer,"#") > 0){
			$hashpos = strpos($answer,"#");
			$comment = addslashes(substr($answer, $hashpos+1));
			$answer  = substr($answer, 0, $hashpos);
		} else {
			$comment = " ";
		}
		return $comment;
	}
	

    function readquestion($lines) {
    /// Given an array of lines known to define a question in this format, this function
    /// converts it into a question object suitable for processing and insertion into Moodle.

        $question = NULL;
        $comment = NULL;
		$answer_weight_regex = "^%\-*([0-9]{1,2})\.?([0-9]*)%";
        $text = implode(" ", $lines);

        /// QUESTION NAME parser
		$text = trim($text);
		if (substr($text, 0, 2) == "::") {
			$text = substr($text, 2);

			$namefinish = strpos($text, "::");
    	    if ($namefinish === false) {
    	        $question->name = false;
				// name will be assigned after processing question text below
	         } else {
	        	$question->name = addslashes(trim(substr($text, 0, $namefinish)));
				$text = substr($text, $namefinish+2); // Remove name from text
	        }
		} else {
			$question->name = false;
		}


        /// FIND ANSWER section
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
        $answertext = trim(substr($text, $answerstart + 1, $answerlength - 1));

        /// SAVE QUESTION TEXT without answer, inserting "_____" as necessary
		if (substr($text, -1) == "}") {
			/// no blank line if answers follow question, outside of closing punctuation
			$question->questiontext = addslashes(substr_replace($text, "", $answerstart, $answerlength+1));
		} else {
			/// inserts blank line for missing word format
			$question->questiontext = addslashes(substr_replace($text, "_____", $answerstart, $answerlength+1));
		}

		/// set question name if not already set
		if ($question->name === false) {
			$question->name = $question->questiontext;
			}


     	/// ANSWERS
		// Only Multiple-choice questions contain tilde ~
		if (strstr($answertext, "~") !== FALSE) {  // tilde conditional

			///MULTIPLE CHOICE
			$answertext = str_replace("=", "~=", $answertext);
			$answers = explode("~", $answertext);
			if (isset($answers[0])) {
				$answers[0] = trim($answers[0]);
			}
			if (empty($answers[0])) {
				array_shift($answers);
			}

			$countanswers = count($answers);
			if ($countanswers < 2) {  // MC $countanswers conditional
                if ($this->displayerrors) {
                   	echo "<P>Found tilde, but " . $countanswers . " answers in $answertext";
                }
                return false;
			} else {	// MC $countanswers conditional

				$question->qtype = MULTICHOICE;
				$question->single = 1;   // Only one answer allowed by default
	
				foreach ($answers as $key => $answer) {
					$answer = trim($answer);
	
					// determine answer weight
					if ($answer[0] == "=") {
						$answer_weight = 1;
						$answer = substr($answer, 1);
	
					} elseif (ereg($answer_weight_regex, $answer)) {	// check for properly formatted answer weight
						$answer_weight = $this->answerweightparser($answer);
					
					} else { 	//default, i.e., wrong anwer
						$answer_weight = 0;
					}
					$question->fraction[$key] = $answer_weight;
	
					$comment = $this->commentparser($answer); // commentparser also cleans up $answer
					$question->feedback[$key] = $comment;
	
					$question->answer[$key]   = addslashes($answer);	
				}  // end foreach answer
	
				$question->defaultgrade = 1;
				$question->image = "";   // No images with this format
				return $question;
			} // end MC $countanswers conditional

		/// Otherwise, begin parsing other question-types
		} else {  // tilde conditional

			/// TRUEFALSE Question
			$TF_check = $answertext;
			if (strpos($answertext,"#") > 0){ 
				// strip comments to check for TrueFalse question
				$TF_check = trim(substr($answertext, 0, strpos($answertext,"#")));
			}
			
			if (($TF_check == "T")				// TrueFalse/ShortAnswer QuestionType conditional
			OR  ($TF_check == "TRUE")
			OR  ($TF_check == "F")
			OR  ($TF_check == "FALSE")) {
				$answer = $answertext;
				$question->qtype = TRUEFALSE;
				$comment = $this->commentparser($answer); // commentparser also cleans up $answer

				if ($answer == "T" OR $answer == "TRUE") {
					$question->answer = 1;
					$question->feedbackfalse = $comment; //feedback if answer is wrong
				} else {
					$question->answer = 0;
					$question->feedbacktrue = $comment; //feedback if answer is wrong
				}


			} else {							// TrueFalse/ShortAnswer QuestionType conditional
	
				/// SHORTANSWER Question
				$answers = explode("=", $answertext);
				if (isset($answers[0])) {
					$answers[0] = trim($answers[0]);
				}
				if (empty($answers[0])) {
					array_shift($answers);
				}
	
				if (count($answers) == 0) {
					/// invalid question
					if ($this->displayerrors) {
						echo "<P>Found equals=, but no answers in $answertext";
					}
					return false;
				} else {
	
					$question->qtype = SHORTANSWER;
					$question->usecase = 0;  // Ignore case
	
					foreach ($answers as $key => $answer) {
						$answer = trim($answer);
	
						// Answer Weight
						if (ereg($answer_weight_regex, $answer)) {	// check for properly formatted answer weight
							$answer_weight = $this->answerweightparser($answer);
						} else { 	//default, i.e., full-credit anwer
							$answer_weight = 1;
						}
						$question->fraction[$key] = $answer_weight;
	
						$comment = $this->commentparser($answer); //commentparser also cleans up $answer
						$question->feedback[$key] = $comment;
	
						$question->answer[$key]   = addslashes($answer);
					} 	// end foreach
				}     	// end ount($answers) conditional
				
			}			// end TrueFalse/ShortAnswer QuestionType conditional

			$question->defaultgrade = 1;
			$question->image = "";   // No images with this format
			return $question;

        }	// end tilde conditional

    }		// end function readquestion($lines)
}

?>

