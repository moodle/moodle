<?PHP // $Id$

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

    if (! $journal = get_record("journal", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    // make some easy ways to access the entries.
    if ( $eee = get_records_sql("SELECT * FROM journal_entries WHERE journal='$journal->id'")) {
        foreach ($eee as $ee) {
            $entrybystudent[$ee->user] = $ee;
            $entrybyentry[$ee->id]     = $ee;
        }
        
    } else {
        $entrybystudent = array () ;
        $entrybyentry   = array () ;
    }

    print_header("$course->shortname: Journals", "$course->fullname",
                 "<A HREF=/course/view.php?id=$course->id>$course->shortname</A> ->
                  <A HREF=index.php?id=$course->id>Journals</A> ->
                  <A HREF=view.php?id=$cm->id>$journal->name</A> -> Responses", "",
                  "", true);

    if (match_referer() && isset($HTTP_POST_VARS)) { // Feedback submitted
       
        $feedback = array();

        // Peel out all the data from variable names.
        foreach ($HTTP_POST_VARS as $key => $val) {
            if ($key <> "id") {
                $type = substr($key,0,1);
                $num  = substr($key,1); 
                $feedback[$num][$type] = $val;
            }
        }

        $timenow = time();
        $count = 0;
        foreach ($feedback as $num => $vals) {
            $entry = $entrybyentry[$num];
            // Only update entries where feedback has actually changed.
            if (($vals[r] <> $entry->rating) || ($vals[c] <> addslashes($entry->comment))) {
                if (!$rs = $db->Execute("UPDATE journal_entries
                                         SET rating='$vals[r]',   comment='$vals[c]',
                                            teacher='$USER->id', timemarked='$timenow'
                                         WHERE id = '$num'")) {
                    error("Failed to update the journal feedback!");
                }
                $entrybystudent[$entry->user]->comment = $vals[c];
                $entrybystudent[$entry->user]->rating = $vals[r];
                $entrybystudent[$entry->user]->timemarked = $timenow;
                $entrybystudent[$entry->user]->teacher = $USER->id;
                $count++;
            }
        }
        add_to_log($course->id, "journal", "update feedback", "report.php?id=$cm->id", "$count students");
        notify("Journal feedback updated for $count students.");
    } else {
        add_to_log($course->id, "journal", "view responses", "report.php?id=$cm->id", "$journal->id");
    }


    if (! $students = get_records_sql("SELECT u.* FROM user u, user_students s 
                                       WHERE s.course = '$course->id' AND s.user = u.id
                                       ORDER BY u.lastaccess DESC")) {
        notify("No students", "/course/view.php?id=$course->id");
        die;
    }

    if (! $teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                       WHERE t.course = '$course->id' AND t.user = u.id
                                       ORDER BY u.lastaccess DESC")) {
        notify("No teachers", "/course/view.php?id=$course->id");
        die;
    }

    echo "<FORM ACTION=report.php METHOD=post>\n";
    foreach ($students as $student) {
        $entry = $entrybystudent[$student->id];

        echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10>";

        echo "\n<TR>";
        echo "\n<TD ROWSPAN=2 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
        print_user_picture($student->id, $course->id, $student->picture);
        echo "</TD>";
        echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">$student->firstname $student->lastname";
        if ($entry) {
            echo "&nbsp;&nbsp;<FONT SIZE=1>Last edited: ".userdate($entry->modified)."</FONT>";
        }
        echo "</TR>";

        echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
        if ($entry) {
            echo text_to_html($entry->text);
        } else {
            echo "No entry";
        }
        echo "</TD></TR>";

        if ($entry) {
            echo "\n<TR>";
            echo "<TD WIDTH=35 VALIGN=TOP>";
            if (!$entry->teacher) {
                $entry->teacher = $USER->id;
            }
            print_user_picture($entry->teacher, $course->id, $teachers[$entry->teacher]->picture);
            echo "<TD BGCOLOR=\"$THEME->cellheading\">Teacher Feedback:";
            choose_from_menu($RATING, "r$entry->id", $entry->rating, "Rate...");
            if ($entry->timemarked) {
                echo "&nbsp;&nbsp;<FONT SIZE=1>".userdate($entry->timemarked)."</FONT>";
            }
            echo "<BR><TEXTAREA NAME=\"c$entry->id\" ROWS=4 COLS=60 WRAP=virtual>";
            p($entry->comment);
            echo "</TEXTAREA><BR>";
            echo "</TD></TR>";
        }
        echo "</TABLE><BR CLEAR=ALL>\n";

    }
    echo "<CENTER>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$cm->id\">";
    echo "<INPUT TYPE=submit VALUE=\"Save all my feedback\">";
    echo "</CENTER>";
    echo "</FORM>";

    print_footer($course);

 
?>

