<?php
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->dirroot . '/mod/assignment/lib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_online extends assignment_base {

    var $filearea = 'submission';

    function assignment_online($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
        $this->type = 'online';
    }

    function view() {
        global $OUTPUT, $CFG, $USER, $PAGE;

        $edit  = optional_param('edit', 0, PARAM_BOOL);
        $saved = optional_param('saved', 0, PARAM_BOOL);

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        require_capability('mod/assignment:view', $context);

        $submission = $this->get_submission($USER->id, false);

        //Guest can not submit nor edit an assignment (bug: 4604)
        if (!is_enrolled($this->context, $USER, 'mod/assignment:submit')) {
            $editable = false;
        } else {
            $editable = $this->isopen() && (!$submission || $this->assignment->resubmit || !$submission->timemarked);
        }
        $editmode = ($editable and $edit);

        if ($editmode) {
            // prepare form and process submitted data
            $editoroptions = array(
                'noclean'  => false,
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'maxbytes' => $this->course->maxbytes,
                'context'  => $this->context
            );

            $data = new stdClass();
            $data->id         = $this->cm->id;
            $data->edit       = 1;
            if ($submission) {
                $data->sid        = $submission->id;
                $data->text       = $submission->data1;
                $data->textformat = $submission->data2;
            } else {
                $data->sid        = NULL;
                $data->text       = '';
                $data->textformat = NULL;
            }

            $data = file_prepare_standard_editor($data, 'text', $editoroptions, $this->context, 'mod_assignment', $this->filearea, $data->sid);

            $mform = new mod_assignment_online_edit_form(null, array($data, $editoroptions));

            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            }

            if ($data = $mform->get_data()) {
                $submission = $this->get_submission($USER->id, true); //create the submission if needed & its id

                $data = file_postupdate_standard_editor($data, 'text', $editoroptions, $this->context, 'mod_assignment', $this->filearea, $submission->id);

                $submission = $this->update_submission($data);

                //TODO fix log actions - needs db upgrade
                add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                $this->email_teachers($submission);

                //redirect to get updated submission date and word count
                redirect(new moodle_url($PAGE->url, array('saved'=>1)));
            }
        }

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

/// print header, etc. and display form if needed
        if ($editmode) {
            $this->view_header(get_string('editmysubmission', 'assignment'));
        } else {
            $this->view_header();
        }

        $this->view_intro();

        $this->view_dates();

        if ($saved) {
            echo $OUTPUT->notification(get_string('submissionsaved', 'assignment'), 'notifysuccess');
        }

        if (is_enrolled($this->context, $USER)) {
            if ($editmode) {
                echo $OUTPUT->box_start('generalbox', 'onlineenter');
                $mform->display();
            } else {
                echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter', 'online');
                if ($submission && has_capability('mod/assignment:exportownsubmission', $this->context)) {
                    $text = file_rewrite_pluginfile_urls($submission->data1, 'pluginfile.php', $this->context->id, 'mod_assignment', $this->filearea, $submission->id);
                    echo format_text($text, $submission->data2, array('overflowdiv'=>true));
                    if ($CFG->enableportfolios) {
                        require_once($CFG->libdir . '/portfoliolib.php');
                        $button = new portfolio_add_button();
                        $button->set_callback_options('assignment_portfolio_caller', array('id' => $this->cm->id), '/mod/assignment/locallib.php');
                        $fs = get_file_storage();
                        if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', $this->filearea, $submission->id, "timemodified", false)) {
                            $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
                        } else {
                            $button->set_formats(PORTFOLIO_FORMAT_PLAINHTML);
                        }
                        $button->render();
                    }
                } else if ($this->isopen()){    //fix for #4206
                    echo '<div style="text-align:center">'.get_string('emptysubmission', 'assignment').'</div>';
                }
            }
            echo $OUTPUT->box_end();
            if (!$editmode && $editable) {
                if (!empty($submission)) {
                    $submitbutton = "editmysubmission";
                } else {
                    $submitbutton = "addsubmission";
                }
                echo "<div style='text-align:center'>";
                echo $OUTPUT->single_button(new moodle_url('view.php', array('id'=>$this->cm->id, 'edit'=>'1')), get_string($submitbutton, 'assignment'));
                echo "</div>";
            }

        }

        $this->view_feedback();

        $this->view_footer();
    }

    /*
     * Display the assignment dates
     */
    function view_dates() {
        global $USER, $CFG, $OUTPUT;

        if (!$this->assignment->timeavailable && !$this->assignment->timedue) {
            return;
        }

        echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
        echo '<table>';
        if ($this->assignment->timeavailable) {
            echo '<tr><td class="c0">'.get_string('availabledate','assignment').':</td>';
            echo '    <td class="c1">'.userdate($this->assignment->timeavailable).'</td></tr>';
        }
        if ($this->assignment->timedue) {
            echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
            echo '    <td class="c1">'.userdate($this->assignment->timedue).'</td></tr>';
        }
        $submission = $this->get_submission($USER->id);
        if ($submission) {
            echo '<tr><td class="c0">'.get_string('lastedited').':</td>';
            echo '    <td class="c1">'.userdate($submission->timemodified);
        /// Decide what to count
            if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_WORDS) {
                echo ' ('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')</td></tr>';
            } else if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_LETTERS) {
                echo ' ('.get_string('numletters', '', count_letters(format_text($submission->data1, $submission->data2))).')</td></tr>';
            }
        }
        echo '</table>';
        echo $OUTPUT->box_end();
    }

    function update_submission($data) {
        global $CFG, $USER, $DB;

        $submission = $this->get_submission($USER->id, true);

        $update = new stdClass();
        $update->id           = $submission->id;
        $update->data1        = $data->text;
        $update->data2        = $data->textformat;
        $update->timemodified = time();

        $DB->update_record('assignment_submissions', $update);

        $submission = $this->get_submission($USER->id);
        $this->update_grade($submission);
        return $submission;
    }


    function print_student_answer($userid, $return=false){
        global $OUTPUT;
        if (!$submission = $this->get_submission($userid)) {
            return '';
        }

        $link = new moodle_url("/mod/assignment/type/online/file.php?id={$this->cm->id}&userid={$submission->userid}");
        $action = new popup_action('click', $link, 'file'.$userid, array('height' => 450, 'width' => 580));
        $popup = $OUTPUT->action_link($link, shorten_text(trim(strip_tags(format_text($submission->data1,$submission->data2))), 15), $action, array('title'=>get_string('submission', 'assignment')));

        $output = '<div class="files">'.
                  '<img src="'.$OUTPUT->pix_url('f/html') . '" class="icon" alt="html" />'.
                  $popup .
                  '</div>';
                  return $output;
    }

    function print_user_files($userid, $return=false) {
        global $OUTPUT, $CFG;

        if (!$submission = $this->get_submission($userid)) {
            return '';
        }

        $link = new moodle_url("/mod/assignment/type/online/file.php?id={$this->cm->id}&userid={$submission->userid}");
        $action = new popup_action('click', $link, 'file'.$userid, array('height' => 450, 'width' => 580));
        $popup = $OUTPUT->action_link($link, get_string('popupinnewwindow','assignment'), $action, array('title'=>get_string('submission', 'assignment')));

        $output = '<div class="files">'.
                  '<img align="middle" src="'.$OUTPUT->pix_url('f/html') . '" height="16" width="16" alt="html" />'.
                  $popup .
                  '</div>';

        $wordcount = '<p id="wordcount">'. $popup . '&nbsp;';
    /// Decide what to count
        if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_WORDS) {
            $wordcount .= '('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')';
        } else if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_LETTERS) {
            $wordcount .= '('.get_string('numletters', '', count_letters(format_text($submission->data1, $submission->data2))).')';
        }
        $wordcount .= '</p>';

        $text = file_rewrite_pluginfile_urls($submission->data1, 'pluginfile.php', $this->context->id, 'mod_assignment', $this->filearea, $submission->id);
        return $wordcount . format_text($text, $submission->data2, array('overflowdiv'=>true));


        }

    function preprocess_submission(&$submission) {
        if ($this->assignment->var1 && empty($submission->submissioncomment)) {  // comment inline
            if ($this->usehtmleditor) {
                // Convert to html, clean & copy student data to teacher
                $submission->submissioncomment = format_text($submission->data1, $submission->data2);
                $submission->format = FORMAT_HTML;
            } else {
                // Copy student data to teacher
                $submission->submissioncomment = $submission->data1;
                $submission->format = $submission->data2;
            }
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

        $mform->addElement('select', 'var1', get_string('commentinline', 'assignment'), $ynoptions);
        $mform->addHelpButton('var1', 'commentinline', 'assignment');
        $mform->setDefault('var1', 0);

    }

    function portfolio_exportable() {
        return true;
    }

    function portfolio_load_data($caller) {
        $submission = $this->get_submission();
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($this->context->id, 'mod_assignment', $this->filearea, $submission->id, "timemodified", false)) {
            $caller->set('multifiles', $files);
        }
    }

    function portfolio_get_sha1($caller) {
        $submission = $this->get_submission();
        $textsha1 = sha1(format_text($submission->data1, $submission->data2));
        $filesha1 = '';
        try {
            $filesha1 = $caller->get_sha1_file();
        } catch (portfolio_caller_exception $e) {} // no files
        return sha1($textsha1 . $filesha1);
    }

    function portfolio_prepare_package($exporter, $user) {
        $submission = $this->get_submission($user->id);
        $options = portfolio_format_text_options();
        $html = format_text($submission->data1, $submission->data2, $options);
        $html = portfolio_rewrite_pluginfile_urls($html, $this->context->id, 'mod_assignment', $this->filearea, $submission->id, $exporter->get('format'));
        if (in_array($exporter->get('formatclass'), array(PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_RICHHTML))) {
            if ($files = $exporter->get('caller')->get('multifiles')) {
                foreach ($files as $f) {
                    $exporter->copy_existing_file($f);
                }
            }
            return $exporter->write_new_file($html, 'assignment.html', !empty($files));
        } else if ($exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $exporter->get('format')->leap2a_writer();
            $entry = new portfolio_format_leap2a_entry('assignmentonline' . $this->assignment->id, $this->assignment->name, 'resource', $html);
            $entry->add_category('web', 'resource_type');
            $entry->published = $submission->timecreated;
            $entry->updated = $submission->timemodified;
            $entry->author = $user;
            $leapwriter->add_entry($entry);
            if ($files = $exporter->get('caller')->get('multifiles')) {
                $leapwriter->link_files($entry, $files, 'assignmentonline' . $this->assignment->id . 'file');
                foreach ($files as $f) {
                    $exporter->copy_existing_file($f);
                }
            }
            $exporter->write_new_file($leapwriter->to_xml(), $exporter->get('format')->manifest_name(), true);
        } else {
            debugging('invalid format class: ' . $exporter->get('formatclass'));
        }
    }

    function extend_settings_navigation($node) {
        global $PAGE, $CFG, $USER;

        // get users submission if there is one
        $submission = $this->get_submission();
        if (is_enrolled($PAGE->cm->context, $USER, 'mod/assignment:submit')) {
            $editable = $this->isopen() && (!$submission || $this->assignment->resubmit || !$submission->timemarked);
        } else {
            $editable = false;
        }

        // If the user has submitted something add a bit more stuff
        if ($submission) {
            // Add a view link to the settings nav
            $link = new moodle_url('/mod/assignment/view.php', array('id'=>$PAGE->cm->id));
            $node->add(get_string('viewmysubmission', 'assignment'), $link, navigation_node::TYPE_SETTING);

            if (!empty($submission->timemodified)) {
                $submittednode = $node->add(get_string('submitted', 'assignment') . ' ' . userdate($submission->timemodified));
                $submittednode->text = preg_replace('#([^,])\s#', '$1&nbsp;', $submittednode->text);
                $submittednode->add_class('note');
                if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                    $submittednode->add_class('early');
                } else {
                    $submittednode->add_class('late');
                }
            }
        }

        if (!$submission || $editable) {
            // If this assignment is editable once submitted add an edit link to the settings nav
            $link = new moodle_url('/mod/assignment/view.php', array('id'=>$PAGE->cm->id, 'edit'=>1, 'sesskey'=>sesskey()));
            $node->add(get_string('editmysubmission', 'assignment'), $link, navigation_node::TYPE_SETTING);
        }
    }

    public function send_file($filearea, $args) {
        global $USER;
        require_capability('mod/assignment:view', $this->context);

        $fullpath = "/{$this->context->id}/mod_assignment/$filearea/".implode('/', $args);

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        if (($USER->id != $file->get_userid()) && !has_capability('mod/assignment:grade', $this->context)) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, true);
    }

    /**
     * creates a zip of all assignment submissions and sends a zip to the browser
     */
    public function download_submissions() {
        global $CFG, $DB;

        raise_memory_limit(MEMORY_EXTRA);

        $submissions = $this->get_submissions('','');
        if (empty($submissions)) {
            print_error('errornosubmissions', 'assignment');
        }
        $filesforzipping = array();

        //NOTE: do not create any stuff in temp directories, we now support unicode file names and that would not work, sorry

        //online assignment can use html
        $filextn=".html";

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
                $submissioncontent = "<html><body>". format_text($submission->data1, $submission->data2). "</body></html>";      //fetched from database
                //get file name.html
                $fileforzipname =  clean_filename(fullname($a_user) . "_" .$a_userid.$filextn);
                $filesforzipping[$fileforzipname] = array($submissioncontent);
            }
        }      //end of foreach

        if ($zipfile = assignment_pack_files($filesforzipping)) {
            send_temp_file($zipfile, $filename); //send file and delete after sending.
        }
    }
}

class mod_assignment_online_edit_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        list($data, $editoroptions) = $this->_customdata;

        // visible elements
        $mform->addElement('editor', 'text_editor', get_string('submission', 'assignment'), null, $editoroptions);
        $mform->setType('text_editor', PARAM_RAW); // to be cleaned before display
        $mform->addRule('text_editor', get_string('required'), 'required', null, 'client');

        // hidden params
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'edit');
        $mform->setType('edit', PARAM_INT);

        // buttons
        $this->add_action_buttons();

        $this->set_data($data);
    }
}


