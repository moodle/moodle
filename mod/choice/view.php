<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

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
        $answerchecked[$i] = '';
    }
    if ($current = get_record("choice_answers", "choice", $choice->id, "userid", $USER->id)) {
        $answerchecked[$current->answer] = 'CHECKED';
    }

    if ($form = data_submitted()) {
        $timenow = time();

        if (empty($form->answer)) {
            redirect("view.php?id=$cm->id", get_string('mustchooseone', 'choice'));

        } else {
            if ($current) {
                $newanswer = $current;
                $newanswer->answer = $form->answer;
                $newanswer->timemodified = $timenow;
                if (! update_record("choice_answers", $newanswer)) {
                    error("Could not update your choice");
                }
                add_to_log($course->id, "choice", "choose again", "view.php?id=$cm->id", $choice->id, $cm->id);
            } else {
                $newanswer->choice = $choice->id;
                $newanswer->userid = $USER->id;
                $newanswer->answer = $form->answer;
                $newanswer->timemodified = $timenow;
                if (! insert_record("choice_answers", $newanswer)) {
                    error("Could not save your choice");
                }
                add_to_log($course->id, "choice", "choose", "view.php?id=$cm->id", $choice->id, $cm->id);
            }
        }
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
        exit;
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", $choice->id, $cm->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    } else {
        $navigation = "";
    }
    print_header("$course->shortname: $choice->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strchoices</A> -> $choice->name", "", "", true,
                  update_module_button($cm->id, $course->id, $strchoice), navmenu($course, $cm));

/// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if (isteacher($course->id)) {
        if ( $allanswers = get_records("choice_answers", "choice", $choice->id)) {
            $responsecount = count($allanswers);
        } else {
            $responsecount = 0;
        }
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">".get_string("viewallresponses", "choice", $responsecount)."</A></P>";
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
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

        if ($currentgroup) {
            $users = get_group_users($currentgroup, "u.firstname ASC");
        } else {
            $users = get_course_users($course->id, "u.firstname ASC");
        }

        if (!$users) {
            print_heading(get_string("nousersyet"));
            print_footer($course);
            exit;
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
                $useranswer[0][] = $user;
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

            $isteacher = isteacher($course->id);

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=5 cellspacing=10 align=center>";
            echo "<tr>";
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<th width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th bgcolor=\"$THEME->body\" width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo choice_get_answer($choice, $key);
                echo "</th>";
            }
            echo "</tr><tr>";
        
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<td width=\"$tablewidth%\" valign=top nowrap bgcolor=\"$THEME->cellcontent\">";
                } else if ($choice->showunanswered) {
                    echo "<td width=\"$tablewidth%\" valign=top nowrap bgcolor=\"$THEME->body\">";
                } else {
                    continue;
                }
    
                echo "<table width=100%>";
                foreach ($answer as $user) {
                    echo "<tr><td width=10 nowrap>";
                    print_user_picture($user->id, $course->id, $user->picture);
                    echo "</td><td width=100% nowrap>";
                    echo "<p>".fullname($user, $isteacher)."</p>";
                    echo "</td></tr>";
                }
                echo "</table>";
        
                echo "</td>";
            }
            echo "</tr></table>";
            break;


          case CHOICE_PUBLISH_ANONYMOUS:
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=5 cellspacing=10 align=center>";
            echo "<tr>";
            foreach ($useranswer as $key => $answer) {
                if ($key) {
                    echo "<th width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th bgcolor=\"$THEME->body\" width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo choice_get_answer($choice, $key);
                echo "</th>";
            }
            echo "</tr>";

            $maxcolumn = 0;
            foreach ($useranswer as $key => $answer) {
                if (!$key and !$choice->showunanswered) {
                    continue;
                }
                $column[$key] = count($answer);
                if ($column[$key] > $maxcolumn) {
                    $maxcolumn = $column[$key];
                }
            }

            echo "<tr>";
            foreach ($useranswer as $key => $answer) {
                if (!$key and !$choice->showunanswered) {
                    continue;
                }
                $height = 0;
                if ($maxcolumn) {
                    $height = $COLUMN_HEIGHT * ((float)$column[$key] / (float)$maxcolumn);
                }
                echo "<td valign=\"bottom\" align=\"center\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\">";
                echo "</td>";
            }
            echo "</tr>";

            echo "<tr>";
            foreach ($useranswer as $key => $answer) {
                if (!$key and !$choice->showunanswered) {
                    continue;
                }
                echo "<td align=\"center\">".$column[$key]."</td>";
            }
            echo "</tr></table>";

            break;
        }
    }
    
    print_footer($course);


?>
