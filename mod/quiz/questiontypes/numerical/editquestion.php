<?PHP // $Id$

            // This will only support one answer of the type NUMERICAL
            // However, lib.php has support for multiple answers
            if (!empty($question->id)) {
                $answersraw= quiz_get_answers($question);
            }
            $answers= array();
            for ($i=0; $i<6; $i++) {
                $answers[$i]->answer   = ""; // Make answer slots, default as blank...
                $answers[$i]->min      = "";
                $answers[$i]->max      = "";
                $answers[$i]->feedback = "";
            }
            if (!empty($answersraw)) {
                $i=0;
                foreach ($answersraw as $answer) {
                    $answers[$i] = $answer;
                    $i++;
                }
            }
            print_heading_with_help(get_string("editingnumerical", "quiz"), "numerical", "quiz");
            require("numerical.html");

?>
