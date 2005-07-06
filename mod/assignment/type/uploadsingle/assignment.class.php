<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_uploadsingle extends assignment_base {

    function assignment_uploadsingle($cmid=0) {
        parent::assignment_base($cmid);

    }

    function view() {

        global $USER;

        $this->view_header();

        $this->view_intro();

        $this->view_dates();

        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) {
            if ($submission->timemarked) {
                $this->view_feedback();
            } else if ($filecount) {
                print_simple_box($this->print_user_files($USER->id, true), 'center');
            }
        }

        if ($this->isopen() && (!$filecount || $this->assignment->resubmit)) {
            $this->view_upload_form();
        }

        $this->view_footer();
    }


    function view_upload_form() {
        global $CFG;
        $struploadafile = get_string("uploadafile");
        $strmaxsize = get_string("maxsize", "", display_size($this->assignment->maxbytes));

        echo '<center>';
        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/upload.php\">";
        echo "<p>$struploadafile ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->libdir.'/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</form>';
        echo '</center>';
    }


    function upload() {
        global $CFG, $USER;

        if (isguest($USER->id)) {
            error(get_string('guestnoupload','assignment'));
        }

        $this->view_header(get_string('upload'));

        if (!$this->isopen()) {
            notify(get_string("uploadfailnoupdate", "assignment"));
        } else {
            if ($submission = $this->get_submission($USER->id)) {
                if ($submission->grade and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }

            $dir = $this->file_area_name($USER->id);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',true,false,$course,false,$this->assignment->maxbytes);
            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    $submission->timemodified = time();
                    $submission->numfiles     = 1;
                    $submission->comment = addslashes($submission->comment);
                    if (update_record("assignment_submissions", $submission)) {
                        $this->email_teachers($submission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }
                } else {
                    $newsubmission->assignment   = $this->assignment->id;
                    $newsubmission->userid       = $USER->id;
                    $newsubmission->timecreated  = time();
                    $newsubmission->timemodified = time();
                    $newsubmission->numfiles     = 1;
                    if (insert_record('assignment_submissions', $newsubmission)) {
                        add_to_log($this->course->id, 'assignment', 'upload', 
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $this->email_teachers($newsubmission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                    }
                }
            }
        }

        print_continue('view.php?id='.$this->cm->id);

        $this->view_footer();
    }


    /*
     *  Display and process the submissions 
     */ 
    function process_feedback() {                 
                
        global $USER;
                    
        if (!$feedback = data_submitted()) {      // No incoming data?
            return false;
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
        
        if (! update_record('assignment_submissions', $newsubmission)) {
            return false;
        }
        
        add_to_log($this->course->id, 'assignment', 'update grades', 
                   'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);   
        
        return $newsubmission;
                 
    }   


}

?>
