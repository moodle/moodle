<?php // $Id$
    if (!empty($question->id)) {
        $options = get_record("question_shortanswer", "question", $question->id);
    } else {
        $options->usecase = 0;
    }
    if (!empty($options->answers)) {
        $answersraw = get_records_list("question_answers", "id", $options->answers, 'id ASC');
    }

    $answers = array();
    if (!empty($answersraw)) {
        foreach ($answersraw as $answer) {
            $answers[] = $answer;   // insert answers into slots
        }
    }

    $emptyanswer->answer = '';
    $i = count($answers);
    $limit = QUESTION_NUMANS;
    $limit = $limit <= $i ? $i+1 : $limit;
    for (; $i < $limit; $i++) {
        $answers[] = $emptyanswer;   // Make answer slots, default as blank
    }

    print_heading_with_help(get_string("editingshortanswer", "quiz"), "shortanswer", "quiz");
    require("$CFG->dirroot/question/type/shortanswer/editquestion.html");

?>
