<?php // $Id
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
        $database->asearchtemplate = backup_todb($info['MOD']['#']['ASEARCHTEMPLATE']['0']['#']);
        $database->approval = backup_todb($info['MOD']['#']['APPROVAL']['0']['#']);
        $database->scale = backup_todb($info['MOD']['#']['SCALE']['0']['#']);
        $database->assessed = backup_todb($info['MOD']['#']['ASSESSED']['0']['#']);
        // Only relevant for restoring backups from 1.6 in a 1.7 install.
        if (isset($info['MOD']['#']['ASSESSPUBLIC']['0']['#'])) {
            $database->assesspublic = backup_todb($info['MOD']['#']['ASSESSPUBLIC']['0']['#']);
        }
        $database->defaultsort = backup_todb($info['MOD']['#']['DEFAULTSORT']['0']['#']);
        $database->defaultsortdir = backup_todb($info['MOD']['#']['DEFAULTSORTDIR']['0']['#']);
        $database->editany = backup_todb($info['MOD']['#']['EDITANY']['0']['#']);
        $database->notification = backup_todb($info['MOD']['#']['NOTIFICATION']['0']['#']);

        // We have to recode the scale field if it's <0 (positive is a grade, not a scale)
        if ($database->scale < 0) {
            $scale = backup_getid($restore->backup_unique_code, 'scale', abs($database->scale));
            if ($scale) {
                $database->scale = -($scale->new_id);
            }
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

            global $fieldids;
            //Restore data_fields first!!! need to hold an array of [oldid]=>newid due to double dependencies
            $status = $status and data_fields_restore_mods ($mod->id, $newid, $info, $restore);

            // now use the new field in the defaultsort
            $newdefaultsort = empty($fieldids[$database->defaultsort]) ? 0 : $fieldids[$database->defaultsort];
            set_field('data', 'defaultsort', $newdefaultsort, 'id', $newid);

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

    $records = isset($info['MOD']['#']['RECORDS']['0']['#']['RECORD']) ? $info['MOD']['#']['RECORDS']['0']['#']['RECORD'] : array();

    if (empty($records)) { // no records to restore
        return true;
    }

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
        $group= restore_group_getid($restore, $record->groupid);

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

    $ratings= isset($rec_info['#']['RATINGS']['0']['#']['RATING']) ? $rec_info['#']['RATINGS']['0']['#']['RATING'] : array();

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

    $comments= isset($rec_info['#']['COMMENTS']['0']['#']['COMMENT']) ? $rec_info['#']['COMMENTS']['0']['#']['COMMENT'] : array();

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

/**
 * Returns a content decoded to support interactivities linking. Every module
 * should have its own. They are called automatically from
 * xxxx_decode_content_links_caller() function in each module
 * in the restore process.
 *
 * @param string $content the content to be decoded
 * @param object $restore the preferences used in restore
 * @return string the decoded string
 */
function data_decode_content_links ($content,$restore) {

    global $CFG;

    $result = $content;

/// Link to the list of datas

    $searchstring='/\$@(DATAINDEX)\*([0-9]+)@\$/';
/// We look for it
    preg_match_all($searchstring,$content,$foundset);
/// If found, then we are going to look for its new id (in backup tables)
    if ($foundset[0]) {
    /// print_object($foundset);                                     //Debug
    /// Iterate over foundset[2]. They are the old_ids
        foreach($foundset[2] as $old_id) {
        /// We get the needed variables here (course id)
            $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
        /// Personalize the searchstring
            $searchstring='/\$@(DATAINDEX)\*('.$old_id.')@\$/';
        /// If it is a link to this course, update the link to its new location
            if($rec->new_id) {
            /// Now replace it
                $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/data/index.php?id='.$rec->new_id,$result);
            } else {
            /// It's a foreign link so leave it as original
                $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/data/index.php?id='.$old_id,$result);
            }
        }
    }

/// Link to data view by moduleid

    $searchstring='/\$@(DATAVIEWBYID)\*([0-9]+)@\$/';
/// We look for it
    preg_match_all($searchstring,$result,$foundset);
/// If found, then we are going to look for its new id (in backup tables)
    if ($foundset[0]) {
    /// print_object($foundset);                                     //Debug
    /// Iterate over foundset[2]. They are the old_ids
        foreach($foundset[2] as $old_id) {
        /// We get the needed variables here (course_modules id)
            $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
        /// Personalize the searchstring
            $searchstring='/\$@(DATAVIEWBYID)\*('.$old_id.')@\$/';
        /// If it is a link to this course, update the link to its new location
            if($rec->new_id) {
            /// Now replace it
                $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/data/view.php?id='.$rec->new_id,$result);
            } else {
            /// It's a foreign link so leave it as original
                $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/data/view.php?id='.$old_id,$result);
            }
        }
    }

/// Link to data view by dataid

    $searchstring='/\$@(DATAVIEWBYD)\*([0-9]+)@\$/';
/// We look for it
    preg_match_all($searchstring,$result,$foundset);
/// If found, then we are going to look for its new id (in backup tables)
    if ($foundset[0]) {
    /// print_object($foundset);                                     //Debug
    /// Iterate over foundset[2]. They are the old_ids
        foreach($foundset[2] as $old_id) {
        /// We get the needed variables here (data id)
            $rec = backup_getid($restore->backup_unique_code,"data",$old_id);
        /// Personalize the searchstring
            $searchstring='/\$@(DATAVIEWBYD)\*('.$old_id.')@\$/';
        /// If it is a link to this course, update the link to its new location
            if($rec->new_id) {
            /// Now replace it
                $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/data/view.php?d='.$rec->new_id,$result);
            } else {
            /// It's a foreign link so leave it as original
                $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/data/view.php?d='.$old_id,$result);
            }
        }
    }

/// Link to data record (element)

    $searchstring='/\$@(DATAVIEWRECORD)\*([0-9]+)\*([0-9]+)@\$/';
/// We look for it
    preg_match_all($searchstring,$result,$foundset);
/// If found, then we are going to look for its new id (in backup tables)
    if ($foundset[0]) {
    /// print_object($foundset);                                     //Debug
    /// Iterate over foundset[2] and foundset[3]. They are the old_ids
        foreach($foundset[2] as $key => $old_id) {
            $old_id2 = $foundset[3][$key];
        /// We get the needed variables here (data id and record id)
            $rec = backup_getid($restore->backup_unique_code,"data",$old_id);
            $rec2 = backup_getid($restore->backup_unique_code,"data_records",$old_id2);
        /// Personalize the searchstring
            $searchstring='/\$@(DATAVIEWRECORD)\*('.$old_id.')\*('.$old_id2.')@\$/';
        /// If it is a link to this course, update the link to its new location
            if($rec->new_id && $rec2->new_id) {
            /// Now replace it
                $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/data/view.php?d='.$rec->new_id.'&amp;rid='.$rec2->new_id,$result);
            } else {
            /// It's a foreign link so leave it as original
                $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/data/view.php?d='.$old_id.'&amp;rid='.$old_id2,$result);
            }
        }
    }

    return $result;
}

/**
 * This function makes all the necessary calls to xxxx_decode_content_links()
 * function in each module, passing them the desired contents to be decoded
 * from backup format to destination site/course in order to mantain inter-activities
 * working in the backup/restore process. It's called from restore_decode_content_links()
 * function in restore process
 *
 * @param object $restore the preferences used in restore
 * @return boolean status of the execution
 */
function data_decode_content_links_caller($restore) {

    global $CFG;
    $status = true;

/// Process every DATA (intro, all HTML templates) in the course
/// Supported fields for main table:
    $supportedfields = array('intro','singletemplate','listtemplate',
        'listtemplateheader','addtemplate','rsstemplate','rsstitletemplate');
    if ($datas = get_records_sql ("SELECT d.id, ".implode(',',$supportedfields)."
                                  FROM {$CFG->prefix}data d
                                  WHERE d.course = $restore->course_id")) {
    /// Iterate over each data
        $i = 0;   //Counter to send some output to the browser to avoid timeouts
        foreach ($datas as $data) {
        /// Increment counter
            $i++;

        /// Make a new copy of the data object with nothing in, to use if
        /// changes are necessary (allows us to do update_record without
        /// worrying about every single field being included and needing
        /// slashes).
            $newdata = new stdClass;
            $newdata->id=$data->id;

        /// Loop through handling each supported field
            $changed = false;
            foreach($supportedfields as $field) {
                $result = restore_decode_content_links_worker($data->{$field},$restore);
                if ($result != $data->{$field}) {
                    $newdata->{$field} = addslashes($result);
                    $changed = true;
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($data->{$field}).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
            }

        /// Update record if any field changed
            if($changed) {
                $status = update_record("data",$newdata);
            }

        /// Do some output
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

/// Process every COMMENT (content) in the course
    if ($comments = get_records_sql ("SELECT dc.id, dc.content
                                      FROM {$CFG->prefix}data d,
                                           {$CFG->prefix}data_records dr,
                                           {$CFG->prefix}data_comments dc
                                      WHERE d.course = $restore->course_id
                                        AND dr.dataid = d.id
                                        AND dc.recordid = dr.id")) {
    /// Iterate over each data_comments->content
        $i = 0;   //Counter to send some output to the browser to avoid timeouts
        foreach ($comments as $comment) {
        /// Increment counter
            $i++;
            $content = $comment->content;
            $result = restore_decode_content_links_worker($content,$restore);
            if ($result != $content) {
            /// Update record
                $comment->content = addslashes($result);
                $status = update_record("data_comments",$comment);
                if (debugging()) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                    }
                }
            }
        /// Do some output
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

/// Process every CONTENT (content, content1, content2, content3, content4) in the course
    if ($contents = get_records_sql ("SELECT dc.id, dc.content, dc.content1, dc.content2, dc.content3, dc.content4
                                      FROM {$CFG->prefix}data d,
                                           {$CFG->prefix}data_records dr,
                                           {$CFG->prefix}data_content dc
                                      WHERE d.course = $restore->course_id
                                        AND dr.dataid = d.id
                                        AND dc.recordid = dr.id")) {
    /// Iterate over each data_content->content, content1, content2, content3 and content4
        $i = 0;   //Counter to send some output to the browser to avoid timeouts
        foreach ($contents as $cnt) {
        /// Increment counter
            $i++;
            $content = $cnt->content;
            $content1 = $cnt->content1;
            $content2 = $cnt->content2;
            $content3 = $cnt->content3;
            $content4 = $cnt->content4;
            $result = restore_decode_content_links_worker($content,$restore);
            $result1 = restore_decode_content_links_worker($content1,$restore);
            $result2 = restore_decode_content_links_worker($content2,$restore);
            $result3 = restore_decode_content_links_worker($content3,$restore);
            $result4 = restore_decode_content_links_worker($content4,$restore);
            if ($result != $content || $result1 != $content1 || $result2 != $content2 ||
                $result3 != $content3 || $result4 != $content4) {
            /// Unset fields to update only the necessary ones
                unset($cnt->content);
                unset($cnt->content1);
                unset($cnt->content2);
                unset($cnt->content3);
                unset($cnt->content4);
            /// Conditionally set the fields
                if ($result != $content) {
                    $cnt->content = addslashes($result);
                }
                if ($result1 != $content1) {
                    $cnt->content1 = addslashes($result1);
                }
                if ($result2 != $content2) {
                    $cnt->content2 = addslashes($result2);
                }
                if ($result3 != $content3) {
                    $cnt->content3 = addslashes($result3);
                }
                if ($result4 != $content4) {
                    $cnt->content4 = addslashes($result4);
                }
            /// Update record with the changed fields
                $status = update_record("data_content",$cnt);
                if (debugging()) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                    }
                }
            }
        /// Do some output
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

?>
