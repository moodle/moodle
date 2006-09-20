<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload one or more files and where teacher can upload one or more response files
 *
 */
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
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    } else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    }
                    //died right here
                    //require_once($ffurl);                
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

        $this->view_header();

        $this->view_intro();

        $this->view_dates(); 

        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) { 
            if ($submission->timemarked) {
                $this->view_feedback();
            }else if ($filecount) { 
                print_simple_box($this->print_user_files($USER->id, true), 'center');
            }
            //from upload&review. check if there are responses from teacher  
        if ($submission->data2 != NULL) {
                print_heading(get_string('responsesfromteacher', 'assignment'));
                $this->print_response_students(true);
            }
        }

        //display Blank if there were no files uploaded yet, otherwithe display submission status
        if (!$submission->data1) {
            $submission_status = get_string('submissionstatusblank','assignment');
        }
        else {
            $submission_status = $submission->data1;
        }

        //display submisison status
        notify("<b>".get_string('submissionstatus', 'assignment')." </b> ".$submission_status);

        if ($submission->data1 != get_string("submissionstatussubmitted", "assignment") && has_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id)) && $this->isopen() && 
            ((!$this->assignment->var1 && $submission->data1 == get_string("submissionstatusreturned", "assignment")) || 
             // $this->assignment->var1 || 
             !$filecount || 
             ($this->assignment->resubmit &&  $submission->data1 == get_string("submissionstatusmarked", "assignment")) ||
             $submission->data1 == get_string("submissionstatusreturned", "assignment") || 
             !$submission->timemarked)
        ) {
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
        echo "<p><b>".get_string('step1','assignment')."</b>".get_string('attachfiletoassignment','assignment')." ($strmaxsize)</p>";//$struploadafile
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->libdir.'/uploadlib.php');
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

        $userid = required_param('userid');

        echo '<center>';
        echo get_string('choosereviewfile','assignment').'<br>';

        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/upload.php\">";
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="offset" value="'.$offset.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->libdir.'/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);       
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</form>';
        echo '</center>';
    }

    //general function which calls function for drafts upload, final upload, teachers responce upload
    function upload(){
        global $offset;

        //if this is final submit
        $savestr = optional_param('save', '', PARAM_ALPHA);
        if ($savestr === get_string('sendformarking','assignment')) {              

           $this->final_upload();

        } else {
           //if this is draft upload
           if ($_POST['save']==get_string('attachfile','assignment') && !isset($_POST['userid'])){         

               $this->submission_upload();

           } else {                                         
               //if this is upload of teacher's response
               $id      = optional_param('id', 0, PARAM_INT);         // Course module ID
               $a       = optional_param('a', 0, PARAM_INT);          // Assignment ID
               $userid  = required_param('userid', 0, PARAM_INT);     // Stores student id for uploading a review file to
               $this->response_upload($userid);   // Upload files
               echo "<form action=\"submissions.php\">";
               echo "<input type=\"hidden\" value=\"$userid\" name=\"userid\">";
               echo "<input type=\"hidden\" value=\"$id\" name=\"id\"><input type=\"hidden\" value=\"$a\" name=\"a\"><input type=\"hidden\" value=\"single\" name=\"mode\">";
               echo '<input type="hidden" name="offset" value="'.$offset.'" />';
               echo "<center><input type=\"submit\" value=\"Back to Feedback\" name=\"submit\"></center></form>";
           }
        }    
    }

    //implements upload regular submissuion - draft files
    function submission_upload() {
        global $CFG, $USER, $counter;

        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));

        $this->view_header(get_string('upload'));

        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);

        //need to get student's directory and all previously uploaded files
        $basedir = $this->file_area($USER->id);
        $files = get_directory_list($basedir);

        if ($this->isopen()) {//&& ($this->assignment->var1 || !$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }

            $dir = $this->file_area_name($USER->id);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',!$this->assignment->var1,false,$course,false,$this->assignment->maxbytes);
 
            if ($um->process_file_uploads($dir)) { 
                $newfile_name = $um->get_new_filename();
                //if student already submitted smth before
                if ($submission) {  
                    // assignment is not submited for marking
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
                    } else {
                      //if file with the same name has not been uploaded before  
                        if (!$flag) {
                            $submission->numfiles ++;
                        }
                    }
                    
                    $submission->comment = addslashes($submission->comment);
                    unset($submission->data1);  // Don't need to update this.
                    //unset($submission->data2);  // Don't need to update this.
                    if (update_record("assignment_submissions", $submission)) { 
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        //we email teachers on final upload
                        //$this->email_teachers($submission);
                        print_heading(get_string('uploadedfile'));
                    } else { 
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }
                //if it's first student's submission
                } else { 
                    $newsubmission = $this->prepare_new_submission($USER->id);
                    //submissions has been created, but not submitted for marking
                    $newsubmission->timecreated  = time();
                    $newsubmission->timemodified = '0';
                    $newsubmission->data1 = get_string("submissionstatusdraft", "assignment");
                    $newsubmission->numfiles = 1;
                    if (insert_record('assignment_submissions', $newsubmission)) { 
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                         //we email teachers on final upload
                        //$this->email_teachers($newsubmission);
                        print_heading(get_string('uploadedfile'));
                    } else { 
                        notify(get_string("uploadnotregistered", "assignment", $newfile_name));
                    }
                }
            }
        } else {  
            notify(get_string("uploaderror", "assignment")); //submitting not allowed!
        }

        print_continue('view.php?id='.$this->cm->id);

        $this->view_footer();
    }

    //implements final upload (submitting for marking)
    function final_upload() { 
        global $CFG, $USER;

        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));

        $this->view_header(get_string('upload'));
        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);

        if ($this->isopen() && ($this->assignment->var1 || $filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }

            $dir = $this->file_area_name($USER->id);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',!$this->assignment->var1,false,$course,false,$this->assignment->maxbytes);

            //files hass been preprocessed i saved already, we don't need to do it again
            //if ($um->process_file_uploads($dir)) { 
            $newfile_name = $um->get_new_filename(); 

            //if student already submitted smth before
            if ($submission) { 
                //if there is no files uploaded we can't do final submit
                 if ( $submission->numfiles == 0){ 
                     notify(get_string("nofilesforsubmit","assignment"));//'uploadnofilefound'));
                 }else{ 
                    $submission->timemodified = time();
                    //$submission->numfiles ++;
                    //$submission->comment = addslashes($submission->comment);
                    $submission->data1 = get_string("submissionstatussubmitted", "assignment");
                    //unset($submission->data2);  // Don't need to update this.     
                    if (update_record("assignment_submissions", $submission)) {
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $this->email_teachers($submission);
                        print_heading(get_string('markingsubmitnotification','assignment'));

                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }    
                 }
            //if it's first student's submission
            } else {/*//probably this block can be removed. 
                 $newsubmission = $this->prepare_new_submission($USER->id);
                 $newsubmission->timecreated  = '0';
                 $newsubmission->timemodified = '1';
                 $newsubmission->data1 = get_string("submissionstatussubmitted", "assignment");  
                 $newsubmission->numfiles = 1;
                 if (insert_record('assignment_submissions', $newsubmission)) {
                     add_to_log($this->course->id, 'assignment', 'upload',
                            'view.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                     $this->email_teachers($newsubmission);
                     print_heading(get_string('markingsubmitnotification','assignment'));
                 } else {
                     notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                 }*/ 
                notify(get_string("nofilesforsubmit","assignment"));
            }
        //  }
        } else {   
            notify(get_string("uploaderror", "assignment")); //submitting not allowed!
        }

        print_continue('view.php?id='.$this->cm->id);

        $this->view_footer();
    }


    //from upload&review 
    function response_file_area_name($userid, $teachid = 0) {
     //  Creates a directory file name, suitable for make_upload_directory()
        global $CFG, $USER;
        $fileloc = "";
        if ($teachid == 0) {
            $fileloc = "$USER->id/$userid";
        } else {
            $fileloc = "$teachid/$USER->id";
        }
        return $this->course->id.'/'.$CFG->moddata.'/assignment/'.$this->assignment->id.'/responses/'.$fileloc;
     }

     //from upload&review
     //make the folder which going to hold response files
     function response_file_area($userid, $teachid = 0) {
        if ($teachid == 0) {
            return make_upload_directory( $this->response_file_area_name($userid) );
        } else {
            return make_upload_directory( $this->response_file_area_name($userid, $teachid) );
        }
     }

     //from upload&review
     //upload responce file   
     function response_upload($userid) {
        global $CFG, $USER;

       // $this->view_header(get_string('upload'));

        if (!$this->isopen()) {
            notify(get_string("uploadfailnoupdate", "assignment"));
        } else {
            $submission = $this->get_submission($userid);

            $dir = $this->response_file_area_name($userid);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            //$um = new upload_manager('newfile',true,false,$course,false,$this->assignment->maxbytes);
            //set up $deletothers=false to allow multiple feedback uploads
            $um = new upload_manager('newfile',false,false,$course,false,$this->assignment->maxbytes);

            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    // stores teacher id's in data2 in comma-separated list so students can view all responses from all teachers
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
                    } else {
                        $submission->data2 = $USER->id;
                    }
                    //$submission->timemodified = time();
                    //$submission->numfiles     = 1;
                    //$submission->comment = addslashes($submission->comment);

                    if (update_record("assignment_submissions", $submission)) {
                        $this->email_students($submission);
                        //print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    } 
                } else {
                    notify(get_string("studentrecorderror", "assignment"));
                }
                notify(get_string("uploadsuccessresponse", "assignment"));
            }
        }

       // print_continue('view.php?id='.$this->cm->id);

       // $this->view_footer();
    }

     //from upload&review
    function email_students($submission) {
        /// Alerts students by email of assignments that recieve a new response
//      Email students when uploaded & when grade changed?
        global $CFG;

        //changed from var1 to var2 becasue of the merging two assignment types
        if (empty($this->assignment->var2)) {          // No need to do anything
            return;
        }
/*
        $user = get_record('user', 'id', $submission->userid);

        if (groupmode($this->course, $this->cm) == SEPARATEGROUPS) {   // Separate groups are being used
            if (!$group = user_group($this->course->id, $user->id)) {             // Try to find a group
                $group->id = 0;                                             // Not in a group, never mind
            }
            $teachers = get_group_teachers($this->course->id, $group->id);        // Works even if not in group
        } else {
            $teachers = get_course_teachers($this->course->id);
        }

        if ($teachers) {

            $strassignments = get_string('modulenameplural', 'assignment');
            $strassignment  = get_string('modulename', 'assignment');
            $strsubmitted  = get_string('submitted', 'assignment');

            foreach ($teachers as $teacher) {
                unset($info);
                $info->username = fullname($user);
                $info->assignment = format_string($this->assignment->name,true);
                $info->url = $CFG->wwwroot.'/mod/assignment/submissions.php?id='.$this->cm->id;

                $postsubject = $strsubmitted.': '.$info->username.' -> '.$this->assignment->name;
                $posttext = $this->email_teachers_text($info);
                $posthtml = ($teacher->mailformat == 1) ? $this->email_teachers_html($info) : '';

                @email_to_user($teacher, $user, $postsubject, $posttext, $posthtml);  // If it fails, oh well, too bad.
            }
        }
*/
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
        //removed by Oksana. it was braking functionality and submitting teacher's feedback to.. teacher-user
        //this was inherited from upload type. check if nothing brackes???????????????
        if ((int)$feedback->saveuserid !== -1) {
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
        //marker graded assignment then status set into Marked; if marker didn't grade it then status set into Returned 
        if (/*$feedback->grade != 0 && */ $feedback->grade != -1 ) {
            $newsubmission->data1 = get_string("submissionstatusmarked", "assignment");
        }
        else {
            $newsubmission->data1 = get_string("submissionstatusreturned", "assignment");
        }
        //unset($newsubmission->data1);  // Don't need to update this.
        //unset($newsubmission->data2);  // Don't need to update this.

        if (! update_record('assignment_submissions', $newsubmission)) {
            return false;
        }
        
        add_to_log($this->course->id, 'assignment', 'update grades', 
                   'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);   
        
        return $newsubmission;
                 
    }   
    
    /*
     * Top-level function for handling of submissions called by submissions.php
     *
     */
    //from lib.php
    //needed to update case of fastgrading. upgrade upload_statuses
    function submissions($mode) {
        ///The main switch is changed to facilitate
        ///1) Batch fast grading
        ///2) Skip to the next one on the popup
        ///3) Save and Skip to the next one on the popup

        //make user global so we can use the id
        global $USER;

        switch ($mode) {
            case 'grade':                         // We are in a popup window grading
                if ($submission = $this->process_feedback()) {
                    //IE needs proper header with encoding
                    print_header(get_string('feedback', 'assignment').':'.format_string($this->assignment->name));
                    print_heading(get_string('changessaved'));
                    $this->update_main_listing($submission);
                } 
                close_window();

                break;

            case 'single':                        // We are in a popup window displaying submission
                $this->display_submission();
                break;

            case 'all':                           // Main window, display everything
                $this->display_submissions();
                break;

            case 'fastgrade': 
                ///do the fast grading stuff  - this process should work for all 3 subclasses
                $grading    = false;
                $commenting = false;
                $col        = false;
                if (isset($_POST['comment'])) {
                    $col = 'comment';
                    $commenting = true;
                }
                if (isset($_POST['menu'])) {
                    $col = 'menu';
                    $grading = true;
                }
                if (!$col) {
                    //both comment and grade columns collapsed..
                    $this->display_submissions();
                    break;
                }

                foreach ($_POST[$col] as $id => $unusedvalue) {

                    $id = (int)$id; //clean parameter name
                    if (!$submission = $this->get_submission($id)) {
                        $submission = $this->prepare_new_submission($id);
                        $newsubmission = true;
                    } else {
                        $newsubmission = false;
                    }
                    unset($submission->data1);  // Don't need to update this.
                    unset($submission->data2);  // Don't need to update this.

                    //for fast grade, we need to check if any changes take place
                    $updatedb = false;

                    if ($grading) { 
                        $grade = $_POST['menu'][$id];  
                        $updatedb = $updatedb || ($submission->grade != $grade);
                        $submission->grade = $grade;
                    } else {
                        if (!$newsubmission) {
                            unset($submission->grade);  // Don't need to update this.
                        }
                    }

                    //change status if assignment was graded or returned
                    if ($submission->grade != -1 ) {
                         $submission->data1 = get_string("submissionstatusmarked", "assignment");
                    }
                    else {
                         $submission->data1 = get_string("submissionstatusreturned", "assignment");
                    }
                    if ($commenting) {
                        $commentvalue = trim($_POST['comment'][$id]);
                        $updatedb = $updatedb || ($submission->comment != stripslashes($commentvalue));
                        $submission->comment = $commentvalue;
                    } else {
                        unset($submission->comment);  // Don't need to update this.
                    }

                    $submission->teacher    = $USER->id;
                    $submission->mailed     = $updatedb?0:$submission->mailed;//only change if it's an update
                    $submission->timemarked = time();

                    //if it is not an update, we don't change the last modified time etc.
                    //this will also not write into database if no comment and grade is entered.

                    if ($updatedb) {
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
                        add_to_log($this->course->id, 'assignment', 'update grades',
                                   'submissions.php?id='.$this->assignment->id.'&user='.$submission->userid,
                                   $submission->userid, $this->cm->id);
                    }

                }
                $this->display_submissions();
                break;


            case 'next':
                /// We are currently in pop up, but we want to skip to next one without saving.
                ///    This turns out to be similar to a single case
                /// The URL used is for the next submission.

                $this->display_submission();
                break;

            case 'saveandnext':
                ///We are in pop up. save the current one and go to the next one.
                //first we save the current changes
                if ($submission = $this->process_feedback()) {
                    //print_heading(get_string('changessaved'));
                    $this->update_main_listing($submission);
                }

                //then we display the next submission
                $this->display_submission();
                break;

            default:
                echo "something seriously is wrong!!";
                break;
        }
    }

    //function that updates the listing on the main script from popup using javascript
    //from lib.php
    //needed to display teachers responce files and upload_statuses
    function update_main_listing($submission) {
        global $SESSION;

        $perpage = get_user_preferences('assignment_perpage', 10);

        $quickgrade = get_user_preferences('assignment_quickgrade', 0);

        /// Run some Javascript to try and update the parent page
        echo '<script type="text/javascript">'."\n<!--\n";
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['comment'])) {
            if ($quickgrade){
                echo 'opener.document.getElementById("comment['.$submission->userid.']").value="'
                .trim($submission->comment).'";'."\n";
             } else {
                echo 'opener.document.getElementById("com'.$submission->userid.
                '").innerHTML="'.shorten_text(trim(strip_tags($submission->comment)), 15)."\";\n";
            }
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['grade'])) {
            //echo optional_param('menuindex');
            if ($quickgrade){
                echo 'opener.document.getElementById("menumenu['.$submission->userid.
                ']").selectedIndex="'.required_param('menuindex', 0, PARAM_INT).'";'."\n";
            } else {
                echo 'opener.document.getElementById("g'.$submission->userid.'").innerHTML="'.
                $this->display_grade($submission->grade)."\";\n";
            }
        }

        //need to add student's assignments in there too.
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemodified']) &&
            $submission->timemodified) {
            echo 'opener.document.getElementById("ts'.$submission->userid.
                 '").innerHTML="'.addslashes($this->print_student_answer($submission->userid)).userdate($submission->timemodified)."\";\n";
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemarked']) &&
            $submission->timemarked) {
            //display teachers feedback files here as well
            echo 'opener.document.getElementById("tt'.$submission->userid.
                 '").innerHTML="'.addslashes($this->print_user_response_files($submission->userid,false)).userdate($submission->timemarked)."\";\n";
         }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['status'])) {
            echo 'opener.document.getElementById("up'.$submission->userid.'").className="s1";';
            //replace "Update" by upload_status
            $buttontext =  $submission->data1; //get_string('update');             
            $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$submission->userid.'&amp;mode=single'.'&amp;offset='.optional_param('offset', '', PARAM_INT),
                      'grade'.$submission->userid, $buttontext, 450, 700, $buttontext, 'none', true, 'button'.$submission->userid);
            echo 'opener.document.getElementById("up'.$submission->userid.'").innerHTML="'.addslashes($button).'";';
        }
        echo "\n-->\n</script>";
        flush();
    }

//display student's submission for marking in pop-up window
    function display_submission() {

        global $CFG, $offset;

        $userid = required_param('userid', PARAM_INT);
        $offset = required_param('offset', PARAM_INT);//offset for where to start looking for student.

        if (!$user = get_record('user', 'id', $userid)) {
            error('No such user!');
        }

        if (!$submission = $this->get_submission($user->id)) {
            $submission = $this->prepare_new_submission($userid);
        }

        if ($submission->timemodified > $submission->timemarked) {
            $subtype = 'assignmentnew';
        } else {
            $subtype = 'assignmentold';
        }

        ///construct SQL, using current offset to find the data of the next student
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;

        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
        } else {
            $currentgroup = false;
        }

    /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $users = get_course_users($course->id);
        }

        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture,'.
                  's.id AS submissionid, s.grade, s.comment, s.timemodified, s.timemarked, ((s.timemarked > 0) AND (s.timemarked >= s.timemodified)) AS status ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid AND s.assignment = '.$this->assignment->id.' '.
               'WHERE u.id IN ('.implode(',', array_keys($users)).') AND s.data1 <> "Draft"  AND s.data1 <> ""';
        //we don't need to grade draft or empty assignments

        require_once($CFG->libdir.'/tablelib.php');
        if ($sort = flexible_table::get_sql_sort('mod-assignment-submissions')) {
            $sort = 'ORDER BY '.$sort.' ';
        }

        $limit = sql_paging_limit($offset+1, 1);
        $nextid = 0;
        if (($auser = get_record_sql($select.$sql.$sort.$limit, false, true)) !== false) {
            $nextid = $auser->id;
        }
//1111
//echo "$auser->id $auser->firstname $auser->lastname -next user <br /> $offset -- offset <br /> $userid -- present userid ";
//echo $offset."-offset on display submission<br>";
        print_header(get_string('feedback', 'assignment').':'.fullname($user, true).':'.format_string($this->assignment->name));

        ///SOme javascript to help with setting up >.>

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

//+++
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
        echo '<input type="hidden" name="offset" value="'.++$offset.'">'; //was ++$offset!!!!!!!
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="mode" value="grade" />';
        echo '<input type="hidden" name="menuindex" value="0" />';//selected menu index
  //new hidden field, initialized to -1.
        echo '<input type="hidden" name="saveuserid" value="-1" />';
//++++++
        // Shows teacher response files to teacher
        echo "<div class=\"from\">". get_string('responsefile','assignment')."</div>";
        echo $this->print_user_response_files($userid,true,--$offset);
        echo "<br /><br />";
////++++
        if ($submission->timemarked) {
            echo '<div class="from">';
            echo '<div class="fullname">'.fullname($teacher, true).'</div>';
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
        } else {
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
  ///End of teacher info row, Start of student info row
        echo '<tr>';
        echo '<td width="35" valign="top" class="picture user">';
        print_user_picture($user->id, $this->course->id, $user->picture);
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        echo '<div class="fullname">'.fullname($user, true).'</div>';
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

    //from upload&review
    // Prints response files to students
    function print_response_students($return) {
        global $CFG, $USER;

        $stuid = $USER->id;

        echo '<table border="0" align="center" cellpadding="5" cellspacing="1" class="feedback">';

        $submission = $this->get_submission($stuid);
        if ($teachids = $submission->data2) {           // Only will show files if there is a submission
            $teachidarr = explode(',', $teachids);

            foreach ($teachidarr as $t) {
                if (! $teacher = get_record('user', 'id', $t)) {
                    print_object($submission);
                    error('Could not find the teacher');
                }
                echo '<tr>';
                echo '<td class="left picture">';
                print_user_picture($teacher->id, $this->course->id, $teacher->picture);
                echo '</td>';
                echo '<td class="topic">';
                echo '<div class="from">';
                echo '<div class="fullname">'.fullname($teacher).'</div>';
                echo '</div>';

                $filearea = $this->response_file_area_name($stuid, $t);
                if ($basedir = $this->response_file_area($stuid, $t)) {
                    $output = '';
                    if ($files = get_directory_list($basedir)) {
                        foreach ($files as $key => $file) {
                            require_once($CFG->libdir.'/filelib.php');
                            $icon = mimeinfo('icon', $file);
                            if ($CFG->slasharguments) {
                                $ffurl = "$CFG->wwwroot/mod/assignment/type/uploadreview/file.php/$filearea/$file";
                            } else {
                                $ffurl = "$CFG->wwwroot/mod/assignment/type/uploadreview/file.php?file=/$filearea/$file";
                            }
                                              /*echo '<div class="files"><center><img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                                               link_to_popup_window ('/'.$ffurl, 'file'.$key, $file, 450, 580, $file, 'none', true).'</div></center><br />';
                                               echo '</td></tr>';*/
                                              //displays multiple teachers responces
                            $output .='<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                            link_to_popup_window ('/'.$ffurl, 'file'.$key, $file, 450, 580, $file, 'none', true)."<br />";
                        }
                    }
                }
                echo '<div class="files"><left>'.$output.'</left></div>';
                echo '</td></tr>';
            }
            echo '</table>';
        }
    }

    //print teacher's files
    function print_user_response_files($stuid,$display_remove_button=false,$offset=NULL) {//, $return=false) {

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
                    } else {
                        $ffurl = "file.php?file=/$filearea/$file";
                    }

                    //get feedback file size, generate and display remove file link
                    $filesize = display_size(filesize($basedir."/".$file));
                    $remove_link=''; 
                    if ($display_remove_button) { 
                         $course_mod_id=$this->cm->id;           
                         $deleteurl="$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=0&view=teacher&userid=$stuid&id=$course_mod_id&name=$file&file=".$basedir."/".$file."&offset=".$offset;
                         $remove_link='[<a href="'.$deleteurl.'">'.get_string("removelink", "assignment").'</a>]';
                    }
                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                             // link_to_popup_window ('/'.$ffurl, 'file'.$key, $file, 450, 580, $file, 'none', true).
                             $file.' ['.$filesize.'] '.$remove_link.'<br />'; 
                }
            }
        }
        $output = '<div class="files">'.$output.'</div>';

        return $output;
    }

    //print student's files. 
    //if it's teacher view - teacher can view the files, but can't delete them
    //if it's student's view - stident can delete files, but can't view them
    function print_user_files($userid=0, $return=false) {
        global $CFG, $USER;

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
                    require_once($CFG->libdir.'/filelib.php');

                    $icon = mimeinfo('icon', $file);
                    //get filesize for displaying
                    $filesize = display_size(filesize($basedir."/".$file));

                    if ($CFG->slasharguments) {
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    } else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    } 
                    if (has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $this->course->id))) {
                        $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                                   '<a href="'.$ffurl.'" >'.$file.'</a> ['.$filesize.'] <br />';
                    } else {
                        if (!empty($USER->id)) {
                            if ($submission = $this->get_submission($USER->id)) { 
                                    //i have changed timemodified=0 for Draft assignments, thats' why we remove this condition 
                                    //otherwise student's dont' se etheir own submissions
//                                 if ($submission->timemodified) { 
                                if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                                            //remove link shouldn't be displayed if file was marked or submited for marking
                                    $remove_link = ''; 
                                    if ($submission->data1 == get_string("submissionstatusdraft", "assignment") || $submission->data1 == get_string("submissionstatusreturned", "assignment")) {
                                        $course_mod_id=$this->cm->id;  
                                        $deleteurl="$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=0&view=student&userid=$userid&id=$course_mod_id&name=$file&file=".$basedir."/".$file;
                                        $remove_link= '[<a href="'.$deleteurl.'">'.get_string("removelink", "assignment").'</a>]'; //students of the course
                                    } 
                                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.$file.' ['.$filesize.']'.$remove_link.'<br />';
                                } else {
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
    *  Display all the submissions ready for grading
    */
    //from lib.php
    //we needed so select more information from database and add upload_statuses display
    function display_submissions() {
         global $CFG, $db, $USER;

        /* first we check to see if the form has just been submitted
         * to request user_preference updates
         */
        if (isset($_POST['updatepref'])) {
            $perpage = optional_param('perpage', 10, PARAM_INT);
            $perpage = ($perpage <= 0) ? 10 : $perpage ;
            set_user_preference('assignment_perpage', $perpage);
            set_user_preference('assignment_quickgrade', optional_param('quickgrade',0, PARAM_BOOL));
        }

        /* next we get perpage and quickgrade (allow quick grade) params
         * from database
         */
        $perpage    = get_user_preferences('assignment_perpage', 10);
        $quickgrade = get_user_preferences('assignment_quickgrade', 0);
 
        $teacherattempts = true; /// Temporary measure
        $page    = optional_param('page', 0, PARAM_INT);
        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

        /// Some shortcuts to make the code read better

        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;

        $tabindex = 1; //tabindex for quick grading tabbing; Not working for dropdowns yet

        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id='.$this->assignment->id, $this->assignment->id, $this->cm->id);

        print_header_simple(format_string($this->assignment->name,true), "", '<a href="index.php?id='.$course->id.'">'.$this->strassignments.'</a> -> <a href="view.php?a='.$this->assignment->id.'">'.format_string($this->assignment->name,true).'</a> -> '. $this->strsubmissions, '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));

        //change column name to upload_status to allow ordering by upload_statuses
        $tablecolumns = array('picture', 'fullname', 'grade', 'comment', 'timemodified', 'timemarked', 'upload_status');
        $tableheaders = array('', get_string('fullname'), get_string('grade'), get_string('comment', 'assignment'), get_string('lastmodified').' ('.$course->student.')', get_string('lastmodified').' ('.$course->teacher.')', get_string('status'));

        require_once($CFG->libdir.'/tablelib.php');
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

        /// Check to see if groups are being used in this assignment
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
        } else {
            $currentgroup = false;
        }

        /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
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

        /// Construct the SQL

        if ($where = $table->get_sql_where()) {
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sort = ' ORDER BY '.$sort;
        }

        //select has been modified. because we also need to get student id & assignment status == upload_status = {Draft, Submitted, Marked, Returned}
        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture, u.idnumber, s.id AS submissionid, s.grade, s.comment, s.timemodified, s.timemarked, ((s.timemarked > 0) AND (s.timemarked >= s.timemodified)) AS status, s.data1 as upload_status ';

        // $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture, s.id AS submissionid, s.grade, s.comment, s.timemodified, s.timemarked, ((s.timemarked > 0) AND (s.timemarked >= s.timemodified)) AS status ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid AND s.assignment = '.$this->assignment->id.' '.
               'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') ';

        $table->pagesize($perpage, count($users));

        if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
            $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
        }
        else {
            $limit = '';
        }

        ///offset used to calculate index of student in that particular query, needed for the pop up to know who's next
        $offset = $page * $perpage;

        // we have changed the wording.
        //$strupdate = get_string('update');
        //$strgrade  = get_string('grade');
        $grademenu = make_grades_menu($this->assignment->grade);

        if (($ausers = get_records_sql($select.$sql.$sort.$limit)) !== false) {

            foreach ($ausers as $auser) {
                $picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);

                if (empty($auser->submissionid)){
                    $auser->grade = -1; //no submission yet
                }

                //if there is no upload status, then display "blank"
                if (empty($auser->upload_status)) {
                    $auser->upload_status=get_string("submissionstatusblank", "assignment");
                }
                if (!empty($auser->submissionid)) {
                   ///Prints student answer and student modified date
                   ///attach file or print link to student answer, depending on the type of the assignment.
                   ///Refer to print_student_answer in inherited classes
                   // do not display draft submissions to marker.
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
                        if ($quickgrade) {//&& ($auser->upload_status != get_string("submissionstatusdraft", "assignment") ||  !$auser->upload_status )){//  get_string("submissionstatusblank", "assignment"))){
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade),
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }

                    } else {
                        $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';

                        if ($quickgrade && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment")) {
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade),
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';                            
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }
                    }
                    ///Print Comment
                    if ($quickgrade && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment")) {
                        $comment = '<div id="com'.$auser->id.'"><textarea tabindex="'.$tabindex++.'" name="comment['.$auser->id.']" id="comment['.$auser->id.']">'.($auser->comment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($auser->comment),15).'</div>';
                    }
                } else {
                    $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                    $status          = '<div id="st'.$auser->id.'">&nbsp;</div>';

                    if ($quickgrade  && $auser->upload_status != get_string("submissionstatusdraft", "assignment") &&  $auser->upload_status != get_string("submissionstatusblank", "assignment")) { // allow editing
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
                //if ($auser->status === NULL) {
                //    $auser->status = 0;
                //}                
                $buttontext=$auser->upload_status;
                //$buttontext = ($auser->status == 1) ? $strupdate : $strgrade;

                //do not display link to the grading pop-up if upload_status={Blank, Draft}
                if ($auser->upload_status == get_string("submissionstatusdraft", "assignment") || $auser->upload_status ==  get_string("submissionstatusblank", "assignment")){
                   $button = $buttontext;
                } else {
                   ///No more buttons, we use popups ;-).                
                   $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$auser->id.'&amp;mode=single'.'&amp;offset='.$offset++,'grade'.$auser->id, $buttontext, 500, 780, $buttontext, 'none', true, 'button'.$auser->id); 
                }
                //changed to $auser->upload_status
                $status  = '<div id="up'.$auser->id.'" class="s'.$auser->upload_status.'">'.$button.'</div>';
                $row = array($picture, $this->fullname($auser), $grade, $comment, $studentmodified, $teachermodified, $status);
                $table->add_data($row);
            }
        }

        /// Print quickgrade form around the table
        if ($quickgrade) {
            echo '<form action="submissions.php" name="fastg" method="post">';
            echo '<input type="hidden" name="id" value="'.$this->cm->id.'">';
            echo '<input type="hidden" name="mode" value="fastgrade">';
            echo '<input type="hidden" name="page" value="'.$page.'">';
        }

        $table->print_html();  /// Print the whole table

        if ($quickgrade) {
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
        if ($quickgrade) {
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
    function deleteonesubmission () {
        global $CFG, $USER;

        require_once($CFG->libdir.'/filelib.php');

        $id      = required_param('id', PARAM_INT); // Course module ID
        $a       = optional_param('a');             // Assignment ID
        $file    = optional_param('file', '', PARAM_PATH);
        $userid  = optional_param('userid');
        $confirm = optional_param('confirm');
        $name    = optional_param('name');
        $offset  = optional_param('offset');
        $view    = optional_param('view');          //teacher or student view

        $submission = $this->get_submission($USER->id);


        if ($view == 'teacher') {
            $yes_url = "$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=1&view=teacher&userid=$userid&id=$id&name=$name&file=$file&offset=$offset";
            $no_url =  "../../submissions.php?userid=$userid&id=$id&mode=single&offset=$offset";
            $back_button = get_string("backtofeedback", "assignment");
            $action_url = '../../submissions.php';
        } else {
            $yes_url = "$CFG->wwwroot/mod/assignment/type/upload/deleteonesubmission.php?confirm=1&view=student&userid=$userid&id=$id&name=$name&file=$file&offset=$offset";
            $no_url =  "../../view.php?id=$id&offset=$offset";
            $back_button = get_string("backtoassignment", "assignment");
            $action_url = '../../view.php';
        }

        if ($view == 'student') {
            $this->view_header();
        }

        if (!empty($confirm)) {

            if (!fulldelete($file)) { 
                notify(get_string("deletefail", "assignment"));
                notify($file);
            } else {
               //if student deletes submitted files then numfiles should be changed
                if ($view == 'student'){
                    $submission->numfiles --;
                    if (update_record("assignment_submissions", $submission)) {
                        notify(get_string("deleteednotification", "assignment"));
                    } else {  
                        notify(get_string("deletefail", "assignment"));
                        notify($file);
                    }
                } else {
                    notify(get_string("deleteednotification", "assignment")); 
                }
                    
            }
/* echo '<form name="submitform" action="submissions.php" method="post">';
        echo '<input type="hidden" name="offset" value="'.++$offset.'">';
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="mode" value="grade" />';
        echo '<input type="hidden" name="menuindex" value="0" />';//selected menu index
  //new hidden field, initialized to -1.
        echo '<input type="hidden" name="saveuserid" value="-1" />';
*/
            echo "<form action=\"".$action_url."\">";
            echo '<input type="hidden" name="offset" value="'.$offset.'">';
            echo "<input type=\"hidden\" value=\"$userid\" name=\"userid\">";
            echo "<input type=\"hidden\" value=\"$id\" name=\"id\">";
           //echo "<input type=\"hidden\" value=\"$a\" name=\"a\">";
            echo "<input type=\"hidden\" value=\"single\" name=\"mode\">";
            echo "<center><input type=\"submit\" value=\"".$back_button."\" name=\"submit\"></center></form>";
             
        } else {
            notify (get_string("namedeletefile", "assignment"));
            notify($name);
            notice_yesno (get_string("deletecheckfile", "assignment"), $yes_url, $no_url);
        }

        if ($view == 'student') $this->view_footer();

    }

  //from moodlelib.php
  //we need to dispaly studentID along with student name in a grading interface
    function fullname($user, $override=false) {

        global $CFG, $SESSION;

        $user_id='';
        if ($user->idnumber) {
            $user_id = ' ('. $user->idnumber .') ';
        }

        if (!isset($user->firstname) and !isset($user->lastname)) {
            return '';
        }

        if (!$override) {
            if (!empty($CFG->forcefirstname)) {
                $user->firstname = $CFG->forcefirstname;
            }
            if (!empty($CFG->forcelastname)) {
                $user->lastname = $CFG->forcelastname;
            }
        }

        if (!empty($SESSION->fullnamedisplay)) {
            $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
        }

        if ($CFG->fullnamedisplay == 'firstname lastname') {
            return $user->firstname .' '. $user->lastname . $user_id;

        } else if ($CFG->fullnamedisplay == 'lastname firstname') {
            return $user->lastname .' '. $user->firstname . $user_id;

        } else if ($CFG->fullnamedisplay == 'firstname') {
            if ($override) {
                return get_string('fullnamedisplay', '', $user);
            } else {
                return $user->firstname . $user_id;;
            }
        } else if ($CFG->fullnamedisplay == 'textuid') {
            if ( $override ) {
               return get_string('fullnamedisplay', '', $user) . $user_id;
            } else if (isset($user->username)) {
               return $user->username . $user_id;
            } else {
               return $user->firstname . $user_id;
            }
        }

        return get_string('fullnamedisplay', '', $user) . $user_id;
    }

}

?>
