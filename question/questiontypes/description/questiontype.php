<?php  // $Id$

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

    function get_question_options(&$question) {
        // No options to be restored for this question type
        return true;
    }

    function save_question_options($question) {
        /// No options to be saved for this question type:
        return true;
    }

    function print_question(&$question, &$state, $number, $cmoptions, $options) {
        global $CFG;

        // For editing teachers print a link to an editing popup window
        $editlink = '';
        if (isteacheredit($cmoptions->course)) {
            $stredit = get_string('edit');
            $linktext = '<img src="'.$CFG->pixpath.'/t/edit.gif" border="0" alt="'.$stredit.'" />';
            $editlink = link_to_popup_window('/question/question.php?id='.$question->id, $stredit, $linktext, 450, 550, $stredit, '', true);
        }

        $formatoptions->noclean = true;
        $formatoptions->para = false;

        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
        $image = get_question_image($question, $cmoptions->course);

        include "$CFG->dirroot/question/questiontypes/description/question.html";
    }

    function actual_number_of_questions($question) {
        /// Used for the feature number-of-questions-per-page
        /// to determine the actual number of questions wrapped
        /// by this question.
        /// The question type description is not even a question
        /// in itself so it will return ZERO!
        return 0;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $state->raw_grade = 0;
        $state->penalty = 0;
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
// define("DESCRIPTION",   "7"); // already defined in questionlib.php
$QTYPES[DESCRIPTION]= new quiz_description_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU[DESCRIPTION] = get_string("description", "quiz");

?>
