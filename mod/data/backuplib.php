<?php //Id:$

//This php script contains all the stuff to backup/restore data mod

    //This is the "graphical" structure of the data mod:
    //
    //                     data
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //      ---------------------------------------------------------------------------------
    //      |                                                                               |
    //data_records (UL,pk->id, fk->data)                                      data_fields (pk->id, fk->data)
    //               |                                                                      |
    //               |                                                                      |
    //     -----------------------------------------------------------------------------    |
    //     |                                  |                                        |    |
    //data_ratings(fk->recordid, pk->id) data_comments (fk->recordid, pk->id)          |    |
    //                                                                  data_content(pk->id, fk->recordid, fk->fieldid)
    //
    //
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //Backup data files because we've selected to backup user info
    //and files are user info's level


    //Return a content encoded to support interactivities linking. Every module

function data_backup_mods($bf,$preferences) {
    global $CFG;

    $status = true;

    // iterate
    if ($datas = get_records('data','course',$preferences->backup_course,"id")) {
        foreach ($datas as $data) {
           if (function_exists('backup_mod_selected')) {
                    // Moodle 1.6
                    $backup_mod_selected = backup_mod_selected($preferences, 'data', $data->id);
            } else {
                    // Moodle 1.5
                $backup_mod_selected = true;
            }
            if ($backup_mod_selected) {
                $status = data_backup_one_mod($bf,$preferences,$data);
                // backup files happens in backup_one_mod now too.
            }
        }
    }
    return $status;
}

function data_backup_one_mod($bf,$preferences,$data) {
    global $CFG;

    if (is_numeric($data)) { // backwards compatibility
        $data = get_record('data','id',$data);
    }
    $instanceid = $data->id;

    $status = true;


    fwrite ($bf,start_tag("MOD",3,true));
    //Print data data
    fwrite ($bf,full_tag("ID",4,false,$data->id));
    fwrite ($bf,full_tag("MODTYPE",4,false,"data"));
    fwrite ($bf,full_tag("NAME",4,false,$data->name));
    fwrite ($bf,full_tag("INTRO",4,false,$data->intro));
    fwrite ($bf,full_tag("COMMENTS",4,false,$data->comments));
    fwrite ($bf,full_tag("TIMEAVAILABLEFROM",4,false,$data->timeavailablefrom));
    fwrite ($bf,full_tag("TIMEAVAILABLETO",4,false,$data->timeavailableto));
    fwrite ($bf,full_tag("TIMEVIEWFROM",4,false,$data->timeviewfrom));
    fwrite ($bf,full_tag("TIMEVIEWTO",4,false,$data->timeviewto));
    fwrite ($bf,full_tag("REQUIREDENTRIES",4,false,$data->requiredentries));
    fwrite ($bf,full_tag("REQUIREDENTRIESTOVIEW",4,false,$data->requiredentriestoview));
    fwrite ($bf,full_tag("MAXENTRIES",4,false,$data->maxentries));
    fwrite ($bf,full_tag("RSSARTICLES",4,false,$data->rssarticles));
    fwrite ($bf,full_tag("SINGLETEMPLATE",4,false,$data->singletemplate));
    fwrite ($bf,full_tag("LISTTEMPLATE",4,false,$data->listtemplate));
    fwrite ($bf,full_tag("LISTTEMPLATEHEADER",4,false,$data->listtemplateheader));
    fwrite ($bf,full_tag("LISTTEMPLATEFOOTER",4,false,$data->listtemplatefooter));
    fwrite ($bf,full_tag("ADDTEMPLATE",4,false,$data->addtemplate));
    fwrite ($bf,full_tag("RSSTEMPLATE",4,false,$data->rsstemplate));
    fwrite ($bf,full_tag("RSSTITLETEMPLATE",4,false,$data->rsstitletemplate));
    fwrite ($bf,full_tag("CSSTEMPLATE",4,false,$data->csstemplate));
    fwrite ($bf,full_tag("JSTEMPLATE",4,false,$data->jstemplate));
    fwrite ($bf,full_tag("ASEARCHTEMPLATE",4,false,$data->asearchtemplate));
    fwrite ($bf,full_tag("APPROVAL",4,false,$data->approval));
    fwrite ($bf,full_tag("SCALE",4,false,$data->scale));
    fwrite ($bf,full_tag("ASSESSED",4,false,$data->assessed));
    fwrite ($bf,full_tag("DEFAULTSORT",4,false,$data->defaultsort));
    fwrite ($bf,full_tag("DEFAULTSORTDIR",4,false,$data->defaultsortdir));
    fwrite ($bf,full_tag("EDITANY",4,false,$data->editany));
    fwrite ($bf,full_tag("NOTIFICATION",4,false,$data->notification));

    // if we've selected to backup users info, then call any other functions we need
    // including backing up individual files

    $status = backup_data_fields($bf,$preferences,$data->id);

    if (backup_userdata_selected($preferences,'data',$data->id)) {
        //$status = backup_someuserdata_for_this_instance();
        //$status = backup_somefiles_for_this_instance();
        // ... etc

        $status = backup_data_records($bf,$preferences,$data->id);
        if ($status) {
            $status = backup_data_files_instance($bf,$preferences,$data->id);    //recursive copy
        }
    }
    fwrite ($bf,end_tag("MOD",3,true));
    return $status;

}


function backup_data_fields($bf,$preferences,$dataid){
    global $CFG;
    $status = true;

    $data_fields = get_records("data_fields","dataid",$dataid);

        //If there is submissions
        if ($data_fields) {
            //Write start tag
            $status =fwrite ($bf,start_tag("FIELDS",4,true));
            //Iterate over each submission
            foreach ($data_fields as $fie_sub) {
                //Start submission
                $status =fwrite ($bf,start_tag("FIELD",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$fie_sub->id));
                fwrite ($bf,full_tag("DATAID",6,false,$fie_sub->dataid));
                fwrite ($bf,full_tag("TYPE",6,false,$fie_sub->type));
                fwrite ($bf,full_tag("NAME",6,false,$fie_sub->name));
                fwrite ($bf,full_tag("DESCRIPTION",6,false,$fie_sub->description));
                fwrite ($bf,full_tag("PARAM1",6,false,$fie_sub->param1));
                fwrite ($bf,full_tag("PARAM2",6,false,$fie_sub->param2));
                fwrite ($bf,full_tag("PARAM3",6,false,$fie_sub->param3));
                fwrite ($bf,full_tag("PARAM4",6,false,$fie_sub->param4));
                fwrite ($bf,full_tag("PARAM5",6,false,$fie_sub->param5));
                fwrite ($bf,full_tag("PARAM6",6,false,$fie_sub->param6));
                fwrite ($bf,full_tag("PARAM7",6,false,$fie_sub->param7));
                fwrite ($bf,full_tag("PARAM8",6,false,$fie_sub->param8));
                fwrite ($bf,full_tag("PARAM9",6,false,$fie_sub->param9));
                fwrite ($bf,full_tag("PARAM10",6,false,$fie_sub->param10));

                //End submission
                $status =fwrite ($bf,end_tag("FIELD",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("FIELDS",4,true));
        }
        return $status;
}

function backup_data_content($bf,$preferences,$recordid){
    global $CFG;
    $status = true;

    $data_contents = get_records("data_content","recordid",$recordid);

        //If there is submissions
        if ($data_contents) {
            //Write start tag
            $status =fwrite ($bf,start_tag("CONTENTS",6,true));
            //Iterate over each submission
            foreach ($data_contents as $cnt_sub) {
                //Start submission
                $status =fwrite ($bf,start_tag("CONTENT",7,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",8,false,$cnt_sub->id));
                fwrite ($bf,full_tag("RECORDID",8,false,$cnt_sub->recordid));
                fwrite ($bf,full_tag("FIELDID",8,false,$cnt_sub->fieldid));
                fwrite ($bf,full_tag("CONTENT",8,false,$cnt_sub->content));
                fwrite ($bf,full_tag("CONTENT1",8,false,$cnt_sub->content1));
                fwrite ($bf,full_tag("CONTENT2",8,false,$cnt_sub->content2));
                fwrite ($bf,full_tag("CONTENT3",8,false,$cnt_sub->content3));
                fwrite ($bf,full_tag("CONTENT4",8,false,$cnt_sub->content4));
                //End submission
                $status =fwrite ($bf,end_tag("CONTENT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("CONTENTS",6,true));
        }
        return $status;

}
function backup_data_ratings($bf,$preferences,$recordid){
    global $CFG;
    $status = true;
    $data_ratings = get_records("data_ratings","recordid",$recordid);

    //If there is submissions
    if ($data_ratings) {
        //Write start tag
        $status =fwrite ($bf,start_tag("RATINGS",6,true));
        //Iterate over each submission
        foreach ($data_ratings as $rat_sub) {
            //Start submission
            $status =fwrite ($bf,start_tag("RATING",7,true));
            //Print submission contents
            fwrite ($bf,full_tag("ID",8,false,$rat_sub->id));
            fwrite ($bf,full_tag("RECORDID",8,false,$rat_sub->recordid));
            fwrite ($bf,full_tag("USERID",8,false,$rat_sub->userid));
            fwrite ($bf,full_tag("RATING",8,false,$rat_sub->rating));
            //End submission
            $status =fwrite ($bf,end_tag("RATING",7,true));
        }
            //Write end tag
        $status =fwrite ($bf,end_tag("RATINGS",6,true));

    }

    return $status;
}
function backup_data_comments($bf,$preferences,$recordid){
    global $CFG;
    $status = true;
    $data_comments = get_records("data_comments","recordid",$recordid);

    //If there is submissions
    if ($data_comments) {
        //Write start tag
        $status =fwrite ($bf,start_tag("COMMENTS",6,true));
            //Iterate over each submission
        foreach ($data_comments as $com_sub) {
            //Start submission
            $status =fwrite ($bf,start_tag("COMMENT",7,true));
            //Print submission contents
            fwrite ($bf,full_tag("ID",8,false,$com_sub->id));
            fwrite ($bf,full_tag("RECORDID",8,false,$com_sub->recordid));
            fwrite ($bf,full_tag("USERID",8,false,$com_sub->userid));
            fwrite ($bf,full_tag("CONTENT",8,false,$com_sub->content));
            fwrite ($bf,full_tag("CREATED",8,false,$com_sub->created));
            fwrite ($bf,full_tag("MODIFIED",8,false,$com_sub->modified));
            //End submission
            $status =fwrite ($bf,end_tag("COMMENT",7,true));
        }
        //Write end tag
        $status =fwrite ($bf,end_tag("COMMENTS",6,true));
    }
    return $status;
}


function backup_data_files_instance($bf,$preferences,$instanceid) {

    global $CFG;
    $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
    $status = check_and_create_moddata_dir($preferences->backup_unique_code);
    $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/data/",true);
        //Now copy the data dir
    if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
        if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data/".$instanceid)) {
            $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/data/".$instanceid);
        }
    }
    return $status;
}

function backup_data_records($bf,$preferences,$dataid){

    global $CFG;
    $status = true;

    $data_records = get_records("data_records","dataid",$dataid);
        //If there is submissions
        if ($data_records) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RECORDS",4,true));
            //Iterate over each submission
            foreach ($data_records as $rec_sub) {
                //Start submission
                $status =fwrite ($bf,start_tag("RECORD",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$rec_sub->id));
                fwrite ($bf,full_tag("USERID",6,false,$rec_sub->userid));
                fwrite ($bf,full_tag("GROUPID",6,false,$rec_sub->groupid));
                fwrite ($bf,full_tag("DATAID",6,false,$rec_sub->dataid));
                fwrite ($bf,full_tag("TIMECREATED",6,false,$rec_sub->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$rec_sub->timemodified));
                fwrite ($bf,full_tag("APPROVED",6,false,$rec_sub->approved));
                //End submission

                backup_data_content($bf,$preferences,$rec_sub->id);
                backup_data_ratings($bf,$preferences,$rec_sub->id);
                backup_data_comments($bf,$preferences,$rec_sub->id);

                $status =fwrite ($bf,end_tag("RECORD",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("RECORDS",4,true));
        }
        return $status;

}

function backup_data_files($bf,$preferences) {

    global $CFG;

    $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
    $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the data dir
    if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
        if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data")) {
            $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data",
                                               $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/data");
        }
    }

    return $status;
}

function backup_data_file_instance($bf,$preferences,$instanceid) {

    global $CFG;
    $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
    $status = check_and_create_moddata_dir($preferences->backup_unique_code);
    $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/data/",true);
        //Now copy the data dir
    if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
        if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data/".$instanceid)) {
            $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/data/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/data/".$instanceid);
        }
    }
    return $status;
}

function data_check_backup_mods_instances($instance,$backup_unique_code) {
    $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
    $info[$instance->id.'0'][1] = '';
    if (!empty($instance->userdata)) {
        // any other needed stuff
    }
    return $info;
}

function data_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
    if (!empty($instances) && is_array($instances) && count($instances)) {
        $info = array();
        foreach ($instances as $id => $instance) {
            $info += data_check_backup_mods_instances($instance,$backup_unique_code);
        }
        return $info;
    }

    // otherwise continue as normal
    //First the course data
    $info[0][0] = get_string("modulenameplural","data");
    if ($ids = data_ids ($course)) {
        $info[0][1] = count($ids);
    } else {
        $info[0][1] = 0;
    }

    //Now, if requested, the user_data
    if ($user_data) {
        // any other needed stuff
    }
    return $info;

}

/**
 * Returns a content encoded to support interactivities linking. Every module
 * should have its own. They are called automatically from the backup procedure.
 *
 * @param string $content content to be encoded
 * @param object $preferences backup preferences in use
 * @return string the content encoded
 */
function data_encode_content_links ($content,$preferences) {

    global $CFG;

    $base = preg_quote($CFG->wwwroot,"/");

/// Link to one "record" of the database
    $search="/(".$base."\/mod\/data\/view.php\?d\=)([0-9]+)\&rid\=([0-9]+)/";
    $result= preg_replace($search,'$@DATAVIEWRECORD*$2*$3@$',$content);

/// Link to the list of databases
    $search="/(".$base."\/mod\/data\/index.php\?id\=)([0-9]+)/";
    $result= preg_replace($search,'$@DATAINDEX*$2@$',$result);

/// Link to database view by moduleid
    $search="/(".$base."\/mod\/data\/view.php\?id\=)([0-9]+)/";
    $result= preg_replace($search,'$@DATAVIEWBYID*$2@$',$result);

/// Link to database view by databaseid
    $search="/(".$base."\/mod\/data\/view.php\?d\=)([0-9]+)/";
    $result= preg_replace($search,'$@DATAVIEWBYD*$2@$',$result);

    return $result;
}

function data_ids($course) {
    // stub function, return number of modules
    return 1;
}
