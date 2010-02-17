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

        if ($files = $fs->get_area_files($this->context->id, 'assignment_submission', $userid, "timemodified", false)) {

            foreach ($files as $file) {
                $filename = $file->get_filename();
                $found = true;
                $mimetype = $file->get_mimetype();
                $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/assignment_submission/'.$userid.'/'.$filename);
                $output .= '<a href="'.$path.'" ><img class="icon" src="'.$OUTPUT->pix_url(file_mimetype_icon($mimetype)).'" alt="'.$mimetype.'" />'.s($filename).'</a><br />';
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

        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) {
            if ($submission->timemarked) {
                $this->view_feedback();
            }
            if ($filecount) {
                echo $OUTPUT->box($this->print_user_files($USER->id, true), 'generalbox boxaligncenter');
            }
        }

        if (has_capability('mod/assignment:submit', $context)  && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }

        $this->view_footer();
    }


    function view_upload_form() {
        $mform = new mod_assignment_upload_file_form('upload.php', $this);
        $mform->display();
    }


    function upload() {
        global $CFG, $USER, $DB, $OUTPUT;

        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));

        $this->view_header(get_string('upload'));

        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    echo $OUTPUT->notification(get_string('alreadygraded', 'assignment'));
                }
            }

            $mform = new mod_assignment_upload_file_form('upload.php', $this);
            if ($mform->get_data()) {
                $fs = get_file_storage();
                $filename = $mform->get_new_filename('newfile');
                if ($filename !== false) {
                    $fs->delete_area_files($this->context->id, 'assignment_submission', $USER->id);
                    if ($file = $mform->save_stored_file('newfile', $this->context->id, 'assignment_submission', $USER->id, '/', $filename, false, $USER->id)) {
                        $submission = $this->get_submission($USER->id, true); //create new submission if needed
                        $submission->timemodified = time();
                        $submission->numfiles     = 1;
                        if ($DB->update_record('assignment_submissions', $submission)) {
                            add_to_log($this->course->id, 'assignment', 'upload',
                                    'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                            $this->update_grade($submission);
                            $this->email_teachers($submission);
                            echo $OUTPUT->heading(get_string('uploadedfile'));
                            redirect('view.php?id='.$this->cm->id);
                        } else {
                            echo $OUTPUT->notification(get_string("uploadnotregistered", "assignment", $newfile_name) );
                            $file->delete();
                        }
                    }
                }
            } else {
                echo $OUTPUT->notification(get_string("uploaderror", "assignment")); //submitting not allowed!
            }
        }

        echo $OUTPUT->continue_button('view.php?id='.$this->cm->id);

        $this->view_footer();
    }

    function setup_elements(&$mform) {
        global $CFG, $COURSE;

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));

        $mform->addElement('select', 'resubmit', get_string("allowresubmit", "assignment"), $ynoptions);
        $mform->setHelpButton('resubmit', array('resubmit', get_string('allowresubmit', 'assignment'), 'assignment'));
        $mform->setDefault('resubmit', 0);

        $mform->addElement('select', 'emailteachers', get_string("emailteachers", "assignment"), $ynoptions);
        $mform->setHelpButton('emailteachers', array('emailteachers', get_string('emailteachers', 'assignment'), 'assignment'));
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

        $userid = (int)array_shift($args);
        $relativepath = '/'.implode('/', $args);
        $fullpath = $this->context->id.$filearea.$userid.$relativepath;

        $fs = get_file_storage();

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        if ($filearea === 'assignment_submission') {
            if ($USER->id != $userid and !has_capability('mod/assignment:grade', $this->context)) {
                return false;
            }

        } else {
            return false;
        }

        send_stored_file($file, 0, 0, true); // download MUST be forced - security!
    }

    function extend_settings_navigation($node) {
        global $CFG, $USER, $OUTPUT;

        // get users submission if there is one
        $submission = $this->get_submission();
        if (has_capability('mod/assignment:submit', $this->cm->context)) {
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
                $key = $node->add(get_string('submitted', 'assignment') . ' ' . userdate($submission->timemodified));
                $node->get($key)->text = preg_replace('#([^,])\s#', '$1&nbsp;', $node->get($key)->text);
                $node->get($key)->add_class('note');
                if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                    $node->get($key)->add_class('early');
                } else {
                    $node->get($key)->add_class('late');
                }
            }
        }

        // Check if the user has uploaded any files, if so we can add some more stuff to the settings nav
        if ($submission && has_capability('mod/assignment:submit', $this->context) && $this->count_user_files($USER->id)) {
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($this->context->id, 'assignment_submission', $USER->id, "timemodified", false)) {
                $filekey = $node->add(get_string('submission', 'assignment'));
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $mimetype = $file->get_mimetype();
                    $link = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/assignment_submission/'.$USER->id.'/'.$filename);
                    $node->get($filekey)->add($filename, $link, navigation_node::TYPE_SETTING, null, null, new pix_icon(file_mimetype_icon($mimetype), ''));
                }
            }
        }
    }
}


