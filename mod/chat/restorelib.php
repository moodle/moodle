<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //chat mods

    //This is the "graphical" structure of the chat mod:
    //
    //                       chat
    //                    (CL,pk->id)             
    //                        |
    //                        |
    //                        |
    //                    chat_messages 
    //                (UL,pk->id, fk->chatid)
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
    function chat_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object   
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the CHAT record structure
            $chat->course = $restore->course_id;
            $chat->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $chat->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $chat->keepdays = backup_todb($info['MOD']['#']['KEEPDAYS']['0']['#']);
            $chat->studentlogs = backup_todb($info['MOD']['#']['STUDENTLOGS']['0']['#']);
            $chat->schedule = backup_todb($info['MOD']['#']['SCHEDULE']['0']['#']);
            $chat->chattime = backup_todb($info['MOD']['#']['CHATTIME']['0']['#']);
            $chat->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the chat
            $newid = insert_record ("chat",$chat);

            //Do some output     
            echo "<ul><li>".get_string("modulename","chat")." \"".$chat->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if ($restore->mods['chat']->userinfo) {
                    //Restore chat_messages
                    $status = chat_messages_restore_mods ($mod->id, $newid,$info,$restore);
                }
            } else {
                $status = false;
            }

            //Finalize ul        
            echo "</ul>";
        
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the chat_messages
    function chat_messages_restore_mods($old_chat_id, $new_chat_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $messages = $info['MOD']['#']['MESSAGES']['0']['#']['MESSAGE'];

        //Iterate over messages
        for($i = 0; $i < sizeof($messages); $i++) {
            $mes_info = $messages[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($mes_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($mes_info['#']['USERID']['0']['#']);

            //Now, build the CHAT_MESSAGES record structure
            $message->chatid = $new_chat_id;
            $message->userid = backup_todb($mes_info['#']['USERID']['0']['#']);
            $message->groupid = backup_todb($mes_info['#']['GROUPID']['0']['#']);
            $message->system = backup_todb($mes_info['#']['SYSTEM']['0']['#']);
            $message->message = backup_todb($mes_info['#']['MESSAGE_TEXT']['0']['#']);
            $message->timestamp = backup_todb($mes_info['#']['TIMESTAMP']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$message->userid);
            if ($user) {
                $message->userid = $user->new_id;
            }

            //We have to recode the groupid field
            $group = backup_getid($restore->backup_unique_code,"group",$message->groupid);
            if ($group) {
                $message->groupid = $group->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("chat_messages",$message);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }
        }
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function chat_restore_logs($restore,$log) {

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
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
