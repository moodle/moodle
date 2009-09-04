<?php 
require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->libdir.'/formslib.php');


/**
 * Extend the base assignment class for offline assignments
 *
 */
class assignment_blog extends assignment_base {

    function assignment_blog($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        parent::assignment_base($cmid, $assignment, $cm, $course);
        $this->type = 'blog';
    }

    function display_lateness($timesubmitted) {
        return '';
    }

    function print_student_answer($userid, $return=false){
        global $CFG, $DB;
        if (!$submission = $this->get_submission($userid)) {
            return '';
        }
    $post = $DB->get_record('post', array('id' => $submission->data1));
        $ret = '<b>'.$post->subject.': </b><br>'.shorten_text(format_text($post->summary));
    $ret .= '<a href="'.$CFG->wwwroot.'/blog/index.php?postid='.$post->id.'" target="_blank">Full Entry</a><br>';

        return $ret;
    }


    function setup_elements(&$mform) {
        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        //$mform->addElement('select', 'var1', get_string('multiassoc', 'assignment'), $ynoptions);
        //$mform->setHelpButton('var1', array('multiassoc', get_string('multiassoc', 'assignment'), 'assignment'));
        //$mform->setDefault('var1', 0);
        
        $publishstates = array();
        $i = 0;
        foreach(blog_applicable_publish_states() as $state) $publishstates[$i++] = $state; 
        $mform->addElement('select', 'var2', get_string('maxpublishstate', 'assignment'), $publishstates);
        $mform->setDefault('var2', 0);
    }

    function prepare_new_submission($userid) {
        $submission = new Object;
        $submission->assignment   = $this->assignment->id;
        $submission->userid       = $userid;
        $submission->timecreated  = time(); // needed for offline assignments
        $submission->timemodified = $submission->timecreated;
        $submission->numfiles     = 0;
        $submission->data1        = '';
        $submission->data2        = '';
        $submission->grade        = -1;
        $submission->submissioncomment      = '';
        $submission->format       = 0;
        $submission->teacher      = 0;
        $submission->timemarked   = 0;
        $submission->mailed       = 0;
        return $submission;
    }

    // needed for the timemodified override
    function process_feedback() {
        global $CFG, $USER, $DB;
        require_once($CFG->libdir.'/gradelib.php');

        if (!$feedback = data_submitted()) {      // No incoming data?
            return false;
        }

        ///For save and next, we need to know the userid to save, and the userid to go
        ///We use a new hidden field in the form, and set it to -1. If it's set, we use this
        ///as the userid to store
        if ((int)$feedback->saveuserid !== -1){
            $feedback->userid = $feedback->saveuserid;
        }

        if (!empty($feedback->cancel)) {          // User hit cancel button
            return false;
        }

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, $feedback->userid);

        // store outcomes if needed
        $this->process_outcomes($feedback->userid);

        $submission = $this->get_submission($feedback->userid, true);  // Get or make one

        if (!$grading_info->items[0]->grades[$feedback->userid]->locked and
            !$grading_info->items[0]->grades[$feedback->userid]->overridden) {

            $submission->grade      = $feedback->grade;
            $submission->submissioncomment    = $feedback->submissioncomment;
            $submission->format     = $feedback->format;
            $submission->teacher    = $USER->id;
            $mailinfo = get_user_preferences('assignment_mailinfo', 0);
            if (!$mailinfo) {
                $submission->mailed = 1;       // treat as already mailed
            } else {
                $submission->mailed = 0;       // Make sure mail goes out (again, even)
            }
            $submission->timemarked = time();

            unset($submission->data1);  // Don't need to update this.
            unset($submission->data2);  // Don't need to update this.

            if (empty($submission->timemodified)) {   // eg for offline assignments
                $submission->timemodified = time();
            }

            if (! $DB->update_record('assignment_submissions', $submission)) {
                return false;
            }

            // triger grade event
            $this->update_grade($submission);

            add_to_log($this->course->id, 'assignment', 'update grades',
                       'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);
        }

        return $submission;

    }


    function view() {

        global $USER, $DB, $CFG, $COURSE;

        $edit  = optional_param('edit', 0, PARAM_BOOL);
        $saved = optional_param('saved', 0, PARAM_BOOL);

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        require_capability('mod/assignment:view', $context);

        $submission = $this->get_submission();

        //Guest can not submit nor edit an assignment (bug: 4604)
        if (!has_capability('mod/assignment:submit', $context)) {
            $editable = null;
        } else {
            $editable = $this->isopen();
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
        $mform = new mod_assignment_blog_edit_form();
        if($this->assignment->var1) {  //allow multiple associations
            $mform->set_multiple_assoc();
        }

        $defaults = new object();
       if($submission = $this->get_submission()) {
            $defaults->selectblog = $submission->data1;
        }
        $defaults->id = $this->cm->id;

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
                notify(get_string("error")); //submitting not allowed!
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
            notify(get_string('submissionsaved', 'assignment'), 'notifysuccess');
        }

        if (has_capability('mod/assignment:submit', $context)) {
            print_simple_box_start('center', '70%', '', 0, 'generalbox', 'online');
            if ($editmode) {
                if($DB->record_exists('post', array('module'=>'blog', 'userid'=>$USER->id))) {
                   $mform->display();
                } else {
                    echo '<div class="noticebox">'.get_string('noblogs', 'assignment').'</div>';
                    echo '<br><a href="'. $CFG->wwwroot. '/blog/edit.php?action=add&courseid='
                                   .$COURSE->id.'">'.get_string('addnewentry', 'blog') ."</a>";
                }
            } else {
                if ($submission) {
                    blog_print_entry($DB->get_record('post', array('id' => $submission->data1)));
                } else if (!has_capability('mod/assignment:submit', $context)) { //fix for #4604
                    echo '<div style="text-align:center">'. get_string('guestnosubmit', 'assignment').'</div>';
                } else if ($this->isopen()) {    //fix for #4206
                    echo '<div style="text-align:center">'.get_string('emptysubmission', 'assignment').'</div>';
                }
            }
            print_simple_box_end();
            if (!$editmode && $editable) {
                echo "<div style='text-align:center'>";
                print_single_button('view.php', array('id'=>$this->cm->id,'edit'=>'1'),
                        get_string('editmysubmission', 'assignment'));
                echo "</div>";
            }

        }

        $this->view_feedback();

        $this->view_footer();
    }


    function update_submission($data) {
        global $CFG, $USER, $DB, $COURSE;

        $submission = $this->get_submission($USER->id, true);

        $update = new object();
        $update->id           = $submission->id;
        $update->data1        = $data->selectblog;
        $update->timemodified = time();
        
        //enforce access restriction
        $postaccess = -1;
        $i=0;
        $post = $DB->get_record('post', array('id' => $data->selectblog));
        if(!$post) {
            print_error('blognotfound', 'blog');
        }
        $publishstates = array();
        foreach(blog_applicable_publish_states() as $state => $desc) {
            if($state == $post->publishstate) {
                $postaccess = $i;
            }
            $publishstates[$i++] = $state;
        } 

        if($this->assignment->var2 < $postaccess) {
            $post->publishstate = $publishstates[$this->assignment->var2];
            $DB->update_record('post', $post);
        } 
        
        //force the user to have strict associations with this post
        blog_remove_associations_for_post($post->id);  //remove all existing associations
        //add assignment association
        $assignmentmodid = $DB->get_field('modules', 'id', array('name' => 'assignment'));
        $modcontext = get_context_instance(CONTEXT_MODULE, $DB->get_field('course_modules', 'id', 
                                           array('module' => $assignmentmodid, 'instance' => $this->assignment->id))); 
        blog_add_association($post->id, $modcontext->id);
        //add course association
        $coursecontext = get_context_instance(CONTEXT_COURSE, $DB->get_field('course_modules', 'course', 
                                           array('module' => $assignmentmodid, 'instance' => $this->assignment->id))); 
        blog_add_association($post->id, $coursecontext->id);
        
        if (!$DB->update_record('assignment_submissions', $update)) {
            return false;
        }
        
        $submission = $this->get_submission($USER->id);
        $this->update_grade($submission);
        return true;
    }


}

class mod_assignment_blog_edit_form extends moodleform {
    function definition() {
        global $USER, $DB;
        $mform =& $this->_form;

        // visible elements
        //$mform->addRule('text', get_string('required'), 'required', null, 'client');
       
        $blogentries = array();
        foreach($DB->get_records('post', array('userid' => $USER->id)) as $rec) {
            $blogentries[$rec->id] = userdate($rec->created) . ' - ' . $rec->summary;
        }
        
        $mform->addElement('select', 'selectblog', get_string('selectblog', 'assignment'), $blogentries); 

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // buttons
        $this->add_action_buttons();
    }
    
    function set_multiple_assoc() {
        $mform =& $this->_form;
        $selectblog = $mform->getElement('selectblog');
        $selectblog->setMultiple(true);
    }
}

?>
