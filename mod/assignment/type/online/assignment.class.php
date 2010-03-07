<?php
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/mod/assignment/lib.php');
/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_online extends assignment_base {

    function assignment_online($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
        $this->type = 'online';
    }

    function view() {

        global $OUTPUT;

        $edit  = optional_param('edit', 0, PARAM_BOOL);
        $saved = optional_param('saved', 0, PARAM_BOOL);

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        require_capability('mod/assignment:view', $context);

        $submission = $this->get_submission();

        //Guest can not submit nor edit an assignment (bug: 4604)
        if (!has_capability('mod/assignment:submit', $context)) {
            $editable = null;
        } else {
            $editable = $this->isopen() && (!$submission || $this->assignment->resubmit || !$submission->timemarked);
        }
        $editmode = ($editable and $edit);

        if ($editmode) {
            //guest can not edit or submit assignment
            if (!has_capability('mod/assignment:submit', $context)) {
                print_error('guestnosubmit', 'assignment');
            }
        }

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

/// prepare form and process submitted data
        $mformdata = new stdClass;
        $mformdata->context = $this->context;
        $mformdata->maxbytes = $this->course->maxbytes;
        $mformdata->submission = $submission;
        $mform = new mod_assignment_online_edit_form(null, $mformdata);

        $defaults = new object();
        $defaults->id = $this->cm->id;
        if (!empty($submission)) {
            if ($this->usehtmleditor) {
                $options = new object();
                $options->smiley = false;
                $options->filter = false;

                $defaults->text   = format_text($submission->data1, $submission->data2, $options);
                $defaults->format = FORMAT_HTML;
            } else {
                $defaults->text   = clean_text($submission->data1, $submission->data2);
                $defaults->format = $submission->data2;
            }
        }
        $mform->set_data($defaults);

        if ($mform->is_cancelled()) {
            redirect('view.php?id='.$this->cm->id);
        }

        if ($data = $mform->get_data()) {      // No incoming data?
            if ($editable && $this->update_submission($data)) {
                //TODO fix log actions - needs db upgrade
                $submission = $this->get_submission();
                add_to_log($this->course->id, 'assignment', 'upload',
                        'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                $this->email_teachers($submission);
                //redirect to get updated submission date and word count
                redirect('view.php?id='.$this->cm->id.'&saved=1');
            } else {
                // TODO: add better error message
                echo $OUTPUT->notification(get_string("error")); //submitting not allowed!
            }
        }

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

        if (has_capability('mod/assignment:submit', $context)) {
            if ($editmode) {
                echo $OUTPUT->box_start('generalbox', 'online');
                $mform->display();
            } else {
                echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter', 'online');
                if ($submission && has_capability('mod/assignment:exportownsubmission', $this->context)) {
                    $text = file_rewrite_pluginfile_urls($submission->data1, 'pluginfile.php', $this->context->id, 'assignment_online_submission', $submission->id);
                    echo format_text($text, $submission->data2);
                    $button = new portfolio_add_button();
                    $button->set_callback_options('assignment_portfolio_caller', array('id' => $this->cm->id), '/mod/assignment/locallib.php');
                    $fs = get_file_storage();
                    if ($files = $fs->get_area_files($this->context->id, 'assignment_online_submission', $submission->id, "timemodified", false)) {
                        $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
                    } else {
                        $button->set_formats(PORTFOLIO_FORMAT_PLAINHTML);
                    }
                    $button->render();
                } else if (!has_capability('mod/assignment:submit', $context)) { //fix for #4604
                    echo '<div style="text-align:center">'. get_string('guestnosubmit', 'assignment').'</div>';
                } else if ($this->isopen()){    //fix for #4206
                    echo '<div style="text-align:center">'.get_string('emptysubmission', 'assignment').'</div>';
                }
            }
            echo $OUTPUT->box_end();
            if (!$editmode && $editable) {
                echo "<div style='text-align:center'>";
                echo $OUTPUT->single_button(new moodle_url('view.php', array('id'=>$this->cm->id, 'edit'=>'1')), get_string('editmysubmission', 'assignment'));
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

        $update = new object();
        $update->id           = $submission->id;
        $update->data1        = $data->text;
        $update->data2        = $data->format;
        $update->timemodified = time();

        $DB->update_record('assignment_submissions', $update);

        $submission = $this->get_submission($USER->id);
        $this->update_grade($submission);
        return true;
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
        $popup = $OUTPUT->action_link($link, shorten_text(trim(strip_tags(format_text($submission->data1,$submission->data2))), 15), $action, array('title'=>get_string('submission', 'assignment')));

        $output = '<div class="files">'.
                  '<img align="middle" src="'.$OUTPUT->pix_url('f/html') . '" height="16" width="16" alt="html" />'.
                  $popup .
                  '</div>';

        ///Stolen code from file.php

        echo $OUTPUT->box_start('generalbox boxaligncenter', 'wordcount');
    /// Decide what to count
        if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_WORDS) {
            echo ' ('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')';
        } else if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_LETTERS) {
            echo ' ('.get_string('numletters', '', count_letters(format_text($submission->data1, $submission->data2))).')';
        }
        echo $OUTPUT->box_end();
        echo $OUTPUT->box(format_text($submission->data1, $submission->data2), 'generalbox boxaligncenter boxwidthwide');

        ///End of stolen code from file.php

        if ($return) {
            //return $output;
        }
        //echo $output;
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

        $mform->addElement('select', 'resubmit', get_string("allowresubmit", "assignment"), $ynoptions);
        $mform->setHelpButton('resubmit', array('resubmit', get_string('allowresubmit', 'assignment'), 'assignment'));
        $mform->setDefault('resubmit', 0);

        $mform->addElement('select', 'emailteachers', get_string("emailteachers", "assignment"), $ynoptions);
        $mform->setHelpButton('emailteachers', array('emailteachers', get_string('emailteachers', 'assignment'), 'assignment'));
        $mform->setDefault('emailteachers', 0);

        $mform->addElement('select', 'var1', get_string("commentinline", "assignment"), $ynoptions);
        $mform->setHelpButton('var1', array('commentinline', get_string('commentinline', 'assignment'), 'assignment'));
        $mform->setDefault('var1', 0);

    }

    function portfolio_exportable() {
        return true;
    }

    function portfolio_load_data($caller) {
        $submission = $this->get_submission();
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($this->context->id, 'assignment_online_submission', $submission->id, "timemodified", false)) {
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
        $html = format_text($submission->data1, $submission->data2);
        $html = portfolio_rewrite_pluginfile_urls($html, $this->context->id, 'assignment_online_submission', $submission->id, $exporter->get('format'));
        if (in_array($exporter->get('formatclass'), array(PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_RICHHTML))) {
            if ($files = $exporter->get('caller')->get('multifiles')) {
                foreach ($files as $f) {
                    $exporter->copy_existing_file($file);
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
                foreach ($files as $f) {
                    $exporter->copy_existing_file($f);
                    $entry->add_attachment($f);
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
        if (has_capability('mod/assignment:submit', $PAGE->cm->context)) {
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

        if (!$submission || $editable) {
            // If this assignment is editable once submitted add an edit link to the settings nav
            $link = new moodle_url('/mod/assignment/view.php', array('id'=>$PAGE->cm->id, 'edit'=>1, 'sesskey'=>sesskey()));
            $node->add(get_string('editmysubmission', 'assignment'), $link, navigation_node::TYPE_SETTING);
        }
    }

    public function send_file($filearea, $args) {
        global $USER;
        require_capability('mod/assignment:view', $this->context);

        $fullpath = $this->context->id.$filearea.implode('/', $args);

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
}

class mod_assignment_online_edit_form extends moodleform {

    public function set_data($data) {
        $editoroptions = $this->get_editor_options();
        if (!isset($data->text)) {
            $data->text = '';
        }
        if (!isset($data->format)) {
            $data->textformat = FORMAT_HTML;
        } else {
            $data->textformat = $data->format;
        }

        if (!empty($this->_customdata->submission->id)) {
            $itemid = $this->_customdata->submission->id;
        } else {
            $itemid = null;
        }

        $data = file_prepare_standard_editor($data, 'text', $editoroptions, $this->_customdata->context, $editoroptions['filearea'], $itemid);
        return parent::set_data($data);
    }

    public function get_data() {
        $data = parent::get_data();

        if (!empty($this->_customdata->submission->id)) {
            $itemid = $this->_customdata->submission->id;
        } else {
            $itemid = null;
        }

        if ($data) {
            $editoroptions = $this->get_editor_options();
            $data = file_postupdate_standard_editor($data, 'text', $editoroptions, $this->_customdata->context, $editoroptions['filearea'], $itemid);
            $data->format = $data->textformat;
        }
        return $data;
    }

    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('editor', 'text_editor', get_string('submission', 'assignment'), null, $this->get_editor_options());
        $mform->setType('text_editor', PARAM_RAW); // to be cleaned before display
        $mform->setHelpButton('text_editor', array('reading', 'writing', 'richtext2'), false, 'editorhelpbutton');
        $mform->addRule('text_editor', get_string('required'), 'required', null, 'client');

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // buttons
        $this->add_action_buttons();
    }

    protected function get_editor_options() {
        $editoroptions = array();
        $editoroptions['filearea'] = 'assignment_online_submission';
        $editoroptions['noclean'] = false;
        $editoroptions['maxfiles'] = EDITOR_UNLIMITED_FILES;
        $editoroptions['maxbytes'] = $this->_customdata->maxbytes;
        return $editoroptions;
    }
}


