<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }

    for ($i=1; $i <= $CHOICE_MAX_NUMBER; $i++) {
        $answerchecked[$i] = "";
    }
    if ($current = get_record("choice_answers", "choice", $choice->id, "userid", $USER->id)) {
        $answerchecked[$current->answer] = "CHECKED";
    }

    if ($form = data_submitted()) {
        $timenow = time();
        if ($current) {
            $newanswer = $current;
            $newanswer->answer = $form->answer;
            $newanswer->timemodified = $timenow;
            if (! update_record("choice_answers", $newanswer)) {
                error("Could not update your choice");
            }
            add_to_log($course->id, "choice", "update", "view.php?id=$cm->id", "$choice->id");
        } else {
            $newanswer->choice = $choice->id;
            $newanswer->userid = $USER->id;
            $newanswer->answer = $form->answer;
            $newanswer->timemodified = $timenow;
            if (! insert_record("choice_answers", $newanswer)) {
                error("Could not save your choice");
            }
            add_to_log($course->id, "choice", "add", "view.php?id=$cm->id", "$choice->id");
        }
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
        exit;
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", "$choice->id");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    print_header("$course->shortname: $choice->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strchoices</A> -> $choice->name", "", "", true,
                  update_module_button($cm->id, $course->id, $strchoice), navmenu($course, $cm));

    if (isteacher($course->id)) {
        if ( $allanswers = get_records("choice_answers", "choice", $choice->id)) {
            $responsecount = count($allanswers);
        } else {
            $responsecount = 0;
        }
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">".get_string("viewallresponses", "choice", $responsecount)."</A></P>";
    }

    print_simple_box( text_to_html($choice->text) , "center");

    if (!$current or !$choice->publish) {  // They haven't made their choice yet
        echo "<CENTER><P><FORM name=\"form\" method=\"post\" action=\"view.php\">";
        echo "<TABLE CELLPADDING=20 CELLSPACING=20><TR>";

        foreach ($choice->answer as $key => $answer) {
            if ($answer) {
                echo "<TD ALIGN=CENTER>";
                echo "<INPUT type=radio name=answer value=\"$key\" ".$answerchecked[$key].">";
                p($answer);
                echo "</TD>";
            }
        }
    
        echo "</TR></TABLE>";
        echo "<INPUT type=hidden name=id value=\"$cm->id\">";
        if (!isguest()) {
            echo "<INPUT type=submit value=\"".get_string("savemychoice","choice")."\">";
        }
        echo "</P></FORM></CENTER>";

    } else {  // Print results.

        print_heading(get_string("responses", "choice"));

        if (! $users = get_course_users($course->id, "u.firstname ASC")) {
            error("No users found (very strange)");
        }

        if ( $allanswers = get_records("choice_answers", "choice", $choice->id)) {
            foreach ($allanswers as $aa) {
                $answers[$aa->userid] = $aa;
            }
        } else {
            $answers = array () ;
        }

        $timenow = time();

        foreach ($choice->answer as $key => $answer) {  
            $useranswer[$key] = array();
        }
        foreach ($users as $user) {
            if (!empty($user->id) and !empty($answers[$user->id])) {
                $answer = $answers[$user->id];
                $useranswer[(int)$answer->answer][] = $user;
            } else {
                $answer = "";
                $useranswer[(int)$answer->answer][] = $user;
            }
        }
        foreach ($choice->answer as $key => $answer) {  
            if (!$choice->answer[$key]) {
                unset($useranswer[$key]);     // Throw away any data that doesn't apply
            }
        }
        ksort($useranswer);

        switch ($choice->publish) {
          case CHOICE_PUBLISH_NAMES:

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<TABLE CELLPADDING=5 CELLSPACING=10 ALIGN=CENTER>";
            echo "<TR>";
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<TH WIDTH=\"$tablewidth%\">";
                } else {
                    echo "<TH BGCOLOR=\"$THEME->body\" WIDTH=\"$tablewidth%\">";
                }
                echo choice_get_answer($choice, $key);
                echo "</TH>";
            }
            echo "</TR><TR>";
        
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<TD WIDTH=\"$tablewidth%\" VALIGN=TOP NOWRAP BGCOLOR=\"$THEME->cellcontent\">";
                } else {
                    echo "<TD WIDTH=\"$tablewidth%\" VALIGN=TOP NOWRAP BGCOLOR=\"$THEME->body\">";
                }
    
                echo "<TABLE WIDTH=100%>";
                foreach ($answer as $user) {
                    echo "<TR><TD WIDTH=10 NOWRAP>";
                    print_user_picture($user->id, $course->id, $user->picture);
                    echo "</TD><TD WIDTH=100% NOWRAP>";
                    echo "<P>$user->firstname $user->lastname</P>";
                    echo "</TD></TR>";
                }
                echo "</TABLE>";
        
                echo "</TD>";
            }
            echo "</TR></TABLE>";
            break;


          case CHOICE_PUBLISH_ANONYMOUS:
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<TABLE CELLPADDING=5 CELLSPACING=10 ALIGN=CENTER>";
            echo "<TR>";
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<TH WIDTH=\"$tablewidth%\">";
                } else {
                    echo "<TH BGCOLOR=\"$THEME->body\" WIDTH=\"$tablewidth%\">";
                }
                echo choice_get_answer($choice, $key);
                echo "</TH>";
            }
            echo "</TR>";

            $maxcolumn = 0;
            foreach ($useranswer as $key => $answer) {
                $column[$key] = count($answer);
                if ($column[$key] > $maxcolumn) {
                    $maxcolumn = $column[$key];
                }
            }

            echo "<TR>";
            foreach ($useranswer as $key => $answer) {
                $height = $COLUMN_HEIGHT * ((float)$column[$key] / (float)$maxcolumn);
                echo "<TD VALIGN=\"BOTTOM\" ALIGN=\"CENTER\">";
                echo "<IMG SRC=\"column.png\" HEIGHT=\"$height\" width=\"49\"></TD>";
            }
            echo "</TR>";

            echo "<TR>";
            foreach ($useranswer as $key => $answer) {
                echo "<TD ALIGN=\"CENTER\">".$column[$key]."</TD>";
            }
            echo "</TR></TABLE>";

            break;
        }
    }
    
    print_footer($course);


?>
