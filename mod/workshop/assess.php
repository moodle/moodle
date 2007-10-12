<?php  // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $id            = required_param('id', PARAM_INT);   // Submission ID
    $allowcomments = optional_param('allowcomments', 0, PARAM_BOOL);
    $redirect      = optional_param('redirect', '', PARAM_URL);
    $frameset      = optional_param('frameset', '', PARAM_ALPHA);
    $sid           = optional_param('sid', 0, PARAM_INT);

    if (! $submission = get_record('workshop_submissions', 'id', $sid)) {
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

    require_login($course->id, false, $cm);


    if (!$redirect) {
        //seems not to work properly
        $redirect = htmlentities($_SERVER["HTTP_REFERER"].'#sid='.$submission->id);
    }


    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strassess = get_string("assess", "workshop");

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
        echo "<title>" . format_string($course->shortname) . ": ".format_string($workshop->name,true)."</title></head>\n";
        echo "<frameset rows=\"50%,*\" border=\"10\">";
        echo "  <frame src=\"assess.php?id=$id&amp;sid=$sid&amp;frameset=top&amp;redirect=$redirect\" border=\"10\" />";
        echo "  <frame src=\"assess.php?id=$id&amp;sid=$sid&amp;frameset=bottom&amp;redirect=$redirect\" />";
        echo "</frameset>";
        echo "</html>";
        exit;
    }

    /// top frame with the navigation bar and the assessment form

    if ($frameset == "top") {
        $navigation = build_navigation($strassess, $cm);
        print_header_simple(format_string($workshop->name), "",$navigation,
                      "", '', true);

        // there can be an assessment record (for teacher submissions), if there isn't...
        if (!$assessment = get_record("workshop_assessments", "submissionid", $submission->id, "userid",
                    $USER->id)) {
            // if it's the teacher see if the user has done a self assessment if so copy it
            if (workshop_is_teacher($workshop) and  ($assessment = get_record("workshop_assessments", "submissionid",
                            $submission->id, "userid", $submission->userid))) {
                $assessment = workshop_copy_assessment($assessment, $submission, true);
                // need to set owner of assessment
                set_field("workshop_assessments", "userid", $USER->id, "id", $assessment->id);
                $assessment->resubmission = 0; // not set by workshop_copy_assessment
                $assessment->timegraded = 0; // not set by workshop_copy_assessment
                $assessment->timeagreed = 0; // not set by workshop_copy_assessment
            } else {
                $yearfromnow = time() + 365 * 86400;
                // ...create one and set timecreated way in the future, this is reset when record is updated
                $assessment->workshopid = $workshop->id;
                $assessment->submissionid = $submission->id;
                $assessment->userid = $USER->id;
                $assessment->timecreated = $yearfromnow;
                $assessment->grade = -1; // set impossible grade
                $assessment->timegraded = 0;
                $assessment->timeagreed = 0;
                $assessment->resubmission = 0;
                $assessment->generalcomment = '';
                $assessment->teachercomment = '';
                if (!$assessment->id = insert_record("workshop_assessments", $assessment)) {
                    error("Could not insert workshop assessment!");
                }
                // if it's the teacher and the workshop is error banded set all the elements to Yes
                if (workshop_is_teacher($workshop) and ($workshop->gradingstrategy == 2)) {
                    for ($i =0; $i < $workshop->nelements; $i++) {
                        unset($element);
                        $element->workshopid = $workshop->id;
                        $element->assessmentid = $assessment->id;
                        $element->elementno = $i;
                        $element->feedback = '';
                        $element->grade = 1;
                        if (!$element->id = insert_record("workshop_grades", $element)) {
                            error("Could not insert workshop grade!");
                        }
                    }
                    // now set the adjustment
                    unset($element);
                    $i = $workshop->nelements;
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->grade = 0;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
            }
        }

        print_heading_with_help(get_string("assessthissubmission", "workshop"), "grading", "workshop");

        // show assessment and allow changes
        workshop_print_assessment($workshop, $assessment, true, $allowcomments, $redirect);

        print_heading("<a $CFG->frametarget href=\"$redirect\">".get_string("cancel")."</a>");
        print_footer($course);
        exit;
    }

    /// print bottom frame with the submission
    // removed <base target="_parent" /> as it does not validate MDL-7861
    print_header('', '', '', '', '');
    $title = '"'.$submission->title.'" ';
    if (workshop_is_teacher($workshop)) {
        $title .= ' '.get_string('by', 'workshop').' '.workshop_fullname($submission->userid, $course->id);
    }
    print_heading($title);
    workshop_print_submission($workshop, $submission);

    if (workshop_is_teacher($workshop)) {
        echo '<br /><div style="text-align:center"><b>'.get_string('assessments', 'workshop').': </b><br />';
        echo workshop_print_submission_assessments($workshop, $submission, "all");
        echo '</div>';
    }


    print_footer('none');

?>
