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
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->
                  <A HREF=\"index.php?id=$course->id\">$strchoices</A> ->
                  <A HREF=\"view.php?id=$cm->id\">$choice->name</A> -> $strresponses", "");


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

    print_footer($course);

 
?>

