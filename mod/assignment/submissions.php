<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Assignment
    optional_variable($sort, "timemodified"); 
    optional_variable($dir, "DESC");
    optional_variable($timenow, 0);

    $timewas = $timenow;
    $timenow = time();

    if (! $assignment = get_record("assignment", "id", $id)) {
        error("Course module is incorrect");
    }
    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment  = get_string("modulename", "assignment");
    $strsubmissions = get_string("submissions", "assignment");
    $strsaveallfeedback = get_string("saveallfeedback", "assignment");

    print_header("$course->shortname: $assignment->name", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strassignments</a> -> 
                  <a href=\"view.php?a=$assignment->id\">$assignment->name</a> -> $strsubmissions", 
                  "", "", true);

/// Check to see if groups are being used in this assignment
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "submissions.php?id=$assignment->id&sort=$sort&dir=$dir");
    } else {
        $currentgroup = false;
    }

/// Get all teachers and students
    $teachers = get_course_teachers($course->id);

    if ($currentgroup) {
        $users = get_group_users($currentgroup);
    } else {
        $users = get_course_students($course->id);
    }
    if (!$users) {
        print_heading(get_string("nostudentsyet"));
        print_footer($course);
        exit;
    }

/// Make some easy ways to reference submissions
    if ($submissions = assignment_get_all_submissions($assignment, $sort, $dir)) {
        foreach ($submissions as $submission) {
            $submissionbyuser[$submission->userid] = $submission;
        }
    }

/// Get all existing submissions and check for missing ones
    foreach($users as $user) {
        if (!isset($submissionbyuser[$user->id])) {  // Need to create empty entry
            $newsubmission->assignment = $assignment->id;
            $newsubmission->userid = $user->id;
            $newsubmission->timecreated = time();
            if (!insert_record("assignment_submissions", $newsubmission)) {
                error("Could not insert a new empty submission");
            }
        }
    }

    if (isset($newsubmission)) {   // Get them all out again to be sure
        $submissions = assignment_get_all_submissions($assignment, $sort, $dir);
    }


/// If data is being submitted, then process it
    if ($data = data_submitted()) {
       
        $feedback = array();
        // Peel out all the data from variable names.
        foreach ($data as $key => $val) {
            if (!in_array($key, array("id", "timenow"))) {
                $type = substr($key,0,1);
                $num  = substr($key,1); 
                $feedback[$num][$type] = $val;
            }
        }

        $count = 0;
        foreach ($feedback as $num => $vals) {
            $submission = $submissions[$num];
            // Only update entries where feedback has actually changed.
            if (($vals['g'] <> $submission->grade) || ($vals['c'] <> addslashes($submission->comment))) {
                unset($newsubmission);
                $newsubmission->grade      = $vals['g'];
                $newsubmission->comment    = $vals['c'];
                $newsubmission->teacher    = $USER->id;
                $newsubmission->timemarked = $timenow;
                $newsubmission->mailed     = 0;           // Make sure mail goes out (again, even)
                $newsubmission->id         = $num;

                // Make sure that we aren't overwriting any recent feedback from other teachers. (see bug #324)
                if ($timewas < $submission->timemarked && (!empty($submission->grade)) && (!empty($submission->comment))) {
                    notify(get_string("failedupdatefeedback", "assignment", fullname($users[$submission->userid]))
                    . "<br>" . get_string("grade") . ": $newsubmission->grade" 
                    . "<br>" . get_string("feedback", "assignment") . ": $newsubmission->comment\n");
                } else { //print out old feedback and grade
                    if (empty($submission->timemodified)) {   // eg for offline assignments
                        $newsubmission->timemodified = $timenow;
                    }
                    if (! update_record("assignment_submissions", $newsubmission)) {
                        notify(get_string("failedupdatefeedback", "assignment", $submission->userid));
                    } else {
                        $count++;
                    }
                }
            }
        }
        $submissions = assignment_get_all_submissions($assignment,$sort, $dir);
        add_to_log($course->id, "assignment", "update grades", "submissions.php?id=$assignment->id", "$count users", $cm->id);
        notify(get_string("feedbackupdated", "assignment", $count));
    } else {
        add_to_log($course->id, "assignment", "view submission", "submissions.php?id=$assignment->id", "$assignment->id", $cm->id);
    }

    // Submission sorting

    $sorttypes = array('firstname', 'lastname', 'timemodified', 'grade');

    print_simple_box_start("center", "50%");
    echo '<p align="center">'.get_string('order').':&nbsp;&nbsp;';

    foreach ($sorttypes as $sorttype) {
        if ($sorttype == 'timemodified') {
            $label = get_string("lastmodified");
        } else {
            $label = get_string($sorttype);
        }
        if ($sort == $sorttype) {   // Current sort
            $newdir = $dir == 'ASC' ? 'DESC' : 'ASC';
        } else {
            $newdir = 'ASC';
        }
        echo "<a href=\"submissions.php?id=$assignment->id&sort=$sorttype&dir=$newdir\">$label</a>";
        if ($sort == $sorttype) {   // Current sort
             $diricon = $dir == 'ASC' ? 'down' : 'up';
             echo " <img src=\"$CFG->pixpath/t/$diricon.gif\" />";
        }
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    echo "</p>";
    print_simple_box_end();

    print_spacer(8,1);

    $allowedtograde = ($groupmode != VISIBLEGROUPS or isteacheredit($course->id) or ismember($currentgroup));

    if ($allowedtograde) {
        echo '<form action="submissions.php" method="post">';
        echo "<center>";
        echo "<input type=hidden name=sort value=\"$sort\">";
        echo "<input type=hidden name=timenow value=\"$timenow\">";
        echo "<input type=hidden name=id value=\"$assignment->id\">";
        echo "<input type=submit value=\"$strsaveallfeedback\">";
        echo "</center>";
    }
    
    $grades = make_grades_menu($assignment->grade);

    foreach ($submissions as $submission) {
        if (isset($users[$submission->userid])) {
            $user = $users[$submission->userid];
            assignment_print_submission($assignment, $user, $submission, $teachers, $grades);
        }
    }

    if ($allowedtograde) {
        echo "<center>";
        echo "<input type=hidden name=sort value=\"$sort\">";
        echo "<input type=hidden name=timenow value=\"$timenow\">";
        echo "<input type=hidden name=id value=\"$assignment->id\">";
        echo "<input type=submit value=\"$strsaveallfeedback\">";
        echo "</center>";
        echo "</form>";
    }
    
    print_footer($course);
 
?>
