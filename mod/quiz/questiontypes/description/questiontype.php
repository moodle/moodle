<?PHP  // $Id$

///////////////////
/// DESCRIPTION ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

//
// The question type DESCRIPTION is not really a question type
// and it therefore often sticks to some kind of odd behaviour
//

class quiz_description_qtype extends quiz_default_questiontype {

    function name() {
        return 'description';
    }

    function save_question_options($question) {
        /// No options to be saved for this question type:
        return true;
    }

    function create_response($question, $nameprefix, $questionsinuse) {
        /// This question type does never have any responses,
        /// so do not return any...

        return array();
    }

    function print_question($currentnumber, $quiz, $question,
                            $readonly, $resultdetails) {
        echo '<p align="center">';
        echo format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);
        echo '</p>';
        return $currentnumber;        
    }

    function grade_response($question, $nameprefix) {
        $result->grade = 0.0;
        $result->answers = array();
        $result->correctanswers = array();
        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[DESCRIPTION]= new quiz_description_qtype();

?>
