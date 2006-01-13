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

    //This function executes all the backup procedure about this mod
    function workshop_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over workshop table
        $workshops = get_records ("workshop","course",$preferences->backup_course,"id");
        if ($workshops) {
            foreach ($workshops as $workshop) {
                if (backup_mod_selected($preferences,'workshop',$workshop->id)) {
                    $status = workshop_backup_one_mod($bf,$preferences,$workshop);
                }
            }
        }
 
        return $status;  
    }

    function workshop_backup_one_mod($bf,$preferences,$workshop) {

        $status = true;

        if (is_numeric($workshop)) {
            $workshop = get_record('workshop','id',$workshop);
        }
        $instanceid = $workshop->id;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print workshop data
        fwrite ($bf,full_tag("ID",4,false,$workshop->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"workshop"));
        fwrite ($bf,full_tag("NAME",4,false,$workshop->name));
        fwrite ($bf,full_tag("DESCRIPTION",4,false,$workshop->description));
        fwrite ($bf,full_tag("WTYPE",4,false,$workshop->wtype));
        fwrite ($bf,full_tag("NELEMENTS",4,false,$workshop->nelements));
        fwrite ($bf,full_tag("NATTACHMENTS",4,false,$workshop->nattachments));
        fwrite ($bf,full_tag("FORMAT",4,false,$workshop->format));
        fwrite ($bf,full_tag("GRADINGSTRATEGY",4,false,$workshop->gradingstrategy));
        fwrite ($bf,full_tag("RESUBMIT",4,false,$workshop->resubmit));
        fwrite ($bf,full_tag("AGREEASSESSMENTS",4,false,$workshop->agreeassessments));
        fwrite ($bf,full_tag("HIDEGRADES",4,false,$workshop->hidegrades));
        fwrite ($bf,full_tag("ANONYMOUS",4,false,$workshop->anonymous));
        fwrite ($bf,full_tag("INCLUDESELF",4,false,$workshop->includeself));
        fwrite ($bf,full_tag("MAXBYTES",4,false,$workshop->maxbytes));
        fwrite ($bf,full_tag("SUBMISSIONSTART",4,false,$workshop->submissionstart));
        fwrite ($bf,full_tag("ASSESSMENTSTART",4,false,$workshop->assessmentstart));
        fwrite ($bf,full_tag("SUBMISSIONEND",4,false,$workshop->submissionend));
        fwrite ($bf,full_tag("ASSESSMENTEND",4,false,$workshop->assessmentend));
        fwrite ($bf,full_tag("RELEASEGRADES",4,false,$workshop->releasegrades));
        fwrite ($bf,full_tag("GRADE",4,false,$workshop->grade));
        fwrite ($bf,full_tag("GRADINGGRADE",4,false,$workshop->gradinggrade));
        fwrite ($bf,full_tag("NTASSESSMENTS",4,false,$workshop->ntassessments));
        fwrite ($bf,full_tag("ASSESSMENTCOMPS",4,false,$workshop->assessmentcomps));
        fwrite ($bf,full_tag("NSASSESSMENTS",4,false,$workshop->nsassessments));
        fwrite ($bf,full_tag("OVERALLOCATION",4,false,$workshop->overallocation));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$workshop->timemodified));
        fwrite ($bf,full_tag("TEACHERWEIGHT",4,false,$workshop->teacherweight));
        fwrite ($bf,full_tag("SHOWLEAGUETABLE",4,false,$workshop->showleaguetable));
        fwrite ($bf,full_tag("USEPASSWORD",4,false,$workshop->usepassword));
        fwrite ($bf,full_tag("PASSWORD",4,false,$workshop->password));
        //Now we backup workshop elements
        $status = backup_workshop_elements($bf,$preferences,$workshop->id);

        //if we've selected to backup users info, then execute backup_workshop_submisions
        if (backup_userdata_selected($preferences,'workshop',$workshop->id)) {
            $ws = array();
            $status = backup_workshop_submissions($bf,$preferences,$workshop->id,$ws);
            $status = backup_workshop_files_instance($bf,$preferences,$workshop->id,$ws);
        }
        
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    //Backup workshop_elements contents (executed from workshop_backup_mods)
    function backup_workshop_elements ($bf,$preferences,$workshop) {

        global $CFG;

        $status = true;

        $workshop_elements = get_records("workshop_elements","workshopid",$workshop,"id");
        //If there is workshop_elements
        if ($workshop_elements) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ELEMENTS",4,true));
            //Iterate over each element
            foreach ($workshop_elements as $wor_ele) {
                //Start element
                $status =fwrite ($bf,start_tag("ELEMENT",5,true));
                //Print element contents
                fwrite ($bf,full_tag("ELEMENTNO",6,false,$wor_ele->elementno));
                fwrite ($bf,full_tag("DESCRIPTION",6,false,$wor_ele->description));
                fwrite ($bf,full_tag("SCALE",6,false,$wor_ele->scale));
                fwrite ($bf,full_tag("MAXSCORE",6,false,$wor_ele->maxscore));
                fwrite ($bf,full_tag("WEIGHT",6,false,$wor_ele->weight));
                fwrite ($bf,full_tag("STDDEV",6,false,$wor_ele->stddev));
                fwrite ($bf,full_tag("TOTALASSESSMENTS",6,false,$wor_ele->totalassessments));
                //Now we backup workshop rubrics
                $status = backup_workshop_rubrics($bf,$preferences,$workshop,$wor_ele->elementno);
                //Now we backup element's stock comments
                $status = backup_workshop_stockcomments($bf,$preferences,$workshop,$wor_ele->elementno);
                //End element
                $status =fwrite ($bf,end_tag("ELEMENT",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ELEMENTS",4,true));
        }
        return $status;
    }

    //Backup workshop_rubrics contents (executed from backup_workshop_elements)
    function backup_workshop_rubrics ($bf,$preferences,$workshop,$elementno) {

        global $CFG;

        $status = true;

        $workshop_rubrics = get_records_sql("SELECT * from {$CFG->prefix}workshop_rubrics r
                                             WHERE r.workshopid = '$workshop' and r.elementno = '$elementno'
                                             ORDER BY r.elementno");

        //If there is workshop_rubrics
        if ($workshop_rubrics) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RUBRICS",6,true));
            //Iterate over each element
            foreach ($workshop_rubrics as $wor_rub) {
                //Start rubric
                $status =fwrite ($bf,start_tag("RUBRIC",7,true));
                //Print rubric contents
                fwrite ($bf,full_tag("RUBRICNO",8,false,$wor_rub->rubricno));
                fwrite ($bf,full_tag("DESCRIPTION",8,false,$wor_rub->description));
                //End rubric
                $status =fwrite ($bf,end_tag("RUBRIC",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("RUBRICS",6,true));
        }
        return $status;
    }

    //Backup workshop_stockcomments contents (executed from backup_workshop_elements)
    function backup_workshop_stockcomments ($bf,$preferences,$workshop,$elementno) {

        global $CFG;

        $status = true;

        $workshop_stockcomments = get_records_sql("SELECT * from {$CFG->prefix}workshop_stockcomments c
                                              WHERE c.workshopid = '$workshop' and c.elementno = '$elementno'
                                              ORDER BY c.id");

        //If there is workshop_stockcomments
        if ($workshop_stockcomments) {
            //Write start tag
            $status =fwrite ($bf,start_tag("STOCKCOMMENTS",6,true));
            //Iterate over each comment
            foreach ($workshop_stockcomments as $wor_com) {
                //Start comment
                $status =fwrite ($bf,start_tag("STOCKCOMMENT",7,true));
                //Print comment contents
                fwrite ($bf,full_tag("COMMENT_TEXT",8,false,$wor_com->comments));
                //End comment
                $status =fwrite ($bf,end_tag("STOCKCOMMENT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("STOCKCOMMENTS",6,true));
        }
        return $status;
    }

    //Backup workshop_submissions contents (executed from workshop_backup_mods)
    function backup_workshop_submissions ($bf,$preferences,$workshop,&$workshop_submissions) {

        global $CFG;

        $status = true;

        $workshop_submissions = get_records("workshop_submissions","workshopid",$workshop,"id");
        //If there is submissions
        if ($workshop_submissions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SUBMISSIONS",4,true));
            //Iterate over each submission
            foreach ($workshop_submissions as $wor_sub) {
                //Start submission
                $status =fwrite ($bf,start_tag("SUBMISSION",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$wor_sub->id));       
                fwrite ($bf,full_tag("USERID",6,false,$wor_sub->userid));       
                fwrite ($bf,full_tag("TITLE",6,false,$wor_sub->title));       
                fwrite ($bf,full_tag("TIMECREATED",6,false,$wor_sub->timecreated));       
                fwrite ($bf,full_tag("MAILED",6,false,$wor_sub->mailed));       
                fwrite ($bf,full_tag("DESCRIPTION",6,false,$wor_sub->description));       
                fwrite ($bf,full_tag("GRADINGGRADE",6,false,$wor_sub->gradinggrade));       
                fwrite ($bf,full_tag("FINALGRADE",6,false,$wor_sub->finalgrade));       
                fwrite ($bf,full_tag("LATE",6,false,$wor_sub->late));       
                fwrite ($bf,full_tag("NASSESSMENTS",6,false,$wor_sub->nassessments));       
                //Now we backup workshop assessments
                $status = backup_workshop_assessments($bf,$preferences,$workshop,$wor_sub->id);
                //End submission
                $status =fwrite ($bf,end_tag("SUBMISSION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SUBMISSIONS",4,true));
        }
        return $status;
    }

    //Backup workshop_assessments contents (executed from backup_workshop_submissions)
    function backup_workshop_assessments ($bf,$preferences,$workshop,$submission) {

        global $CFG;

        $status = true;

        //NOTE: I think that the workshopid can go out (submissionid is a good unique fk), but mantain it, as is in db !!
        $workshop_assessments = get_records_sql("SELECT * from {$CFG->prefix}workshop_assessments a
                                                 WHERE a.workshopid = '$workshop' and a.submissionid = '$submission'
                                                 ORDER BY a.id");

        //If there is workshop_assessments
        if ($workshop_assessments) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ASSESSMENTS",6,true));
            //Iterate over each assessment
            foreach ($workshop_assessments as $wor_ass) {
                //Start assessment
                $status =fwrite ($bf,start_tag("ASSESSMENT",7,true));
                //Print assessment contents
                fwrite ($bf,full_tag("ID",8,false,$wor_ass->id));
                fwrite ($bf,full_tag("USERID",8,false,$wor_ass->userid));
                fwrite ($bf,full_tag("TIMECREATED",8,false,$wor_ass->timecreated));
                fwrite ($bf,full_tag("TIMEGRADED",8,false,$wor_ass->timegraded));
                fwrite ($bf,full_tag("TIMEAGREED",8,false,$wor_ass->timeagreed));
                fwrite ($bf,full_tag("GRADE",8,false,$wor_ass->grade));
                fwrite ($bf,full_tag("GRADINGGRADE",8,false,$wor_ass->gradinggrade));
                fwrite ($bf,full_tag("TEACHERGRADED",8,false,$wor_ass->teachergraded));
                fwrite ($bf,full_tag("MAILED",8,false,$wor_ass->mailed));
                fwrite ($bf,full_tag("RESUBMISSION",8,false,$wor_ass->resubmission));
                fwrite ($bf,full_tag("DONOTUSE",8,false,$wor_ass->donotuse));
                fwrite ($bf,full_tag("GENERALCOMMENT",8,false,$wor_ass->generalcomment));
                fwrite ($bf,full_tag("TEACHERCOMMENT",8,false,$wor_ass->teachercomment));
                //Now we backup workshop comments
                $status = backup_workshop_comments($bf,$preferences,$workshop,$wor_ass->id);
                //Now we backup workshop grades
                $status = backup_workshop_grades($bf,$preferences,$workshop,$wor_ass->id);
                //End assessment
                $status =fwrite ($bf,end_tag("ASSESSMENT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ASSESSMENTS",6,true));
        }
        return $status;
    }

    //Backup workshop_comments contents (executed from backup_workshop_assessments)
    function backup_workshop_comments ($bf,$preferences,$workshop,$assessmentid) {

        global $CFG;

        $status = true;

        //NOTE: I think that the workshopid can go out (assessmentid is a good unique fk), but mantain it, as is in db !!
        $workshop_comments = get_records_sql("SELECT * from {$CFG->prefix}workshop_comments c
                                              WHERE c.workshopid = '$workshop' and c.assessmentid = '$assessmentid'
                                              ORDER BY c.id");

        //If there is workshop_comments
        if ($workshop_comments) {
            //Write start tag
            $status =fwrite ($bf,start_tag("COMMENTS",8,true));
            //Iterate over each comment
            foreach ($workshop_comments as $wor_com) {
                //Start comment
                $status =fwrite ($bf,start_tag("COMMENT",9,true));
                //Print comment contents
                fwrite ($bf,full_tag("USERID",10,false,$wor_com->userid));
                fwrite ($bf,full_tag("TIMECREATED",10,false,$wor_com->timecreated));
                fwrite ($bf,full_tag("MAILED",10,false,$wor_com->mailed));
                fwrite ($bf,full_tag("COMMENT_TEXT",10,false,$wor_com->comments));
                //End comment
                $status =fwrite ($bf,end_tag("COMMENT",9,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("COMMENTS",8,true));
        }
        return $status;
    }

    //Backup workshop_grades contents (executed from backup_workshop_assessments)
    function backup_workshop_grades ($bf,$preferences,$workshop,$assessmentid) {

        global $CFG;

        $status = true;

        //NOTE: I think that the workshopid can go out (assessmentid is a good unique fk), but mantain it, as is in db !!
        $workshop_grades = get_records_sql("SELECT * from {$CFG->prefix}workshop_grades g
                                              WHERE g.workshopid = '$workshop' and g.assessmentid = '$assessmentid'
                                              ORDER BY g.elementno");

        //If there is workshop_grades
        if ($workshop_grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("GRADES",8,true));
            //Iterate over each grade
            foreach ($workshop_grades as $wor_gra) {
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


    //Backup workshop files because we've selected to backup user info
    //and files are user info's level
    function backup_workshop_files($bf,$preferences) {

        global $CFG;
       
        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the workshop dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/workshop")) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/workshop/",
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/workshop/");
            }
        }

        return $status;

    } 

    function backup_workshop_files_instance($bf,$preferences,$instanceid,$ws) {
        global $CFG;
        
        $status = true;
        
        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/workshop/",true);
        //Now copy the forum dir
        if ($status) {
            foreach ($ws as $submission) {
                //Only if it exists !! Thanks to Daniel Miksik.
                if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/workshop/".$submission->id)) {
                    $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/workshop/".$submission->id,
                                               $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/workshop/".$submission->id);
                }
            }
        }

        return $status;
    }

function workshop_check_backup_mods_instances($instance,$backup_unique_code) {
    //First the course data
    $info[$instance->id.'0'][0] = $instance->name;
    $info[$instance->id.'0'][1] = '';
    //Now, if requested, the user_data
    if (!empty($instance->userdata)) {
        $info[$instance->id.'1'][0] = get_string("submissions","workshop");
        if ($ids = workshop_submission_ids_by_instance ($instance->id)) { 
            $info[$instance->id.'1'][1] = count($ids);
        } else {
            $info[$instance->id.'1'][1] = 0;
        }
    }
    return $info;
}


    //Return an array of info (name,value)
    function workshop_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += workshop_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","workshop");
        if ($ids = workshop_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("submissions","workshop");
            if ($ids = workshop_submission_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function workshop_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of workshops
        $buscar="/(".$base."\/mod\/workshop\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@WORKSHOPINDEX*$2@$',$content);

        //Link to workshop view by moduleid
        $buscar="/(".$base."\/mod\/workshop\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@WORKSHOPVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of workshop id 
    function workshop_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT w.id, w.course
                                 FROM {$CFG->prefix}workshop w
                                 WHERE w.course = '$course'");
    }
    
    //Returns an array of workshop_submissions id
    function workshop_submission_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.workshopid
                                 FROM {$CFG->prefix}workshop_submissions s,
                                      {$CFG->prefix}workshop w
                                 WHERE w.course = '$course' AND
                                       s.workshopid = w.id");
    }

    function workshop_submission_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.workshopid
                                 FROM {$CFG->prefix}workshop_submissions s
                                 WHERE s.workshopid = $instanceid");
    }
?>
