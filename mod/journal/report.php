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


/// Check to see if groups are being used in this journal
/// and if so, set $currentgroup to reflect the current group

    $groupmode = groupmode($course, $cm);   // Groups are being used
    $currentgroup = get_and_set_current_group($course, $groupmode, $_GET['group']);

    if (!isteacheredit($course->id) and $groupmode and !$currentgroup) {
        print_heading("Sorry, but you can't see this group");
        print_footer();
        exit;
    }

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<table align="center"><tr><td>';
            if ($groupmode == VISIBLEGROUPS) {
                print_string('groupsvisible');
            } else {
                print_string('groupsseparate');
            }
            echo ':';
            echo '</td><td nowrap="nowrap" align="left" width="50%">';
            popup_form("report.php?id=$cm->id&sort=$sort&dir=$dir&group=", 
                       $groups, 'selectgroup', $currentgroup, "", "", "", false, "self");
            echo '</tr></table>';
        }
    }


/// Process incoming data if there is any
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
        add_to_log($course->id, "journal", "update feedback", "report.php?id=$cm->id", "$count users", $cm->id);
        notify(get_string("feedbackupdated", "journal", "$count"), "green");
    } else {
        add_to_log($course->id, "journal", "view responses", "report.php?id=$cm->id", "$journal->id", $cm->id);
    }

/// Print out the journal entries

    if ($currentgroup) {
        $users = get_course_students($course->id, "", "", 0, 99999, "", "", $currentgroup);
    } else {
        $users = get_course_students($course->id);
    }

    if (!$users) {
        print_heading(get_string("nousersyet"));

    } else {

        $grades = make_grades_menu($journal->assessed);
        $teachers = get_course_teachers($course->id);

        $allowedtograde = ($groupmode != VISIBLEGROUPS or isteacheredit($course->id) or ismember($currentgroup));

        if ($allowedtograde) {
            echo '<form action="report.php" method="post">';
        }

        if ($usersdone = journal_get_users_done($journal)) {
            foreach ($usersdone as $user) {
                if ($currentgroup) {
                    if (!ismember($currentgroup, $user->id)) {    /// Yes, it's inefficient, but this module will die
                        continue;
                    }
                }
                journal_print_user_entry($course, $user, $entrybyuser[$user->id], $teachers, $grades);
                unset($users[$user->id]);
            }
        }

        foreach ($users as $user) {       // Remaining users
            journal_print_user_entry($course, $user, NULL, $teachers, $grades);
        }

        if ($allowedtograde) {
            echo "<center>";
            echo "<input type=hidden name=id value=\"$cm->id\">";
            echo "<input type=submit value=\"".get_string("saveallfeedback", "journal")."\">";
            echo "</center>";
            echo "</form>";
        }
    }
    
    print_footer($course);
 
?>

