<?php // $Id$

    if (!empty($question->id)) {
        $options = get_record("quiz_essay", "question", "$question->id");
    }

    if (!empty($options->answer)) {
        $essayfeedback   = get_record("question_answers", "id", $options->answer);
    } else {
        $essayfeedback->feedback = "";
    }

    print_heading_with_help(get_string("editingessay", "quiz"), "essay", "quiz");
    require("$CFG->dirroot/question/questiontypes/essay/essay.html");

?>
