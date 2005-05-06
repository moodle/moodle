<?php // $Id$
    if (!empty($question->id)) {
        $options = get_record("quiz_randomsamatch", "question", $question->id);
    } else {
        $options->choose = "";
    }
    $saquestions = $QUIZ_QTYPES[RANDOMSAMATCH]->get_sa_candidates($category->id);
    $numberavailable = count($saquestions);
    unset($saquestions);
    print_heading_with_help(get_string("editingrandomsamatch", "quiz"), "randomsamatch", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/randomsamatch/randomsamatch.html");

?>
