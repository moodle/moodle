<?php // $Id$
    if (!empty($question->id)) {
        $options = get_record("quiz_shortanswer", "question", $question->id);
    } else {
        $options->usecase = 0;
    }
    if (!empty($options->answers)) {
        $answersraw = get_records_list("quiz_answers", "id", $options->answers);
    }
    for ($i=0; $i<quiz_MAX_NUMBER_ANSWERS; $i++) {
        $answers[] = "";   // Make answer slots, default as blank
    }
    if (!empty($answersraw)) {
        $i=0;
        foreach ($answersraw as $answer) {
            $answers[$i] = $answer;   // insert answers into slots
            $i++;
        }
    }
    print_heading_with_help(get_string("editingshortanswer", "quiz"), "shortanswer", "quiz");
    require("shortanswer.html");

?>
