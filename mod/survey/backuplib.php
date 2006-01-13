<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //survey mods

    //This is the "graphical" structure of the survey mod:
    //                                                    --------------------
    //                           survey                   | survey_questions |
    //                        (CL,pk->id)                 |(CL,pk->id,?????) |
    //                            |                       --------------------
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        survey_analysis                   survey_answers
    //    (UL,pk->id, fk->survey)           (UL,pk->id, fk->survey)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function survey_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over survey table
        $surveys = get_records ("survey","course",$preferences->backup_course,"id");
        if ($surveys) {
            foreach ($surveys as $survey) {
                if (backup_mod_selected($preferences,'survey',$survey->id)) {
                    $status = survey_backup_one_mod($bf,$preferences,$survey);
                }
            }
        }
        return $status;
    }

    function survey_backup_one_mod($bf,$preferences,$survey) {
        
        $status = true;
        
        if (is_numeric($survey)) {
            $survey = get_record('survey','id',$survey);
        }
        
        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print choice data
        fwrite ($bf,full_tag("ID",4,false,$survey->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"survey"));
        fwrite ($bf,full_tag("TEMPLATE",4,false,$survey->template));
        fwrite ($bf,full_tag("DAYS",4,false,$survey->days));
        fwrite ($bf,full_tag("TIMECREATED",4,false,$survey->timecreated));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$survey->timemodified));
        fwrite ($bf,full_tag("NAME",4,false,$survey->name));
        fwrite ($bf,full_tag("INTRO",4,false,$survey->intro));
        fwrite ($bf,full_tag("QUESTIONS",4,false,$survey->questions));
        
        //if we've selected to backup users info, then execute backup_survey_answers and
        //backup_survey_analysis
        if (backup_userdata_selected($preferences,'survey',$survey->id)) {
            $status = backup_survey_answers($bf,$preferences,$survey->id);
            $status = backup_survey_analysis($bf,$preferences,$survey->id);
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));
        return $status;
    }

    //Backup survey_answers contents (executed from survey_backup_mods)
    function backup_survey_answers ($bf,$preferences,$survey) {

        global $CFG;

        $status = true;

        $survey_answers = get_records("survey_answers","survey",$survey,"id");
        //If there is answers
        if ($survey_answers) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ANSWERS",4,true));
            //Iterate over each answer
            foreach ($survey_answers as $sur_ans) {
                //Start answer
                $status =fwrite ($bf,start_tag("ANSWER",5,true));
                //Print survey_answers contents
                fwrite ($bf,full_tag("ID",6,false,$sur_ans->id));
                fwrite ($bf,full_tag("USERID",6,false,$sur_ans->userid));
                fwrite ($bf,full_tag("QUESTION",6,false,$sur_ans->question));
                fwrite ($bf,full_tag("TIME",6,false,$sur_ans->time));
                fwrite ($bf,full_tag("ANSWER1",6,false,$sur_ans->answer1));
                fwrite ($bf,full_tag("ANSWER2",6,false,$sur_ans->answer2));
                //End answer
                $status =fwrite ($bf,end_tag("ANSWER",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ANSWERS",4,true));
        }
        return $status;
    }

    //Backup survey_analysis contents (executed from survey_backup_mods)
    function backup_survey_analysis ($bf,$preferences,$survey) {

        global $CFG;

        $status = true;

        $survey_analysis = get_records("survey_analysis","survey",$survey,"id");
        //If there is analysis
        if ($survey_analysis) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ANALYSIS",4,true));
            //Iterate over each analysis
            foreach ($survey_analysis as $sur_ana) {
                //Start answer
                $status =fwrite ($bf,start_tag("ANALYS",5,true));
                //Print survey_analysis contents
                fwrite ($bf,full_tag("ID",6,false,$sur_ana->id));
                fwrite ($bf,full_tag("USERID",6,false,$sur_ana->userid));
                fwrite ($bf,full_tag("NOTES",6,false,$sur_ana->notes));
                //End answer
                $status =fwrite ($bf,end_tag("ANALYS",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ANALYSIS",4,true));
        }
        return $status;
    }

    function survey_check_backup_mods_instances($instance,$backup_unique_code) {
        $info[$instance->id.'0'][0] = $instance->name;
        $info[$instance->id.'0'][1] = '';
        if (!empty($instance->userdata)) {
              //Subscriptions
            $info[$instance->id.'1'][0] = get_string("answers","survey");
            if ($ids = survey_answer_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }


    ////Return an array of info (name,value)
    function survey_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += survey_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
       //First the course data
       $info[0][0] = get_string("modulenameplural","survey");
       if ($ids = survey_ids ($course)) {
           $info[0][1] = count($ids);
       } else {
           $info[0][1] = 0;
       }

        //Now, if requested, the user_data
        if ($user_data) {
            //Subscriptions
            $info[1][0] = get_string("answers","survey");
            if ($ids = survey_answer_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function servey_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of serveys
        $buscar="/(".$base."\/mod\/servey\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@SURVEYINDEX*$2@$',$content);

        //Link to servey view by moduleid
        $buscar="/(".$base."\/mod\/servey\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@SURVEYVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of surveys id
    function survey_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}survey a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of survey answer id
    function survey_answer_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.survey
                                 FROM {$CFG->prefix}survey_answers s,
                                      {$CFG->prefix}survey a
                                 WHERE a.course = '$course' AND
                                       s.survey = a.id");
    }

    function survey_answer_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.survey
                                 FROM {$CFG->prefix}survey_answers s
                                 WHERE s.survey = $instanceid");
    }

?>
