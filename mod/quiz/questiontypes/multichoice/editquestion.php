<?php // $Id$

    if (!empty($question->id)) {
        $options = get_record("quiz_multichoice", "question", $question->id);
    } else {
        $options->single = 1;
    }
    if (!empty($options->answers)) {
        $answersraw = get_records_list("quiz_answers", "id", $options->answers);

    }

    $answers = array();
    if (!empty($answersraw)) {
        foreach ($answersraw as $answer) {
            $answers[] = $answer;   // insert answers into slots
        }
    }

    $i = count($answers);
    $limit = QUIZ_MAX_NUMBER_ANSWERS;
    $limit = $limit <= $i ? $i+1 : $limit;
    for (; $i < $limit; $i++) {
        $answers[] = "";   // Make answer slots, default as blank
    }
    print_heading_with_help(get_string("editingmultichoice", "quiz"), "multichoice", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/multichoice/multichoice.html");

?>
