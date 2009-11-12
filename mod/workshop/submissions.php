<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    adminamendtitle
    confirmdelete
    delete
    adminlist
    editsubmission
    listallsubmissions
    listforassessmentstudent
    listforassessmentteacher
    showsubmission
    updatesubmission


************************************************/

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $id          = required_param('id', PARAM_INT);    // Course Module ID
    $action      = optional_param('action', '', PARAM_ALPHA);
    $sid         = optional_param('sid', 0, PARAM_INT); //submission id
    $order       = optional_param('order', 'name', PARAM_ALPHA);
    $title       = optional_param('title', '', PARAM_CLEAN);
    $nentries    = optional_param('nentries', '', PARAM_ALPHANUM);
    $anonymous   = optional_param('anonymous', '', PARAM_CLEAN);
    $description = optional_param('description', '', PARAM_CLEAN);

    $timenow = time();

    // get some useful stuff...
    if (! $cm = get_coursemodule_from_id('workshop', $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $workshop = get_record("workshop", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strsubmissions = get_string("submissions", "workshop");

    // ... print the header and...
    $navigation = build_navigation($strsubmissions, $cm);
    print_header_simple(format_string($workshop->name), "", $navigation,
                  "", "", true);

    //...get the action or set up an suitable default
    if (empty($action)) {
        $action = "listallsubmissions";
    }


/******************* admin amend title ************************************/
    elseif ($action == 'adminamendtitle' ) {

        require_capability('mod/workshop:manage', $context);
        if (empty($sid)) {
            error("Admin Amend Title: submission id missing");
        }

        $submission = get_record("workshop_submissions", "id", $sid);
        print_heading(get_string("amendtitle", "workshop"));
        ?>
        <form id="amendtitleform" action="submissions.php" method="post">
        <fieldset class="invisiblefieldset">
        <input type="hidden" name="action" value="adminupdatetitle" />
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="sid" value="<?php echo $sid ?>" />
        <div class="boxaligncenter">
        <table cellpadding="5" border="1">
        <?php

        // now get the comment
        echo "<tr valign=\"top\">\n";
        echo "  <td align=\"right\"><p><b>". get_string("title", "workshop").":</b></p></td>\n";
        echo "  <td>\n";
        echo "      <input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\" />\n";
        echo "  </td></tr></table>\n";
        echo "<input type=\"submit\" value=\"".get_string("amendtitle", "workshop")."\" />\n";
        echo "</div></fieldset></form>\n";

        print_heading("<a $CFG->frametarget href=\"view.php?id=$cm->id#sid=$submission->id\">".get_string("cancel")."</a>");
    }


    /******************* admin clear late (flag) ************************************/
    elseif ($action == 'adminclearlate' ) {

        require_capability('mod/workshop:manage', $context);
        require_sesskey();
        if (empty($sid)) {
            error("Admin clear late flag: submission id missing");
        }

        if (!$submission = get_record("workshop_submissions", "id", $sid)) {
            error("Admin clear late flag: can not get submission record");
        }
        if (set_field("workshop_submissions", "late", 0, "id", $sid)) {
            print_heading(get_string("clearlateflag", "workshop")." ".get_string("ok"));
        }

        add_to_log($course->id, "workshop", "late flag cleared", "view.php?id=$cm->id", "submission $submission->id");

        redirect("view.php?id=$cm->id");
    }


    /******************* confirm delete ************************************/
    elseif ($action == 'confirmdelete' ) {

        if (empty($sid)) {
            error("Confirm delete: submission id missing");
            }
        notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("submission", "workshop")),
             "submissions.php?sesskey=" . sesskey() . "&amp;action=delete&amp;id=$cm->id&amp;sid=$sid", "view.php?id=$cm->id#sid=$sid");
        }


    /******************* delete ************************************/
    elseif ($action == 'delete' ) {

        require_sesskey();
        if (empty($sid)) {
            error("Delete: submission id missing");
        }

        if (!$submission = get_record("workshop_submissions", "id", $sid)) {
            error("Admin delete: can not get submission record");
        }

        // students are only allowed to delete their own submission and only up to the deadline
        if (!(workshop_is_teacher($workshop) or
               (($USER->id = $submission->userid) and ($timenow < $workshop->submissionend)
                   and (($timenow < $workshop->assessmentstart) or ($timenow < $submission->timecreated + $CFG->maxeditingtime))))) {
            error("You are not authorized to delete this submission");
        }

        print_string("deleting", "workshop");
        // first get any assessments...
        if ($assessments = workshop_get_assessments($submission, 'ALL')) {
            foreach($assessments as $assessment) {
                // ...and all the associated records...
                delete_records("workshop_comments", "assessmentid", $assessment->id);
                delete_records("workshop_grades", "assessmentid", $assessment->id);
                echo ".";
            }
        }
        // ...now delete the assessments...
        delete_records("workshop_assessments", "submissionid", $submission->id);
        // ...and the submission record...
        delete_records("workshop_submissions", "id", $submission->id);
        // ..and finally the submitted file
        workshop_delete_submitted_files($workshop, $submission);

        redirect("view.php?id=$cm->id");
    }


    /******************* admin (confirm) late flag ************************************/
    elseif ($action == 'adminlateflag' ) {

        require_capability('mod/workshop:manage', $context);
        if (empty($sid)) {
            error("Admin confirm late flag: submission id missing");
        }
        if (!$submission = get_record("workshop_submissions", "id", $sid)) {
            error("Admin confirm late flag: can not get submission record");
        }

        notice_yesno(get_string("clearlateflag","workshop")."?",
             "submissions.php?sesskey=" . sesskey() . "&amp;action=adminclearlate&amp;id=$cm->id&amp;sid={$submission->id}",
             "view.php?id=$cm->id");
    }


    /******************* list all submissions ************************************/
    elseif ($action == 'adminlist' ) {

        require_capability('mod/workshop:manage', $context);

        workshop_list_submissions_for_admin($workshop, $order);
        print_continue("view.php?id=$cm->id");

    }


    /******************* admin update title ************************************/
    elseif ($action == 'adminupdatetitle' ) {

        require_capability('mod/workshop:manage', $context);
        require_sesskey();
        if (empty($sid)) {
            error("Admin Update Title: submission id missing");
        }

        if (set_field("workshop_submissions", "title", $title, "id", $sid)) {
            print_heading(get_string("amendtitle", "workshop")." ".get_string("ok"));
        }
        print_continue("view.php?id=$cm->id");
    }


    /******************* confirm remove attachments ************************************/
    elseif ($action == 'confirmremoveattachments' ) {

        if (empty($sid)) {
            error("Admin confirm delete: submission id missing");
        }
        if (!$submission = get_record("workshop_submissions", "id", $sid)) {
            error("Admin delete: can not get submission record");
        }

        notice_yesno(get_string("confirmremoveattachments","workshop"),
             "submissions.php?sesskey=" . sesskey() . "&amp;action=removeattachments&amp;id=$cm->id&amp;sid=$sid",
             "view.php?id=$cm->id");
    }


    /******************* edit submission ************************************/
    elseif ($action == 'editsubmission' ) {

        if (empty($sid)) {
            error("Edit submission: submission id missing");
        }
        $usehtmleditor = can_use_html_editor();

        $submission = get_record("workshop_submissions", "id", $sid);
        print_heading(get_string("editsubmission", "workshop"));
        if ($submission->userid <> $USER->id) {
            error("Edit submission: Userids do not match");
        }
        if (($submission->timecreated < ($timenow - $CFG->maxeditingtime)) and ($workshop->assessmentstart < $timenow)) {
            print_error('notallowed', 'workshop');
        }
        ?>
        <form id="editform" enctype="multipart/form-data" action="submissions.php" method="post">
        <fieldset class="invisiblefieldset">
        <input type="hidden" name="action" value="updatesubmission" />
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="sid" value="<?php echo $sid ?>" />
        <div class="boxaligncenter">
        <table cellpadding="5" border="1">
        <?php
        echo "<tr valign=\"top\"><td><b>". get_string("title", "workshop").":</b>\n";
        echo "<input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\" />\n";
        echo "</td></tr><tr><td><b>".get_string("submission", "workshop").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "description", $submission->description);
        use_html_editor("description");
        echo "</td></tr>\n";
        if ($workshop->nattachments) {
            $filearea = workshop_file_area_name($workshop, $submission);
            if ($basedir = workshop_file_area($workshop, $submission)) {
                if ($files = get_directory_list($basedir)) {
                    echo "<tr><td><b>".get_string("attachments", "workshop").
                        "</b><div style=\"text-align:right;\"><input type=\"button\" value=\"".get_string("removeallattachments",
                        "workshop")."\" onclick=\"getElementById('editform').action.value='removeattachments';
                        getElementById('editform').submit();\"/></div></td></tr>\n";
                    $n = 1;
                    require_once($CFG->libdir .'/filelib.php');
                    foreach ($files as $file) {
                        $icon = mimeinfo("icon", $file);
                        $ffurl = get_file_url("$filearea/$file");
                        // removed target=\"uploadedfile\" 
                        // as it does not validate MDL_7861
                        echo "<tr><td>".get_string("attachment", "workshop")." $n: <img src=\"$CFG->pixpath/f/$icon\"
                            class=\"icon\" alt=\"".get_string('file')."\" />".
                            "&nbsp;<a href=\"$ffurl\">$file</a></td></tr>\n";
                    }
                } else {
                    echo "<tr><td><b>".get_string("noattachments", "workshop")."</b></td></tr>\n";
                }
            }
            echo "<tr><td>\n";
            require_once($CFG->dirroot.'/lib/uploadlib.php');
            for ($i=0; $i < $workshop->nattachments; $i++) {
                $iplus1 = $i + 1;
                $tag[$i] = get_string("newattachment", "workshop")." $iplus1:";
            }
            upload_print_form_fragment($workshop->nattachments,null,$tag,false,null,$course->maxbytes,
                $workshop->maxbytes,false);
            echo "</td></tr>\n";
        }

        echo "</table>\n";
        echo "<input type=\"submit\" value=\"".get_string("savemysubmission", "workshop")."\" />\n";
        echo "</div></fieldset></form>\n";
    }


    /******************* list all submissions ************************************/
    elseif ($action == 'listallsubmissions' ) {
        if (!$users = workshop_get_students($workshop)) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
        }
        print_heading(get_string("listofallsubmissions", "workshop").":", "CENTER");
        workshop_list_all_submissions($workshop, $USER);
        print_continue("view.php?id=$cm->id");

    }


    /******************* list for assessment student (submissions) ************************************/
    elseif ($action == 'listforassessmentstudent' ) {
        if (!$users = workshop_get_students($workshop)) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
        }
        workshop_list_unassessed_student_submissions($workshop, $USER);
        print_continue("view.php?id=$cm->id");

    }


    /******************* list for assessment teacher (submissions) ************************************/
    elseif ($action == 'listforassessmentteacher' ) {

        require_capability('mod/workshop:manage', $context);

        workshop_list_unassessed_teacher_submissions($workshop, $USER);
        print_continue("view.php?id=$cm->id");

    }


    /******************* remove (all) attachments ************************************/
    elseif ($action == 'removeattachments' ) {

        require_sesskey();
        $form = data_submitted();

        if (empty($form->sid)) {
            error("Update submission: submission id missing");
        }

        $submission = get_record("workshop_submissions", "id", $form->sid);

        // students are only allowed to remove their own attachments and only up to the deadline
        if (!(workshop_is_teacher($workshop) or
               (($USER->id = $submission->userid) and ($timenow < $workshop->submissionend)
                   and (($timenow < $workshop->assessmentstart) or ($timenow < $submission->timecreated + $CFG->maxeditingtime))))) {
            error("You are not authorized to delete these attachments");
        }

        // amend title... just in case they were modified
        // check existence of title
        if (empty($form->title)) {
            notify(get_string("notitlegiven", "workshop"));
        } else {
            set_field("workshop_submissions", "title", $form->title, "id", $submission->id);
            set_field("workshop_submissions", "description", trim($form->description), "id", $submission->id);
        }
        print_string("removeallattachments", "workshop");
        workshop_delete_submitted_files($workshop, $submission);
        add_to_log($course->id, "workshop", "removeattachments", "view.php?id=$cm->id", "submission $submission->id");

        print_continue("view.php?id=$cm->id#sid=$submission->id");
    }


    /******************* show submission ************************************/
    elseif ($action == 'showsubmission' ) {

        if (empty($sid)) {
            error("Show submission: submission id missing");
        }

        $submission = get_record("workshop_submissions", "id", $sid);
        $title = '"'.$submission->title.'" ';
        if (workshop_is_teacher($workshop)) {
            $title .= get_string('by', 'workshop').' '.workshop_fullname($submission->userid, $course->id);
        }
        print_heading($title);
        echo '<div style="text-align:center">'.get_string('submitted', 'workshop').': '.userdate($submission->timecreated).'</div><br />';
        workshop_print_submission($workshop, $submission);
        print_continue(htmlentities($_SERVER['HTTP_REFERER'].'#sid='.$submission->id));
    }


    /*************** update (league table options teacher) ***************************/
    elseif ($action == 'updateleaguetable') {

        require_capability('mod/workshop:manage', $context);

        // save number of entries in showleaguetable option
        if ($nentries == 'All') {
            $nentries = 99;
        }
        set_field("workshop", "showleaguetable", $nentries, "id", "$workshop->id");

        // save the anonymous option
        set_field("workshop", "anonymous", $anonymous, "id", "$workshop->id");
        add_to_log($course->id, "workshop", "league table", "view.php?id=$cm->id", $nentries, $cm->id);

        redirect("view.php?id=$cm->id");
    }


    /*************** update submission ***************************/
    elseif ($action == 'updatesubmission') {

        require_sesskey();
        if (empty($sid)) {
            error("Update submission: submission id missing");
        }
        $submission = get_record("workshop_submissions", "id", $sid);

        // students are only allowed to update their own submission and only up to the deadline
        if (!(workshop_is_teacher($workshop) or
               (($USER->id = $submission->userid) and ($timenow < $workshop->submissionend)
                   and (($timenow < $workshop->assessmentstart) or ($timenow < $submission->timecreated + $CFG->maxeditingtime))))) {
            error("You are not authorized to update your submission");
        }

        // check existence of title
        if (empty($title)) {
            $title = get_string("notitle", "workshop");
        }
        set_field("workshop_submissions", "title", $title, "id", $submission->id);
        set_field("workshop_submissions", "description", trim($description), "id", $submission->id);
        set_field("workshop_submissions", "timecreated", $timenow, "id", $submission->id);
        if ($workshop->nattachments) {
            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager(null,false,false,$course,false,$workshop->maxbytes);
            if ($um->preprocess_files()) {
                $dir = workshop_file_area_name($workshop, $submission);
                if ($um->save_files($dir)) {
                    add_to_log($course->id, "workshop", "newattachment", "view.php?id=$cm->id", "$workshop->id");
                    print_heading(get_string("uploadsuccess", "workshop"));
                }
                // upload manager will print errors.
            }
            print_continue("view.php?id=$cm->id");
        } else {
            echo '</div>'; // close <div id='page'>
            redirect("view.php?id=$cm->id#sid=$submission->id");
        }
    }


    /*************** no man's land **************************************/

    else {

        error("Fatal Error: Unknown Action: ".$action."\n");

    }


    print_footer($course);

?>
