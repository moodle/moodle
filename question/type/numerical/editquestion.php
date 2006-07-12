<?php // $Id$

    // Get all the extra information if we're editing
    if (!empty($question->id) && isset($question->qtype) &&
            $QTYPES[$question->qtype]->get_question_options($question)) {

        $answers = array_values($question->options->answers);
        $units  = array_values($question->options->units);
        usort($units, create_function('$a, $b', // make sure the default unit is at index 0
                'if (1.0 === (float)$a->multiplier) { return -1; } else '.
                'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
    } else {
        $answers = array();
        $units     = array();
    }

    // Add blank answers to make the number up to QUESTION_NUMANS
    // or one more than current, if there are already lots.
    $emptyanswer = new stdClass;
    $emptyanswer->answer = '';
    $i = count($answers);
    $limit = QUESTION_NUMANS;
    $limit = $limit <= $i ? $i+1 : $limit;
    for (; $i < $limit; $i++) {
        $answers[] = $emptyanswer;
    }

    print_heading_with_help(get_string("editingnumerical", "quiz"), "numerical", "quiz");
    require("$CFG->dirroot/question/type/numerical/editquestion.html");
?>
