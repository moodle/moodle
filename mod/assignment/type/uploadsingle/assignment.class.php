<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_uploadsingle extends assignment_base {

    function assignment_uploadsingle($cmid=0) {
        parent::assignment_base($cmid);

    }

    function setup($form) {
        global $CFG, $usehtmleditor;

        parent::setup($form);

        print_simple_box(get_string('', 'assignment'), 'center');

        include("$CFG->dirroot/mod/assignment/type/uploadsingle/mod.html");
        parent::setup_end(); 
    }

    function submittedlink() {
        global $USER;

        $submitted = '';

        if (isteacher($this->course->id)) {
            if ($this->currentgroup and isteacheredit($this->course->id)) {
                $group = get_record('groups', 'id', $this->currentgroup);
                $groupname = ' ('.$group->name.')';
            } else {
                $groupname = '';
            }
            $count = $this->count_real_submissions($this->currentgroup);
            $submitted = '<a href="submissions.php?id='.$this->cm->id.'">'.
                         get_string('viewsubmissions', 'assignment', $count).'</a>'.$groupname;
        } else {
            if (isset($USER->id)) {
                if ($submission = $this->get_submission($USER)) {
                    if ($submission->timemodified <= $this->assignment->timedue) {
                        $submitted = userdate($submission->timemodified);
                    } else {
                        $submitted = '<span class="late">'.userdate($submission->timemodified).'</span>';
                    }
                }
            }
        }

        return $submitted;
    }

    function view() {

        $this->view_header();

        print_simple_box_start('center');
        echo format_text($this->assignment->description, $this->assignment->format);
        print_simple_box_end();

        print_simple_box_start('center', '', '', '', 'time');
        echo '<table>';
        echo '<tr><td class="c0">'.get_string('availabledate','assignment').':</td>';
        echo '    <td class="c1">'.userdate($this->assignment->timeavailable).'</td></tr>';
        echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
        echo '    <td class="c1">'.userdate($this->assignment->timedue).'</td></tr>';
        echo '</table>';
        print_simple_box_end();

        $this->view_feedback();

        $this->view_upload_form();

        $this->view_footer();
    }

    function view_upload_form() {
        global $CFG;

        echo '<center>';
        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/upload.php\">";
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->dirroot.'/lib/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</form>';
        echo '</center>';
    }


    function get_user_file($user) {
        global $CFG;

        $tmpfile = "";

        $filearea = file_area_name($user);

        if ($basedir = file_area($user)) {
            if ($files = get_directory_list($basedir)) {
                foreach ($files as $file) {                 // Just gets the first one
                    $icon = mimeinfo("icon", $file);
                    if ($CFG->slasharguments) {
                        $ffurl = "file.php/$filearea/$file";
                    } else {
                        $ffurl = "file.php?file=/$filearea/$file";
                    }
                    $tmpfile->url  = $ffurl;
                    $tmpfile->name = $file;
                    $tmpfile->icon = $icon;
                    break;
                }
            }
        }
        return $tmpfile;
    }

    function upload() {
        global $CFG, $USER;

        $this->view_header();

        if ($submission = $this->get_submission($USER->id)) {
            if ($submission->grade and !$this->assignment->resubmit) {
                notify(get_string('alreadygraded', 'assignment'));
            }
        }

        $dir = $this->file_area_name($USER);

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
                if (insert_record("assignment_submissions", $newsubmission)) {
                    add_to_log($this->course->id, "assignment", "upload", 
                               "view.php?a=$this->assignment->id", $this->assignment->id, $this->cm->id);
                    $this->email_teachers($newsubmission);
                    print_heading(get_string('uploadedfile'));
                } else {
                    notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
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
