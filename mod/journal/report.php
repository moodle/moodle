<?PHP // $Id$

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

    if (! $journal = get_record("journal", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    // make some easy ways to access the entries.
    if ( $eee = get_records("journal_entries", "journal", $journal->id)) {
        foreach ($eee as $ee) {
            $entrybyuser[$ee->userid] = $ee;
            $entrybyentry[$ee->id]  = $ee;
        }
        
    } else {
        $entrybyuser  = array () ;
        $entrybyentry = array () ;
    }

    $strentries = get_string("entries", "journal");
    $strjournals = get_string("modulenameplural", "journal");

    print_header("$course->shortname: $strjournals", "$course->fullname",
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"index.php?id=$course->id\">$strjournals</a> ->
                  <a href=\"view.php?id=$cm->id\">$journal->name</a> -> $strentries", "", "", true);

    if ($data = data_submitted()) {
       
        $feedback = array();
        $data = (array)$data;

        // Peel out all the data from variable names.
        foreach ($data as $key => $val) {
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
                $newentry->rating     = $vals[r];
                $newentry->comment    = $vals[c];
                $newentry->teacher    = $USER->id;
                $newentry->timemarked = $timenow;
                $newentry->mailed     = 0;           // Make sure mail goes out (again, even)
                $newentry->id         = $num;
                if (! update_record("journal_entries", $newentry)) {
                    notify("Failed to update the journal feedback for user $entry->userid");
                } else {
                    $count++;
                }
                $entrybyuser[$entry->userid]->rating     = $vals[r];
                $entrybyuser[$entry->userid]->comment    = $vals[c];
                $entrybyuser[$entry->userid]->teacher    = $USER->id;
                $entrybyuser[$entry->userid]->timemarked = $timenow;
            }
        }
        add_to_log($course->id, "journal", "update feedback", "report.php?id=$cm->id", "$count users");
        notify(get_string("feedbackupdated", "journal", "$count"), "green");
    } else {
        add_to_log($course->id, "journal", "view responses", "report.php?id=$cm->id", "$journal->id");
    }

/// Print out the journal entries

    if (! $users = get_course_users($course->id)) {
        print_heading(get_string("nousersyet"));

    } else {

        $grades = make_grades_menu($journal->assessed);
        $teachers = get_course_teachers($course->id);

        echo "<form action=report.php method=post>\n";

        if ($usersdone = journal_get_users_done($journal)) {
            foreach ($usersdone as $user) {
                journal_print_user_entry($course, $user, $entrybyuser[$user->id], $teachers, $grades);
                unset($users[$user->id]);
            }
        }

        foreach ($users as $user) {       // Remaining users
            journal_print_user_entry($course, $user, NULL, $teachers, $grades);
        }

        $strsaveallfeedback = get_string("saveallfeedback", "journal");
        echo "<center>";
        echo "<input type=hidden name=id value=\"$cm->id\">";
        echo "<input type=submit value=\"$strsaveallfeedback\">";
        echo "</center>";
        echo "</form>";
    }
    
    print_footer($course);
 
?>

