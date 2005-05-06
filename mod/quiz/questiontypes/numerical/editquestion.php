<?php // $Id$

    // Get all the extra information if we're editing
    if (!empty($question->id) && isset($question->qtype) &&
     $QUIZ_QTYPES[$question->qtype]->get_question_options($question)) {
        $answer = array_values($question->options->answers);
        usort($answer, create_function('$a, $b',
         'if ($a->fraction == $a->fraction) { return 0; }' .
         'else { return $a->fraction < $b->fraction ? -1 : 1; }'));
        $answer = $answer[0]; // Get the answer with the highest fraction (i.e. 1)
        $units  = array_values($question->options->units);
        usort($units, create_function('$a, $b', // make sure the default unit is at index 0
         'if (1.0 === (float)$a->multiplier) { return -1; } else '.
         'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
        $tolerance = $question->options->tolerance;
    } else {
        $answer   = new stdClass;
        $answer->answer   = '';
        $answer->feedback = '';
        $units     = array();
        $tolerance = '';
    }

    print_heading_with_help(get_string("editingnumerical", "quiz"), "numerical", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/numerical/numerical.html");

?>
