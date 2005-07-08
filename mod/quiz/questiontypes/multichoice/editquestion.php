<?php // $Id$

    if (!empty($question->id)) {
        $options = get_record("quiz_multichoice", "question", $question->id);
    } else {
        $options->single = 1;
    }
    if (!empty($options->answers)) {
        $answersraw = get_records("quiz_answers", "question", $question->id,
         'seq_number ASC');
    }
    for ($i=0 ; $i < QUIZ_MAX_NUMBER_ANSWERS ; $i++) {
        $answers[] = "";   // Make answer slots, default as blank
    }
    if (!empty($answersraw)) {
        $i=0;
        foreach ($answersraw as $answer) {
            $answers[$i] = $answer;   // insert answers into slots
            $i++;
        }
    }
    print_heading_with_help(get_string("editingmultichoice", "quiz"), "multichoice", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/multichoice/multichoice.html");

?>
