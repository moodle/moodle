<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 * @package   mod-assignment
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/mod/assignment/lib.php');
require_once(dirname(__FILE__).'/upload_form.php');

class assignment_uploadsingle extends assignment_base {


    function print_student_answer($userid, $return=false){
        global $CFG, $USER, $OUTPUT;

        $fs = get_file_storage();
        $browser = get_file_browser();

        $output = '';

        if ($submission = $this->get_submission($userid)) {
            if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false)) {
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $found = true;
                    $mimetype = $file->get_mimetype();
                    $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_assignment/submission/'.$submission->id.'/'.$filename);
                    $output .= '<a href="'.$path.'" >'.$OUTPUT->pix_icon(file_file_icon($file), get_mimetype_description($file), 'moodle', array('class' => 'icon')).s($filename).'</a><br />';
                    $output .= plagiarism_get_links(array('userid'=>$userid, 'file'=>$file, 'cmid'=>$this->cm->id, 'course'=>$this->course, 'assignment'=>$this->assignment));
                    $output .='<br/>';
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

        $context = context_module::instance($this->cm->id);
        require_capability('mod/assignment:view', $context);

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

        $this->view_header();

        $this->view_intro();

        $this->view_dates();

        $filecount = false;

        if ($submission = $this->get_submission($USER->id)) {
            $filecount = $this->count_user_files($submission->id);
            if ($submission->timemarked) {
                $this->view_feedback($submission);
            }
            if ($filecount) {
                echo $OUTPUT->box($this->print_user_files($USER->id, true), 'generalbox boxaligncenter');
            }
        }

        if (is_enrolled($this->context, $USER, 'mod/assignment:submit') && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }

        $this->view_footer();
    }

    /**
     * Display the response file to the student
     *
     * This default method prints the response file
     *
     * @param object $submission The submission object
     */
    function view_responsefile($submission) {
        $fs = get_file_storage();
        $noresponsefiles = $fs->is_area_empty($this->context->id, 'mod_assignment', 'response', $submission->id);
        if (!$noresponsefiles) {
            echo '<tr>';
            echo '<td class="left side">&nbsp;</td>';
            echo '<td class="content">';
            echo $this->print_responsefiles($submission->userid);
            echo '</td></tr>';
        }
    }

    function process_feedback($formdata=null) {
        if (!$feedback = data_submitted() or !confirm_sesskey()) {      // No incoming data?
            return false;
        }
        $userid = required_param('userid', PARAM_INT);
        $offset = required_param('offset', PARAM_INT);
        $mform = $this->display_submission($offset, $userid, false);
        parent::process_feedback($mform);
    }

    /**
     * Counts all complete (real) assignment submissions by enrolled students. This overrides assignment_base::count_real_submissions().
     * This is necessary for simple file uploads where we need to check that the numfiles field is greater than zero to determine if a
     * submission is complete.
     *
     * @param  int $groupid (optional) If nonzero then count is restricted to this group
     * @return int          The number of submissions
     */
    function count_real_submissions($groupid=0) {
        global $DB;

        // Grab the context assocated with our course module
        $context = context_module::instance($this->cm->id);

        // Get ids of users enrolled in the given course.
        list($enroledsql, $params) = get_enrolled_sql($context, 'mod/assignment:submit', $groupid);
        $params['assignmentid'] = $this->cm->instance;

        // Get ids of users enrolled in the given course.
        return $DB->count_records_sql("SELECT COUNT('x')
                                         FROM {assignment_submissions} s
                                    LEFT JOIN {assignment} a ON a.id = s.assignment
                                   INNER JOIN ($enroledsql) u ON u.id = s.userid
                                        WHERE s.assignment = :assignmentid AND
                                              s.numfiles > 0", $params);
    }

    function print_responsefiles($userid, $return=false) {
        global $OUTPUT, $PAGE;

        $output = $OUTPUT->box_start('responsefiles');

        if ($submission = $this->get_submission($userid)) {
            $renderer = $PAGE->get_renderer('mod_assignment');
            $output .= $renderer->assignment_files($this->context, $submission->id, 'response');
        }
        $output .= $OUTPUT->box_end();

        if ($return) {
            return $output;
        }
        echo $output;
    }

    function can_manage_responsefiles() {
        if (has_capability('mod/assignment:grade', $this->context)) {
            return true;
        } else {
            return false;
        }
    }

    function view_upload_form() {
        global $OUTPUT, $USER;
        echo $OUTPUT->box_start('uploadbox');
        $fs = get_file_storage();
        // edit files in another page
        if ($submission = $this->get_submission($USER->id)) {
            if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false)) {
                $str = get_string('editthisfile', 'assignment');
            } else {
                $str = get_string('uploadafile', 'assignment');
            }
        } else {
            $str = get_string('uploadafile', 'assignment');
        }
        echo $OUTPUT->single_button(new moodle_url('/mod/assignment/type/uploadsingle/upload.php', array('contextid'=>$this->context->id, 'userid'=>$USER->id)), $str, 'get');
        echo $OUTPUT->box_end();
    }

    function upload($mform) {
        $action = required_param('action', PARAM_ALPHA);
        switch ($action) {
            case 'uploadresponse':
                $this->upload_responsefile($mform);
                break;
            case 'uploadfile':
                $this->upload_file($mform);
        }
    }

    function upload_file($mform) {
        global $CFG, $USER, $DB, $OUTPUT;
        $viewurl = new moodle_url('/mod/assignment/view.php', array('id'=>$this->cm->id));
        if (!is_enrolled($this->context, $USER, 'mod/assignment:submit')) {
            redirect($viewurl);
        }

        $submission = $this->get_submission($USER->id);
        $filecount = 0;
        if ($submission) {
            $filecount = $this->count_user_files($submission->id);
        }
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    redirect($viewurl, get_string('alreadygraded', 'assignment'));
                }
            }

            if ($formdata = $mform->get_data()) {
                $fs = get_file_storage();
                $submission = $this->get_submission($USER->id, true); //create new submission if needed
                $fs->delete_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id);

                if ($newfilename = $mform->get_new_filename('assignment_file')) {
                    $file = $mform->save_stored_file('assignment_file', $this->context->id, 'mod_assignment', 'submission',
                        $submission->id, '/', $newfilename);

                    $updates = new stdClass(); //just enough data for updating the submission
                    $updates->timemodified = time();
                    $updates->numfiles     = 1;
                    $updates->id     = $submission->id;
                    $DB->update_record('assignment_submissions', $updates);
                    add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                    $this->update_grade($submission);
                    $this->email_teachers($submission);

                    // Let Moodle know that an assessable file was uploaded (eg for plagiarism detection)
                    $eventdata = new stdClass();
                    $eventdata->modulename   = 'assignment';
                    $eventdata->cmid         = $this->cm->id;
                    $eventdata->itemid       = $submission->id;
                    $eventdata->courseid     = $this->course->id;
                    $eventdata->userid       = $USER->id;
                    $eventdata->file         = $file; // This is depreceated - please use pathnamehashes instead!
                    $eventdata->pathnamehashes = array($file->get_pathnamehash());
                    events_trigger('assessable_file_uploaded', $eventdata);
                }

                redirect($viewurl, get_string('uploadedfile'));
            } else {
                redirect($viewurl, get_string('uploaderror', 'assignment'));  //submitting not allowed!
            }
        }

        redirect($viewurl);
    }

    function upload_responsefile($mform) {
        global $CFG, $USER, $OUTPUT, $PAGE;

        $userid = required_param('userid', PARAM_INT);
        $mode   = required_param('mode', PARAM_ALPHA);
        $offset = required_param('offset', PARAM_INT);

        $returnurl = new moodle_url("/mod/assignment/submissions.php", array('id'=>$this->cm->id,'userid'=>$userid,'mode'=>$mode,'offset'=>$offset)); //not xhtml, just url.

        if ($formdata = $mform->get_data() and $this->can_manage_responsefiles()) {
            $fs = get_file_storage();
            $submission = $this->get_submission($userid, true); //create new submission if needed
            $fs->delete_area_files($this->context->id, 'mod_assignment', 'response', $submission->id);

            if ($newfilename = $mform->get_new_filename('assignment_file')) {
                $file = $mform->save_stored_file('assignment_file', $this->context->id,
                        'mod_assignment', 'response',$submission->id, '/', $newfilename);
            }
            redirect($returnurl, get_string('uploadedfile'));
        } else {
            redirect($returnurl, get_string('uploaderror', 'assignment'));  //submitting not allowed!
        }
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
        $mform->addElement('select', 'maxbytes', get_string('maximumsize', 'assignment'), $choices);
        $mform->setDefault('maxbytes', $CFG->assignment_maxbytes);

        $course_context = context_course::instance($COURSE->id);
        plagiarism_get_form_elements_module($mform, $course_context, 'mod_assignment');
    }

    function portfolio_exportable() {
        return true;
    }

    function send_file($filearea, $args, $forcedownload, array $options=array()) {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir.'/filelib.php');

        require_login($this->course, false, $this->cm);

        if ($filearea !== 'submission' && $filearea !== 'response') {
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
        $fullpath = '/'.$this->context->id.'/mod_assignment/'.$filearea.'/'.$submissionid.'/'.$relativepath;

        $fs = get_file_storage();

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
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

        // If the user has submitted something add some related links and data
        if (isset($submission->numfiles) AND $submission->numfiles) {
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
        if ($submission && is_enrolled($this->context, $USER, 'mod/assignment:submit') && $this->count_user_files($submission->id)) {
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', 'submission', $submission->id, "timemodified", false)) {
                $filenode = $node->add(get_string('submission', 'assignment'));
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $mimetype = $file->get_mimetype();
                    $link = moodle_url::make_pluginfile_url($this->context->id, 'mod_assignment', 'submission', $submission->id, $file->get_filepath(), $filename);
                    $filenode->add($filename, $link, navigation_node::TYPE_SETTING, null, null, new pix_icon(file_file_icon($file), ''));
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
            print_error('errornosubmissions', 'assignment');
        }
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->cm);
        $groupid = 0;   // All users
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->cm, true);
            $groupname = groups_get_group_name($groupid).'-';
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
                    $fileext = strstr($file->get_filename(), '.');
                    $fileoriginal = str_replace($fileext, '', $file->get_filename());
                    $fileforzipname =  clean_filename(fullname($a_user) . "_" . $fileoriginal."_".$a_userid.$fileext);
                    //save file name to array for zipping.
                    $filesforzipping[$fileforzipname] = $file;
                }
            }
        } // End of foreach
        if ($zipfile = assignment_pack_files($filesforzipping)) {
            send_temp_file($zipfile, $filename); //send file and delete after sending.
        }
    }

    /**
     * Check the given submission is complete. Preliminary rows are often created in the assignment_submissions
     * table before a submission actually takes place. This function checks to see if the given submission has actually
     * been submitted.
     *
     * @param  stdClass $submission The submission we want to check for completion
     * @return bool                 Indicates if the submission was found to be complete
     */
    public function is_submitted_with_required_data($submission) {
        return ($submission->timemodified AND $submission->numfiles > 0);
    }
}

class mod_assignment_uploadsingle_response_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        // visible elements
        $mform->addElement('filepicker', 'assignment_file', get_string('uploadafile'), null, $instance->options);

        // hidden params
        $mform->addElement('hidden', 'id', $instance->cm->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'contextid', $instance->contextid);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'uploadresponse');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'mode', $instance->mode);
        $mform->setType('mode', PARAM_ALPHA);
        $mform->addElement('hidden', 'offset', $instance->offset);
        $mform->setType('offset', PARAM_INT);
        $mform->addElement('hidden', 'forcerefresh' , $instance->forcerefresh);
        $mform->setType('forcerefresh', PARAM_INT);
        $mform->addElement('hidden', 'userid', $instance->userid);
        $mform->setType('userid', PARAM_INT);

        // buttons
        $this->add_action_buttons(false, get_string('uploadthisfile'));
    }
}
