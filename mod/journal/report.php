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
            $entrybyuser[$ee->user] = $ee;
            $entrybyentry[$ee->id]  = $ee;
        }
        
    } else {
        $entrybyuser = array () ;
        $entrybyentry   = array () ;
    }

    print_header("$course->shortname: Journals", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
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
                $entrybyuser[$entry->user]->rating = $vals[r];
                $entrybyuser[$entry->user]->comment = $vals[c];
                $entrybyuser[$entry->user]->teacher = $USER->id;
                $entrybyuser[$entry->user]->timemarked = $timenow;
                $entrybyuser[$entry->user]->id = $num;
                if (! update_record("journal_entries", $entrybyuser[$entry->user])) {
                    error("Failed to update the journal feedback!");
                } else {
                    $count++;
                }
            }
        }
        add_to_log($course->id, "journal", "update feedback", "report.php?id=$cm->id", "$count users");
        notify("Journal feedback updated for $count people.");
    } else {
        add_to_log($course->id, "journal", "view responses", "report.php?id=$cm->id", "$journal->id");
    }


    $teachers = get_course_teachers($course->id);
    if (! $users = get_course_users($course->id)) {
        print_heading("No users in this course yet");

    } else {
        echo "<FORM ACTION=report.php METHOD=post>\n";

        if ($usersdone = journal_get_users_done($course, $journal)) {
            foreach ($usersdone as $user) {
                $entry = $entrybyuser[$user->id];
                journal_print_user_entry($course, $user, $entry, $teachers, $RATING);
            }
        }

        foreach ($users as $user) {
            if (! $usersdone[$user->id]) {
                $entry = NULL;
                journal_print_user_entry($course, $user, $entry, $teachers, $RATING);
            }
        }
        echo "<CENTER>";
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$cm->id\">";
        echo "<INPUT TYPE=submit VALUE=\"Save all my feedback\">";
        echo "</CENTER>";
        echo "</FORM>";
    }
    
    print_footer($course);
 
?>

