<?PHP // $Id$

    if (!empty($question->id)) {
        $options = get_record("quiz_match", "question", $question->id);
        if (!empty($options->subquestions)) {
            $oldsubquestions = get_records_list("quiz_match_sub", "id", $options->subquestions);
        }
    }
    if (empty($subquestions) and empty($subanswers)) {
        for ($i=0; $i<QUIZ_MAX_NUMBER_ANSWERS; $i++) {
            $subquestions[] = "";   // Make question slots, default as blank
            $subanswers[] = "";     // Make answer slots, default as blank
        }
        if (!empty($oldsubquestions)) {
            $i=0;
            foreach ($oldsubquestions as $oldsubquestion) {
                $subquestions[$i] = $oldsubquestion->questiontext;   // insert questions into slots
                $subanswers[$i] = $oldsubquestion->answertext;       // insert answers into slots
                $i++;
            }
        }
    }
    print_heading_with_help(get_string("editingmatch", "quiz"), "match", "quiz");
    require("match.html");

?>
