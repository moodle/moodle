<?PHP // $Id$

$DIALOGUE_DAYS = array (0 => 0, 7 => 7, 14 => 14, 30 => 30, 150 => 150, 365 => 365 );


// STANDARD MODULE FUNCTIONS /////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
function dialogue_add_instance($dialogue) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $dialogue->timemodified = time();

    return insert_record("dialogue", $dialogue);
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_cron () {
// Function to be run periodically according to the moodle cron

    global $CFG, $USER;

// delete any closed conversations which have expired
    dialogue_delete_expired_conversations();

// Finds all dialogue entries that have yet to be mailed out, and mails them
    if ($entries = get_records_select("dialogue_entries", "mailed = '0'")) {
        foreach ($entries as $entry) {

            echo "Processing dialogue entry $entry->id\n";

            if (! $userfrom = get_record("user", "id", "$entry->userid")) {
                echo "Could not find user $entry->userid\n";
                continue;
            }
            // get conversation record
            if(!$conversation = get_record("dialogue_conversations", "id", $entry->conversationid)) {
                echo "Could not find conversation $entry->conversationid\n";
            }
            if ($userfrom->id == $conversation->userid) {
                if (!$userto = get_record("user", "id", $conversation->recipientid)) {
                    echo "Could not find use $conversation->recipientid\n";
                }
            }
            else {
                if (!$userto = get_record("user", "id", $conversation->userid)) {
                    echo "Could not find use $conversation->userid\n";
                }
            }

            $USER->lang = $userto->lang;

            if (! $dialogue = get_record("dialogue", "id", $conversation->dialogueid)) {
                echo "Could not find dialogue id $conversation->dialogueid\n";
                continue;
            }
            if (! $course = get_record("course", "id", "$dialogue->course")) {
                echo "Could not find course $dialogue->course\n";
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
                echo "Course Module ID was incorrect\n";
            }

            if (! isstudent($course->id, $userfrom->id) and !isteacher($course->id, $userfrom->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $userto->id) and !isteacher($course->id, $userto->id)) {
                continue;  // Not an active participant
            }

            $strdialogues = get_string("modulenameplural", "dialogue");
            $strdialogue  = get_string("modulename", "dialogue");
    
            unset($dialogueinfo);
            $dialogueinfo->userfrom = fullname($userfrom);
            $dialogueinfo->dialogue = "$dialogue->name";
            $dialogueinfo->url = "$CFG->wwwroot/mod/dialogue/view.php?id=$cm->id";

            $postsubject = "$course->shortname: $strdialogues: $dialogue->name: ".
                get_string("newentry", "dialogue");
            $posttext  = "$course->shortname -> $strdialogues -> $dialogue->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("dialoguemail", "dialogue", $dialogueinfo);
            $posttext .= "---------------------------------------------------------------------\n";
            if ($userto->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                "<a href=\"$CFG->wwwroot/mod/dialogue/index.php?id=$course->id\">dialogues</a> ->".
                "<a href=\"$CFG->wwwroot/mod/dialogue/view.php?id=$cm->id\">$dialogue->name</a></font></p>";
                $posthtml .= "<hr><font face=\"sans-serif\">";
                $posthtml .= "<p>".get_string("dialoguemailhtml", "dialogue", $dialogueinfo)."</p>";
                $posthtml .= "</font><hr>";
            } else {
              $posthtml = "";
            }

            if (! email_to_user($userto, $userfrom, $postsubject, $posttext, $posthtml)) {
                echo "Error: dialogue cron: Could not send out mail for id $entry->id to user $userto->id ($userto->email)\n";
            }
            if (! set_field("dialogue_entries", "mailed", "1", "id", "$entry->id")) {
                echo "Could not update the mailed field for id $entry->id\n";
            }
        }
    }

    return true;
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $dialogue = get_record("dialogue", "id", $id)) {
        return false;
    }

    $result = true;

    if (! delete_records("dialogue_conversations", "dialogueid", $dialogue->id)) {
        $result = false;
    }

    if (! delete_records("dialogue_entries", "dialogueid", $dialogue->id)) {
        $result = false;
    }

    if (! delete_records("dialogue", "id", $dialogue->id)) {
        $result = false;
    }

    return $result;

}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;
    
    // have a look for new entries
    $addentrycontent = false;
    if ($logs = dialogue_get_add_entry_logs($course, $timestart)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $log) {
            // Create a temp valid module structure (only need courseid, moduleid)
            $tempmod->course = $course->id;
            $tempmod->id = $log->dialogueid;
            //Obtain the visible property from the instance
            if (instance_is_visible("dialogue",$tempmod)) {
                $addentrycontent = true;
                break;
            }
        }
        // if we got some "live" ones then output them
        if ($addentrycontent) {
            $strftimerecent = get_string("strftimerecent");
            print_headline(get_string("newdialogueentries", "dialogue").":");
            foreach ($logs as $log) {
                //Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->dialogueid;
                //Obtain the visible property from the instance
                if (instance_is_visible("dialogue",$tempmod)) {
                    $date = userdate($log->time, $strftimerecent);
                    echo '<p><font size="1">'.$date.' - '.fullname($log).'<br />';
                    echo "\"<a href=\"$CFG->wwwroot/mod/dialogue/$log->url\">";
                    echo "$log->name";
                    echo "</a>\"</font></p>";
                }
            }
        }
    }

    // have a look for open conversations
    $opencontent = false;
    if ($logs = dialogue_get_open_conversations($course)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $log) {
            // Create a temp valid module structure (only need courseid, moduleid)
            $tempmod->course = $course->id;
            $tempmod->id = $log->dialogueid;
            //Obtain the visible property from the instance
            if (instance_is_visible("dialogue",$tempmod)) {
                $opencontent = true;
                break;
            }
        }
        // if we got some "live" ones then output them
        if ($opencontent) {
            $strftimerecent = get_string("strftimerecent");
            print_headline(get_string("opendialogueentries", "dialogue").":");
            foreach ($logs as $log) {
                //Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->dialogueid;
                //Obtain the visible property from the instance
                if (instance_is_visible("dialogue",$tempmod)) {
                    $date = userdate($log->time, $strftimerecent);
                    echo '<p><font size="1">'.$date.' - '.fullname($log).'<br />';
                    echo "\"<a href=\"$CFG->wwwroot/mod/dialogue/$log->url\">";
                    echo "$log->name";
                    echo "</a>\"</font></p>";
                }
            }
        }
    }

    // have a look for closed conversations
    $closedcontent = false;
    if ($logs = dialogue_get_closed_logs($course, $timestart)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $log) {
            // Create a temp valid module structure (only need courseid, moduleid)
            $tempmod->course = $course->id;
            $tempmod->id = $log->dialogueid;
            //Obtain the visible property from the instance
            if (instance_is_visible("dialogue",$tempmod)) {
                $closedcontent = true;
                break;
            }
        }
        // if we got some "live" ones then output them
        if ($closedcontent) {
            $strftimerecent = get_string("strftimerecent");
            print_headline(get_string("modulenameplural", "dialogue").":");
            foreach ($logs as $log) {
                //Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->dialogueid;
                //Obtain the visible property from the instance
                if (instance_is_visible("dialogue",$tempmod)) {
                    $date = userdate($log->time, $strftimerecent);
                    echo "<p><font size=1>$date - ".get_string("namehascloseddialogue", "dialogue",
                        fullname($log))."<br />";
                    echo "\"<a href=\"$CFG->wwwroot/mod/dialogue/$log->url\">";
                    echo "$log->name";
                    echo "</a>\"</font></p>";
                }
            }
        }
    }
    return $addentrycontent or $closedcontent;
}



//////////////////////////////////////////////////////////////////////////////////////
function dialogue_update_instance($dialogue) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $dialogue->timemodified = time();
    $dialogue->id = $dialogue->instance;

    return update_record("dialogue", $dialogue);
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_user_complete($course, $user, $mod, $dialogue) {

    if ($conversations = dialogue_get_conversations($dialogue, $user)) {
        print_simple_box_start();
        $table->head = array (get_string("dialoguewith", "dialogue"),  
            get_string("numberofentries", "dialogue"), get_string("lastentry", "dialogue"),
            get_string("status", "dialogue"));
        $table->width = "100%";
        $table->align = array ("left", "center", "left", "left");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        foreach ($conversations as $conversation) {
            if ($user->id != $conversation->userid) {
                if (!$with = get_record("user", "id", $conversation->userid)) {
                    error("User's record not found");
                }
            }
            else {
                if (!$with = get_record("user", "id", $conversation->recipientid)) {
                    error("User's record not found");
                }
            }
            $total = dialogue_count_entries($dialogue, $conversation);
            $byuser = dialogue_count_entries($dialogue, $conversation, $user);
            if ($conversation->closed) {
                $status = get_string("closed", "dialogue");
            } else {
                $status = get_string("open", "dialogue");
            }
            $table->data[] = array(fullname($with), $byuser." ".
                get_string("of", "dialogue")." ".$total, userdate($conversation->timemodified), $status);
        }
        print_table($table);
        print_simple_box_end();
    } 
    else {
        print_string("noentry", "dialogue");
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_user_outline($course, $user, $mod, $dialogue) {
    if ($entries = dialogue_get_user_entries($dialogue, $user)) {
        $result->info = count($entries);
        foreach ($entries as $entry) {
            // dialogue_get_user_entries returns the most recent entry first
            $result->time = $entry->timecreated;
            break;
        }
        return $result;
    }
    return NULL;
}

//////////////////////////////////////////////////////////////////////////////////////
// Extra functions needed by the Standard functions
//////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_entries($dialogue, $conversation, $user = '') {
    
    if (empty($user)) {
        return count_records_select("dialogue_entries", "conversationid = $conversation->id");
    }
    else {
        return count_records_select("dialogue_entries", "conversationid = $conversation->id AND 
            userid = $user->id");
    }   
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_delete_expired_conversations() {

    if ($dialogues = get_records("dialogue")) {
       foreach ($dialogues as $dialogue) {
           if ($dialogue->deleteafter) {
               $expirytime = time() - $dialogue->deleteafter * 86400;
               if ($conversations = get_records_select("dialogue_conversations",
                   "timemodified < $expirytime AND closed = 1")) {
                   foreach ($conversations as $conversation) {
                       delete_records("dialogue_conversations", "id", $conversation->id);
                       delete_records("dialogue_entries", "conversationid", $conversation->id);
                   }
               }
           }
       }
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_add_entry_logs($course, $timestart) {
    // get the "add entry" entries and add the first and last names, we are not interested in the entries 
    // make by this user (the last condition)!
    global $CFG, $USER;
    if (!isset($USER->id)) {
        return false;
    }
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, e.dialogueid, d.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}dialogue d, 
                                {$CFG->prefix}dialogue_conversations c, 
                                {$CFG->prefix}dialogue_entries e, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.course = $course->id AND l.module = 'dialogue'
                                AND l.action = 'add entry'
                                AND e.id = l.info 
                                AND c.id = e.conversationid
                                AND (c.userid = $USER->id or c.recipientid = $USER->id)
                                AND d.id = e.dialogueid
                                AND u.id = e.userid 
                                AND e.userid != $USER->id");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_closed_logs($course, $timestart) {
    // get the "closed" entries and add the first and last names, we are not interested in the entries 
    // make by this user (the last condition)!
    global $CFG, $USER;
    if (!isset($USER->id)) {
        return false;
    }
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, c.dialogueid, d.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}dialogue d, 
                                {$CFG->prefix}dialogue_conversations c, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.course = $course->id AND l.module = 'dialogue'
                                AND l.action = 'closed'
                                AND c.id = l.info 
                                AND (c.userid = $USER->id or c.recipientid = $USER->id)
                                AND d.id = c.dialogueid
                                AND u.id = c.lastid 
                                AND c.lastid != $USER->id");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_conversations($dialogue, $user, $condition = '', $order = '') {
    global $CFG;
    
    if (!empty($condition)) {
        $condition = ' AND '.$condition;
    }
    if (empty($order)) {
        $order = "timemodified DESC";
    }
    return get_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
            (userid = $user->id OR recipientid = $user->id) $condition", $order);
    
    
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_open_conversations($course) {
    // get the conversations which are waiting for a response for this user. 
    // Add the first and last names of the other participant
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    if ($conversations = get_records_sql("SELECT d.name as dialoguename, c.id, c.dialogueid, c.timemodified, c.lastid
                            FROM {$CFG->prefix}dialogue d, {$CFG->prefix}dialogue_conversations c
                            WHERE d.course = $course->id
                                AND c.dialogueid = d.id
                                AND (c.userid = $USER->id OR c.recipientid = $USER->id)
                                AND c.lastid != $USER->id
                                AND c.closed =0")) {
        
         foreach ($conversations as $conversation) {
            if (!$user = get_record("user", "id", $conversation->lastid)) {
                error("Get open conversations: user record not found");
            }
            if (!$cm = get_coursemodule_from_instance("dialogue", $conversation->dialogueid, $course->id)) {
                error("Course Module ID was incorrect");
            }
            $entry[$conversation->id]->dialogueid = $conversation->dialogueid;
            $entry[$conversation->id]->time = $conversation->timemodified;
            $entry[$conversation->id]->url = "view.php?id=$cm->id";
            $entry[$conversation->id]->firstname = $user->firstname; 
            $entry[$conversation->id]->lastname = $user->lastname;
            $entry[$conversation->id]->name = $conversation->dialoguename;
        }
        return $entry;
    }
    return;
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_user_entries($dialogue, $user) {
    global $CFG;
    return get_records_select("dialogue_entries", "dialogueid = $dialogue->id AND userid = $user->id",
                "timecreated DESC");
}



?>
