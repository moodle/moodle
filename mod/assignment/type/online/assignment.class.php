<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_online extends assignment_base {

    function assignment_online($cmid=0) {
        parent::assignment_base($cmid);
    }

    function view() {

        global $USER;

        $submission = $this->get_submission();
        $editable = $this->isopen() && (!$submission || $this->assignment->resubmit);
        $editmode = ($editable && !empty($_GET['edit']));

        if ($editmode) {
            $this->view_header(get_string('editmysubmission', 'assignment'));
        } else {
            $this->view_header();
        }

        $this->view_intro();

        $this->view_dates();

        if ($data = data_submitted()) {      // No incoming data?
            if ($editable && $this->update_submission($data)) {
                //TODO fix log actions - needs db upgrade
                add_to_log($this->course->id, 'assignment', 'upload', 
                        'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                $this->email_teachers($submission);
                //redirect to get updated submission date and word count
                redirect('view.php?id='.$this->cm->id.'&saved=1');
            } else {
                // TODO: add better error message
                notify(get_string("error")); //submitting not allowed!
            }
        } else if (!empty($_GET['saved'])) {
            notify(get_string('submissionsaved', 'assignment'));
        }

        print_simple_box_start('center', '70%', '', '', 'generalbox', 'online');
        $submission = $this->get_submission();
        if ($editmode) {
            $this->view_edit_form($submission);
        } else {
            if ($submission) {
                echo format_text($submission->data1, $submission->data2);
            } else {
                echo '<center>'.get_string('emptysubmission', 'assignment').'</center>';
            }
            if ($editable) {
                print_single_button('view.php', array('id'=>$this->cm->id,'edit'=>'1'), 
                                     get_string('editmysubmission', 'assignment'));
            }
        }
        print_simple_box_end();

        if ($editmode and $this->usehtmleditor) {
            use_html_editor();   // MUst be at the end of the page
        }

        $this->view_feedback();

        $this->view_footer();
    }

    /*
     * Display the assignment dates
     */
    function view_dates() {
        global $USER;

        if (!$this->assignment->timeavailable && !$this->assignment->timedue) {
            return;
        }

        print_simple_box_start('center', '', '', '', 'generalbox', 'dates');
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
            echo ' ('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')</td></tr>';
        }
        echo '</table>';
        print_simple_box_end();
    }

    function view_edit_form($submission = NULL) {
        global $CFG;

        $defaulttext = $submission ? $submission->data1 : '';
        $defaultformat = $submission ? $submission->data2 : $this->defaultformat;

        echo '<form name="theform" action="view.php" method="post">';  // do this so URLs look good

        echo '<table cellspacing="0" class="editbox" align="center">';
        echo '<tr><td align="right">';
        helpbutton('reading', get_string('helpreading'), 'moodle', true, true);
        echo '<br />';
        helpbutton('writing', get_string('helpwriting'), 'moodle', true, true);
        echo '<br />';
        if ($this->usehtmleditor) {
            helpbutton('richtext', get_string('helprichtext'), 'moodle', true, true);
        } else {
            emoticonhelpbutton('theform', 'text');
        } 
        echo '<br />';
        echo '</td></tr>';
        echo '<tr><td align="center">';
        print_textarea($this->usehtmleditor, 20, 60, 630, 400, 'text', $defaulttext);
        if (!$this->usehtmleditor) {
            echo '<div align="right" class="format">';
            print_string('formattexttype');
            echo ':&nbsp;';
            choose_from_menu(format_text_menu(), 'format', $defaultformat, '');
            helpbutton('textformat', get_string('helpformatting'));
            echo '</div>';
        } else {
            echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
        }
        echo '</td></tr>';
        echo '<tr><td align="center">';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="submit" value="'.get_string('savechanges').'" />';
        echo '<input type="reset" value="'.get_string('revert').'" />';
        echo '</td></tr></table>';

        echo '</form>';

    }

    function update_submission($data) {
        global $CFG, $USER;

        $submission = $this->get_submission($USER->id, true);

        $update = NULL;
        $update->id = $submission->id;
        $update->data1  = $data->text;
        $update->format = $data->format;
        $update->timemodified = time();

        return update_record('assignment_submissions', $update);
    }


    /*
     *  Display and process the submissions 
     */ 
    function process_feedback() {                 
                
        global $USER;
                    
        if (!$feedback = data_submitted()) {      // No incoming data?
            return false;
        }       
        
        ///For save and next, we need to know the userid to save, and the userid to go...
        ///We use a new hidden field in the form, and set it to -1. If it's set, we use this
        ///as the userid to store...
        if ((int)$feedback->saveuserid !== -1){
            $feedback->userid = $feedback->saveuserid;
        }
        
        if (!empty($feedback->cancel)) {          // User hit cancel button
            return false;
        }       
        
        $newsubmission = $this->get_submission($feedback->userid, true);  // Get or make one
            
        $newsubmission->grade      = $feedback->grade;
        $newsubmission->comment    = $feedback->comment;
        $newsubmission->format     = $feedback->format;
        $newsubmission->teacher    = $USER->id;
        $newsubmission->mailed     = 0;       // Make sure mail goes out (again, even)
        $newsubmission->timemarked = time();

        unset($newsubmission->data1);  // Don't need to update this.
        unset($newsubmission->data2);  // Don't need to update this.

        if (! update_record('assignment_submissions', $newsubmission)) {
            return false;
        }
        
        add_to_log($this->course->id, 'assignment', 'update grades', 
                   'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);   
        
        return $newsubmission;
                 
    }   

    function print_student_answer($userid, $return=false){
        global $CFG;
        if (!$submission = $this->get_submission($userid)) {
            return '';
        }
        $output = '<div class="files">'.
                  '<img align="middle" src="'.$CFG->pixpath.'/f/html.gif" height="16" width="16" alt="html" />'.
                  link_to_popup_window ('/mod/assignment/type/online/file.php?id='.$this->cm->id.'&amp;userid='.
                  $submission->userid, 'file'.$userid, shorten_text(trim(strip_tags(format_text($submission->data1,$submission->data2))), 15), 450, 580, 
                  get_string('submission', 'assignment'), 'none', true).
                  '</div>';
                  return $output;
    }
    
    function print_user_files($userid, $return=false) {
        global $CFG;
    
        if (!$submission = $this->get_submission($userid)) {
            return '';
        }
        
        $output = '<div class="files">'.
                  '<img align="middle" src="'.$CFG->pixpath.'/f/html.gif" height="16" width="16" alt="html" />'.
                  link_to_popup_window ('/mod/assignment/type/online/file.php?id='.$this->cm->id.'&amp;userid='.
                  $submission->userid, 'file'.$userid, shorten_text(trim(strip_tags(format_text($submission->data1,$submission->data2))), 15), 450, 580, 
                  get_string('submission', 'assignment'), 'none', true).
                  '</div>';

        ///Stolen code from file.php
        
        print_simple_box_start('center', '', '', '', 'generalbox', 'wordcount');
        echo '<table>';
        //if ($assignment->timedue) {
        //    echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
        //    echo '    <td class="c1">'.userdate($assignment->timedue).'</td></tr>';
        //}
        echo '<tr>';//<td class="c0">'.get_string('lastedited').':</td>';
        echo '    <td class="c1">';//.userdate($submission->timemodified);
        echo ' ('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')</td></tr>';
        echo '</table>';
        print_simple_box_end();
        print_simple_box(format_text($submission->data1, $submission->data2), 'center', '100%');
        
        ///End of stolen code from file.php
        
        if ($return) {
            //return $output;
        }
        //echo $output;
    }

    function preprocess_submission(&$submission) {
        if ($this->assignment->var1 && empty($submission->comment)) {  // comment inline
            if ($this->usehtmleditor) {
                // Convert to html, clean & copy student data to teacher
                $submission->comment = format_text($submission->data1, $submission->data2);
                $submission->format  = FORMAT_HTML;
            } else {
                // Copy student data to teacher
                $submission->comment = $submission->data1;
                $submission->format  = $submission->data2;
            }
        }
    }

}

?>
