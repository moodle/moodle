<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload one or more files and where teacher can upload one or more response files
 *
 */

 /*
  *defining constants for status indicators
  */

 //submissionstatusblank
 define('BLANK', 'Blank');

 //submissionstatussubmitted
 define('SUBMITTED', 'Submitted');

 //submissionstatusgraded
 define('GRADED', 'Graded');

 //submissionstatusreturned
 define('RETURNED', 'Returned');
 /***********************************************************************
* CUSTOM SQL
************************************************************************/
    function display_submissions_sql($table, $users, $assignmentID) {
        global $CFG, $USER;

        if ($where = $table->get_sql_where()) {
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sort = ' ORDER BY '.$sort;
        }

        /*
         *select has been modified. because we also need to get student
         *id & assignment status == upload_status = {Draft,
         *Submitted, Marked, Returned}
        */
        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture, u.idnumber, s.id AS submissionid, s.grade, s.comment, s.timemodified, s.timemarked, ((s.timemarked > 0) AND (s.timemarked >= s.timemodified)) AS status, s.data1 as upload_status ';

        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid AND s.assignment = '.$assignmentID.' '.
               'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') ';



        if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
            $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
        }
        else {
            $limit = '';
        }
        $temp = $select.$sql.$sort.$limit;

        return $temp;
    } // end function display_submissions_sql()

    function next_student_sql($assignmentID, $users) {
        global $CFG, $offset;
        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture,'.
                  's.id AS submissionid, s.grade, s.comment, s.timemodified, s.timemarked, ((s.timemarked > 0) AND (s.timemarked >= s.timemodified)) AS status ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid AND s.assignment = '.$assignmentID.' '.
               'WHERE u.id IN ('.implode(',', array_keys($users)).') AND s.data1 <> "Draft"  AND s.data1 <> ""';
        //we don't need to grade drafts or empty assignments

        require_once($CFG->libdir.'/tablelib.php');
        if($sort = flexible_table::get_sql_sort('mod-assignment-submissions')) {
            $sort = 'ORDER BY '.$sort.' ';
        }

        $limit = sql_paging_limit($offset+1, 1);

        $temp = $select.$sql.$sort.$limit;
        return $temp;

        }

/***********************************************************************
* END CUSTOM SQL
************************************************************************/
class assignment_upload extends assignment_base {

    function print_student_answer($userid, $return=false){
        global $CFG, $USER;

        $filearea = $this->file_area_name($userid);
        $output = '';

        if ($basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                require_once($CFG->libdir.'/filelib.php');
                foreach ($files as $key => $file) {

                    $icon = mimeinfo('icon', $file);

                    if ($CFG->slasharguments) {
                        $ffurl = "{$CFG->wwwroot}/file.php/$filearea/$file";
                    } 
                    else {
                        $ffurl = "{$CFG->wwwroot}/file.php/$filearea/$file";
                    }
                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                            '<a href="'.$ffurl.'" >'.$file.'</a><br />';
                }
            }
        }

        $output = '<div class="files">'.$output.'</div>';
        return $output;
    }

    function assignment_upload($cmid=0) {
        parent::assignment_base($cmid);
    }

    //generates page for file upload, list of uploaded files, feedbacks from teachers, etc
    function view() {
        global $USER;

        require_login($this->course->id, false, $cm);

        // Guests are not allowed to view student's or teacher's assignment information.
        if (isguest($USER->id)) {
            error(get_string('usererror', 'assignment'));
        } 
        else if (isteacher($this->course->id, $USER->id, true) or isstudent($this->course->id, $USER->id)) {

            $this->view_header();
            $this->view_intro();
            $this->view_dates();

            $filecount = $this->count_user_files($USER->id);

            //prints students draft and final uploads
            if ($submission = $this->get_submission()) {
                if ($submission->timemarked == true) {
                    print_simple_box($this->print_user_files($USER->id, true), 'center');
                    $this->view_feedback();
                }
                else if ($filecount >= 0) {
                    print_simple_box($this->print_user_files($USER->id, true), 'center');
                }

                // data2 = holds teacher's id for teacher responses
                if ($submission->data2 != NULL) {
                    print_heading(get_string('responsesfromteacher', 'assignment'));
                    $this->print_response_students(true);
                }
            }

            //display Blank if there were no files uploaded yet, otherwise display submission status
            //if there are no more files uploaded, $submission_status should be blank.
            if ($submission->numfiles == 0) {
                $submission_status = get_string('submissionstatusblank','assignment');
            }
            else {
                // data1 = submission status of the submitted assignment.
                if($submission->data1 == GRADED) {
                    $submission_status = get_string('submissionstatusgraded','assignment');
                }else {
                    $submission_status = $submission->data1;
                }
            }


            //display submission status
            notify("<b>".get_string('submissionstatus', 'assignment').":" . " </b> ". $submission_status);

            //checks to see if files are of submitted status or not
            //checks if the file is part of multiple file uploading
            //and that it is of returned status
            //checks if the file is of graded status
            //checks again to see if file is of returned status
            //checks if the teacher has graded the assignment.
            //will allow users to see the file uploading page.
            /*
             *var1 indicates that multiple file upload is active(1 active, 0 off).
            */

            if ($submission->data1 != SUBMITTED && $this->isopen() &&
            ((!$this->assignment->var1 && $submission->data1 == RETURNED) ||
                 !$filecount ||
                 ($this->assignment->resubmit && $submission->data1 == GRADED) ||
                 $submission->data1 == RETURNED ||
                 !$submission->timemarked)
            ) {
                  $this->view_upload_form();
              }
              $this->view_footer();

        }

    }

    function view_upload_form() {
        global $CFG;
        require_once($CFG->libdir.'/uploadlib.php');


        $struploadafile = get_string("uploadafile");

        $strmaxsize = get_string("maxsize", "", display_size($this->assignment->maxbytes));

        // for student uploads
        echo '<center>';
        echo '<form enctype="multipart/form-data" method="post" ';
        echo 'action="'.$CFG->wwwroot.'/mod/assignment/upload.php">';
        echo '<p><b>'.get_string('step1','assignment').'</b>'.get_string('attachfiletoassignment','assignment')." ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'">';
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);
        
        //upload files
        echo '<input type="submit" name="save" value="'.get_string('attachfile','assignment').'" />';
        echo "<p><b>".get_string('step2','assignment')."</b>".get_string('submitformarking','assignment')."</p>";
        
        //final submit
        echo '<input type="submit" name="save" value="'.get_string('sendformarking','assignment').'" />';
        echo "<p>".get_string('onceassignmentsent','assignment')."</p>";
        echo '</form>';
        echo '</center>';
    }

    //from upload&review. generates upload form for markers response
    function view_upload_response_form($userid,$offset) {
        global $CFG;
        require_once($CFG->libdir.'/uploadlib.php');
        
        $userid = required_param('userid', PARAM_INT);

        //for teacher's responses
        echo '<center>';
        echo get_string('choosereviewfile','assignment').'<br>';

        echo '<form enctype="multipart/form-data" method="post" ';
        echo 'action="'.$CFG->wwwroot.'/mod/assignment/upload.php">';
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="offset" value="'.$offset.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'">';
        upload_print_form_fragment(1, array('newfile'), false, null, 0, $this->assignment->maxbytes, false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</form>';
        echo '</center>';
    }

    //general function which calls function for drafts upload, final upload, teachers response upload
    function upload(){
         global $offset, $USER;
        //if this is final submit
        if(optional_param('save', PARAM_ALPHANUM) === get_string('sendformarking','assignment')) {
            $this->final_upload(sesskey());
        }
        else {
            //if this is draft upload
            if (optional_param('save', PARAM_ALPHANUM)==get_string('attachfile','assignment') && !$userid) {
                $this->submission_upload(sesskey());
            }
            else {              //if this is upload of teacher's response
                $id      = optional_param('id', '', PARAM_INT);  //Course module ID
                $a       = optional_param('a', '', PARAM_INT);   //Assignment ID
                $userid  = required_param('userid', PARAM_INT);  //Stores student id for uploading a review file to

                $this->response_upload($userid, sesskey());   // Upload files   // Upload files
                echo '<form action="submissions.php">';
                echo '<input type="hidden" value="'.$userid.'" name="userid" />';
                echo '<input type="hidden" value="'.$id.'" name="id" />';
                echo '<input type="hidden" value="'.$a.'" name="a" />';
                echo '<input type="hidden" value="single" name="mode" />';
                echo '<input type="hidden" name="offset" value="'.$offset.'" />';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '<center><input type="submit" value="'.get_string('feedback', 'assignment').'" name="submit"></center></form>';
            }
        }
    }

    //implements upload regular submissuion - draft files
    function submission_upload($sesskey) {
        global $CFG, $USER, $counter;

        require_once($CFG->dirroot.'/lib/uploadlib.php');

        if (!confirm_sesskey()) {
            error('Bad Session Key');
        }

        //checking for teachers/students/guests of the course
        if (isguest($USER->id)) {
            error(get_string('guestnoupload','assignment'));
        } 
        else if (isteacher($this->course->id, $USER->id, true) or isstudent($this->course->id, $USER->id)){

            $course = $this->course;
            $this->view_header(get_string('upload'));

            $filecount = $this->count_user_files($USER->id);
            $submission = $this->get_submission($USER->id);

            //need to get student's directory and all previously uploaded files
            $basedir = $this->file_area($USER->id);
            $files = get_directory_list($basedir);

            if ($this->isopen() ){
                if ($submission) {
                    //TODO: change later to ">= 0", to prevent resubmission when graded 0
                    if (($submission->grade >= 0) and $this->assignment->resubmit) {
                        error(get_string('submissionerror','assignment'));
                    } else if ($submission->grade >= 0 and $this->numfiles == 0){
                        error(get_string('nofilesforsubmit', 'assignment'));
                    }
                }

                $dir = $this->file_area_name($USER->id);
                $um = new upload_manager('newfile',!$this->assignment->var1,false,$course,false,$this->assignment->maxbytes);

                if ($um->process_file_uploads($dir)) {
                    $newfile_name = $um->get_new_filename();
                    //if student already submitted something before
                    if ($submission) {
                        //assignment is not submited for marking
                        $newsubmission->timemodified = '0';
                        //$submission->timemodified = time();
                        $flag=false;
                        foreach ($files as $key => $file) {
                            if ($file == $newfile_name) {
                                $flag = true;
                            }
                        }
                        
                        //if this is an assignment for single upload
                        if (!$this->assignment->var1) {
                            //if numfiles=1
                            if ($submission->numfiles==0 && !$flag) {
                                $submission->numfiles ++;
                            }
                        }
                        else { 
                            //if file with the same name has not been uploaded before
                            if (!$flag)  $submission->numfiles ++;
                        }
                        
                        $submission->comment = addslashes($submission->comment);
                        $submission->data1 = addslashes($submission->data1);
                        unset($submission->data2);  //Don't need to update.
                        
                        if (update_record("assignment_submissions", $submission)) {
                            add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                            print_heading(get_string('uploadedfile'));
                        } 
                        else {
                            error(get_string("uploadfailnoupdate", "assignment"));
                        }
                    
                    //if it's first student's submission
                    } 
                    else {
                        $newsubmission = $this->prepare_new_submission($USER->id);
                        //submissions has been created, but not submitted for marking
                        $newsubmission->timecreated  = time();
                        $newsubmission->timemodified = '0';
                        $newsubmission->data1 = get_string("submissionstatusdraft", "assignment");
                        $newsubmission->numfiles = 1;
                        if (insert_record('assignment_submissions', $newsubmission)) {
                            add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                            print_heading(get_string('uploadedfile'));
                        } 
                        else {
                            error(get_string("uploadnotregistered", "assignment", $newfile_name) );
                        }
                    }
                }
            } 
            else {
                error(get_string("uploaderror", "assignment")); //submitting not allowed!
            }
            print_continue('view.php?id='.$this->cm->id);
            $this->view_footer();
        }
    }

    //implements final upload (submitting for marking)
    function final_upload($sesskey) {
        global $CFG, $USER;

        require_once($CFG->dirroot.'/lib/uploadlib.php');

        if (!confirm_sesskey()) {
            error('Bad Session Key');
        }

        if (isguest($USER->id)) {
            error(get_string('guestnoupload','assignment'));
        } 
        else if (isteacher($this->course->id, $USER->id, true) or isstudent($this->course->id, $USER->id)) {
            $this->view_header(get_string('upload'));
            $filecount = $this->count_user_files($USER->id);
            $submission = $this->get_submission($USER->id);

            if ($this->isopen() && ($this->assignment->var1 || $filecount || $this->assignment->resubmit || !$submission->timemarked)) {
                if ($submission) {
                    //TODO: change later to ">= 0", to prevent resubmission when graded 0
                    if (($submission->grade > 0) and $this->assignment->resubmit) {
                        error(get_string('submissionerror', 'assignment'));
                    }
                }

                $dir = $this->file_area_name($USER->id);
                $um = new upload_manager('newfile',!$this->assignment->var1,false,$course,false,$this->assignment->maxbytes);

                $newfile_name = $um->get_new_filename();

                //if student already submitted something before
                if ($submission) {
                    if ( $submission->numfiles == 0){
                        error(get_string("nofilesforsubmit","assignment"));
                    }
                    else {
                        $submission->timemodified = time();
                        $submission->data1 = get_string("submissionstatussubmitted", "assignment");
                        if (update_record("assignment_submissions", $submission)) {
                            add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        } 
                        else {
                            error(get_string("uploadfailnoupdate", "assignment"));
                        }
                    }
                } 
                else {
                    error(get_string("nofilesforsubmit","assignment"));
                }
            } 
            else {
                error(get_string("uploaderror", "assignment")); //submitting not allowed!
            }
            notify(get_string('onceassignmentsent', 'assignment'));
            print_continue('view.php?id='.$this->cm->id);

            $this->view_footer();
        } 
        else {
            error(get_string('unauthorizeduserupload', 'assignment'));
        }
    }


    //from upload&review
    function response_file_area_name($userid, $teachid = 0) {
        //Creates a directory file name, suitable for make_upload_directory()
        global $CFG, $USER;
        $fileloc = "";
        
        if ($teachid == 0) {
            $fileloc = "$USER->id/$userid";
        } 
        else {
            $fileloc = "$teachid/$USER->id";
        }
        return $this->course->id.'/'.$CFG->moddata.'/assignment/'.$this->assignment->id.'/responses/'.$fileloc;
     }

    //from upload&review
    //make the folder which going to hold response files
    function response_file_area($userid, $teachid = 0) {
        if ($teachid == 0) {
            return make_upload_directory( $this->response_file_area_name($userid) );
        } 
        else {
            return make_upload_directory( $this->response_file_area_name($userid, $teachid) );
        }
    }

    //from upload&review
    //upload responce file
    function response_upload($userid, $sesskey) {
        global $CFG, $USER;

        require_once($CFG->dirroot.'/lib/uploadlib.php');

        if (!confirm_sesskey()) {
            error('Bad Session Key');
        }

        if (!$this->isopen()) {
            error(get_string("uploadfailnoupdate", "assignment"));
        } 
        else {
            $submission = $this->get_submission($userid);
            $dir = $this->response_file_area_name($userid);
            $um = new upload_manager('newfile',false,false,$course,false,$this->assignment->maxbytes);

            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    //stores teacher id's in data2 in comma-separated list so students can view all responses from all teachers
                    if ($teachids = $submission->data2) {
                        $teachidarr = explode(',', $teachids);
                        $teachidexists = false;
                        foreach($teachidarr as $t) {
                            if ($t == $USER->id) {
                                $teachidexists = true;
                            }
                        }
                        if ($teachidexists == false) {
                            $teachids .= ",$USER->id";
                        }
                        $submission->data2 = $teachids;
                    } 
                    else {
                        $submission->data2 = $USER->id;
                    }

                    if (!update_record("assignment_submissions", $submission)) {
                        error(get_string("uploadfailnoupdate", "assignment"));
                    }
                } 
                else {
                    error(get_string("studentrecorderror", "assignment"));
                }
                notify(get_string("uploadsuccessresponse", "assignment"));
            }
        }
    }

    /*
     *Display and process the submissions
     */
    function process_feedback($sesskey) {
        global $USER;

        if (!confirm_sesskey()) {
            error('Bad Session Key');
        }

        if (!$feedback = data_submitted()) {      //No incoming data?
            return false;
        }

        ///For save and next, we need to know the userid to save, and the userid to go...
        ///We use a new hidden field in the form, and set it to -1. If it's set, we use this
        ///as the userid to store...
        //removed by Oksana. it was braking functionality and submitting teacher's feedback to.. teacher-user
        //this was inherited from upload type. check if nothing brakes???????????????
        if ((int)$feedback->saveuserid !== -1){
            $feedback->userid = $feedback->saveuserid;
        }
        if (!empty($feedback->cancel)) {      //User hit cancel button
            return false;
        }
        
        //Get or make a new submission
        $newsubmission = new stdClass;
        $newsubmission = $this->get_submission($feedback->userid, true);
        $newsubmission->grade      = $feedback->grade;
        $newsubmission->comment    = $feedback->comment;
        $newsubmission->format     = $feedback->format;
        $newsubmission->teacher    = $USER->id;
        $newsubmission->timemarked = time();

        if ($feedback->grade != -1 ) {
            $newsubmission->data1 = get_string('submissionstatusgraded', 'assignment');
        }
        else {
            $newsubmission->data1 = get_string('submissionstatusreturned', 'assignment');
        }

        if (!update_record('assignment_submissions', $newsubmission)) {
            return false;
        }

        add_to_log($this->course->id, 'assignment', 'update grades', 'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);

        $newsubmission->data1 = addslashes($newsubmission->data1);
        unset($newsubmission->data2); //Don't need to update
        $newsubmission->comment = addslashes($newsubmission->comment);

        return $newsubmission;
    }

    /*
     *Top-level function for handling of submissions called by submissions.php
     *
     */
    //from lib.php
    //needed to update case of fastgrading. upgrade upload_statuses
    function submissions($mode) {
        //make user global so we can use the id
        global $USER;

        $id      = required_param('id', PARAM_INT);         // Course module ID
        $userid  = optional_param('userid', PARAM_INT);     // User ID
        $comment = optional_param('comment', '', PARAM_ALPHANUM);  //replaced $_POST['comment']
        $menu    = optional_param('menu', '', PARAM_ALPHANUM);  //replaced $_POST['menu']
        $colum   = optional_param($col, '', PARAM_ALPHANUM);  //replaced $_POST['$col']

        //Guests, non-studnets and non-teachers are not allowed to view student's or teacher's assignment information.
        if (isguest($USER->id)){
            error(get_string('unauthorizeduser', 'assignment'));
        } else if (isstudent($this->course->id, $USER->id)){
            error(get_string('unauthorizeduser', 'assignment'));
        } else if (!isteacher($this->course->id, $USER->id, true)){
            error(get_string('unauthorizeduser', 'assignment'));
        } else {
            if ($submission = $this->get_submission()) {
                if ($submission->timemarked == true) {
                    print_simple_box($this->print_user_files($USER->id, true), 'center');
                    $this->view_feedback();
                }
                else if ($filecount >= 0) {
                    print_simple_box($this->print_user_files($USER->id, true), 'center');
                }
                if ($submission->data2 != NULL) {
                    print_heading(get_string('responsesfromteacher', 'assignment'));
                    $this->print_response_students(true);
                }
            }

            switch ($mode) {
                case 'grade':
                    //We are in a popup window grading
                    if ($submission = $this->process_feedback(sesskey())) {
                        print_header(get_string('feedback', 'assignment').':'.format_string($this->assignment->name));
                        print_heading(get_string('changessaved'));
                        $this->update_main_listing($submission);
                    }
                    close_window();
                    break;
                
                case 'single':
                    //We are in a popup window displaying submission
                    $this->display_submissions(sesskey());
                    break;
                
                case 'all':
                    //Main window, display everything
                    $this->display_submissions(sesskey());
                    break;

                case 'fastgrade':
                    //fast grading - this process should work for all 3 subclasses
                    $grading    = false;
                    $commenting = false;
                    $col        = false;
                    if ($comment) {
                        $col = 'comment';
                        $commenting = true;
                    }
                    if ($menu) {
                        $col = 'menu';
                        $grading = true;
                    }
                    if (!$col) {
                        //both comment and grade columns collapsed..
                        $this->display_submissions(sesskey());
                        break;
                    }

                    foreach ($colum as $id => $unusedvalue){

                        $id = (int)$id; //clean parameter name
                        if (!$submission = $this->get_submission($id)) {
                            $submission = $this->prepare_new_submission($id);
                            $newsubmission = true;
                        } 
                        else {
                            $newsubmission = false;
                        }
                        $submission->data1 = addslashes($submission->data1);
                        unset($submission->data2); //Don't need to update

                        //for fast grade, we need to check if any changes take place
                        $updatedb = false;

                        if ($grading) {
                            $grade = $menu[$id];
                            $updatedb = $updatedb || ($submission->grade != $grade);
                            $submission->grade = $grade;
                        }
                        else {
                            if (!$newsubmission) {
                                unset($submission->grade);  // Don't need to update this.
                            }
                        }
                        
                        //change status if assignment was graded or returned
                        if ($submission->grade != -1 )
                             $submission->data1 = get_string('submissionstatusgraded', 'assignment');
                        else
                             $submission->data1 = get_string('submissionstatusreturned', 'assignment');

                        if ($commenting) {
                            $commentvalue = trim($comment[$id]);
                            $updatedb = $updatedb || ($submission->comment != stripslashes($commentvalue));
                            $submission->comment = $commentvalue;
                        } else {
                            unset($submission->comment);  // Don't need to update this.
                        }

                        $submission->teacher    = $USER->id;
                        $submission->timemarked = time();

                        //if it is not an update, we don't change the last modified time etc.
                        //this will also not write into database if no comment and grade is entered.
                        if ($updatedb){
                            if ($newsubmission) {
                                if (!insert_record('assignment_submissions', $submission)) {
                                    return false;
                                }
                            } else {
                                if (!update_record('assignment_submissions', $submission)) {
                                    return false;
                                }
                            }
                            //add to log only if updating
                            add_to_log($this->course->id, 'assignment', 'update grades', 'submissions.php?id='.$this->assignment->id.'&user='.$submission->userid, $submission->userid, $this->cm->id);
                        }
                    }
                    $this->display_submissions();
                    break;

                case 'next':
                    // in pop up skip to next without saving
                    $this->display_submission();
                    break;

                case 'saveandnext':
                    //We are in pop up. save the current one and go to the next one.
                    //first we save the current changes
                    if ($submission = $this->process_feedback()) {
                        $this->update_main_listing($submission);
                    }
                    $this->display_submission();
                    break;

                default:
                    error ("There has been an error, cannot move or save to the next assignment!!");
                    break;
            }
        }
    }

    /*
      function that updates the listing on the main script from popup
      using javascript
      from lib.php
      needed to display teachers response files and upload_statuses
    */
    function update_main_listing($submission) {
        global $SESSION;

        $perpage = get_user_preferences('assignment_perpage', 10);
        $quickgrade = get_user_preferences('assignment_quickgrade', 0);

        /// Run some Javascript to try and update the parent page
        echo '<script type="text/javascript">'."\n<!--\n";
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['comment'])) {
            if ($quickgrade){
                echo 'opener.document.getElementById("comment['.$submission->userid.']").value="'.trim($submission->comment).'";'."\n";
             } 
             else {
                echo 'opener.document.getElementById("com'.$submission->userid.'").innerHTML="'.shorten_text(trim(strip_tags($submission->comment)), 15)."\";\n";
            }
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['grade'])) {
            if ($quickgrade){
                echo 'opener.document.getElementById("menumenu['.$submission->userid.']").selectedIndex="'.required_param('menuindex', 0, PARAM_INT).'";'."\n";
            } else {
                echo 'opener.document.getElementById("g'.$submission->userid.'").innerHTML="'.$this->display_grade($submission->grade)."\";\n";

            }
        }

        //need to add student's assignments in there too.
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemodified']) &&
            $submission->timemodified) {
            echo 'opener.document.getElementById("ts'.$submission->userid.'").innerHTML="'.addslashes($this->print_student_answer($submission->userid)).userdate($submission->timemodified)."\";\n";
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemarked']) &&
            $submission->timemarked) {
            //display teachers feedback files here as well
            echo 'opener.document.getElementById("tt'.$submission->userid.'").innerHTML="'.addslashes($this->print_user_response_files($submission->userid,false)).userdate($submission->timemarked)."\";\n";
         }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['status'])) {
            echo 'opener.document.getElementById("up'.$submission->userid.'").className="s1";';
            //replace "Update" by upload_status
            $buttontext =  $submission->data1;
            $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$submission->userid.'&amp;mode=single'.'&amp;offset='.optional_param('offset', '', PARAM_INT), 'grade'.$submission->userid, $buttontext, 450, 700, $buttontext, 'none', true, 'button'.$submission->userid);
            echo 'opener.document.getElementById("up'.$submission->userid.'").innerHTML="'.addslashes($button).'";';
        }
        echo "\n-->\n</script>";
        flush();
    }

    //display student's submission for marking in pop-up window
    function display_submission() {

        global $CFG, $offset;
        
        $userid = required_param('userid', PARAM_INT);   //the user's id
        $offset = required_param('offset', PARAM_INT);

        // Guests, non-studnets and non-teachers are not allowed to view student's or teacher's assignment information.
        if (isguest($USER->id)) {
            error(get_string('unauthorizeduser', 'assignment'));
        } else if (isstudent($this->course->id, $USER->id)) {
            error(get_string('unauthorizeduser', 'assignment'));
        } else if (!isteacher($this->course->id, $USER->id, true)) {
            error(get_string('unauthorizeduser', 'assignment'));
        } else {
            if (!$user = get_record('user', 'id', $userid)) {
                error(get_string('nouser'));
            }

            if (!$submission = $this->get_submission($user->id)) {
                $submission = $this->prepare_new_submission($userid);
            }

            if ($submission->timemodified > $submission->timemarked) {
                $subtype = 'assignmentnew';
            } 
            else {
                $subtype = 'assignmentold';
            }

            $course     = $this->course;
            $assignment = $this->assignment;
            $cm         = $this->cm;

            if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
                $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
            } else {
                $currentgroup = false;
            }

            // Get all teachers and students
            if ($currentgroup) {
                $users = get_group_users($currentgroup);
            } else {
                $users = get_course_users($course->id);
            }

            $sql_next_student = next_student_sql($this->assignment->id, $users);

            $nextid = 0;
            if (($auser = get_record_sql($sql_next_student, false, true)) !== false) {
                $nextid = $auser->id;
            }
            print_header(get_string('feedback', 'assignment').':'.$user->firstname. ' '. $user->lastname.':'.format_string($this->assignment->name));

            ///Some javascript to help with setting up student grading >.>

            echo '<script type="text/javascript">'."\n";
            echo 'function setNext(){'."\n";
            echo 'document.submitform.mode.value=\'next\';'."\n";
            echo 'document.submitform.userid.value="'.$nextid.'";'."\n";
            echo '}'."\n";

            echo 'function saveNext(){'."\n";
            echo 'document.submitform.mode.value=\'saveandnext\';'."\n";
            echo 'document.submitform.userid.value="'.$nextid.'";'."\n";
            echo 'document.submitform.saveuserid.value="'.$userid.'";'."\n";
            echo 'document.submitform.menuindex.value = document.submitform.grade.selectedIndex;'."\n";
            echo '}'."\n";

            echo '</script>'."\n";

            // Prints upload form for teachers to upload response file
            $this->view_upload_response_form($userid,$offset);
            //+++
            echo '<table border="0" align="center" cellpadding="5" cellspacing="1" class="feedback '.$subtype.'" >';

            ///Start of teacher info row

            echo '<tr>';
            echo '<td width="35" valign="top" class="picture teacher">';
            if ($submission->teacher) {
                $teacher = get_record('user', 'id', $submission->teacher);
            } else {
                global $USER;
                $teacher = $USER;
            }
            print_user_picture($teacher->id, $this->course->id, $teacher->picture);
            echo '</td>';
            echo '<td class="content">';
            echo '<form name="submitform" action="submissions.php" method="post">';
            echo '<input type="hidden" name="offset" value="'.++$offset.'">';
            echo '<input type="hidden" name="userid" value="'.$userid.'" />';
            echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
            echo '<input type="hidden" name="mode" value="grade" />';
            //selected menu index
            echo '<input type="hidden" name="menuindex" value="0" />';
            //new hidden field, initialized to -1.
            echo '<input type="hidden" name="saveuserid" value="-1" />';

            // Shows teacher response files to teacher
            echo "<div class=\"from\">". get_string('responsefile','assignment')."</div>";
            echo $this->print_user_response_files($userid,true,--$offset);
            echo "<br /><br />";

            if ($submission->timemarked) {
                echo '<div class="from">';
                echo '<div class="fullname">'.$auser->firstname. ' '. $auser->lastname.'</div>';
                echo '<div class="time">'.userdate($submission->timemarked).'</div>';
                echo '</div>';
            }

            echo '<div class="grade">'.get_string('grade').':';
            choose_from_menu(make_grades_menu($this->assignment->grade), 'grade', $submission->grade, get_string('nograde'), '', -1);

            echo '</div>';
            echo '<div class="clearer"></div>';


            $this->preprocess_submission($submission);

            echo '<br />';
            print_textarea($this->usehtmleditor, 14, 58, 0, 0, 'comment', $submission->comment, $this->course->id);

            if ($this->usehtmleditor) {
                echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
            } 
            else {
                echo '<div align="right" class="format">';
                choose_from_menu(format_text_menu(), "format", $submission->format, "");
                helpbutton("textformat", get_string("helpformatting"));
                echo '</div>';
            }

            ///Print Buttons in Single View
            echo '<div class="buttons" align="center">';
            echo '<input type="submit" name="submit" value="'.get_string('savechanges').'" onclick = "document.submitform.menuindex.value = document.submitform.grade.selectedIndex" />';
            echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />';
            //if there are more to be graded.
            if ($nextid) {
                echo '<input type="submit" name="saveandnext" value="'.get_string('saveandnext').'" onclick="saveNext()" />';
                echo '<input type="submit" name="next" value="'.get_string('next').'" onclick="setNext();" />';
            }
            echo '</div>';
            echo '</form>';
            echo '</td></tr>';
            //End of teacher info row, Start of student info row
            echo '<tr>';
            echo '<td width="35" valign="top" class="picture user">';
            print_user_picture($user->id, $this->course->id, $user->picture);
            echo '</td>';
            echo '<td class="topic">';
            echo '<div class="from">';
            echo '<div class="fullname">'.$auser->firstname. ' '. $auser->lastname.'</div>';
            if ($submission->timemodified) {
                echo '<div class="time">'.userdate($submission->timemodified).
                                         $this->display_lateness($submission->timemodified).'</div>';
            }
            echo '</div>';
            $this->print_user_files($user->id);
            echo '</td>';
            echo '</tr>';

            ///End of student info row

            echo '</table>';

            if ($this->usehtmleditor) {
                use_html_editor();
            }

            print_footer('none');
        }
    }

    /*
      From upload&review
      Prints response files to students
    */
    function print_response_students($return) {
        global $CFG, $USER;
        require_once($CFG->libdir.'/filelib.php');
        
        $stuid = $USER->id;

        echo '<table border="0" align="center" cellpadding="5" cellspacing="1" class="feedback">';

        $submission = $this->get_submission($stuid);

        // Only will show files if there is a submission
        if ($teachids = $submission->data2) {
            $teachidarr = explode(',', $teachids);
            foreach ($teachidarr as $t) {
                if (! $teacher = get_record('user', 'id', $t)) {
                    print_object($submission);
                    error(get_string('teachererror', 'assignment'));
                }
                echo '<tr>';
                echo '<td class="left picture">';
                print_user_picture($teacher->id, $this->course->id, $teacher->picture);
                echo '</td>';
                echo '<td class="topic">';
                echo '<div class="from">';
                echo '<div class="fullname">'.$auser->firstname. ' '. $user->lastname.'</div>';
                echo '</div>';

                $filearea = $this->response_file_area_name($stuid, $t);
                if ($basedir = $this->response_file_area($stuid, $t)) {
                    $output = '';
                    if ($files = get_directory_list($basedir)) {
                        foreach ($files as $key => $file) {
                            $icon = mimeinfo('icon', $file);
                            if ($CFG->slasharguments) {
                                $ffurl = "mod/assignment/type/uploadreview/file.php/$filearea/$file";
                            }
                            else {
                                $ffurl = "mod/assignment/type/uploadreview/file.php?file=/$filearea/$file";
                            }
                        }
                        //displays multiple teachers responces
                        $output .='<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />';
                        $output .= link_to_popup_window ('/'.$ffurl, 'file'.$key, $file, 450, 580, $file, 'none', true)."<br />";
                    }
                }
            }
            echo '<div class="files"><left>'.$output.'</left></div>';
            echo '</td></tr>';
        }
        echo '</table>';
    }


    //print teacher's files
    function print_user_response_files($stuid,$display_remove_button=false,$offset=NULL){

        global $CFG, $USER;
        $userid = $USER->id;

        $filearea = $this->response_file_area_name($stuid);
        $output = '';
        if ($basedir = $this->response_file_area($stuid)) {
            if ($files = get_directory_list($basedir)) {
                require_once($CFG->libdir.'/filelib.php');
                foreach ($files as $key => $file) {
                    $icon = mimeinfo('icon', $file);

                    if ($CFG->slasharguments) {
                        $ffurl = "file.php/$filearea/$file";
                    } 
                    else {
                        $ffurl = "file.php?file=/$filearea/$file";
                    }
                    //get feedback file size, generate and display remove file link
                    $filesize = display_size(filesize($basedir."/".$file));
                    $remove_link='';
                    if ($display_remove_button) {
                        $course_mod_id=$this->cm->id;
                        $deleteurl="$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=0&view=teacher&userid=$stuid&id=$course_mod_id&name=$file&file=".$filearea."/".$file."&offset=".$offset."&sesskey=".sesskey();
                        $remove_link='[<a href="'.$deleteurl.'">'.get_string("removelink", "assignment").'</a>]'."";
                    }
                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                    $output .= $file.' ['.$filesize.'] '.$remove_link.'<br />';
                }
            }
        }
        $output = '<div class="files">'.$output.'</div>';
        return $output;
    }

    /*
      print student's files. 
      if it's teacher view - teacher can view the files, but can't delete them
      if it's student's view - student can delete files, but can't view them
    */
    function print_user_files($userid=0, $return=false) {
        global $CFG, $USER;

        require_once($CFG->libdir.'/filelib.php');


        if (!$userid) {
            if (!isloggedin()) {
                return '';
            }
            $userid = $USER->id;
        }

        $filearea = $this->file_area_name($userid);
        $output = '';

        if ($basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                foreach ($files as $key => $file) {

                    $icon = mimeinfo('icon', $file);
                    //get filesize for displaying
                    $filesize = display_size(filesize($basedir."/".$file));

                    if ($CFG->slasharguments) {
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    }
                    else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    } 
                    if (isteacher($this->course->id, $USER->id, true)) {
                        $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" /><a href="'.$ffurl.'" >'.$file.'</a> ['.$filesize.'] <br />';
                    }
                    else {
                        if (isset($USER->id)) {
                            if ($submission = $this->get_submission($USER->id)){
                                //changed timemodified=0 for Draft assignments so student's see their own submissions                        
                                if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                                    $remove_link = ''; 
                                    if ($submission->data1 == get_string("submissionstatusdraft", "assignment") || $submission->data1 == get_string("submissionstatusreturned", "assignment")) {
                                        $course_mod_id=$this->cm->id;  
                                        $deleteurl="$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=0&view=student&userid=$userid&id=$course_mod_id&name=$file&file=".$filearea."/".$file."&sesskey=".sesskey();
                                        $remove_link= '[<a href="'.$deleteurl.'">'.get_string("removelink", "assignment").'</a>]'; //students of the course
                                    }
                                    $file_path = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                                    $file_link = '<a href="'.$file_path.'">'.$file.'</a>';
                                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.$file_link.' ['.$filesize.']'.$remove_link.'<br />';                                
                                }
                                else {
                                    $output .= '';
                                }
                            }
                        }
                    }
                }
            }
        }

        $output = '<div class="files">'.$output.'</div>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    /*
     *Display all the submissions ready for grading    
     *from lib.php
     *we needed so select more information from database and add 
     *upload_statuses display
     */
    function display_submissions() {
        global $CFG, $db, $USER;

        require_once($CFG->libdir.'/tablelib.php');


        //see if the form has just been submitted to request user_preference updates
        $updatepref = optional_param('updatepref', '', PARAM_ALPHANUM);
        if (isset($updatepref)){
            $perpage = optional_param('perpage', 10, PARAM_INT);
            $perpage = ($perpage <= 0) ? 10 : $perpage ;
            set_user_preference('assignment_perpage', $perpage);
            set_user_preference('assignment_quickgrade', optional_param('quickgrade',0, PARAM_BOOL));
        }

        // get perpage and quickgrade params from database
        $perpage    = get_user_preferences('assignment_perpage', 10);
        $quickgrade = get_user_preferences('assignment_quickgrade', 0);
 
        $teacherattempts = true;
        $page    = optional_param('page', 0, PARAM_INT);
        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

        //Some shortcuts to make the code read better
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;

        //tabindex for quick grading tabbing; Not working for dropdowns yet  
        $tabindex = 1; 

        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id='.$this->assignment->id, $this->assignment->id, $this->cm->id);

        print_header_simple(format_string($this->assignment->name,true), "", '<a href="index.php?id='.$course->id.'">'.$this->strassignments.'</a> -> <a href="view.php?a='.$this->assignment->id.'">'.format_string($this->assignment->name,true).'</a> -> '. $this->strsubmissions, '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));

        //change column name to upload_status to allow ordering by upload_statuses
        $tablecolumns = array('picture', 'fullname', 'grade', 'comment', 'timemodified', 'timemarked', 'upload status');
        $tableheaders = array('', get_string('fullname'), get_string('grade'), get_string('comment', 'assignment'), get_string('lastmodified').' ('.$course->student.')', get_string('lastmodified').' ('.$course->teacher.')', get_string('status'));

        $table = new flexible_table('mod-assignment-submissions');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/assignment/submissions.php?id='.$this->cm->id);

        $table->sortable(true);
        $table->collapsible(true);
        $table->initialbars(true);

        $table->column_suppress('picture');
        $table->column_suppress('fullname');

        $table->column_class('picture', 'picture');
        $table->column_class('fullname', 'fullname');
        $table->column_class('grade', 'grade');
        $table->column_class('comment', 'comment');
        $table->column_class('timemodified', 'timemodified');
        $table->column_class('timemarked', 'timemarked');
        $table->column_class('status', 'status');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'submissions');
        $table->set_attribute('width', '90%');
        $table->set_attribute('align', 'center');

        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();

        // Check to see if groups are being used in this assignment
        // Groups are being used
        if ($groupmode = groupmode($course, $cm)) {   
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
        } 
        else {
            $currentgroup = false;
        }

        // Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } 
        else {
            $users = get_course_users($course->id);
        }

        if (!$teacherattempts) {
            $teachers = get_course_teachers($course->id);
            if (!empty($teachers)) {
                $keys = array_keys($teachers);
            }
            foreach ($keys as $key) {
                unset($users[$key]);
            }
        }

       if (empty($users)) {
            print_heading(get_string('noattempts','assignment'));
            return true;
        }

        $table->pagesize($perpage, count($users));

        $customQuery = display_submissions_sql($table, $users, $this->assignment->id);

        // offset used to calculate index of student in that particular query, needed for the pop up to know who's next
        $offset = $page * $perpage;

        if (($ausers = get_records_sql($customQuery)) !== false) {

            foreach ($ausers as $auser) {
                $picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);

                if (empty($auser->submissionid)) {
                    $auser->grade = -1; //no submission yet
                }

                //if there is no upload status, then display "blank"
                if (empty($auser->upload_status)) {
                    $auser->upload_status=get_string("submissionstatusblank", "assignment");
                }
                if (!empty($auser->submissionid)) {
                   
                  //Print student answer and student modified date attach file or print link to student answer, depending on the type of the assignment.
                  //Refer to print_student_answer in inherited classes do not display draft submissions to marker.                   
                    if ($auser->timemodified > 0 &&  $auser->upload_status != get_string("submissionstatusdraft", "assignment")) {
                        $studentmodified = '<div id="ts'.$auser->id.'">'.$this->print_student_answer($auser->id).userdate($auser->timemodified).'</div>';
                    } else {
                        $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    }
                    
                    ///Print grade, dropdown or text
                    if ($auser->timemarked > 0) {
                        //display teachers feedback files here as well
                        $teachermodified = '<div id="tt'.$auser->id.'">'.$this->print_user_response_files($auser->id,false).userdate($auser->timemarked).'</div>';
                        //disable grading ability in case of Blank or Draft assignment
                        if ($quickgrade ){
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade),
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }

                    } else {
                        $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';

                        if ($quickgrade && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment")){
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade),
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';                            
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }
                    }
                    ///Print Comment
                    if ($quickgrade && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment")){
                        $comment = '<div id="com'.$auser->id.'"><textarea tabindex="'.$tabindex++.'" name="comment['.$auser->id.']" id="comment['.$auser->id.']">'.($auser->comment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($auser->comment),15).'</div>';
                    }
                } else {
                    $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                    $status          = '<div id="st'.$auser->id.'">&nbsp;</div>';

                    if ($quickgrade  && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment") ){ 
                        // allow editing
                        $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade),
                                 'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                    } else {
                        $grade = '<div id="g'.$auser->id.'">-</div>';
                    }
                    if ($quickgrade && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment") ){
                        $comment = '<div id="com'.$auser->id.'"><textarea tabindex="'.$tabindex++.'" name="comment['.$auser->id.']" id="comment['.$auser->id.']">'.($auser->comment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">&nbsp;</div>';
                    }
                }

                //display upload statuses instead of old ones ={Grade, Update}
            
                $buttontext=$auser->upload_status;

        //do not display link to the grading pop-up if upload_status={Blank, Draft}
                if ($auser->upload_status == get_string("submissionstatusdraft", "assignment") || $auser->upload_status ==  get_string("submissionstatusblank", "assignment")){
                   $button = $buttontext;
                }
                else{
                
                   ///No more buttons, we use popups ;-).                
                   $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$auser->id.'&amp;mode=single'.'&amp;offset='.$offset++,'grade'.$auser->id, $buttontext, 500, 780, $buttontext, 'none', true, 'button'.$auser->id); 
                }
                //changed to $auser->upload_status
                $status  = '<div id="up'.$auser->id.'" class="s'.$auser->upload_status.'">'.$button.'</div>';
                $row = array($picture, $auser->firstname. ' '. $auser->lastname, $grade, $comment, $studentmodified, $teachermodified, $status);
                $table->add_data($row);
            }
        
        }

        /// Print quickgrade form around the table
        if ($quickgrade){
            echo '<form action="submissions.php" name="fastg" method="post">';
            echo '<input type="hidden" name="id" value="'.$this->cm->id.'">';
            echo '<input type="hidden" name="mode" value="fastgrade">';
            echo '<input type="hidden" name="page" value="'.$page.'">';
        }

        $table->print_html();  /// Print the whole table

        if ($quickgrade){
            echo '<p align="center"><input type="submit" name="fastg" value="'.get_string('saveallfeedback', 'assignment').'" /></p>';
            echo '</form>';
        }
        /// End of fast grading form

        /// Mini form for setting user preference
        echo '<br />';
        echo '<form name="options" action="submissions.php?id='.$this->cm->id.'" method="post">';
        echo '<input type="hidden" id="updatepref" name="updatepref" value="1" />';
        echo '<table id="optiontable" align="center">';
        echo '<tr align="right"><td>';
        echo '<label for="perpage">'.get_string('pagesize','assignment').'</label>';
        echo ':</td>';
        echo '<td align="left">';
        echo '<input type="text" id="perpage" name="perpage" size="1" value="'.$perpage.'" />';
        helpbutton('pagesize', get_string('pagesize','assignment'), 'assignment');
        echo '</td></tr>';
        echo '<tr align="right">';
        echo '<td>';
        print_string('quickgrade','assignment');
        echo ':</td>';
        echo '<td align="left">';
        if ($quickgrade){
            echo '<input type="checkbox" name="quickgrade" value="1" checked="checked" />';
        } else {
            echo '<input type="checkbox" name="quickgrade" value="1" />';
        }
        helpbutton('quickgrade', get_string('quickgrade', 'assignment'), 'assignment').'</p></div>';
        echo '</td></tr>';
        echo '<tr>';
        echo '<td colspan="2" align="right">';
        echo '<input type="submit" value="'.get_string('savepreferences').'" />';
        echo '</td></tr></table>';
        echo '</form>';
        ///End of mini form
        print_footer($this->course);
    }

    //deletes submitted file (assignment or response)
    function deleteonesubmission (){
         global $CFG, $USER;
         
         require_login($this->course->id, false, $cm);

         require_once($CFG->libdir.'/filelib.php');

         $id      = required_param('id', PARAM_INT);            //Course module ID
         $a       = optional_param('a', '', PARAM_INT);         //Assignment ID
         $file    = optional_param('file', '', PARAM_PATH);     //file path 
         $userid  = $USER->id;                                  //current user's id
         $confirm = optional_param('confirm', '', PARAM_INT);   //confirmation of deletion
         $name    = optional_param('name', '', PARAM_FILE);     //name of file
         $offset  = optional_param('offset', '', PARAM_INT);    //offset value
         $view    = optional_param('view', '', PARAM_ALPHA);  

         $submission = $this->get_submission($USER->id);        //teacher or student view 
         $filearea = $this->file_area_name($userid);
         
         //path where upload assignment is located
         $filepath = "$CFG->dataroot/$filearea/$name";
         $SESSION->file_path = $filepath;
         $fileresponse = $this->response_file_area_name($userid);
        
         //path where reponse files are held
         $fileresponsepath = "$CFG->dataroot/$fileresponse/$name";
         $SESSION->file_response_path = $fileresponsepath;
         
         if ($view == 'teacher'){
          if (!isteacher($this->course->id, $USER->id, true)){
            error(get_string('deleteerror', 'assignment'));         
          } else {  
            $yes_url = "$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=1&view=teacher&userid=$userid&id=$id&name=$name&offset=$offset&sesskey=".sesskey();
            $no_url =  "../../submissions.php?userid=$userid&id=$id&mode=single&offset=$offset";
            
            $back_button = get_string("backtofeedback", "assignment");
            $action_url = '../../submissions.php';
            }   
         }else{
            $yes_url = "$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=1&view=student&userid=$userid&id=$id&name=$name&offset=$offset&sesskey=".sesskey();
            $no_url =  "../../view.php?id=$id&offset=$offset";
            $back_button = get_string("backtoassignment", "assignment");
            $action_url = '../../view.php';
         }
         
        if ($view == 'student') $this->view_header();
        
            // see if the user is a student if not, then they can't delete files, except for their assignment response file 
            if (!isstudent($this->courseid->id, $USER->id) || isteacher($this->course->id)){
                if (!empty($confirm)) {
                    //this code will allow the teacher to delete the user response
                    //file
                    if($view == 'teacher') {
                        fulldelete($SESSION->file_response_path);}
                    else {
                    if (!fulldelete($SESSION->file_path)) { 
                        print_object($SESSION);
                        error(get_string("deletefail", "assignment").'  '.$name);
                    }
                else {
                    //if student deletes submitted files then numfiles should be changed
                    if ($view == 'student'){
                        //deleting the files                                                    
                        fulldelete($SESSION->file_path);   

                        $submission->numfiles--;

                        if (update_record("assignment_submissions", $submission)) {
                            error(get_string("deleteednotification", "assignment"));
                        } 
                        else {  
                            error(get_string("deletefail", "assignment"));
                        }
                   }
                   else {
                      error(get_string("deleteednotification", "assignment")); 
                   }
                } 
            }
            echo "<form action=\"".$action_url."\">";
            echo '<input type="hidden" name="offset" value="'.$offset.'">';
            echo "<input type=\"hidden\" value=\"$userid\" name=\"userid\">";
            echo "<input type=\"hidden\" value=\"$id\" name=\"id\">";
            echo "<input type=\"hidden\" value=\"single\" name=\"mode\">";
            echo "<center><input type=\"submit\" value=\"".$back_button."\" name=\"submit\"></center></form>";
         
            } 
            else {
                notify (get_string("namedeletefile", "assignment"));
                notify($name);
                notice_yesno (get_string("deletecheckfile", "assignment"), $yes_url, $no_url);
            }
        }
        else {
            error(get_string('unauthorizeduser', 'assignment'));
        }

        if ($view == 'student') {
            $this->view_footer();
        }

        unset($SESSION->file_path);
        unset($SESSION->file_response_path);
    }
}
?>
