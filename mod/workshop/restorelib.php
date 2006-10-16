<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //workshop mods

    //This is the "graphical" structure of the workshop mod:
    //
    //                                          workshop
    //                                         (CL,pk->id)
    //                                             |
    //                                             |
    //                                             |
    //              |------------------------------|-----------------------------------------------------|
    //              |                                                                                    |
    //              |                                                                                    |
    //              |                                                                                    |
    //              |                                                                            workshop_submissions
    //              |                                                                        (UL,pk->id,fk->workshopid,files)
    //              |                                                                                    |
    //              |        |-------------------------------------|      |----------------------|       |
    //              |        |                                     |      |                      |       |
    //             workshop_elements                           workshop_grades                  workshop_assessments
    //         (CL,pk->id,fk->workshopid)                (UL,pk->id,fk->assessmentid)       (UL,pk->id,fk->submissionid)
    //              |                  |                 (          fk->elementno   )                    |
    //              |                  |                                                                 |
    //              |                  |                                                                 |
    //      workshop_rubrics          workshop_stockcomments                                        workshop_comments
    // (CL,pk->id,fk->elementno)   (CL, pk->id, fk->elementno)                             (UL,pk->id,fk->assessmentid)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the restore procedure about this mod
    function workshop_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object   
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Workshop', $restore, $info['MOD']['#'], array('SUBMISSIONSTART','ASSESSMENTSTART', 'SUBMISSIONEND', 'ASSESSMENTEND', 'RELEASEGRADES'));
            }
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the WORKSHOP record structure
            $workshop->course = $restore->course_id;
            $workshop->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $workshop->description = backup_todb($info['MOD']['#']['DESCRIPTION']['0']['#']);
            $workshop->wtype = backup_todb($info['MOD']['#']['WTYPE']['0']['#']);
            $workshop->nelements = backup_todb($info['MOD']['#']['NELEMENTS']['0']['#']);
            $workshop->nattachments = backup_todb($info['MOD']['#']['NATTACHMENTS']['0']['#']);
            $workshop->phase = backup_todb($info['MOD']['#']['PHASE']['0']['#']);
            $workshop->format = backup_todb($info['MOD']['#']['FORMAT']['0']['#']);
            $workshop->gradingstrategy = backup_todb($info['MOD']['#']['GRADINGSTRATEGY']['0']['#']);
            $workshop->resubmit = backup_todb($info['MOD']['#']['RESUBMIT']['0']['#']);
            $workshop->agreeassessments = backup_todb($info['MOD']['#']['AGREEASSESSMENTS']['0']['#']);
            $workshop->hidegrades = backup_todb($info['MOD']['#']['HIDEGRADES']['0']['#']);
            $workshop->anonymous = backup_todb($info['MOD']['#']['ANONYMOUS']['0']['#']);
            $workshop->includeself = backup_todb($info['MOD']['#']['INCLUDESELF']['0']['#']);
            $workshop->maxbytes = backup_todb($info['MOD']['#']['MAXBYTES']['0']['#']);
            $workshop->submissionstart = backup_todb($info['MOD']['#']['SUBMISSIONSTART']['0']['#']);
            $workshop->assessmentstart = backup_todb($info['MOD']['#']['ASSESSMENTSTART']['0']['#']);
            $workshop->deadline = backup_todb($info['MOD']['#']['DEADLINE']['0']['#']);
            $workshop->submissionend = backup_todb($info['MOD']['#']['SUBMISSIONEND']['0']['#']);
            $workshop->assessmentend = backup_todb($info['MOD']['#']['ASSESSMENTEND']['0']['#']);
            $workshop->releasegrades = backup_todb($info['MOD']['#']['RELEASEGRADES']['0']['#']);
            $workshop->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $workshop->gradinggrade = backup_todb($info['MOD']['#']['GRADINGGRADE']['0']['#']);
            $workshop->ntassessments = backup_todb($info['MOD']['#']['NTASSESSMENTS']['0']['#']);
            $workshop->assessmentcomps = backup_todb($info['MOD']['#']['ASSESSMENTCOMPS']['0']['#']);
            $workshop->nsassessments = backup_todb($info['MOD']['#']['NSASSESSMENTS']['0']['#']);
            $workshop->overallocation = backup_todb($info['MOD']['#']['OVERALLOCATION']['0']['#']);
            $workshop->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $workshop->teacherweight = backup_todb($info['MOD']['#']['TEACHERWEIGHT']['0']['#']);
            $workshop->showleaguetable = backup_todb($info['MOD']['#']['SHOWLEAGUETABLE']['0']['#']);
            $workshop->usepassword = backup_todb($info['MOD']['#']['USEPASSWORD']['0']['#']);
            $workshop->password = backup_todb($info['MOD']['#']['PASSWORD']['0']['#']);

            //If we have retrieved workshop->phase, it's a pre 1.5 backup, so we have to do
            //some conversions before inserting to DB. Upwards compatibility :-)
            if ( isset($info['MOD']['#']['PHASE']['0']['#'])) { //It's a pre-15 backup file

                //Adjust the wtype field (mimetised from the upgrade script)
                $workshop->wtype = 0;
                if ($workshop->includeself || $workshop->ntassessments) {
                    $workshop->wtype = 1;    // 3 phases with grading grades
                } else if ($workshop->nsassessments) {
                    $workshop->wtype = 2;   // 5 phases with grading grades
                }

                //Now, adjust phases time limits (mimetised from the upgrade script too)
                $early = 0;
                $late = 0;
                $now = time();
                if ($now < $workshop->deadline) {
                    $late = $workshop->deadline;
                } else {
                    $early = $workshop->deadline;
                }
                if ($workshop->phase > 1) {
                    $workshop->submissionstart = $early;
                } else {
                    $workshop->submissionstart = $late;
                }
                if ($workshop->phase > 2) {
                    $workshop->assessmentstart = $early;
                } else {
                    $workshop->assessmentstart = $late;
                }
                if ($workshop->phase > 3) {
                    $workshop->submissionend = $early;
                } else {
                    $workshop->submissionend = $late;
                }
                if ($workshop->phase > 4) {
                    $workshop->assessmentend = $early;
                } else {
                    $workshop->assessmentend = $late;
                }
                if ($workshop->phase > 5) {
                    $workshop->releasegrades = $now;
                } else {
                    $workshop->releasegrades = $now + (4 * 7 * 24 * 60 * 60); //Grades will be available in 4 weeks
                }
            }

            //The structure is equal to the db, so insert the workshop
            $newid = insert_record ("workshop",$workshop);

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","workshop")." \"".format_string(stripslashes($workshop->name),true)."\"</li>";
            }
            backup_flush(300);
            
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the workshop_elements table now (course level table)
                $status = workshop_elements_restore_mods($newid,$info,$restore);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'workshop',$mod->id)) {
                    //Restore workshop_submissions
                    $status = workshop_submissions_restore_mods ($mod->id, $newid,$info,$restore);
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the workshop_elements
    function workshop_elements_restore_mods($workshop_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the workshop_elements array
        $elements = $info['MOD']['#']['ELEMENTS']['0']['#']['ELEMENT'];

        //Iterate over workshop_elements
        for($i = 0; $i < sizeof($elements); $i++) {
            $ele_info = $elements[$i];
            //traverse_xmlize($ele_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the WORKSHOP_ELEMENTS record structure
            $element->workshopid = $workshop_id;
            $element->elementno = backup_todb($ele_info['#']['ELEMENTNO']['0']['#']);
            $element->description = backup_todb($ele_info['#']['DESCRIPTION']['0']['#']);
            $element->scale = backup_todb($ele_info['#']['SCALE']['0']['#']);
            $element->maxscore = backup_todb($ele_info['#']['MAXSCORE']['0']['#']);
            $element->weight = backup_todb($ele_info['#']['WEIGHT']['0']['#']);
            $element->stddev = backup_todb($ele_info['#']['STDDEV']['0']['#']);
            $element->totalassessments = backup_todb($ele_info['#']['TOTALASSESSMENTS']['0']['#']);

            //The structure is equal to the db, so insert the workshop_elements
            $newid = insert_record ("workshop_elements",$element);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have to restore the workshop_rubrics table now (course level table)
                $status = workshop_rubrics_restore_mods($workshop_id,$element->elementno,$ele_info,$restore);
                //We have to restore the workshop_stockcomment table now (course level table)
                $status = workshop_stockcomments_restore_mods($workshop_id,$element->elementno,$ele_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }


    //This function restores the workshop_rubrics
    function workshop_rubrics_restore_mods($workshop_id,$elementno,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the workshop_rubrics array (optional)
        if (isset($info['#']['RUBRICS']['0']['#']['RUBRIC'])) {
            $rubrics = $info['#']['RUBRICS']['0']['#']['RUBRIC'];

            //Iterate over workshop_rubrics
            for($i = 0; $i < sizeof($rubrics); $i++) {
                $rub_info = $rubrics[$i];
                //traverse_xmlize($rub_info);                             //Debug
                //print_object ($GLOBALS['traverse_array']);              //Debug
                //$GLOBALS['traverse_array']="";                          //Debug

                //Now, build the WORKSHOP_RUBRICS record structure
                $rubric->workshopid = $workshop_id;
                $rubric->elementno = $elementno;
                $rubric->rubricno = backup_todb($rub_info['#']['RUBRICNO']['0']['#']);
                $rubric->description = backup_todb($rub_info['#']['DESCRIPTION']['0']['#']);

                //The structure is equal to the db, so insert the workshop_rubrics
                $newid = insert_record ("workshop_rubrics",$rubric);

                //Do some output
                if (($i+1) % 10 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 200 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }
        return $status;
    }


    //This function restores the workshop_stockcomments
    function workshop_stockcomments_restore_mods($new_workshop_id, $elementno, $info, $restore) {

        global $CFG;

        $status = true;

        //Get the stockcomments array (optional)
        if (isset($info['#']['STOCKCOMMENTS']['0']['#']['STOCKCOMMENT'])) {
            $stockcomments = $info['#']['STOCKCOMMENTS']['0']['#']['STOCKCOMMENT'];

            //Iterate over stock comments
            for($i = 0; $i < sizeof($stockcomments); $i++) {
                $com_info = $stockcomments[$i];
                //traverse_xmlize($com_info);                            //Debug
                //print_object ($GLOBALS['traverse_array']);             //Debug
                //$GLOBALS['traverse_array']="";                         //Debug

                //Now, build the WORKSHOP_STOCKCOMMENTS record structure
                $stockcomment->workshopid = $new_workshop_id;
                $stockcomment->elementno = $elementno;
                $stockcomment->comments = backup_todb($com_info['#']['COMMENT_TEXT']['0']['#']);

                //The structure is equal to the db, so insert the workshop_comment
                $newid = insert_record ("workshop_stockcomments",$stockcomment);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the workshop_submissions
    function workshop_submissions_restore_mods($old_workshop_id, $new_workshop_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the submissions array 
        $submissions = $info['MOD']['#']['SUBMISSIONS']['0']['#']['SUBMISSION'];
            
        //Iterate over submissions
        for($i = 0; $i < sizeof($submissions); $i++) {
            $sub_info = $submissions[$i];
            //traverse_xmlize($sub_info);                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                      //Debug
            //$GLOBALS['traverse_array']="";                                  //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the WORKSHOP_SUBMISSIONS record structure
            $submission->workshopid = $new_workshop_id;
            $submission->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $submission->title = backup_todb($sub_info['#']['TITLE']['0']['#']); 
            $submission->timecreated = backup_todb($sub_info['#']['TIMECREATED']['0']['#']);
            $submission->timecreated += $restore->course_startdateoffset;
            $submission->mailed = backup_todb($sub_info['#']['MAILED']['0']['#']);
            $submission->description = backup_todb($sub_info['#']['DESCRIPTION']['0']['#']);
            $submission->gradinggrade = backup_todb($sub_info['#']['GRADINGGRADE']['0']['#']);
            $submission->finalgrade = backup_todb($sub_info['#']['FINALGRADE']['0']['#']);
            $submission->late = backup_todb($sub_info['#']['LATE']['0']['#']);
            $submission->nassessments = backup_todb($sub_info['#']['NASSESSMENTS']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $submission->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the workshop_submission
            $newid = insert_record ("workshop_submissions",$submission);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"workshop_submissions",$oldid,
                             $newid);

                //Now copy moddata associated files
                $status = workshop_restore_files ($oldid, $newid,$restore); 
                //Now we need to restore workshop_assessments (user level table)
                if ($status) {
                    $status = workshop_assessments_restore_mods ($new_workshop_id, $newid,$sub_info,$restore);
                }
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the workshop_assessments       
    function workshop_assessments_restore_mods($new_workshop_id, $new_submission_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the assessments array (if any)
        if (isset($info['#']['ASSESSMENTS']['0']['#']['ASSESSMENT'])) {
            $assessments = $info['#']['ASSESSMENTS']['0']['#']['ASSESSMENT'];

            //Iterate over assessments
            for($i = 0; $i < sizeof($assessments); $i++) {
                $ass_info = $assessments[$i];
                //traverse_xmlize($ass_info);                                                                 //Debug
                //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                //$GLOBALS['traverse_array']="";                                                              //Debug

                //We'll need this later!!
                $oldid = backup_todb($ass_info['#']['ID']['0']['#']);
                $olduserid = backup_todb($ass_info['#']['USERID']['0']['#']);

                //Now, build the WORKSHOP_ASSESSMENTS record structure
                $assessment->workshopid = $new_workshop_id;
                $assessment->submissionid = $new_submission_id;
                $assessment->userid = backup_todb($ass_info['#']['USERID']['0']['#']);
                $assessment->timecreated = backup_todb($ass_info['#']['TIMECREATED']['0']['#']);
                $assessment->timegraded = backup_todb($ass_info['#']['TIMEGRADED']['0']['#']);
                $assessment->timeagreed = backup_todb($ass_info['#']['TIMEAGREED']['0']['#']);
                $assessment->grade = backup_todb($ass_info['#']['GRADE']['0']['#']);
                $assessment->gradinggrade = backup_todb($ass_info['#']['GRADINGGRADE']['0']['#']);
                $assessment->mailed = backup_todb($ass_info['#']['MAILED']['0']['#']);
                $assessment->resubmission = backup_todb($ass_info['#']['RESUBMISSION']['0']['#']);
                $assessment->donotuse = backup_todb($ass_info['#']['DONOTUSE']['0']['#']);
                $assessment->generalcomment = backup_todb($ass_info['#']['GENERALCOMMENT']['0']['#']);
                $assessment->teachercomment = backup_todb($ass_info['#']['TEACHERCOMMENT']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $assessment->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the workshop_assessment
                $newid = insert_record ("workshop_assessments",$assessment);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if ($newid) {
                    //We have the newid, update backup_ids
                    backup_putid($restore->backup_unique_code,"workshop_assessments",$oldid,
                            $newid);

                    //Now we need to restore workshop_comments (user level table)
                    if ($status) {
                        $status = workshop_comments_restore_mods ($new_workshop_id, $newid,$ass_info,$restore);
                    }
                    //Now we need to restore workshop_grades (user level table)   
                    if ($status) {
                        $status = workshop_grades_restore_mods ($new_workshop_id, $newid,$ass_info,$restore);   
                    }
                } else {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the workshop_comments
    function workshop_comments_restore_mods($new_workshop_id, $new_assessment_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the comments array (optional)
        if (isset($info['#']['COMMENTS']['0']['#']['COMMENT'])) {
            $comments = $info['#']['COMMENTS']['0']['#']['COMMENT'];

            //Iterate over comments
            for($i = 0; $i < sizeof($comments); $i++) {
                $com_info = $comments[$i];
                //traverse_xmlize($com_info);                            //Debug
                //print_object ($GLOBALS['traverse_array']);             //Debug
                //$GLOBALS['traverse_array']="";                         //Debug

                //We'll need this later!!
                $olduserid = backup_todb($com_info['#']['USERID']['0']['#']);

                //Now, build the WORKSHOP_COMMENTS record structure
                $comment->workshopid = $new_workshop_id;
                $comment->assessmentid = $new_assessment_id;
                $comment->userid = backup_todb($com_info['#']['USERID']['0']['#']);
                $comment->timecreated = backup_todb($com_info['#']['TIMECREATED']['0']['#']);
                $comment->mailed = backup_todb($com_info['#']['MAILED']['0']['#']);
                $comment->comments = backup_todb($com_info['#']['COMMENT_TEXT']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $comment->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the workshop_comment
                $newid = insert_record ("workshop_comments",$comment);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the workshop_grades
    function workshop_grades_restore_mods($new_workshop_id, $new_assessment_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the grades array (optional)
        if (isset($info['#']['GRADES']['0']['#']['GRADE'])) {
            $grades = $info['#']['GRADES']['0']['#']['GRADE'];

            //Iterate over grades
            for($i = 0; $i < sizeof($grades); $i++) {
                $gra_info = $grades[$i];
                //traverse_xmlize($gra_info);                             //Debug
                //print_object ($GLOBALS['traverse_array']);              //Debug
                //$GLOBALS['traverse_array']="";                          //Debug

                //Now, build the WORKSHOP_GRADES record structure
                $grade->workshopid = $new_workshop_id;
                $grade->assessmentid = $new_assessment_id;
                $grade->elementno = backup_todb($gra_info['#']['ELEMENTNO']['0']['#']);
                $grade->feedback = backup_todb($gra_info['#']['FEEDBACK']['0']['#']);
                $grade->grade = backup_todb($gra_info['#']['GRADE_VALUE']['0']['#']);

                //The structure is equal to the db, so insert the workshop_grade
                $newid = insert_record ("workshop_grades",$grade);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function copies the workshop related info from backup temp dir to course moddata folder,
    //creating it if needed and recoding everything (submission_id) 
    function workshop_restore_files ($oldsubmiss, $newsubmiss, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $workshop_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //Now, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
   
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate workshop directory
        if ($status) {
            $workshop_path = $moddata_path."/workshop";
            //Check it exists and create it
            $status = check_dir_exists($workshop_path,true);
        }

        //Now locate the temp dir we are gong to restore
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/workshop/".$oldsubmiss;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/workshop
        if ($status and $todo) {
            //First this workshop id
            $this_workshop_path = $workshop_path."/".$newsubmiss;
            $status = check_dir_exists($this_workshop_path,true);
            //And now, copy temp_path to this_workshop_path
            $status = backup_copy_file($temp_path, $this_workshop_path); 
        }
       
        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function workshop_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert workshop->description
        if ($records = get_records_sql ("SELECT w.id, w.description, w.format
                                         FROM {$CFG->prefix}workshop w,
                                              {$CFG->prefix}backup_ids b
                                         WHERE w.course = $restore->course_id AND
                                               format = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'workshop' AND
                                               b.new_id = w.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->description = restore_decode_wiki_content($record->description, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->description = $wtm->convert($record->description, $restore->course_id);
                $record->format = FORMAT_MARKDOWN;
                $status = update_record('workshop', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }

        }
        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //workshop_decode_content_links_caller() function in each module
    //in the restore process
    function workshop_decode_content_links ($content,$restore) {

        global $CFG;

        $result = $content;

        //Link to the list of workshops

        $searchstring='/\$@(WORKSHOPINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(WORKSHOPINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/workshop/index.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/workshop/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to workshop view by moduleid

        $searchstring='/\$@(WORKSHOPVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(WORKSHOPVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/workshop/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/workshop/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function workshop_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        //Process every WORKSHOP (description) in the course
        if ($workshops = get_records_sql ("SELECT w.id, w.description
                                           FROM {$CFG->prefix}workshop w
                                           WHERE w.course = $restore->course_id")) {
            //Iterate over each workshop->description
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($workshops as $workshop) {
                //Increment counter
                $i++;
                $content = $workshop->description;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $workshop->description = addslashes($result);
                    $status = update_record("workshop",$workshop);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function workshop_restore_logs($restore,$log) {
                    
        $status = false;
                    
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "submit":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "resubmit":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }   
            }   
            break;
        case "league table":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "submissions":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "allow both":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "set up":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "assessments onl":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "close":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "display grades":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "over allocation":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "assess":
            if ($log->cmid) {
                //Get the new_id of the workshop assessments
                $ass = backup_getid($restore->backup_unique_code,"workshop_assessments",$log->info);
                if ($ass) {
                    $log->url = "assessments.php?action=viewassessment&id=".$log->cmid."&aid=".$ass->new_id;
                    $log->info = $ass->new_id;
                    $status = true;
                }
            }
            break;
        case "grade":
            if ($log->cmid) {
                //Get the new_id of the workshop assessments
                $ass = backup_getid($restore->backup_unique_code,"workshop_assessments",$log->info);
                if ($ass) {
                    $log->url = "assessments.php?action=viewassessment&id=".$log->cmid."&aid=".$ass->new_id;
                    $log->info = $ass->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
