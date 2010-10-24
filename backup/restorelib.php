<?php
    //This function iterates over all modules in backup file, searching for a
    //MODNAME_refresh_events() to execute. Perhaps it should ve moved to central Moodle...
    function restore_refresh_events($restore) {

        global $CFG;
        $status = true;

        //Take all modules in backup
        $modules = $restore->mods;
        //Iterate
        foreach($modules as $name => $module) {
            //Only if the module is being restored
            if (isset($module->restore) && $module->restore == 1) {
                //Include module library
                include_once("$CFG->dirroot/mod/$name/lib.php");
                //If module_refresh_events exists
                $function_name = $name."_refresh_events";
                if (function_exists($function_name)) {
                    $status = $function_name($restore->course_id);
                }
            }
        }
        return $status;
    }

    //Called to set up any course-format specific data that may be in the file
    function restore_set_format_data($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Load data from XML to info
        if(!($info = restore_read_xml_formatdata($xml_file))) {
                return false;
        }

        //Process format data if there is any
        if (isset($info->format_data)) {
                if(!$format=$DB->get_field('course','format', array('id'=>$restore->course_id))) {
                    return false;
                }
                // If there was any data then it must have a restore method
                $file=$CFG->dirroot."/course/format/$format/restorelib.php";
                if(!file_exists($file)) {
                    return false;
                }
                require_once($file);
                $function=$format.'_restore_format_data';
                if(!function_exists($function)) {
                    return false;
                }
                return $function($restore,$info->format_data);
        }

        // If we got here then there's no data, but that's cool
        return true;
    }

    //This function creates all the structures messages and contacts
    function restore_create_messages($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the id and name of every table
            //(message, message_read and message_contacts)
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_messages($restore,$xml_file);

            //If we have info, then process messages & contacts
            if ($info > 0) {
                //Count how many we have
                $unreadcount  = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message'));
                $readcount    = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message_read'));
                $contactcount = $DB->count_records ('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'message_contacts'));
                if ($unreadcount || $readcount || $contactcount) {
                    //Start ul
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<ul>';
                    }
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process unread
                    if ($unreadcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('unreadmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $unreadcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE record structure
                                        $dbrec = new stdClass();
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message', array('useridfrom'=>$dbrec->useridfrom,
                                                                                  'useridto'=>$dbrec->useridto,
                                                                                  'timecreated'=>$dbrec->timecreated));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process read
                    if ($readcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('readmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $readcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message_read', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_read",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_READ record structure
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        $dbrec->timeread = backup_todb($info['MESSAGE']['#']['TIMEREAD']['0']['#']);
                                        $dbrec->mailed = backup_todb($info['MESSAGE']['#']['MAILED']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message_read', array('useridfrom'=>$dbrec->useridfrom,
                                                                                       'useridto'=>$dbrec->useridto,
                                                                                       'timecreated'=>$dbrec->timecreated));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message_read',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process contacts
                    if ($contactcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.moodle_strtolower(get_string('contacts','message')).'</li>';
                        }
                        $counter = 0;
                        while ($counter < $contactcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array('table_name'=>'message_contacts', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_contacts",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_CONTACTS record structure
                                        $dbrec->userid = backup_todb($info['CONTACT']['#']['USERID']['0']['#']);
                                        $dbrec->contactid = backup_todb($info['CONTACT']['#']['CONTACTID']['0']['#']);
                                        $dbrec->blocked = backup_todb($info['CONTACT']['#']['BLOCKED']['0']['#']);
                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }
                                        //We have to recode the contactid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->contactid);
                                        if ($user) {
                                            //echo "User ".$dbrec->contactid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->contactid = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('message_contacts', array('userid'=>$dbrec->userid,
                                                                                           'contactid'=>$dbrec->contactid));
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = $DB->insert_record('message_contacts',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                    if (!defined('RESTORE_SILENTLY')) {
                        //End ul
                        echo '</ul>';
                    }
                }
            }
        }

       return $status;
    }

    //This function creates all the structures for blogs and blog tags
    function restore_create_blogs($restore,$xml_file) {
        global $CFG, $DB;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the number of blogs in the backup file
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_blogs($restore,$xml_file);

            //If we have info, then process blogs & blog_tags
            if ($info > 0) {
                //Count how many we have
                $blogcount = $DB->count_records('backup_ids', array('backup_code'=>$restore->backup_unique_code, 'table_name'=>'blog'));
                if ($blogcount) {
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process blog
                    if ($blogcount) {
                        $counter = 0;
                        while ($counter < $blogcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = $DB->get_records("backup_ids", array("table_name"=>'blog', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"blog",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the BLOG record structure
                                        $dbrec = new stdClass();
                                        $dbrec->module = backup_todb($info['BLOG']['#']['MODULE']['0']['#']);
                                        $dbrec->userid = backup_todb($info['BLOG']['#']['USERID']['0']['#']);
                                        $dbrec->courseid = backup_todb($info['BLOG']['#']['COURSEID']['0']['#']);
                                        $dbrec->groupid = backup_todb($info['BLOG']['#']['GROUPID']['0']['#']);
                                        $dbrec->moduleid = backup_todb($info['BLOG']['#']['MODULEID']['0']['#']);
                                        $dbrec->coursemoduleid = backup_todb($info['BLOG']['#']['COURSEMODULEID']['0']['#']);
                                        $dbrec->subject = backup_todb($info['BLOG']['#']['SUBJECT']['0']['#']);
                                        $dbrec->summary = backup_todb($info['BLOG']['#']['SUMMARY']['0']['#']);
                                        $dbrec->content = backup_todb($info['BLOG']['#']['CONTENT']['0']['#']);
                                        $dbrec->uniquehash = backup_todb($info['BLOG']['#']['UNIQUEHASH']['0']['#']);
                                        $dbrec->rating = backup_todb($info['BLOG']['#']['RATING']['0']['#']);
                                        $dbrec->format = backup_todb($info['BLOG']['#']['FORMAT']['0']['#']);
                                        $dbrec->attachment = backup_todb($info['BLOG']['#']['ATTACHMENT']['0']['#']);
                                        $dbrec->publishstate = backup_todb($info['BLOG']['#']['PUBLISHSTATE']['0']['#']);
                                        $dbrec->lastmodified = backup_todb($info['BLOG']['#']['LASTMODIFIED']['0']['#']);
                                        $dbrec->created = backup_todb($info['BLOG']['#']['CREATED']['0']['#']);
                                        $dbrec->usermodified = backup_todb($info['BLOG']['#']['USERMODIFIED']['0']['#']);

                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }

                                        //Check if the record doesn't exist in DB!
                                        $exist = $DB->get_record('post', array('userid'=>$dbrec->userid,
                                                                               'subject'=>$dbrec->subject,
                                                                               'created'=>$dbrec->created));
                                        $newblogid = 0;
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $newblogid = $DB->insert_record('post',$dbrec);
                                        }

                                        //Going to restore related tags. Check they are enabled and we have inserted a blog
                                        if ($CFG->usetags && $newblogid) {
                                            //Look for tags in this blog
                                            if (isset($info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'])) {
                                                $tagsarr = $info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'];
                                                //Iterate over tags
                                                $tags = array();
                                                for($i = 0; $i < sizeof($tagsarr); $i++) {
                                                    $tag_info = $tagsarr[$i];
                                                    ///traverse_xmlize($tag_info);                        //Debug
                                                    ///print_object ($GLOBALS['traverse_array']);         //Debug
                                                    ///$GLOBALS['traverse_array']="";                     //Debug

                                                    $name = backup_todb($tag_info['#']['NAME']['0']['#']);
                                                    $rawname = backup_todb($tag_info['#']['RAWNAME']['0']['#']);

                                                    $tags[] = $rawname;  //Rawname is all we need
                                                }
                                                tag_set('post', $newblogid, $tags); //Add all the tags in one API call
                                            }
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    //This function creates all the categories and questions
    //from xml
    function restore_create_questions($restore,$xml_file) {
        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the old_id of every category
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_questions($restore,$xml_file);
        }
        //Now, if we have anything in info, we have to restore that
        //categories/questions
        if ($info) {
            if ($info !== true) {
                $status = $status &&  restore_question_categories($info, $restore);
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function creates all the course events
    function restore_create_events($restore,$xml_file) {
        global $DB;

        global $CFG, $SESSION;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //events will contain the old_id of every event
            //in backup_ids->info will be the real info (serialized)
            $events = restore_read_xml_events($restore,$xml_file);
        }

        //Get admin->id for later use
        $admin = get_admin();
        $adminid = $admin->id;

        //Now, if we have anything in events, we have to restore that
        //events
        if ($events) {
            if ($events !== true) {
                //Iterate over each event
                foreach ($events as $event) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"event",$event->id);
                    //Init variables
                    $create_event = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug

                        //if necessary, write to restorelog and adjust date/time fields
                        if ($restore->course_startdateoffset) {
                            restore_log_date_changes('Events', $restore, $info['EVENT']['#'], array('TIMESTART'));
                        }

                        //Now build the EVENT record structure
                        $eve->name = backup_todb($info['EVENT']['#']['NAME']['0']['#']);
                        $eve->description = backup_todb($info['EVENT']['#']['DESCRIPTION']['0']['#']);
                        $eve->format = backup_todb($info['EVENT']['#']['FORMAT']['0']['#']);
                        $eve->courseid = $restore->course_id;
                        $eve->groupid = backup_todb($info['EVENT']['#']['GROUPID']['0']['#']);
                        $eve->userid = backup_todb($info['EVENT']['#']['USERID']['0']['#']);
                        $eve->repeatid = backup_todb($info['EVENT']['#']['REPEATID']['0']['#']);
                        $eve->modulename = "";
                        if (!empty($info['EVENT']['#']['MODULENAME'])) {
                            $eve->modulename = backup_todb($info['EVENT']['#']['MODULENAME']['0']['#']);
                        }
                        $eve->instance = 0;
                        $eve->eventtype = backup_todb($info['EVENT']['#']['EVENTTYPE']['0']['#']);
                        $eve->timestart = backup_todb($info['EVENT']['#']['TIMESTART']['0']['#']);
                        $eve->timeduration = backup_todb($info['EVENT']['#']['TIMEDURATION']['0']['#']);
                        $eve->visible = backup_todb($info['EVENT']['#']['VISIBLE']['0']['#']);
                        $eve->timemodified = backup_todb($info['EVENT']['#']['TIMEMODIFIED']['0']['#']);

                        //Now search if that event exists (by name, description, timestart fields) in
                        //restore->course_id course
                        //Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides.
                        $compare_description_clause = $DB->sql_compare_text('description')  . "=" .  $DB->sql_compare_text("'" . $eve->description . "'");
                        $eve_db = $DB->get_record_select('event',
                            "courseid = ? AND name = ? AND $compare_description_clause AND timestart = ?",
                            array($eve->courseid, $eve->name, $eve->timestart));
                        //If it doesn't exist, create
                        if (!$eve_db) {
                            $create_event = true;
                        }
                        //If we must create the event
                        if ($create_event) {

                            //We must recode the userid
                            $user = backup_getid($restore->backup_unique_code,"user",$eve->userid);
                            if ($user) {
                                $eve->userid = $user->new_id;
                            } else {
                                //Assign it to admin
                                $eve->userid = $adminid;
                            }

                            //We have to recode the groupid field
                            $group = backup_getid($restore->backup_unique_code,"groups",$eve->groupid);
                            if ($group) {
                                $eve->groupid = $group->new_id;
                            } else {
                                //Assign it to group 0
                                $eve->groupid = 0;
                            }

                            //The structure is equal to the db, so insert the event
                            $newid = $DB->insert_record ("event",$eve);

                            //We must recode the repeatid if the event has it
                            //The repeatid now refers to the id of the original event. (see Bug#5956)
                            if ($newid && !empty($eve->repeatid)) {
                                $repeat_rec = backup_getid($restore->backup_unique_code,"event_repeatid",$eve->repeatid);
                                if ($repeat_rec) {    //Exists, so use it...
                                    $eve->repeatid = $repeat_rec->new_id;
                                } else {              //Doesn't exists, calculate the next and save it
                                    $oldrepeatid = $eve->repeatid;
                                    $eve->repeatid = $newid;
                                    backup_putid($restore->backup_unique_code,"event_repeatid", $oldrepeatid, $eve->repeatid);
                                }
                                $eve->id = $newid;
                                // update the record to contain the correct repeatid
                                $DB->update_record('event',$eve);
                            }
                        } else {
                            //get current event id
                            $newid = $eve_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"event",
                                         $event->id, $newid);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function creates all the structures for every log in backup file
    //Depending what has been selected.
    function restore_create_logs($restore,$xml_file) {
        global $CFG, $DB;

        //Number of records to get in every chunk
        $recordset_size = 4;
        //Counter, points to current record
        $counter = 0;
        //To count all the recods to restore
        $count_logs = 0;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //count_logs will contain the number of logs entries to process
            //in backup_ids->info will be the real info (serialized)
            $count_logs = restore_read_xml_logs($restore,$xml_file);
        }

        //Now, if we have records in count_logs, we have to restore that logs
        //from backup_ids. This piece of code makes calls to:
        // - restore_log_course() if it's a course log
        // - restore_log_user() if it's a user log
        // - restore_log_module() if it's a module log.
        //And all is segmented in chunks to allow large recordsets to be restored !!
        if ($count_logs > 0) {
            while ($counter < $count_logs) {
                //Get a chunk of records
                //Take old_id twice to avoid adodb limitation
                $logs = $DB->get_records("backup_ids", array("table_name"=>'log', 'backup_code'=>$restore->backup_unique_code),"old_id","old_id",$counter,$recordset_size);
                //We have logs
                if ($logs) {
                    //Iterate
                    foreach ($logs as $log) {
                        //Get the full record from backup_ids
                        $data = backup_getid($restore->backup_unique_code,"log",$log->old_id);
                        if ($data) {
                            //Now get completed xmlized object
                            $info = $data->info;
                            //traverse_xmlize($info);                                                                     //Debug
                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                            //$GLOBALS['traverse_array']="";                                                              //Debug
                            //Now build the LOG record structure
                            $dblog = new stdClass();
                            $dblog->time = backup_todb($info['LOG']['#']['TIME']['0']['#']);
                            $dblog->userid = backup_todb($info['LOG']['#']['USERID']['0']['#']);
                            $dblog->ip = backup_todb($info['LOG']['#']['IP']['0']['#']);
                            $dblog->course = $restore->course_id;
                            $dblog->module = backup_todb($info['LOG']['#']['MODULE']['0']['#']);
                            $dblog->cmid = backup_todb($info['LOG']['#']['CMID']['0']['#']);
                            $dblog->action = backup_todb($info['LOG']['#']['ACTION']['0']['#']);
                            $dblog->url = backup_todb($info['LOG']['#']['URL']['0']['#']);
                            $dblog->info = backup_todb($info['LOG']['#']['INFO']['0']['#']);
                            //We have to recode the userid field
                            $user = backup_getid($restore->backup_unique_code,"user",$dblog->userid);
                            if ($user) {
                                //echo "User ".$dblog->userid." to user ".$user->new_id."<br />";                             //Debug
                                $dblog->userid = $user->new_id;
                            }
                            //We have to recode the cmid field (if module isn't "course" or "user")
                            if ($dblog->module != "course" and $dblog->module != "user") {
                                $cm = backup_getid($restore->backup_unique_code,"course_modules",$dblog->cmid);
                                if ($cm) {
                                    //echo "Module ".$dblog->cmid." to module ".$cm->new_id."<br />";                         //Debug
                                    $dblog->cmid = $cm->new_id;
                                } else {
                                    $dblog->cmid = 0;
                                }
                            }
                            //print_object ($dblog);                                                                        //Debug
                            //Now, we redirect to the needed function to make all the work
                            if ($dblog->module == "course") {
                                //It's a course log,
                                $stat = restore_log_course($restore,$dblog);
                            } elseif ($dblog->module == "user") {
                                //It's a user log,
                                $stat = restore_log_user($restore,$dblog);
                            } else {
                                //It's a module log,
                                $stat = restore_log_module($restore,$dblog);
                            }
                        }

                        //Do some output
                        $counter++;
                        if ($counter % 10 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 200 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                } else {
                    //We never should arrive here
                    $counter = $count_logs;
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function inserts a course log record, calculating the URL field as necessary
    function restore_log_course($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            $log->url = "view.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "guest":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "user report":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                //Now, extract the mode from the url field
                $mode = substr(strrchr($log->url,"="),1);
                $log->url = "user.php?id=".$log->course."&user=".$log->info."&mode=".$mode;
                $toinsert = true;
            }
            break;
        case "add mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "update mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "delete mod":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "update":
            $log->url = "edit.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "unenrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "enrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "editsection":
            //Extract the course_section from the url field
            $secid = substr(strrchr($log->url,"="),1);
            //recode the course_section to see if it has been restored
            $sec = backup_getid($restore->backup_unique_code,"course_sections",$secid);
            if ($sec) {
                $secid = $sec->new_id;
                //Now I have everything so reconstruct url and info
                $log->url = "editsection.php?id=".$secid;
                $toinsert = true;
            }
            break;
        case "new":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "recent":
            $log->url = "recent.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "report log":
            $log->url = "report/log/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report live":
            $log->url = "report/log/live.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report outline":
            $log->url = "report/outline/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report participation":
            $log->url = "report/participation/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report stats":
            $log->url = "report/stats/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a user log record, calculating the URL field as necessary
    function restore_log_user($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "change password":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "login":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "logout":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "view all":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
        case "update":
            //We split the url by ampersand char
            $first_part = strtok($log->url,"&");
            //Get data after the = char. It's the user being updated
            $userid = substr(strrchr($first_part,"="),1);
            //Recode the user
            $user = backup_getid($restore->backup_unique_code,"user",$userid);
            if ($user) {
                $log->info = "";
                $log->url = "view.php?id=".$user->new_id."&course=".$log->course;
                $toinsert = true;
            }
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a module log record, calculating the URL field as necessary
    function restore_log_module($restore,$log) {
        global $DB;

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug

        //Now we see if the required function in the module exists
        $function = $log->module."_restore_logs";
        if (function_exists($function)) {
            //Call the function
            $log = $function($restore,$log);
            //If everything is ok, mark the insert flag
            if ($log) {
                $toinsert = true;
            }
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = $DB->insert_record("log",$log);
        }
        return $status;
    }

    /**
     * @param string $errorstr passed by reference, if silent is true,
     * errorstr will be populated and this function will return false rather than calling print_error() or notify()
     * @param boolean $noredirect (optional) if this is passed, this function will not print continue, or
     * redirect to the next step in the restore process, instead will return $backup_unique_code
     */
    function restore_precheck($id,$file,&$errorstr,$noredirect=false) {

        global $CFG, $SESSION, $OUTPUT;

        //Prepend dataroot to variable to have the absolute path
        $file = $CFG->dataroot."/".$file;

        if (!defined('RESTORE_SILENTLY')) {
            //Start the main table
            echo "<table cellpadding=\"5\">";
            echo "<tr><td>";

            //Start the mail ul
            echo "<ul>";
        }

        //Check the file exists
        if (!is_file($file)) {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('nofile');
            } else {
                $errorstr = "File not exists ($file)";
                return false;
            }
        }

        //Check the file name ends with .zip
        if (!substr($file,-4) == ".zip") {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('incorrectext');
            } else {
                $errorstr = get_string('incorrectext', 'error');
                return false;
            }
        }

        //Now calculate the unique_code for this restore
        $backup_unique_code = time();

        //Now check and create the backup dir (if it doesn't exist)
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingtemporarystructures").'</li>';
        }
        $status = check_and_create_backup_dir($backup_unique_code);
        //Empty dir
        if ($status) {
            $status = clear_backup_dir($backup_unique_code);
        }

        //Now delete old data and directories under dataroot/temp/backup
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("deletingolddata").'</li>';
            }
            if (!backup_delete_old_data()) {;
                $errorstr = "An error occurred deleting old data";
                add_to_backup_log(time(),$preferences->backup_course,$errorstr,'restoreprecheck');
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification($errorstr);
                }
            }
        }

        //Now copy he zip file to dataroot/temp/backup/backup_unique_code
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingzipfile").'</li>';
            }
            if (! $status = backup_copy_file($file,$CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error copying backup file. Invalid name or bad perms.");
                } else {
                    $errorstr = "Error copying backup file. Invalid name or bad perms";
                    return false;
                }
            }
        }

        //Now unzip the file
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("unzippingbackup").'</li>';
            }
            if (! $status = restore_unzip ($CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error unzipping backup file. Invalid zip file.");
                } else {
                    $errorstr = "Error unzipping backup file. Invalid zip file.";
                    return false;
                }
            }
        }

        // If experimental option is enabled (enableimsccimport)
        // check for Common Cartridge packages and convert to Moodle format
        if ($status && isset($CFG->enableimsccimport) && $CFG->enableimsccimport == 1) {
            require_once($CFG->dirroot. '/backup/cc/restore_cc.php');
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string('checkingforimscc', 'imscc').'</li>';
            }
            $status = cc_convert($CFG->dataroot. DIRECTORY_SEPARATOR .'temp'. DIRECTORY_SEPARATOR . 'backup'. DIRECTORY_SEPARATOR . $backup_unique_code);
        }

        //Check for Blackboard backups and convert
        if ($status){
            require_once("$CFG->dirroot/backup/bb/restore_bb.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingforbbexport").'</li>';
            }
            $status = blackboard_convert($CFG->dataroot."/temp/backup/".$backup_unique_code);
        }

        //Now check for the moodle.xml file
        if ($status) {
            $xml_file  = $CFG->dataroot."/temp/backup/".$backup_unique_code."/moodle.xml";
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingbackup").'</li>';
            }
            if (! $status = restore_check_moodle_file ($xml_file)) {
                if (!is_file($xml_file)) {
                    $errorstr = 'Error checking backup file. moodle.xml not found at root level of zip file.';
                } else {
                    $errorstr = 'Error checking backup file. moodle.xml is incorrect or corrupted.';
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification($errorstr);
                } else {
                    return false;
                }
            }
        }

        $info = "";
        $course_header = "";

        //Now read the info tag (all)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("readinginfofrombackup").'</li>';
            }
            //Reading info from file
            $info = restore_read_xml_info ($xml_file);
            //Reading course_header from file
            $course_header = restore_read_xml_course_header ($xml_file);

            if(!is_object($course_header)){
                // ensure we fail if there is no course header
                $course_header = false;
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>\n";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        //We compare Moodle's versions
        if ($status && $CFG->version < $info->backup_moodle_version) {
            $message = new stdClass();
            $message->serverversion = $CFG->version;
            $message->serverrelease = $CFG->release;
            $message->backupversion = $info->backup_moodle_version;
            $message->backuprelease = $info->backup_moodle_release;
            echo $OUTPUT->box(get_string('noticenewerbackup','',$message), "noticebox");

        }

        //Now we print in other table, the backup and the course it contains info
        if ($info and $course_header and $status) {
            //First, the course info
            if (!defined('RESTORE_SILENTLY')) {
                $status = restore_print_course_header($course_header);
            }
            //Now, the backup info
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    $status = restore_print_info($info);
                }
            }
        }

        //Save course header and info into php session
        if ($status) {
            $SESSION->info = $info;
            $SESSION->course_header = $course_header;
        }

        //Finally, a little form to continue
        //with some hidden fields
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<br /><div style='text-align:center'>";
                $hidden["backup_unique_code"] = $backup_unique_code;
                $hidden["launch"]             = "form";
                $hidden["file"]               =  $file;
                $hidden["id"]                 =  $id;
                echo $OUTPUT->single_button(new moodle_url("restore.php", $hidden), get_string("continue"));
                echo "</div>";
            }
            else {
                if (empty($noredirect)) {
                    echo $OUTPUT->continue_button(new moodle_url('/backup/restore.php', array('backup_unique_code'=>$backup_unique_code, 'launch'=>'form', 'file'=>$file, 'id'=>$id, 'sesskey'=>sesskey())));
                    echo $OUTPUT->footer();
                    die;

                } else {
                    return $backup_unique_code;
                }
            }
        }

        if (!$status) {
            if (!defined('RESTORE_SILENTLY')) {
                print_error('error');
            } else {
                $errorstr = "An error has occured"; // helpful! :P
                return false;
            }
        }
        return true;
    }

    function restore_execute(&$restore,$info,$course_header,&$errorstr) {
        global $CFG, $USER, $DB, $OUTPUT;

        $status = true;


        //If we've selected to restore into new course
        //create it (course)
        //Saving conversion id variables into backup_tables
        if ($restore->restoreto == RESTORETO_NEW_COURSE) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('creatingnewcourse') . '</li>';
            }
            $oldidnumber = $course_header->course_idnumber;
            if (!$status = restore_create_new_course($restore,$course_header)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error while creating the new empty course.");
                } else {
                    $errorstr = "Error while creating the new empty course.";
                    return false;
                }
            }

            //Print course fullname and shortname and category
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("category").": ".$course_header->category->name.'</li>';
                    if (!empty($oldidnumber)) {
                        echo "<li>".get_string("nomoreidnumber","moodle",$oldidnumber)."</li>";
                    }
                    echo "</ul>";
                    //Put the destination course_id
                }
                $restore->course_id = $course_header->course_id;
            }

            if ($status = restore_open_html($restore,$course_header)){
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>Creating the Restorelog.html in the course backup folder</li>";
                }
            }

        } else {
            $course = $DB->get_record("course", array("id"=>$restore->course_id));
            if ($course) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("usingexistingcourse");
                    echo "<ul>";
                    echo "<li>".get_string("from").": ".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("to").": ". format_string($course->fullname) ." (".format_string($course->shortname).")".'</li>';
                    if (($restore->deleting)) {
                        echo "<li>".get_string("deletingexistingcoursedata").'</li>';
                    } else {
                        echo "<li>".get_string("addingdatatoexisting").'</li>';
                    }
                    echo "</ul></li>";
                }
                //If we have selected to restore deleting, we do it now.
                if ($restore->deleting) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string("deletingolddata").'</li>';
                    }
                    $status = remove_course_contents($restore->course_id,false) and
                        delete_dir_contents($CFG->dataroot."/".$restore->course_id,"backupdata");
                    if ($status) {
                        //Now , this situation is equivalent to the "restore to new course" one (we
                        //have a course record and nothing more), so define it as "to new course"
                        $restore->restoreto = RESTORETO_NEW_COURSE;
                    } else {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo $OUTPUT->notification("An error occurred while deleting some of the course contents.");
                        } else {
                            $errrostr = "An error occurred while deleting some of the course contents.";
                            return false;
                        }
                    }
                }
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Error opening existing course.");
                    $status = false;
                } else {
                    $errorstr = "Error opening existing course.";
                    return false;
                }
            }
        }

        //Now create groupings as needed
        if ($status and ($restore->groups == RESTORE_GROUPINGS_ONLY or $restore->groups == RESTORE_GROUPS_GROUPINGS)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupings");
            }
            if (!$status = restore_create_groupings($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore groupings!");
                } else {
                    $errorstr = "Could not restore groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create groupingsgroups as needed
        if ($status and $restore->groups == RESTORE_GROUPS_GROUPINGS) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupingsgroups");
            }
            if (!$status = restore_create_groupings_groups($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore groups in groupings!");
                } else {
                    $errorstr = "Could not restore groups in groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }


        //Now create the course_sections and their associated course_modules
        //we have to do this after groups and groupings are restored, because we need the new groupings id
        if ($status) {
            //Into new course
            if ($restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Into existing course
            } else if ($restore->restoreto != RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("checkingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Error
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Neither a new course or an existing one was specified.");
                    $status = false;
                } else {
                    $errorstr = "Neither a new course or an existing one was specified.";
                    return false;
                }
            }
        }

        //Now create categories and questions as needed
        if ($status) {
            include_once("$CFG->dirroot/question/restorelib.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcategoriesandquestions");
                echo "<ul>";
            }
            if (!$status = restore_create_questions($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore categories and questions!");
                } else {
                    $errorstr = "Could not restore categories and questions!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</ul></li>";
            }
        }


        //Now create course files as needed
        if ($status and ($restore->course_files)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingcoursefiles");
            }
            if (!$status = restore_course_files($restore)) {
                if (empty($status)) {
                    echo $OUTPUT->notification("Could not restore course files!");
                } else {
                    $errorstr = "Could not restore course files!";
                    return false;
                }
            }
            //If all is ok (and we have a counter)
            if ($status and ($status !== true)) {
                //Inform about user dirs created from backup
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".get_string("filesfolders").": ".$status.'</li>';
                    echo "</ul>";
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }

        //Now create events as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingevents");
            }
            if (!$status = restore_create_events($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore course events!");
                } else {
                    $errorstr = "Could not restore course events!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create course modules as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcoursemodules");
            }
            if (!$status = restore_create_modules($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore modules!");
                } else {
                    $errorstr = "Could not restore modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Bring back the course blocks -- do it AFTER the modules!!!
        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('creatingblocks');
                }
                $course_header->blockinfo = !empty($course_header->blockinfo) ? $course_header->blockinfo : NULL;
                if (!$status = restore_create_blocks($restore, $info->backup_block_format, $course_header->blockinfo, $xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification('Error while creating the course blocks');
                    } else {
                        $errorstr = "Error while creating the course blocks";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('courseformatdata');
                }
                if (!$status = restore_set_format_data($restore, $xml_file)) {
                        $error = "Error while setting the course format data";
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification($error);
                    } else {
                        $errorstr=$error;
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        //Now create log entries as needed
        if ($status and ($info->backup_logs == 'true' && $restore->logs)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinglogentries");
            }
            if (!$status = restore_create_logs($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not restore logs!");
                } else {
                    $errorstr = "Could not restore logs!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust the instance field in course_modules !!
        //this also calculates the final modinfo information so, after this,
        //code needing it can be used (like role_assignments. MDL-13740)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkinginstances");
            }
            if (!$status = restore_check_instances($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not adjust instances in course_modules!");
                } else {
                    $errorstr = "Could not adjust instances in course_modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust activity events
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("refreshingevents");
            }
            if (!$status = restore_refresh_events($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not refresh events for activities!");
                } else {
                    $errorstr = "Could not refresh events for activities!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust inter-activity links
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("decodinginternallinks");
            }
            if (!$status = restore_decode_content_links($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not decode content links!");
                } else {
                    $errorstr = "Could not decode content links!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create gradebook as needed -- AFTER modules and blocks!!!
        if ($status) {
            if ($restore->backup_version > 2007090500) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatinggradebook");
                }
                if (!$status = restore_create_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Could not restore gradebook!");
                    } else {
                        $errorstr = "Could not restore gradebook!";
                        return false;
                    }
                }

                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }

            } else {
                // for moodle versions before 1.9, those grades need to be converted to use the new gradebook
                // this code needs to execute *after* the course_modules are sorted out
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("migratinggrades");
                }

            /// force full refresh of grading data before migration == crete all items first
                if (!$status = restore_migrate_old_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo $OUTPUT->notification("Could not migrate gradebook!");
                    } else {
                        $errorstr = "Could not migrade gradebook!";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
            /// force full refresh of grading data after all items are created
                grade_force_full_regrading($restore->course_id);
                grade_grab_course_grades($restore->course_id);
        }

        /*******************************************************************************
         ************* Restore of Roles and Capabilities happens here ******************
         *******************************************************************************/
         // try to restore roles even when restore is going to fail - teachers might have
         // at least some role assigned - this is not correct though
        $status = restore_create_roles($restore, $xml_file) && $status;
        $status = restore_roles_and_filter_settings($restore, $xml_file) && $status;

        //Now if all is OK, update:
        //   - course modinfo field
        //   - categories table
        //   - add user as teacher
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingcourse");
            }
            //categories table
            $course = $DB->get_record("course", array("id"=>$restore->course_id));
            fix_course_sortorder();
            // Check if the user has course update capability in the newly restored course
            // there is no need to load his capabilities again, because restore_roles_and_filter_settings
            // would have loaded it anyway, if there is any assignments.
            // fix for MDL-6831
            $newcontext = get_context_instance(CONTEXT_COURSE, $restore->course_id);
            if (!has_capability('moodle/course:manageactivities', $newcontext)) {
                // fix for MDL-9065, use the new config setting if exists
                if ($CFG->creatornewroleid) {
                    role_assign($CFG->creatornewroleid, $USER->id, 0, $newcontext->id);
                } else {
                    if ($legacyteachers = get_archetype_roles('editingteacher')) {
                        if ($legacyteacher = array_shift($legacyteachers)) {
                            role_assign($legacyteacher->id, $USER->id, 0, $newcontext->id);
                        }
                    } else {
                        echo $OUTPUT->notification('Could not find a legacy teacher role. You might need your moodle admin to assign a role with editing privilages to this course.');
                    }
                }
            }
            // Availability system, if used, needs to find IDs for grade items
            $rs=$DB->get_recordset_sql("
SELECT
    cma.id,cma.gradeitemid
FROM
    {course_modules) cm
    INNER JOIN {course_modules_availability} cma on cm.id=cma.coursemoduleid
WHERE
    cma.gradeitemid IS NOT NULL
    AND cm.course=?
",array($restore->course_id));
            foreach($rs as $rec) {
                $newgradeid=backup_getid($restore->backup_unique_code,
                    'grade_items',$rec->gradeitemid);
                if($newgradeid) {
                    $newdata=(object)array(
                        'id'=>$rec->id,
                        'gradeitemid'=>$newgradeid->new_id);
                    $DB->update_record('course_modules_availability',$newdata);
                } else {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<p>Can't find new ID for grade item $data->gradeitemid, ignoring availability condition.</p>";
                    }
                    continue;
                }
            }
            $rs->close();

            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Cleanup temps (files and db)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("cleaningtempdata");
            }
            if (!$status = clean_temp_data ($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo $OUTPUT->notification("Could not clean up temporary data from files and database");
                } else {
                    $errorstr = "Could not clean up temporary data from files and database";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        // this is not a critical check - the result can be ignored
        if (restore_close_html($restore)){
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>Closing the Restorelog.html file.</li>';
            }
        }
        else {
            if (!defined('RESTORE_SILENTLY')) {
                echo $OUTPUT->notification("Could not close the restorelog.html file");
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        return $status;
    }
