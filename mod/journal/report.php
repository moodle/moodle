<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course module

    if (! $cm = get_coursemodule_from_id('journal', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course module is misconfigured");
    }

    require_login($course->id, false);

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

    $crumbs[] = array('name' => $strjournals, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $crumbs[] = array('name' => format_string($journal->name), 'link' => "view.php?id=$cm->id", 'type' => 'activityinstance');
    $crumbs[] = array('name' => $strentries, 'link' => '', 'type' => 'title');
    $navigation = build_navigation($crumbs);

    print_header_simple("$strjournals", "", $navigation, "", "", true);


/// Check to see if groups are being used in this journal
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
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
            if (($vals['r'] <> $entry->rating) || ($vals['c'] <> addslashes($entry->entrycomment))) {
                $newentry->rating     = $vals['r'];
                $newentry->entrycomment    = $vals['c'];
                $newentry->teacher    = $USER->id;
                $newentry->timemarked = $timenow;
                $newentry->mailed     = 0;           // Make sure mail goes out (again, even)
                $newentry->id         = $num;
                if (! update_record("journal_entries", $newentry)) {
                    notify("Failed to update the journal feedback for user $entry->userid");
                } else {
                    $count++;
                }
                $entrybyuser[$entry->userid]->rating     = $vals['r'];
                $entrybyuser[$entry->userid]->entrycomment    = $vals['c'];
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
        $users = get_group_users($currentgroup);
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
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
            echo "<input type=\"submit\" value=\"".get_string("saveallfeedback", "journal")."\" />";
            echo "</center>";
            echo "</form>";
        }
    }

    print_footer($course);

?>

