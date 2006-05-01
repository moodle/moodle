<?php // $Id$

    if (!empty($question->id)) {
        $options->answer = get_record("question_answers", "question", $question->id);
    } else {
        $options->answer->feedback = '';
    }

    print_heading_with_help(get_string("editingessay", "quiz"), "essay", "quiz");
    require("$CFG->dirroot/question/type/essay/editquestion.html");

?>
