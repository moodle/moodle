<?php // $Id$

    if (!empty($question->id)) {
        $options = get_record("quiz_match", "question", $question->id);
        if (!empty($options->subquestions)) {
            $oldsubquestions = get_records_list("quiz_match_sub", "id", $options->subquestions);
        }
    }
    if (empty($subquestions) and empty($subanswers)) {
        $subquestions = array();
        $subanswers   = array();
        if (!empty($oldsubquestions)) {
            foreach ($oldsubquestions as $oldsubquestion) {
                $subquestions[] = $oldsubquestion->questiontext;   // insert questions into slots
                $subanswers[] = $oldsubquestion->answertext;       // insert answers into slots
            }
        }

        $i = count($subquestions);
        $limit = QUIZ_MAX_NUMBER_ANSWERS;
        $limit = $limit <= $i ? $i+1 : $limit;
        for (; $i < $limit; $i++) {
            $subquestions[] = "";   // Make question slots, default as blank
            $subanswers[] = "";     // Make answer slots, default as blank
        }
    }
    print_heading_with_help(get_string("editingmatch", "quiz"), "match", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/match/match.html");

?>
