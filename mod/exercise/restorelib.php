<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //exercise mods

    //This is the "graphical" structure of the exercise mod: 
    //
    //                                          exercise
    //                                         (CL,pk->id)             
    //                                             |
    //                                             |
    //                                             |
    //              |------------------------------|---------------------------------------------|
    //              |                                                                            |
    //              |                                                                            |
    //              |                                                                   exercise_submissions
    //              |                                                        (UL,pk->id,fk->exerciseid,files)
    //              |                                                                            |
    //              |                                                                            |
    //              |                                                                            |
    //              |        |---------------------|      |------------------------------|       |
    //              |        |                     |      |                              |       |
    //        exercise_elements                 exercise_grades                    exercise_assessments
    //    (CL,pk->id,fk->exerciseid)      (UL,pk->id,fk->assessmentid)         (UL,pk->id,fk->submissionid)
    //              |                     (          fk->elementno   )  
    //              |                                                        
    //              |                                                        
    //          exercise_rubrics             
    //    (CL,pk->id,fk->elementno)          
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
    function exercise_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            // if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Exercise', $restore, $info['MOD']['#'], array('DEADLINE'));
            }
            //traverse_xmlize($info);                                                              //Debug
            //print_object ($GLOBALS['traverse_array']);                                           //Debug
            //$GLOBALS['traverse_array']="";                                                       //Debug

            //Now, build the exercise record structure
            $exercise->course = $restore->course_id;
            $exercise->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $exercise->nelements = backup_todb($info['MOD']['#']['NELEMENTS']['0']['#']);
            $exercise->phase = backup_todb($info['MOD']['#']['PHASE']['0']['#']);
            $exercise->gradingstrategy = backup_todb($info['MOD']['#']['GRADINGSTRATEGY']['0']['#']);
            $exercise->usemaximum = backup_todb($info['MOD']['#']['USEMAXIMUM']['0']['#']);
            $exercise->assessmentcomps = backup_todb($info['MOD']['#']['ASSESSMENTCOMPS']['0']['#']);
            $exercise->anonymous = backup_todb($info['MOD']['#']['ANONYMOUS']['0']['#']);
            $exercise->maxbytes = backup_todb($info['MOD']['#']['MAXBYTES']['0']['#']); 
            $exercise->deadline = backup_todb($info['MOD']['#']['DEADLINE']['0']['#']);
            $date = usergetdate($exercise->deadline);
            fwrite ($restorelog_file,"<br/>The Exercise - ".$exercise->name." <br/>");
            fwrite ($restorelog_file,"The DEADLINE was  " .$date['weekday'].", ".$date['mday']." ".$date['month']." ".$date['year']."");            
            $exercise->deadline += $restore->course_startdateoffset;
            $date = usergetdate($exercise->deadline);
            fwrite ($restorelog_file,"&nbsp;&nbsp;&nbsp;the DEADLINE is now  " .$date['weekday'].",  ".$date['mday']." ".$date['month']." ".$date['year']."<br/>");         
            $exercise->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $exercise->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $exercise->gradinggrade = backup_todb($info['MOD']['#']['GRADINGGRADE']['0']['#']);
            $exercise->showleaguetable = backup_todb($info['MOD']['#']['SHOWLEAGUETABLE']['0']['#']);
            $exercise->usepassword = backup_todb($info['MOD']['#']['USEPASSWORD']['0']['#']);
            $exercise->password = backup_todb($info['MOD']['#']['PASSWORD']['0']['#']);

            //The structure is equal to the db, so insert the exercise
            $newid = insert_record ("exercise",$exercise);

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","exercise")." \"".format_string(stripslashes($exercise->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the exercise_elements table now (course level table)
                $status = exercise_elements_restore($newid,$info,$restore);
                //restore the teacher submissions and optionally the student submissions
                $status = exercise_submissions_restore($mod->id, $newid,$info,$restore);
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the exercise_elements
    function exercise_elements_restore($exercise_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the exercise_elements array
        $elements = $info['MOD']['#']['ELEMENTS']['0']['#']['ELEMENT'];

        //Iterate over exercise_elements
        for($i = 0; $i < sizeof($elements); $i++) {
            $ele_info = $elements[$i];
            //traverse_xmlize($ele_info);                                                          //Debug
            //print_object ($GLOBALS['traverse_array']);                                           //Debug
            //$GLOBALS['traverse_array']="";                                                       //Debug

            //Now, build the exercise_ELEMENTS record structure
            $element->exerciseid = $exercise_id;
            $element->elementno = backup_todb($ele_info['#']['ELEMENTNO']['0']['#']);
            $element->description = backup_todb($ele_info['#']['DESCRIPTION']['0']['#']);
            $element->scale = backup_todb($ele_info['#']['SCALE']['0']['#']);
            $element->maxscore = backup_todb($ele_info['#']['MAXSCORE']['0']['#']);
            $element->weight = backup_todb($ele_info['#']['WEIGHT']['0']['#']);

            //The structure is equal to the db, so insert the exercise_elements
            $newid = insert_record ("exercise_elements",$element);

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
                //We have to restore the exercise_rubrics table now (course level table)
                $status = exercise_rubrics_restore($exercise_id,$element->elementno,$ele_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }


    //This function restores the exercise_rubrics
    function exercise_rubrics_restore($exercise_id,$elementno,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the exercise_rubrics array (optional)
        if (isset($info['#']['RUBRICS']['0']['#']['RUBRIC'])) {
            $rubrics = $info['#']['RUBRICS']['0']['#']['RUBRIC'];

            //Iterate over exercise_rubrics
            for($i = 0; $i < sizeof($rubrics); $i++) {
                $rub_info = $rubrics[$i];
                //traverse_xmlize($rub_info);                                  //Debug
                //print_object ($GLOBALS['traverse_array']);                   //Debug
                //$GLOBALS['traverse_array']="";                               //Debug

                //Now, build the exercise_RUBRICS record structure
                $rubric->exerciseid = $exercise_id;
                $rubric->elementno = $elementno;
                $rubric->rubricno = backup_todb($rub_info['#']['RUBRICNO']['0']['#']);
                $rubric->description = backup_todb($rub_info['#']['DESCRIPTION']['0']['#']);

                //The structure is equal to the db, so insert the exercise_rubrics
                $newid = insert_record ("exercise_rubrics",$rubric);

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


    //This function restores the submissions
    function exercise_submissions_restore($old_exercise_id, $new_exercise_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the submissions array (teacher submissions)
        $submissions = $info['MOD']['#']['SUBMISSIONS']['0']['#']['SUBMISSION'];
        //Iterate over submissions
        for($i = 0; $i < sizeof($submissions); $i++) {
            $sub_info = $submissions[$i];
            //traverse_xmlize($sub_info);                                                         //Debug
            //print_object ($GLOBALS['traverse_array']);                                          //Debug
            //$GLOBALS['traverse_array']="";                                                      //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the exercise_SUBMISSIONS record structure
            $submission->exerciseid = $new_exercise_id;
            $submission->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $submission->title = backup_todb($sub_info['#']['TITLE']['0']['#']);
            $submission->timecreated = backup_todb($sub_info['#']['TIMECREATED']['0']['#']);
            $submission->resubmit = backup_todb($sub_info['#']['RESUBMIT']['0']['#']);
            $submission->mailed = backup_todb($sub_info['#']['MAILED']['0']['#']);
            $submission->isexercise = backup_todb($sub_info['#']['ISEXERCISE']['0']['#']);
            $submission->late = backup_todb($sub_info['#']['LATE']['0']['#']);

            // always save the exercise descriptions and optionally the student submissions
            if ($submission->isexercise or restore_userdata_selected($restore,'exercise',$old_exercise_id)) {
                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $submission->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the exercise_submission
                $newid = insert_record ("exercise_submissions",$submission);

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
                    backup_putid($restore->backup_unique_code,"exercise_submissions",$oldid,
                            $newid);

                    //Now copy moddata associated files
                    $status = exercise_restore_files($oldid, $newid,$restore); 
                    //Now we need to restore exercise_assessments (user level table)
                    if ($status and restore_userdata_selected($restore,'exercise',$old_exercise_id)) {
                        $status = exercise_assessments_restore($new_exercise_id, $newid,$sub_info,$restore);
                    }
                } else {
                    $status = false;
                }
            }
        }

    return $status;
    }

    //This function restores the exercise_assessments       
    function exercise_assessments_restore($new_exercise_id, $new_submission_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the assessments array (optional)
        if (isset($info['#']['ASSESSMENTS']['0']['#']['ASSESSMENT'])) {
            $assessments = $info['#']['ASSESSMENTS']['0']['#']['ASSESSMENT'];

            //Iterate over assessments
            for($i = 0; $i < sizeof($assessments); $i++) {
                $ass_info = $assessments[$i];
                //traverse_xmlize($ass_info);                                                         //Debug
                //print_object ($GLOBALS['traverse_array']);                                          //Debug
                //$GLOBALS['traverse_array']="";                                                      //Debug

                //We'll need this later!!
                $oldid = backup_todb($ass_info['#']['ID']['0']['#']);
                $olduserid = backup_todb($ass_info['#']['USERID']['0']['#']);

                //Now, build the exercise_ASSESSMENTS record structure
                $assessment->exerciseid = $new_exercise_id;
                $assessment->submissionid = $new_submission_id;
                $assessment->userid = backup_todb($ass_info['#']['USERID']['0']['#']);
                $assessment->timecreated = backup_todb($ass_info['#']['TIMECREATED']['0']['#']);
                $assessment->timegraded = backup_todb($ass_info['#']['TIMEGRADED']['0']['#']);
                $assessment->grade = backup_todb($ass_info['#']['GRADE']['0']['#']);
                $assessment->gradinggrade = backup_todb($ass_info['#']['GRADINGGRADE']['0']['#']);
                $assessment->mailed = backup_todb($ass_info['#']['MAILED']['0']['#']);
                $assessment->generalcomment = backup_todb($ass_info['#']['GENERALCOMMENT']['0']['#']);
                $assessment->teachercomment = backup_todb($ass_info['#']['TEACHERCOMMENT']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $assessment->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the exercise_assessment
                $newid = insert_record ("exercise_assessments",$assessment);

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
                    backup_putid($restore->backup_unique_code,"exercise_assessments",$oldid,
                            $newid);

                    //Now we need to restore exercise_grades (user level table)   
                    if ($status) {
                        $status = exercise_grades_restore_mods ($new_exercise_id, $newid,$ass_info,$restore);   
                    }
                } else {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the exercise_grades
    function exercise_grades_restore_mods($new_exercise_id, $new_assessment_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the grades array (optional)
        if (isset($info['#']['GRADES']['0']['#']['GRADE'])) {
            $grades = $info['#']['GRADES']['0']['#']['GRADE'];

            //Iterate over grades
            for($i = 0; $i < sizeof($grades); $i++) {
                $gra_info = $grades[$i];
                //traverse_xmlize($gra_info);                           //Debug
                //print_object ($GLOBALS['traverse_array']);            //Debug
                //$GLOBALS['traverse_array']="";                        //Debug

                //Now, build the exercise_GRADES record structure
                $grade->exerciseid = $new_exercise_id;
                $grade->assessmentid = $new_assessment_id;
                $grade->elementno = backup_todb($gra_info['#']['ELEMENTNO']['0']['#']);
                $grade->feedback = backup_todb($gra_info['#']['FEEDBACK']['0']['#']);
                $grade->grade = backup_todb($gra_info['#']['GRADE_VALUE']['0']['#']);

                //The structure is equal to the db, so insert the exercise_grade
                $newid = insert_record ("exercise_grades",$grade);

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

    //This function copies the exercise related info from backup temp dir to course moddata folder,
    //creating it if needed and recoding everything (submission_id) 
    function exercise_restore_files ($oldsubmiss, $newsubmiss, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $exercise_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //Now, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
   
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate exercise directory
        if ($status) {
            $exercise_path = $moddata_path."/exercise";
            //Check it exists and create it
            $status = check_dir_exists($exercise_path,true);
        }

        //Now locate the temp dir we are gong to restore
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/exercise/".$oldsubmiss;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/exercise
        if ($status and $todo) {
            //First this exercise id
            $this_exercise_path = $exercise_path."/".$newsubmiss;
            $status = check_dir_exists($this_exercise_path,true);
            //And now, copy temp_path to this_exercise_path
            $status = backup_copy_file($temp_path, $this_exercise_path); 
        }
       
        return $status;
    }
?>
