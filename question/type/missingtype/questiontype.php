<?php  // $Id$

///////////////////
/// missingtype ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

/**
 * Missing question type class
 *
 * When a question is encountered with a type that is not installed then its
 * type is changed to 'missingtype'. This questiontype just makes sure that the
 * necessary information is printed about the question.
 * @package questionbank
 * @subpackage questiontypes
 */
class question_missingtype_qtype extends default_questiontype {

    function name() {
        return 'missingtype';
    }

    function menu_name() {
        return false;
    }

    function is_usable_by_random() {
        return false;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $answers = &$question->options->answers;

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;

        // Print formulation
        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
        $image = get_question_image($question);

        // Print each answer in a separate row if there are any
        $anss = array();
        if ($answers) {
            foreach ($answers as $answer) {
                $a = new stdClass;
                $a->text = format_text("$answer->answer", FORMAT_MOODLE, $formatoptions, $cmoptions->course);

                $anss[] = clone($a);
            }
        }
        include("$CFG->dirroot/question/type/missingtype/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        return true;
    }

    function display_question_editing_page(&$mform, $question, $wizardnow){

        print_heading(get_string('warningmissingtype', 'quiz'));

        $mform->display();

    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_missingtype_qtype());

?>
