<?php // $Id$
    if (!empty($question->id)) {
        $options = get_record("question_randomsamatch", "question", $question->id);
    } else {
        $options->choose = "";
    }
    $saquestions = $QTYPES['randomsamatch']->get_sa_candidates($category->id);
    $numberavailable = count($saquestions);
    unset($saquestions);

    if (empty($question->name)) {
        $question->name =  get_string("randomsamatch", "quiz");
    }

    if (empty($question->questiontext)) {
        $question->questiontext =  get_string("randomsamatchintro", "quiz");
    }

    $yesnooptions = array();
    $yesnooptions[0] = get_string("no");
    $yesnooptions[1] = get_string("yes");

    print_heading_with_help(get_string("editingrandomsamatch", "quiz"), "randomsamatch", "quiz");
    require("$CFG->dirroot/question/type/randomsamatch/editquestion.html");

?>
