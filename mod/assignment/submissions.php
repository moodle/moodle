<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);    // Assignment

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

    print_header("$course->shortname: $assignment->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strassignments</A> -> 
                  <A HREF=\"view.php?a=$assignment->id\">$assignment->name</A> -> $strsubmissions", 
                  "", "", true);

    // Some easy ways to reference submissions
    if ($submissions = assignment_get_all_submissions($assignment)) {
        foreach ($submissions as $submission) {
            $submissionbyuser[$submission->user] = $submission;
            $submissionbyid[$submission->id]  = $submission;
        }
    }

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
            $submission = $submissionbyid[$num];
            // Only update entries where feedback has actually changed.
            if (($vals[g] <> $submission->grade) || ($vals[c] <> addslashes($submission->comment))) {
                $newsubmission->grade      = $vals[g];
                $newsubmission->comment    = $vals[c];
                $newsubmission->teacher    = $USER->id;
                $newsubmission->timemarked = $timenow;
                $newsubmission->mailed     = 0;           // Make sure mail goes out (again, even)
                $newsubmission->id         = $num;
                if (! update_record("assignment_submissions", $newsubmission)) {
                    notify(get_string("failedupdatefeedback", "assignment", $submission->user));
                } else {
                    $count++;
                }
                $submissionbyuser[$submission->user]->grade      = $vals[g];
                $submissionbyuser[$submission->user]->comment    = $vals[c];
                $submissionbyuser[$submission->user]->teacher    = $USER->id;
                $submissionbyuser[$submission->user]->timemarked = $timenow;
            }
        }
        add_to_log($course->id, "assignment", "update grades", "submissions.php?id=$assignment->id", "$count users");
        notify(get_string("feedbackupdated", "assignment", $count));
    } else {
        add_to_log($course->id, "assignment", "view submissions", "submissions.php?id=$assignment->id", "$assignment->id");
    }

    for ($i=$assignment->grade; $i>=0; $i--) {
        $grades[$i] = $i;
    }

    $teachers = get_course_teachers($course->id);
    if (! $users = get_course_students($course->id)) {
        print_heading(get_string("nostudentsyet"));

    } else {
        echo "<FORM ACTION=submissions.php METHOD=post>\n";

        if ($usersdone = assignment_get_users_done($assignment)) {
            foreach ($usersdone as $user) {
                $submission = $submissionbyuser[$user->id];
                assignment_print_submission($assignment, $user, $submission, $teachers, $grades);
            }
        }

        $submission = NULL;
        foreach ($users as $user) {
            if (! $usersdone[$user->id]) {
                assignment_print_submission($assignment, $user, $submission, $teachers, $grades);
            }
        }
        echo "<CENTER>";
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$assignment->id\">";
        echo "<INPUT TYPE=submit VALUE=\"Save all my feedback\">";
        echo "</CENTER>";
        echo "</FORM>";
    }
    
    print_footer($course);
 
?>

