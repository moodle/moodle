<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Assignment
    optional_variable($sort, ""); 
    optional_variable($dir, "");

    $timewas = $_POST['timenow'];
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
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment  = get_string("modulename", "assignment");
    $strsubmissions = get_string("submissions", "assignment");
    $strsaveallfeedback = get_string("saveallfeedback", "assignment");

    print_header("$course->shortname: $assignment->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strassignments</A> -> 
                  <A HREF=\"view.php?a=$assignment->id\">$assignment->name</A> -> $strsubmissions", 
                  "", "", true);

/// Get all teachers and students
    $teachers = get_course_teachers($course->id);

    if (!$users = get_course_students($course->id)) {
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
                    $u = $users[$submission->userid];
                    $uname = $u->firstname . " " . $u->lastname;
                    notify(get_string("failedupdatefeedback", "assignment", $uname)
                    . "<br>" . get_string("grade") . ": $newsubmission->grade" 
                    . "<br>" . get_string("feedback", "assignment") . ": $newsubmission->comment\n");
                    unset($u);
                    unset($uname);
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
        add_to_log($course->id, "assignment", "view submissions", "submissions.php?id=$assignment->id", "$assignment->id", $cm->id);
    }

    // Submission sorting
    print_simple_box_start("CENTER", "50%");
    echo "<P align=\"CENTER\">";
    print_string("order");

    if ($dir == "ASC")
        $dir = "DESC";
    else
        $dir = "ASC";

    echo ": <A HREF=\"submissions.php?id=$assignment->id&sort=lastname&dir=$dir\">".get_string("name")."</a> - ";
    echo "<A HREF=\"submissions.php?id=$assignment->id&sort=timemodified&dir=$dir\">".get_string("lastmodified")."</a> - ";
    echo "<A HREF=\"submissions.php?id=$assignment->id&sort=grade&dir=$dir\">".get_string("feedback")."</a>";
    echo "</P>";
    print_simple_box_end();
    print_spacer(8,1);

    echo "<FORM ACTION=submissions.php METHOD=post>\n";
    
    $grades = make_grades_menu($assignment->grade);

    foreach ($submissions as $submission) {
        $user = $users[$submission->userid];
        assignment_print_submission($assignment, $user, $submission, $teachers, $grades);
    }

    echo "<CENTER>";
    echo "<INPUT TYPE=hidden NAME=timenow VALUE=\"$timenow\">";
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$assignment->id\">";
    echo "<INPUT TYPE=submit VALUE=\"$strsaveallfeedback\">";
    echo "</CENTER>";
    echo "</FORM>";
    
    print_footer($course);
 
?>
