<?php
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

$fieldids = array();    //array in the format of $fieldids[$oldid]=$newid. This is needed because of double dependencies of multiple tables.


    //Return a content encoded to support interactivities linking. Every module
function data_restore_mods($mod,$restore) {

    global $CFG;

    $status = true;

    $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

    if ($data) {
        //Now get completed xmlized object
        $info = $data->info;
        // if necessary, write to restorelog and adjust date/time fields
        if ($restore->course_startdateoffset) {
            restore_log_date_changes('Database', $restore, $info['MOD']['#'], array('TIMEAVAILABLEFROM', 'TIMEAVAILABLETO','TIMEVIEWFROM', 'TIMEVIEWTO'));
        }
        //traverse_xmlize($info);                                                                     //Debug
        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
        //$GLOBALS['traverse_array']="";                                                              //Debug

        $database->course = $restore->course_id;

        $database->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
        $database->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
        // Only relevant for restoring backups from 1.6 in a 1.7 install.
        if (isset($info['MOD']['#']['RATINGS']['0']['#'])) {
            $database->ratings = backup_todb($info['MOD']['#']['RATINGS']['0']['#']);
        }
        $database->comments = backup_todb($info['MOD']['#']['COMMENTS']['0']['#']);
        $database->timeavailablefrom = backup_todb($info['MOD']['#']['TIMEAVAILABLEFROM']['0']['#']);
        $database->timeavailableto = backup_todb($info['MOD']['#']['TIMEAVAILABLETO']['0']['#']);
        $database->timeviewfrom = backup_todb($info['MOD']['#']['TIMEVIEWFROM']['0']['#']);
        $database->timeviewto = backup_todb($info['MOD']['#']['TIMEVIEWTO']['0']['#']);
        // Only relevant for restoring backups from 1.6 in a 1.7 install.
        if (isset($info['MOD']['#']['PARTICIPANTS']['0']['#'])) {
            $database->participants = backup_todb($info['MOD']['#']['PARTICIPANTS']['0']['#']);
        }
        $database->requiredentries = backup_todb($info['MOD']['#']['REQUIREDENTRIES']['0']['#']);
        $database->requiredentriestoview = backup_todb($info['MOD']['#']['REQUIREDENTRIESTOVIEW']['0']['#']);
        $database->maxentries = backup_todb($info['MOD']['#']['MAXENTRIES']['0']['#']);
        $database->rssarticles = backup_todb($info['MOD']['#']['RSSARTICLES']['0']['#']);
        $database->singletemplate = backup_todb($info['MOD']['#']['SINGLETEMPLATE']['0']['#']);
        $database->listtemplate = backup_todb($info['MOD']['#']['LISTTEMPLATE']['0']['#']);
        $database->listtemplateheader = backup_todb($info['MOD']['#']['LISTTEMPLATEHEADER']['0']['#']);
        $database->listtemplatefooter = backup_todb($info['MOD']['#']['LISTTEMPLATEFOOTER']['0']['#']);
        $database->addtemplate = backup_todb($info['MOD']['#']['ADDTEMPLATE']['0']['#']);
        $database->rsstemplate = backup_todb($info['MOD']['#']['RSSTEMPLATE']['0']['#']);
        $database->rsstitletemplate = backup_todb($info['MOD']['#']['RSSTITLETEMPLATE']['0']['#']);
        $database->csstemplate = backup_todb($info['MOD']['#']['CSSTEMPLATE']['0']['#']);
        $database->jstemplate = backup_todb($info['MOD']['#']['JSTEMPLATE']['0']['#']);
        $database->approval = backup_todb($info['MOD']['#']['APPROVAL']['0']['#']);
        $database->scale = backup_todb($info['MOD']['#']['SCALE']['0']['#']);
        $database->assessed = backup_todb($info['MOD']['#']['ASSESSED']['0']['#']);
        // Only relevant for restoring backups from 1.6 in a 1.7 install.
        if (isset($info['MOD']['#']['ASSESSPUBLIC']['0']['#'])) {
            $database->assesspublic = backup_todb($info['MOD']['#']['ASSESSPUBLIC']['0']['#']);
        }
        
        $newid = insert_record ('data', $database);

        //Do some output
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("modulename","data")." \"".format_string(stripslashes($database->name),true)."\"</li>";
        }
        
        if ($newid) {
            //We have the newid, update backup_ids
            backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
            //Now check if want to restore user data and do it.
            if (function_exists('restore_userdata_selected')) {
                // Moodle 1.6
                $restore_userdata_selected = restore_userdata_selected($restore, 'data', $mod->id);
            } else {
                // Moodle 1.5
                $restore_userdata_selected = $restore->mods['data']->userinfo;
            }
            
            //Restore data_fields first!!! need to hold an array of [oldid]=>newid due to double dependencies
            $status = $status and data_fields_restore_mods ($mod->id, $newid, $info, $restore);
            
            if ($restore_userdata_selected) {
                $status = $status and data_records_restore_mods ($mod->id, $newid, $info, $restore);
            }

            // If the backup contained $data->participants, $data->assesspublic
            // and $data->groupmode, we need to convert the data to use Roles.
            // It means the backup was made pre Moodle 1.7. We check the
            // backup_version to make sure.
            if (isset($database->participants) && isset($database->assesspublic)) {
                            
                if (!$teacherroles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
                      notice('Default teacher role was not found. Roles and permissions '.
                             'for your database modules will have to be manually set.');
                }
                if (!$studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                      notice('Default student role was not found. Roles and permissions '.
                             'for all your database modules will have to be manually set.');
                }
                if (!$guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
                      notice('Default guest role was not found. Roles and permissions '.
                             'for all your database modules will have to be manually set.');
                }
                require_once($CFG->dirroot.'/mod/data/lib.php');
                data_convert_to_roles($database, $teacherroles, $studentroles,
                                      $restore->mods['data']->instances[$mod->id]->restored_as_course_module);
            }
            
        } else {
            $status = false;
        }
    } else {
        $status = false;
    }
    
    return $status;
}

function data_fields_restore_mods ($old_data_id, $new_data_id, $info, $restore) {

    global $CFG, $fieldids;
    
    
    $fields = $info['MOD']['#']['FIELDS']['0']['#']['FIELD'];

    for ($i = 0; $i < sizeof($fields); $i++) {
        
        $fie_info = $fields[$i];
        $oldid = backup_todb($fie_info['#']['ID']['0']['#']);
        
        $field -> dataid = $new_data_id;
        $field -> type = backup_todb($fie_info['#']['TYPE']['0']['#']);
        $field -> name = backup_todb($fie_info['#']['NAME']['0']['#']);
        $field -> description = backup_todb($fie_info['#']['DESCRIPTION']['0']['#']);
        $field -> param1 = backup_todb($fie_info['#']['PARAM1']['0']['#']);
        $field -> param2 = backup_todb($fie_info['#']['PARAM2']['0']['#']);
        $field -> param3 = backup_todb($fie_info['#']['PARAM3']['0']['#']);
        $field -> param4 = backup_todb($fie_info['#']['PARAM4']['0']['#']);
        $field -> param5 = backup_todb($fie_info['#']['PARAM5']['0']['#']);
        $field -> param6 = backup_todb($fie_info['#']['PARAM6']['0']['#']);
        $field -> param7 = backup_todb($fie_info['#']['PARAM7']['0']['#']);
        $field -> param8 = backup_todb($fie_info['#']['PARAM8']['0']['#']);
        $field -> param9 = backup_todb($fie_info['#']['PARAM9']['0']['#']);
        $field -> param10 = backup_todb($fie_info['#']['PARAM10']['0']['#']);
        
        $newid = insert_record ("data_fields",$field);
        
        $fieldids[$oldid] = $newid;    //so we can use them in sub tables that depends on both fieldid and recordid

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
            $status = backup_putid($restore->backup_unique_code,"data_fields",$oldid, $newid);
        } else {
            $status = false;
        }
        
    }
    return $status;

}

function data_records_restore_mods ($old_data_id, $new_data_id, $info, $restore) {

    global $CFG, $fieldids;

    $status = true;

    $records = $info['MOD']['#']['RECORDS']['0']['#']['RECORD'];

    for ($i = 0; $i < sizeof($records); $i++) {

        $rec_info = $records[$i];
        $oldid = backup_todb($rec_info['#']['ID']['0']['#']);

        $record = new object();
        $record -> dataid = $new_data_id;
        $record -> userid = backup_todb($rec_info['#']['USERID']['0']['#']);
        $record -> groupid = backup_todb($rec_info['#']['GROUPID']['0']['#']);
        $record -> timecreated = backup_todb($rec_info['#']['TIMECREATED']['0']['#']);
        $record -> timemodified = backup_todb($rec_info['#']['TIMEMODIFIED']['0']['#']);
        $record -> approved = backup_todb($rec_info['#']['APPROVED']['0']['#']);
        $user = backup_getid($restore->backup_unique_code,"user",$record->userid);
        $group= backup_getid($restore->backup_unique_code,"groups",$record->groupid);

        if ($user) {
            $record->userid = $user->new_id;
        }
        if ($group) {
            $record->groupid= $group->new_id;
        }

        $newid = insert_record ("data_records",$record);

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
            $status = $status and backup_putid($restore->backup_unique_code,"data_records",$oldid, $newid);

            $status = $status and data_content_restore_mods ($oldid, $newid, $old_data_id, $new_data_id, $rec_info, $restore);
            $status = $status and data_ratings_restore_mods ($oldid, $newid, $info, $rec_info);
            $status = $status and data_comments_restore_mods ($oldid, $newid, $info, $rec_info);

        } else {
            $status = false;
        }
    }
    return $status;
}

function data_content_restore_mods ($old_record_id, $new_record_id, $old_data_id, $new_data_id, $recinfo, $restore) {

    global $CFG, $fieldids;

    $status = true;

    $contents = $recinfo['#']['CONTENTS']['0']['#']['CONTENT'];

    for ($i = 0; $i < sizeof($contents); $i++) {

        $con_info = $contents[$i];
        $oldid = backup_todb($con_info['#']['ID']['0']['#']);
        $oldfieldid = backup_todb($con_info['#']['FIELDID']['0']['#']);
        $oldrecordid = backup_todb($con_info['#']['RECORDID']['0']['#']);

        $content -> recordid = $new_record_id;
        $content -> fieldid = $fieldids[$oldfieldid];
        $content -> content = backup_todb($con_info['#']['CONTENT']['0']['#']);
        $content -> content1 = backup_todb($con_info['#']['CONTENT1']['0']['#']);
        $content -> content2 = backup_todb($con_info['#']['CONTENT2']['0']['#']);
        $content -> content3 = backup_todb($con_info['#']['CONTENT3']['0']['#']);
        $content -> content4 = backup_todb($con_info['#']['CONTENT4']['0']['#']);
        $newid = insert_record ("data_content",$content);

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

            $status = $status and data_restore_files ($old_data_id, $new_data_id, $oldfieldid, $content->fieldid, $oldrecordid, $content->recordid, $recinfo, $restore);
            $status = $status and backup_putid($restore->backup_unique_code,"data_content",$oldid, $newid);
        } else {
            $status = false;
        }
    }
    return $status;
}


function data_restore_files ($old_data_id, $new_data_id, $old_field_id, $new_field_id, $old_record_id, $new_record_id, $recinfo, $restore) {

    global $CFG, $db;

    $status = true;
    $todo = false;
    $moddata_path = "";
    $data_path = "";
    $temp_path = "";

    //First, we check to "course_id" exists and create is as necessary
    //in CFG->dataroot
    $dest_dir = $CFG->dataroot."/".$restore->course_id;
    $status = check_dir_exists($dest_dir,true);
    
    //Now, locate course's moddata directory
    $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;

    //Check it exists and create it
    $status = check_dir_exists($moddata_path,true);

    //Now, locate data directory
    if ($status) {
        $data_path = $moddata_path."/data";
        //Check it exists and create it
        $status = check_dir_exists($data_path,true);
    }

    //Now locate the temp dir we are gong to restore
    if ($status) {
        $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                    "/moddata/data/".$old_data_id."/".$old_field_id."/".$old_record_id;
        $todo = check_dir_exists($temp_path);
    }

    //If todo, we create the neccesary dirs in course moddata/data
    if ($status and $todo) {
        //First this data id
        $this_data_path = $data_path."/".$new_data_id;

        $status = check_dir_exists($this_data_path,true);
        //Now this user id
        $this_field_path = $this_data_path."/".$new_field_id;
        $status = check_dir_exists($this_field_path,true);
        $this_record_path = $this_field_path = $this_field_path."/".$new_record_id;
        $status = check_dir_exists($this_record_path,true);
        //And now, copy temp_path to user_data_path
        
        $status = @backup_copy_file($temp_path, $this_record_path);
    }

    return $status;
}

function data_ratings_restore_mods ($oldid, $newid, $info, $rec_info) {

    global $CFG;

    $status = true;

    $ratings= $rec_info['#']['RATINGS']['0']['#']['RATING'];

    if (empty($ratings)) { // no ratings to restore
        return true;
    }
    for ($i = 0; $i < sizeof($ratings); $i++) {

        $rat_info = $ratings[$i];

        $rating -> recordid = $newid;
        $rating -> userid = backup_todb($rat_info['#']['USERID']['0']['#']);
        $rating -> rating = backup_todb($rat_info['#']['RATING']['0']['#']);

        if (! insert_record ("data_ratings",$rating)) {
            $status = false;
        }
    }
    return $status;
}

function data_comments_restore_mods ($oldid, $newid, $info, $rec_info) {

    global $CFG;

    $status = true;

    $comments= $rec_info['#']['COMMENTS']['0']['#']['COMMENT'];

    if (empty($comments)) { // no comments to restore
        return true;
    }

    for ($i = 0; $i < sizeof($comments); $i++) {

        $com_info = $comments[$i];

        $comment -> recordid = $newid;
        $comment -> userid = backup_todb($com_info['#']['USERID']['0']['#']);
        $comment -> content = backup_todb($com_info['#']['CONTENT']['0']['#']);
        $comment -> created = backup_todb($com_info['#']['CREATED']['0']['#']);
        $comment -> modified = backup_todb($com_info['#']['MODIFIED']['0']['#']);
        if (! insert_record ("data_comments",$comment)) {
            $status = false;
        }

    }
    return $status;

}
?>
