<?php

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_uploadsingle extends assignment_base {


    function print_student_answer($userid, $return=false){
        global $CFG, $USER, $OUTPUT;

        $fs = get_file_storage();
        $browser = get_file_browser();

        $output = '';

        if ($submission = $this->get_submission($USER->id)) {
            if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false)) {
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $found = true;
                    $mimetype = $file->get_mimetype();
                    $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_assignment/submission/'.$submission->id.'/'.$filename);
                    $output .= '<a href="'.$path.'" ><img class="icon" src="'.$OUTPUT->pix_url(file_mimetype_icon($mimetype)).'" alt="'.$mimetype.'" />'.s($filename).'</a><br />';
                }
            }
        }

        $output = '<div class="files">'.$output.'</div>';
        return $output;
    }

    function assignment_uploadsingle($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
        $this->type = 'uploadsingle';
    }

    function view() {

        global $USER, $OUTPUT;

        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        require_capability('mod/assignment:view', $context);

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

        $this->view_header();

        $this->view_intro();

        $this->view_dates();

        $filecount = false;

        if ($submission = $this->get_submission($USER->id)) {
            $filecount = $this->count_user_files($submission->id);
            if ($submission->timemarked) {
                $this->view_feedback();
            }
            if ($filecount) {
                echo $OUTPUT->box($this->print_user_files($submission->id, true), 'generalbox boxaligncenter');
            }
        }

        if (is_enrolled($this->context, $USER, 'mod/assignment:submit') && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }

        $this->view_footer();
    }


    function view_upload_form() {
        $mform = new mod_assignment_upload_file_form('upload.php', $this);
		echo "<div class=\"uploadbox\">";
        $mform->display();
        echo "</div>";
    }


    function upload() {
        global $CFG, $USER, $DB, $OUTPUT;

        if (!is_enrolled($this->context, $USER, 'mod/assignment:submit')) {
            redirect('view.php?id='.$this->cm->id);
        }

        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    redirect('view.php?id='.$this->cm->id, get_string('alreadygraded', 'assignment'));
                }
            }

            $mform = new mod_assignment_upload_file_form('upload.php', $this);
            if ($mform->get_data()) {
                $fs = get_file_storage();
                $filename = $mform->get_new_filename('newfile');
                if ($filename !== false) {
                    $submission = $this->get_submission($USER->id, true); //create new submission if needed
                    $fs->delete_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id);

                    if ($file = $mform->save_stored_file('newfile', $this->context->id, 'mod_assignment', 'submission', $submission->id, '/', $filename, false, $USER->id)) {
                        $updates = new object(); //just enough data for updating the submission
                        $updates->timemodified = time();
                        $updates->numfiles     = 1;
                        $updates->id     = $submission->id;
                        $DB->update_record('assignment_submissions', $updates);
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $this->update_grade($submission);
                        $this->email_teachers($submission);

                        // Let Moodle know that an assessable file was uploaded (eg for plagiarism detection)
                        $eventdata = new object();
                        $eventdata->modulename   = 'assignment';
                        $eventdata->cmid         = $this->cm->id;
                        $eventdata->itemid       = $submission->id;
                        $eventdata->courseid     = $this->course->id;
                        $eventdata->userid       = $USER->id;
                        $eventdata->file         = $file;
                        events_trigger('assessable_file_uploaded', $eventdata);

                        redirect('view.php?id='.$this->cm->id, get_string('uploadedfile'));
                    }
                }
            } else {
                redirect('view.php?id='.$this->cm->id, get_string('uploaderror', 'assignment'));  //submitting not allowed!
            }
        }

        redirect('view.php?id='.$this->cm->id);
    }

    function setup_elements(&$mform) {
        global $CFG, $COURSE;

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));

        $mform->addElement('select', 'resubmit', get_string('allowresubmit', 'assignment'), $ynoptions);
        $mform->addHelpButton('resubmit', 'allowresubmit', 'assignment');
        $mform->setDefault('resubmit', 0);

        $mform->addElement('select', 'emailteachers', get_string('emailteachers', 'assignment'), $ynoptions);
        $mform->addHelpButton('emailteachers', 'emailteachers', 'assignment');
        $mform->setDefault('emailteachers', 0);

        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        $choices[0] = get_string('courseuploadlimit') . ' ('.display_size($COURSE->maxbytes).')';
        $mform->addElement('select', 'maxbytes', get_string('maximumsize', 'assignment'), $choices);
        $mform->setDefault('maxbytes', $CFG->assignment_maxbytes);

    }

    function portfolio_exportable() {
        return true;
    }

    function send_file($filearea, $args) {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir.'/filelib.php');

        require_login($this->course, false, $this->cm);

        if ($filearea !== 'submission') {
            return false;
        }

        $submissionid = (int)array_shift($args);

        if (!$submission = $DB->get_record('assignment_submissions', array('assignment'=>$this->assignment->id, 'id'=>$submissionid))) {
            return false;
        }

        if ($USER->id != $submission->userid and !has_capability('mod/assignment:grade', $this->context)) {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = "/$this->context->id/mod_assignment/submission/$submissionid/$relativepath";

        $fs = get_file_storage();

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, true); // download MUST be forced - security!
    }

    function extend_settings_navigation($node) {
        global $CFG, $USER, $OUTPUT;

        // get users submission if there is one
        $submission = $this->get_submission();
        if (is_enrolled($this->context, $USER, 'mod/assignment:submit')) {
            $editable = $this->isopen() && (!$submission || $this->assignment->resubmit || !$submission->timemarked);
        } else {
            $editable = false;
        }

        // If the user has submitted something add a bit more stuff
        if ($submission) {
            // Add a view link to the settings nav
            $link = new moodle_url('/mod/assignment/view.php', array('id'=>$this->cm->id));
            $node->add(get_string('viewmysubmission', 'assignment'), $link, navigation_node::TYPE_SETTING);
            if (!empty($submission->timemodified)) {
                $submissionnode = $node->add(get_string('submitted', 'assignment') . ' ' . userdate($submission->timemodified));
                $submissionnode->text = preg_replace('#([^,])\s#', '$1&nbsp;', $submissionnode->text);
                $submissionnode->add_class('note');
                if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                    $submissionnode->add_class('early');
                } else {
                    $submissionnode->add_class('late');
                }
            }
        }

        // Check if the user has uploaded any files, if so we can add some more stuff to the settings nav
        if ($submission && is_enrolled($this->context, $USER, 'mod/assignment:submit') && $this->count_user_files($USER->id)) {
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false)) {
                $filenode = $node->add(get_string('submission', 'assignment'));
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $mimetype = $file->get_mimetype();
                    $link = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_assignment', 'submission/'.$submission->id.'/'.$filename);
                    $filenode->add($filename, $link, navigation_node::TYPE_SETTING, null, null, new pix_icon(file_mimetype_icon($mimetype), ''));
                }
            }
        }
    }

    /**
     * creates a zip of all assignment submissions and sends a zip to the browser
     */
    function download_submissions() {
        global $CFG,$DB;
        require_once($CFG->libdir.'/filelib.php');

        $submissions = $this->get_submissions('','');
        if (empty($submissions)) {
            error("there are no submissions to download");
        }
        $filesforzipping = array();
        $filenewname = clean_filename($this->assignment->name); //create prefix of individual files
        $fs = get_file_storage();

        $groupmode = groupmode($this->course,$this->cm);
        $groupid = 0;   // All users
        $groupname = '';
        if($groupmode) {
            $group = get_current_group($this->course->id, true);
            $groupid = $group->id;
            $groupname = $group->name.'-';
        }
        $filename = str_replace(' ', '_', clean_filename($this->course->shortname.'-'.$this->assignment->name.'-'.$groupname.$this->assignment->id.".zip")); //name of new zip file.
        foreach ($submissions as $submission) {
            $a_userid = $submission->userid; //get userid
            if ((groups_is_member($groupid,$a_userid)or !$groupmode or !$groupid)) {
                $a_assignid = $submission->assignment; //get name of this assignment for use in the file names.
                $a_user = $DB->get_record("user", array("id"=>$a_userid),'id,username,firstname,lastname'); //get user firstname/lastname

                $files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false);
                foreach ($files as $file) {
                    //get files new name.
                    $fileforzipname =  $a_user->username . "_" . $filenewname . "_" . $file->get_filename();
                    //save file name to array for zipping.
                    $filesforzipping[$fileforzipname] = $file;
                }
            }
        } // End of foreach
        if ($zipfile = assignment_pack_files($filesforzipping)) {
            send_temp_file($zipfile, $filename); //send file and delete after sending.
        }
    }
}


