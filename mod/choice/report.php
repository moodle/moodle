<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

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

    if (! $choice = get_record("choice", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    $strresponses = get_string("responses", "choice");

    add_to_log($course->id, "choice", "report", "report.php?id=$cm->id", "$choice->id");

    print_header("$course->shortname: $choice->name: $strresponses", "$course->fullname",
                 "<A HREF=/course/view.php?id=$course->id>$course->shortname</A> ->
                  <A HREF=index.php?id=$course->id>$strchoices</A> ->
                  <A HREF=view.php?id=$cm->id>$choice->name</A> -> $strresponses", "");


    if (! $participants = get_records_sql("SELECT u.* FROM user u, user_students s, user_teachers t
                                       WHERE (s.course = '$course->id' AND s.user = u.id) 
                                          OR (t.course = '$course->id' AND t.user = u.id)
                                       ORDER BY u.lastaccess DESC")) {

        notify("No participants (strange)", "/course/view.php?id=$course->id");
        die;
    }

    if ( $allanswers = get_records_sql("SELECT * FROM choice_answers WHERE choice='$choice->id'")) {
        foreach ($allanswers as $aa) {
            $answers[$aa->user] = $aa;
        }
        
    } else {
        $answers = array () ;
    }
    


    $timenow = time();

    echo "<TABLE BORDER=1 CELLSPACING=0 valign=top align=center cellpadding=10>";
    foreach ($participants as $user) {
        $answer = $answers[$user->id];

        echo "<TR>";

        echo "<TD BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
        print_user_picture($user->id, $course->id, $user->picture);
        echo "</TD>";

        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\">$user->firstname $user->lastname</TD>";
        echo "<TD><P>&nbsp;";
        if ($answer->timemodified) {
            echo userdate($answer->timemodified);
        } 
        
        echo "</P> </TD>";

        echo "<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\"><P>";
        echo choice_get_answer($choice, $answer->answer);
        echo "</P></TD></TR>";
    }
    echo "</TABLE>";

    print_footer($course);

 
?>

