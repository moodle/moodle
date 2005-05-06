<?php // $Id$

    require_once($CFG->dirroot . '/mod/quiz/questiontypes/rqp/lib.php');

    $types = quiz_rqp_get_types();
    print_heading_with_help(get_string('editingrqp', 'quiz'), 'rqp', 'quiz');
    if (empty($question->id)) {
        $question->options->type = '';
        $question->options->source = '';
        $question->options->format = '';
    }
    else if (!$QUIZ_QTYPES[$question->qtype]->get_question_options($question)) {
        $question->options->type = '';
        $question->options->source = '';
        $question->options->format = '';
        echo "<p align=\"center\">Error! Could not load the options for this question!</p>\n";
    }
    require('rqp.html');

?>
