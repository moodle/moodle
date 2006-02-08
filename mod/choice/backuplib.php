<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //choice mods

    //This is the "graphical" structure of the choice mod:
    //
    //                      choice                                      
    //                    (CL,pk->id)----------|
    //                        |                |
    //                        |                |
    //                        |                |
    //                  choice_options         |
    //             (UL,pk->id, fk->choiceid)   |  
    //                        |                |
    //                        |                |
    //                        |                |
    //                   choice_answers        |
    //        (UL,pk->id, fk->choiceid, fk->optionid)     
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function choice_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over choice table
        $choices = get_records ("choice","course",$preferences->backup_course,"id");
        if ($choices) {
            foreach ($choices as $choice) {
                if (backup_mod_selected($preferences,'choice',$choice->id)) {
                    $status = choice_backup_one_mod($bf,$preferences,$choice);
                }
            }
        }
        return $status;
    }

    function choice_backup_one_mod($bf,$preferences,$choice) {

        global $CFG;
    
        if (is_numeric($choice)) {
            $choice = get_record('choice','id',$choice);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print choice data
        fwrite ($bf,full_tag("ID",4,false,$choice->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"choice"));
        fwrite ($bf,full_tag("NAME",4,false,$choice->name));
        fwrite ($bf,full_tag("TEXT",4,false,$choice->text));
        fwrite ($bf,full_tag("FORMAT",4,false,$choice->format));
        fwrite ($bf,full_tag("PUBLISH",4,false,$choice->publish));
        fwrite ($bf,full_tag("SHOWRESULTS",4,false,$choice->showresults));
        fwrite ($bf,full_tag("DISPLAY",4,false,$choice->display));
        fwrite ($bf,full_tag("ALLOWUPDATE",4,false,$choice->allowupdate));
        fwrite ($bf,full_tag("SHOWUNANSWERED",4,false,$choice->showunanswered));
        fwrite ($bf,full_tag("LIMITANSWERS",4,false,$choice->limitanswers));
        fwrite ($bf,full_tag("TIMEOPEN",4,false,$choice->timeopen));
        fwrite ($bf,full_tag("TIMECLOSE",4,false,$choice->timeclose));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$choice->timemodified));

        //Now backup choice_options
        $status = backup_choice_options($bf,$preferences,$choice->id);

        //if we've selected to backup users info, then execute backup_choice_answers
        if (backup_userdata_selected($preferences,'choice',$choice->id)) {
            $status = backup_choice_answers($bf,$preferences,$choice->id);
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    //Backup choice_answers contents (executed from choice_backup_mods)
    function backup_choice_answers ($bf,$preferences,$choice) {

        global $CFG;

        $status = true;

        $choice_answers = get_records("choice_answers","choiceid",$choice,"id");
        //If there is answers
        if ($choice_answers) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ANSWERS",4,true));
            //Iterate over each answer
            foreach ($choice_answers as $cho_ans) {
                //Start answer
                $status =fwrite ($bf,start_tag("ANSWER",5,true));
                //Print answer contents
                fwrite ($bf,full_tag("ID",6,false,$cho_ans->id));
                fwrite ($bf,full_tag("USERID",6,false,$cho_ans->userid));
                fwrite ($bf,full_tag("OPTIONID",6,false,$cho_ans->optionid));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$cho_ans->timemodified));
                //End answer
                $status =fwrite ($bf,end_tag("ANSWER",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ANSWERS",4,true));
        }
        return $status;
    }


    //backup choice_options contents (executed from choice_backup_mods)
    function backup_choice_options ($bf,$preferences,$choice) {

        global $CFG;

        $status = true;
        
        $choice_options = get_records("choice_options","choiceid",$choice,"id");
        //If there is options
        if ($choice_options) {            
            //Write start tag
            $status =fwrite ($bf,start_tag("OPTIONS",4,true));
            //Iterate over each answer
            foreach ($choice_options as $cho_opt) {
                //Start option
                $status =fwrite ($bf,start_tag("OPTION",5,true));
                //Print option contents
                fwrite ($bf,full_tag("ID",6,false,$cho_opt->id));
                fwrite ($bf,full_tag("TEXT",6,false,$cho_opt->text));
                fwrite ($bf,full_tag("MAXANSWERS",6,false,$cho_opt->maxanswers));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$cho_opt->timemodified));
                //End answer
                $status =fwrite ($bf,end_tag("OPTION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("OPTIONS",4,true));
        }
        return $status;
    }
   
   ////Return an array of info (name,value)
   function choice_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += choice_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","choice");
        if ($ids = choice_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("responses","choice");
            if ($ids = choice_answer_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

   ////Return an array of info (name,value)
   function choice_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("responses","choice");
            if ($ids = choice_answer_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function choice_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of choices
        $buscar="/(".$base."\/mod\/choice\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@CHOICEINDEX*$2@$',$content);

        //Link to choice view by moduleid
        $buscar="/(".$base."\/mod\/choice\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@CHOICEVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of choices id
    function choice_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}choice a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of choice_answers id
    function choice_answer_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.choiceid
                                 FROM {$CFG->prefix}choice_answers s,
                                      {$CFG->prefix}choice a
                                 WHERE a.course = '$course' AND
                                       s.choiceid = a.id");
    }

    //Returns an array of choice_answers id
    function choice_answer_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.choiceid
                                 FROM {$CFG->prefix}choice_answers s
                                 WHERE s.choiceid = $instanceid");
    }
?>
