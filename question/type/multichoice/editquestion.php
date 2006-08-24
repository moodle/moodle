<?php // $Id$

    if (!empty($question->id)) {
        $options = get_record("question_multichoice", "question", $question->id);
    } else {
        $options->single = 1;
        $options->shuffleanswers = 1;
        $options->correctfeedback = '';
        $options->partiallycorrectfeedback = '';
        $options->incorrectfeedback = '';
    }
    if (!empty($options->answers)) {
        $answersraw = get_records_list("question_answers", "id", $options->answers);

    }

    $answers = array();
    if (!empty($answersraw)) {
        foreach ($answersraw as $answer) {
            $answers[] = $answer;   // insert answers into slots
        }
    }

    $i = count($answers);
    $limit = QUESTION_NUMANS;
    $limit = $limit <= $i ? $i+1 : $limit;
    for (; $i < $limit; $i++) {
        $answers[] = "";   // Make answer slots, default as blank
    }

    $yesnooptions = array();
    $yesnooptions[0] = get_string("no");
    $yesnooptions[1] = get_string("yes");

    print_heading_with_help(get_string("editingmultichoice", "qtype_multichoice"), "multichoice", "quiz");
    require("$CFG->dirroot/question/type/multichoice/editquestion.html");

?>
