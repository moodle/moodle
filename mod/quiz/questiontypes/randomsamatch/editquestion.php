<?PHP // $Id$
            if (!empty($question->id)) {
                $options = get_record("quiz_randomsamatch", "question", $question->id);
            } else {
                $options->choose = "";
            }
            $numberavailable = count_records("quiz_questions", "category", $category->id, "qtype", SHORTANSWER);
            print_heading_with_help(get_string("editingrandomsamatch", "quiz"), "randomsamatch", "quiz");
            require("randomsamatch.html");

?>
