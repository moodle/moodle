<?php  // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $aid           = required_param('aid', PARAM_INT);   // Assessment ID
    $allowcomments = optional_param('allowcomments', false, PARAM_BOOL);
    $redirect      = optional_param('redirect', '', PARAM_URL);
    $frameset      = optional_param('frameset', '', PARAM_ALPHA);

    if (! $assessment = get_record("workshop_assessments", "id", $aid)) {
        error("Assessment id is incorrect");
    }
    if (! $submission = get_record('workshop_submissions', 'id', $assessment->submissionid)) {
        error("Incorrect submission id");
    }
    if (! $workshop = get_record("workshop", "id", $submission->workshopid)) {
        error("Submission is incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Workshop is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
        error("No coursemodule found");
    }

    if (!$redirect) {
        $redirect = htmlentities($_SERVER["HTTP_REFERER"].'#sid='.$submission->id);
    }

    require_login($course->id, false, $cm);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strassess = get_string("viewassessment", "workshop");

    /// Now check whether we need to display a frameset

    if (empty($frameset)) {
        if ( get_string('thisdirection') == 'rtl' ) {
            $direction = ' dir="rtl"';
        } else {
            $direction = ' dir="ltr"';
        }
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
        echo "<html $direction>\n";
        echo "<head><meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
        echo "<title>{$course->shortname}: ".format_string($workshop->name,true)."</title></head>\n";
        echo "<frameset rows=\"90%,*\" border=\"10\">";
        echo "  <frame src=\"viewassessment.php?id=$id&amp;aid=$aid&amp;allowcomments=$allowcomments&amp;frameset=top&amp;redirect=$redirect\" border=\"10\" />";
        echo "  <frame src=\"viewassessment.php?id=$id&amp;aid=$aid&amp;allowcomments=$allowcomments&amp;frameset=bottom&amp;redirect=$redirect\" />";
        echo "</frameset>";
        echo "</html>";
        exit;
    }

    /// top frame with the navigation bar and the assessment form

    if ($frameset == "top") {
        $navigation = build_navigation($strassess, $cm);
        print_header_simple(format_string($workshop->name), "", $navigation,
                      "", '', true);

        // show assessment but don't allow changes
        workshop_print_assessment($workshop, $assessment, false, $allowcomments);

        if (workshop_is_teacher($workshop) and !workshop_is_teacher($workshop, $assessment->userid)) {
            print_heading_with_help(get_string("gradeassessment", "workshop"), "gradingassessments", "workshop");
            include('assessment_grading_form.html');
        }
        print_continue($redirect);
        print_footer($course);
        exit;
    }

    /// print bottom frame with the submission
    // removed <base target="_parent" /> as it does not validate
    print_header('', '', '', '', '');
    $title = '"'.$submission->title.'" ';
    if (workshop_is_teacher($workshop)) {
        $title .= ' '.get_string('by', 'workshop').' '.workshop_fullname($submission->userid, $course->id);
    }
    print_heading($title);
    workshop_print_submission($workshop, $submission);
    print_footer('none');

?>
