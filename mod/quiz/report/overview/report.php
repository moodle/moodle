<?PHP  // $Id$

/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG;

        if (!$grades = quiz_get_grade_records($quiz)) {
            return;
        }

        $strname  = get_string("name");
        $strattempts  = get_string("attempts", "quiz");
        $strbestgrade  = get_string("bestgrade", "quiz");

        $table->head = array("&nbsp;", $strname, $strattempts, "$strbestgrade /$quiz->grade");
        $table->align = array("center", "left", "left", "center");
        $table->width = array(10, "*", "*", 20);

        foreach ($grades as $grade) {
            $picture = print_user_picture($grade->userid, $course->id, $grade->picture, false, true);
    
            if ($attempts = quiz_get_user_attempts($quiz->id, $grade->userid)) {
                $userattempts = quiz_get_user_attempts_string($quiz, $attempts, $grade->grade);
            }
    
            $table->data[] = array ($picture, 
                                    "<a href=\"$CFG->wwwroot/user/view.php?id=$grade->userid&course=$course->id\">".
                                    "$grade->firstname $grade->lastname</a>", 
                                    "$userattempts", round($grade->grade,0));
        }
    
        print_table($table);

        return true;
    }
}

?>
