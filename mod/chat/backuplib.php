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
    //                   chat_messages 
    //               (UL,pk->id, fk->chatid)
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
    function chat_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over chat table
        $chats = get_records ("chat","course",$preferences->backup_course,"id");
        if ($chats) {
            foreach ($chats as $chat) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print chat data
                fwrite ($bf,full_tag("ID",4,false,$chat->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"chat"));
                fwrite ($bf,full_tag("NAME",4,false,$chat->name));
                fwrite ($bf,full_tag("INTRO",4,false,$chat->intro));
                fwrite ($bf,full_tag("KEEPDAYS",4,false,$chat->keepdays));
                fwrite ($bf,full_tag("STUDENTLOGS",4,false,$chat->studentlogs));
                fwrite ($bf,full_tag("SCHEDULE",4,false,$chat->schedule));
                fwrite ($bf,full_tag("CHATTIME",4,false,$chat->chattime));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$chat->timemodified));
                //if we've selected to backup users info, then execute backup_chat_messages
                if ($preferences->mods["chat"]->userinfo) {
                    $status = backup_chat_messages($bf,$preferences,$chat->id);
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;  
    }

    //Backup chat_messages contents (executed from chat_backup_mods)
    function backup_chat_messages ($bf,$preferences,$chat) {

        global $CFG;

        $status = true;

        $chat_messages = get_records("chat_messages","chatid",$chat,"id");
        //If there is messages
        if ($chat_messages) {
            //Write start tag
            $status =fwrite ($bf,start_tag("MESSAGES",4,true));
            //Iterate over each message
            foreach ($chat_messages as $cha_mes) {
                //Start message
                $status =fwrite ($bf,start_tag("MESSAGE",5,true));
                //Print message contents
                fwrite ($bf,full_tag("ID",6,false,$cha_mes->id));       
                fwrite ($bf,full_tag("USERID",6,false,$cha_mes->userid));       
                fwrite ($bf,full_tag("GROUPID",6,false,$cha_mes->groupid)); 
                fwrite ($bf,full_tag("SYSTEM",6,false,$cha_mes->system));       
                fwrite ($bf,full_tag("MESSAGE_TEXT",6,false,$cha_mes->message));       
                fwrite ($bf,full_tag("TIMESTAMP",6,false,$cha_mes->timestamp));       
                //End submission
                $status =fwrite ($bf,end_tag("MESSAGE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("MESSAGES",4,true));
        }
        return $status;
    }

    //Return an array of info (name,value)
    function chat_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","chat");
        if ($ids = chat_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("messages","chat");
            if ($ids = chat_message_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of chats id 
    function chat_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT c.id, c.course
                                 FROM {$CFG->prefix}chat c
                                 WHERE c.course = '$course'");
    }
    
    //Returns an array of assignment_submissions id
    function chat_message_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT m.id , m.chatid
                                 FROM {$CFG->prefix}chat_messages m,
                                      {$CFG->prefix}chat c
                                 WHERE c.course = '$course' AND
                                       m.chatid = c.id");
    }
?>
