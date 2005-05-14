<?php // $Id$

    require_once($CFG->dirroot . '/mod/quiz/questiontypes/rqp/lib.php');

    if (empty($question->id)) {
        if (!isset($typeid)) {
            error('No remote question type specified');
        }
        $question->options->type = $typeid;
        $question->options->source = '';
        $question->options->format = '';
    }
    else if (!$QUIZ_QTYPES[$question->qtype]->get_question_options($question)) {
        error("Could not load the options for this question");
    }

    if (!$type = get_record('quiz_rqp_types', 'id', $question->options->type = $typeid)) {
        error("Invalid remote question type");
    }

    print_heading_with_help(get_string('editingrqp', 'quiz', $type->name), 'rqp', 'quiz');
    require('rqp.html');

?>
