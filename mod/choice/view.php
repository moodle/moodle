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

    if ($current = get_record_sql("SELECT * FROM choice_answers
                                     WHERE choice='$choice->id' AND user='$USER->id'")) {
        $answerchecked[$current->answer] = "CHECKED";
    }

    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted
        $form = (object)$HTTP_POST_VARS;
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
            $newanswer->user   = $USER->id;
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

    print_footer($course);


?>
