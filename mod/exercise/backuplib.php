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

    //This function executes all the backup procedure about this mod
    function exercise_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over exercise table
        $exercises = get_records ("exercise","course",$preferences->backup_course,"id");
        if ($exercises) {
            foreach ($exercises as $exercise) {
                if (backup_mod_selected($preferences,'exercise',$exercise->id)) {
                    $status = exercise_backup_one_mod($bf,$preferences,$exercise);
                }
            }
        }
        return $status;  
    }

    function exercise_backup_one_mod($bf,$preferences,$exercise) {

        global $CFG;
    
        if (is_numeric($exercise)) {
            $exercise = get_record('exercise','id',$exercise);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print exercise data
        fwrite ($bf,full_tag("ID",4,false,$exercise->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"exercise"));
        fwrite ($bf,full_tag("NAME",4,false,$exercise->name));
        fwrite ($bf,full_tag("NELEMENTS",4,false,$exercise->nelements));
        fwrite ($bf,full_tag("PHASE",4,false,$exercise->phase));
        fwrite ($bf,full_tag("GRADINGSTRATEGY",4,false,$exercise->gradingstrategy));
        fwrite ($bf,full_tag("USEMAXIMUM",4,false,$exercise->usemaximum));
        fwrite ($bf,full_tag("ASSESSMENTCOMPS",4,false,$exercise->assessmentcomps));
        fwrite ($bf,full_tag("ANONYMOUS",4,false,$exercise->anonymous));
        fwrite ($bf,full_tag("MAXBYTES",4,false,$exercise->maxbytes));
        fwrite ($bf,full_tag("DEADLINE",4,false,$exercise->deadline));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$exercise->timemodified));
        fwrite ($bf,full_tag("GRADE",4,false,$exercise->grade));
        fwrite ($bf,full_tag("GRADINGGRADE",4,false,$exercise->gradinggrade));
        fwrite ($bf,full_tag("SHOWLEAGUETABLE",4,false,$exercise->showleaguetable));
        fwrite ($bf,full_tag("USEPASSWORD",4,false,$exercise->usepassword));
        fwrite ($bf,full_tag("PASSWORD",4,false,$exercise->password));
        //Now we backup exercise elements
        $status = backup_exercise_elements($bf,$preferences,$exercise->id);
        //Now we backup any teacher submissions (these are an integral part of the exercise)
        $status = backup_exercise_submissions($bf, $preferences, $exercise->id);
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));
        //we need to backup the teacher files (the exercise descriptions)
        $status = backup_exercise_teacher_files($bf, $preferences, $exercise->id);
        //if we've selected to backup users info, then backup files too
        if ($status) {
            if (backup_userdata_selected($preferences,'exercise',$exercise->id)) {
                $status = backup_exercise_student_files($bf,$preferences, $exercise->id);
            }
        }

        return $status;
    }

    //Backup exercise_elements contents (executed from exercise_backup_mods)
    function backup_exercise_elements ($bf,$preferences,$exercise) {

        global $CFG;

        $status = true;

        $exercise_elements = get_records("exercise_elements","exerciseid",$exercise,"id");
        //If there is exercise_elements
        if ($exercise_elements) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ELEMENTS",4,true));
            //Iterate over each element
            foreach ($exercise_elements as $element) {
                //Start element
                $status =fwrite ($bf,start_tag("ELEMENT",5,true));
                //Print element contents
                fwrite ($bf,full_tag("ELEMENTNO",6,false,$element->elementno));
                fwrite ($bf,full_tag("DESCRIPTION",6,false,$element->description));
                fwrite ($bf,full_tag("SCALE",6,false,$element->scale));
                fwrite ($bf,full_tag("MAXSCORE",6,false,$element->maxscore));
                fwrite ($bf,full_tag("WEIGHT",6,false,$element->weight));
                //Now we backup exercise rubrics
                $status = backup_exercise_rubrics($bf,$preferences,$exercise,$element->elementno);
                //End element
                $status =fwrite ($bf,end_tag("ELEMENT",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ELEMENTS",4,true));
        }
        return $status;
    }

    //Backup exercise_rubrics contents (executed from backup_exercise_elements)
    function backup_exercise_rubrics ($bf,$preferences,$exercise,$elementno) {

        global $CFG;

        $status = true;

        $exercise_rubrics = get_records_sql("SELECT * from {$CFG->prefix}exercise_rubrics r
                                             WHERE r.exerciseid = '$exercise' and r.elementno = '$elementno'
                                             ORDER BY r.elementno");

        //If there is exercise_rubrics
        if ($exercise_rubrics) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RUBRICS",6,true));
            //Iterate over each element
            foreach ($exercise_rubrics as $rubric) {
                //Start rubric
                $status =fwrite ($bf,start_tag("RUBRIC",7,true));
                //Print rubric contents
                fwrite ($bf,full_tag("RUBRICNO",8,false,$rubric->rubricno));
                fwrite ($bf,full_tag("DESCRIPTION",8,false,$rubric->description));
                //End rubric
                $status =fwrite ($bf,end_tag("RUBRIC",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("RUBRICS",6,true));
        }
        return $status;
    }

    //Backup exercise_submissions contents (executed from exercise_backup_mods)
    function backup_exercise_submissions ($bf,$preferences,$exerciseid) {

        global $CFG;

        $status = true;

        $exercise_submissions = get_records_select("exercise_submissions","exerciseid = $exerciseid
                AND isexercise = 1");
        //If there is submissions
        if ($exercise_submissions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SUBMISSIONS",4,true));
            //Iterate over each submission
            foreach ($exercise_submissions as $submission) {
                //Start submission
                $status =fwrite ($bf,start_tag("SUBMISSION",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$submission->id));       
                fwrite ($bf,full_tag("USERID",6,false,$submission->userid));       
                fwrite ($bf,full_tag("TITLE",6,false,$submission->title));       
                fwrite ($bf,full_tag("TIMECREATED",6,false,$submission->timecreated));       
                fwrite ($bf,full_tag("RESUBMIT",6,false,$submission->resubmit));       
                fwrite ($bf,full_tag("MAILED",6,false,$submission->mailed));       
                fwrite ($bf,full_tag("ISEXERCISE",6,false,$submission->isexercise));       
                fwrite ($bf,full_tag("LATE",6,false,$submission->late));       
                //Now we backup any exercise assessments (if student data required)
                if (backup_userdata_selected($preferences,'exercise',$exerciseid)) {
                    $status = backup_exercise_assessments($bf,$preferences,$exerciseid,$submission->id);
                }
                //End submission
                $status =fwrite ($bf,end_tag("SUBMISSION",5,true));
            }
            //if we've selected to backup users info, then backup the student submisions
            if (backup_userdata_selected($preferences,'exercise',$exerciseid)) {
                $exercise_submissions = get_records_select("exercise_submissions","exerciseid = $exerciseid
                        AND isexercise = 0");
                //If there is submissions
                if ($exercise_submissions) {
                    //Iterate over each submission
                    foreach ($exercise_submissions as $submission) {
                        //Start submission
                        $status =fwrite ($bf,start_tag("SUBMISSION",5,true));
                        //Print submission contents
                        fwrite ($bf,full_tag("ID",6,false,$submission->id));       
                        fwrite ($bf,full_tag("USERID",6,false,$submission->userid));       
                        fwrite ($bf,full_tag("TITLE",6,false,$submission->title));       
                        fwrite ($bf,full_tag("TIMECREATED",6,false,$submission->timecreated));       
                        fwrite ($bf,full_tag("RESUBMIT",6,false,$submission->resubmit));       
                        fwrite ($bf,full_tag("MAILED",6,false,$submission->mailed));       
                        fwrite ($bf,full_tag("ISEXERCISE",6,false,$submission->isexercise));       
                        fwrite ($bf,full_tag("LATE",6,false,$submission->late));       
                        //Now we backup any exercise assessments
                        $status = backup_exercise_assessments($bf,$preferences,$exerciseid,$submission->id);
                        //End submission
                        $status =fwrite ($bf,end_tag("SUBMISSION",5,true));
                    }
                }
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SUBMISSIONS",4,true));
        }
        return $status;
    }

    //Backup exercise_student_submissions contents (executed from exercise_backup_mods)
    function backup_exercise_student_submissions ($bf,$preferences,$exerciseid) {

        global $CFG;

        $status = true;

        return $status;
    }

    //Backup exercise_assessments contents (executed from backup_exercise_submissions)
    function backup_exercise_assessments ($bf,$preferences,$exercise,$submission) {

        global $CFG;

        $status = true;

        //NOTE: I think that the exerciseid can go out (submissionid is a good unique fk), but mantain it, as is in db !!
        $exercise_assessments = get_records_sql("SELECT * from {$CFG->prefix}exercise_assessments a
                                                 WHERE a.exerciseid = '$exercise' and a.submissionid = '$submission'
                                                 ORDER BY a.id");

        //If there is exercise_assessments
        if ($exercise_assessments) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ASSESSMENTS",6,true));
            //Iterate over each assessment
            foreach ($exercise_assessments as $wor_ass) {
                //Start assessment
                $status =fwrite ($bf,start_tag("ASSESSMENT",7,true));
                //Print assessment contents
                fwrite ($bf,full_tag("ID",8,false,$wor_ass->id));
                fwrite ($bf,full_tag("USERID",8,false,$wor_ass->userid));
                fwrite ($bf,full_tag("TIMECREATED",8,false,$wor_ass->timecreated));
                fwrite ($bf,full_tag("TIMEGRADED",8,false,$wor_ass->timegraded));
                fwrite ($bf,full_tag("GRADE",8,false,$wor_ass->grade));
                fwrite ($bf,full_tag("GRADINGGRADE",8,false,$wor_ass->gradinggrade));
                fwrite ($bf,full_tag("MAILED",8,false,$wor_ass->mailed));
                fwrite ($bf,full_tag("GENERALCOMMENT",8,false,$wor_ass->generalcomment));
                fwrite ($bf,full_tag("TEACHERCOMMENT",8,false,$wor_ass->teachercomment));
                //Now we backup exercise grades
                $status = backup_exercise_grades($bf,$preferences,$exercise,$wor_ass->id);
                //End assessment
                $status =fwrite ($bf,end_tag("ASSESSMENT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ASSESSMENTS",6,true));
        }
        return $status;
    }


    //Backup exercise_grades contents (executed from backup_exercise_assessments)
    function backup_exercise_grades ($bf,$preferences,$exercise,$assessmentid) {

        global $CFG;

        $status = true;

        //NOTE: I think that the exerciseid can go out (assessmentid is a good unique fk), but mantain it, as is in db !!
        $exercise_grades = get_records_sql("SELECT * from {$CFG->prefix}exercise_grades g
                                              WHERE g.exerciseid = '$exercise' and g.assessmentid = '$assessmentid'
                                              ORDER BY g.elementno");

        //If there is exercise_grades
        if ($exercise_grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("GRADES",8,true));
            //Iterate over each grade
            foreach ($exercise_grades as $wor_gra) {
                //Start grade
                $status =fwrite ($bf,start_tag("GRADE",9,true));
                //Print grade contents
                fwrite ($bf,full_tag("ELEMENTNO",10,false,$wor_gra->elementno));
                fwrite ($bf,full_tag("FEEDBACK",10,false,$wor_gra->feedback));
                fwrite ($bf,full_tag("GRADE_VALUE",10,false,$wor_gra->grade));
                //End comment
                $status =fwrite ($bf,end_tag("GRADE",9,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("GRADES",8,true));
        }
        return $status;
    }


    //Backup the teacher's exercise files (they are an integral part of the exercise)
    function backup_exercise_teacher_files($bf,$preferences, $exerciseid) {

        global $CFG;
       
        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //in temp/backup/$backup_code/moddate create the exercise diretory
        $status = check_dir_exists("$CFG->dataroot/temp/backup/$preferences->backup_unique_code/moddata/exercise", true);
        if ($status) {
            //Now copy the submission dirs
            if ($submissions = get_records_select("exercise_submissions", "exerciseid = $exerciseid
                        AND isexercise = 1")) {
                foreach ($submissions as $submission) {
                    //Only if it exists !! Thanks to Daniel Miksik.
                    if (is_dir("{$CFG->dataroot}/$preferences->backup_course/{$CFG->moddata}/exercise/$submission->id")) {
                        // create directory
                        // $status = check_dir_exists("$CFG->dataroot/temp/backup/$preferences->backup_unique_code/moddata/exercise", true);
                        // copy all the files in the directory
                        $status = backup_copy_file("{$CFG->dataroot}/$preferences->backup_course/{$CFG->moddata}/exercise/$submission->id", "{$CFG->dataroot}/temp/backup/$preferences->backup_unique_code/moddata/exercise/$submission->id");
                    }
                }
            }
        }

        return $status;

    } 

    //Backup students' exercise files because we've selected to backup user info
    //and files are user info's level
    function backup_exercise_student_files($bf,$preferences, $exerciseid) {

        global $CFG;
       
        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        if ($status) {
            //Now copy the submission dirs
            if ($submissions = get_records_select("exercise_submissions", "exerciseid = $exerciseid
                        AND isexercise = 0")) {
                foreach ($submissions as $submission) {
                    //Only if it exists !! Thanks to Daniel Miksik.
                    if (is_dir("{$CFG->dataroot}/$preferences->backup_course/$CFG->moddata/exercise/$submission->id")) {
                        $status = backup_copy_file("{$CFG->dataroot}/$preferences->backup_course/{$CFG->moddata}/exercise/$submission->id", "{$CFG->dataroot}/temp/backup/$preferences->backup_unique_code/moddata/exercise/$submission->id");
                    }
                }
            }
        }

        return $status;

    } 

    //Return an array of info (name,value)
    function exercise_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += exercise_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","exercise");
        if ($ids = exercise_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("submissions","exercise");
            if ($ids = exercise_submission_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    //Return an array of info (name,value)
    function exercise_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("submissions","exercise");
            if ($ids = exercise_submission_ids_by_instance ($instance->id)) { 
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of exercise id 
    function exercise_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT w.id, w.course
                                 FROM {$CFG->prefix}exercise w
                                 WHERE w.course = '$course'");
    }
    
    //Returns an array of exercise_submissions id
    function exercise_submission_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.exerciseid
                                 FROM {$CFG->prefix}exercise_submissions s,
                                      {$CFG->prefix}exercise w
                                 WHERE w.course = '$course' AND
                                       s.exerciseid = w.id");
    }

    //Returns an array of exercise_submissions id
    function exercise_submission_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.exerciseid
                                 FROM {$CFG->prefix}exercise_submissions s
                                 WHERE s.exerciseid = $instanceid");
    }
?>
