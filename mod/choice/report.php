<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course module

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course module is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    $strresponses = get_string("responses", "choice");

    add_to_log($course->id, "choice", "report", "report.php?id=$cm->id", "$choice->id");

    print_header("$course->shortname: $choice->name: $strresponses", "$course->fullname",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"index.php?id=$course->id\">$strchoices</a> ->
                  <a href=\"view.php?id=$cm->id\">$choice->name</a> -> $strresponses", "");

/// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

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
        if (!empty($answers[$user->id])) {
            $answer = $answers[$user->id];
        } else {
            $answer->answer = 0;
        }
        $useranswer[(int)$answer->answer][] = $user;
    }
    foreach ($choice->answer as $key => $answer) {  
        if (!$choice->answer[$key]) {
            unset($useranswer[$key]);     // Throw away any data that doesn't apply
        }
    }
    ksort($useranswer);

    $tablewidth = (int) (100.0 / count($useranswer));

    echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\">";
    echo "<tr>";
    foreach ($useranswer as $key => $answer) {
        if ($key) {
            echo "<th width=\"$tablewidth%\">";
        } else {
            echo "<th bgcolor=\"$THEME->body\" width=\"$tablewidth%\">";
        }
        echo choice_get_answer($choice, $key);
        echo "</th>";
    }
    echo "</tr><tr>";

    foreach ($useranswer as $key => $answer) {
        if ($key) {
            echo "<td width=\"$tablewidth%\" valign=top nowrap bgcolor=\"$THEME->cellcontent\">";
        } else {
            echo "<td width=\"$tablewidth%\" valign=top nowrap bgcolor=\"$THEME->body\">";
        }

        echo "<table width=\"100%\">";
        foreach ($answer as $user) {
            echo "<tr><td width=\"10\" nowrap>";
            print_user_picture($user->id, $course->id, $user->picture);
            echo "</td><td width=\"100%\" nowrap>";
            echo "<p>".fullname($user, true)."</p>";
            echo "</td></tr>";
        }
        echo "</table>";

        echo "</td>";
    }
    echo "</tr></table>";

    print_footer($course);

 
?>

