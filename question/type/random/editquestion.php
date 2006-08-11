<?php // $Id$

    if (empty($question->name)) {
        $question->name = get_string("random", "quiz");
    }
    
    print_heading_with_help(get_string("editingrandom", "quiz"), "random", "quiz");
    require("$CFG->dirroot/question/type/random/editquestion.html");
?>
