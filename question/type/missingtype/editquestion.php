<?php // $Id$

    if (!empty($options->answers)) {
        $answersraw = get_records_list("question_answers", "id", $options->answers);

    }

    $answers = array();
    if (!empty($answersraw)) {
        foreach ($answersraw as $answer) {
            $answers[] = $answer;   // insert answers into slots
        }
    }

    $yesnooptions = array();
    $yesnooptions[0] = get_string("no");
    $yesnooptions[1] = get_string("yes");

    print_heading_with_help(get_string("editingmissingtype", "quiz"), "multichoice", "quiz");
    require("$CFG->dirroot/question/type/missingtype/editquestion.html");

?>
