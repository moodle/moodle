<?php // $Id$
    if (!empty($question->id)) {
        $options = get_record("quiz_randomsamatch", "question", $question->id);
    } else {
        $options->choose = "";
        $options->shuffleanswers = 1;
    }
    $saquestions = $QTYPES[RANDOMSAMATCH]->get_sa_candidates($category->id);
    $numberavailable = count($saquestions);
    unset($saquestions);

    $yesnooptions = array();
    $yesnooptions[0] = get_string("no");
    $yesnooptions[1] = get_string("yes");

    print_heading_with_help(get_string("editingrandomsamatch", "quiz"), "randomsamatch", "quiz");
    require("$CFG->dirroot/question/questiontypes/randomsamatch/randomsamatch.html");

?>
