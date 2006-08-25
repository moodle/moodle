<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //forum mods

    //This is the "graphical" structure of the forum mod:
    //
    //                               forum                                      
    //                            (CL,pk->id)
    //                                 |
    //         ---------------------------------------------------        
    //         |                                                 |
    //    subscriptions                                  forum_discussions
    //(UL,pk->id, fk->forum)           ---------------(UL,pk->id, fk->forum)
    //                                 |                         |
    //                                 |                         |
    //                                 |                         |
    //                                 |                     forum_posts
    //                                 |-------------(UL,pk->id,fk->discussion,
    //                                 |                  nt->parent,files) 
    //                                 |                         |
    //                                 |                         |
    //                                 |                         |
    //                            forum_read                forum_ratings
    //                       (UL,pk->id,fk->post        (UL,pk->id,fk->post)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function forum_backup_mods($bf,$preferences) {
       
        global $CFG;

        $status = true;

        //Iterate over forum table
        $forums = get_records ("forum","course",$preferences->backup_course,"id");
        if ($forums) {
            foreach ($forums as $forum) {
                if (backup_mod_selected($preferences,'forum',$forum->id)) {
                    $status = forum_backup_one_mod($bf,$preferences,$forum);
                    // backup files happens in backup_one_mod now too.
                }
            }
        }
        return $status;
    }


    function forum_backup_one_mod($bf,$preferences,$forum) {
    
        global $CFG;
        
        if (is_numeric($forum)) {
            $forum = get_record('forum','id',$forum);
        }
        $instanceid = $forum->id;
        
        $status = true;
        
        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        
        //Print forum data
        fwrite ($bf,full_tag("ID",4,false,$forum->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"forum"));
        fwrite ($bf,full_tag("TYPE",4,false,$forum->type));
        fwrite ($bf,full_tag("NAME",4,false,$forum->name));
        fwrite ($bf,full_tag("INTRO",4,false,$forum->intro));
        fwrite ($bf,full_tag("ASSESSED",4,false,$forum->assessed));
        fwrite ($bf,full_tag("ASSESSTIMESTART",4,false,$forum->assesstimestart));
        fwrite ($bf,full_tag("ASSESSTIMEFINISH",4,false,$forum->assesstimefinish));
        fwrite ($bf,full_tag("MAXBYTES",4,false,$forum->maxbytes));
        fwrite ($bf,full_tag("SCALE",4,false,$forum->scale));
        fwrite ($bf,full_tag("FORCESUBSCRIBE",4,false,$forum->forcesubscribe));
        fwrite ($bf,full_tag("TRACKINGTYPE",4,false,$forum->trackingtype));
        fwrite ($bf,full_tag("RSSTYPE",4,false,$forum->rsstype));
        fwrite ($bf,full_tag("RSSARTICLES",4,false,$forum->rssarticles));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$forum->timemodified));
        fwrite ($bf,full_tag("WARNAFTER",4,false,$forum->warnafter));
        fwrite ($bf,full_tag("BLOCKAFTER",4,false,$forum->blockafter));
        fwrite ($bf,full_tag("BLOCKPERIOD",4,false,$forum->blockperiod));
        
        //if we've selected to backup users info, then execute backup_forum_suscriptions and
        //backup_forum_discussions
        if (backup_userdata_selected($preferences,'forum',$forum->id)) {
            $status = backup_forum_subscriptions($bf,$preferences,$forum->id);
            if ($status) {
                $status = backup_forum_discussions($bf,$preferences,$forum->id);
            }
            if ($status) {
                $status = backup_forum_read($bf,$preferences,$forum->id);
            }
            if ($status) {
                $status = backup_forum_files_instance($bf,$preferences,$forum->id);
            }
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));
        return $status;
    }


    function forum_check_backup_mods_instances($instance,$backup_unique_code) {
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("subscriptions","forum");
            if ($ids = forum_subscription_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
            //Discussions
            $info[$instance->id.'2'][0] = get_string("discussions","forum");
            if ($ids = forum_discussion_ids_by_instance ($instance->id)) {
                $info[$instance->id.'2'][1] = count($ids);
            } else {
                $info[$instance->id.'2'][1] = 0;
            }
            //Posts
            $info[$instance->id.'3'][0] = get_string("posts","forum");
            if ($ids = forum_post_ids_by_instance ($instance->id)) {
                $info[$instance->id.'3'][1] = count($ids);
            } else {
                $info[$instance->id.'3'][1] = 0;
            }
            //Ratings
            $info[$instance->id.'4'][0] = get_string("ratings","forum");
            if ($ids = forum_rating_ids_by_instance ($instance->id)) {
                $info[$instance->id.'4'][1] = count($ids);
            } else {
                $info[$instance->id.'4'][1] = 0;
            }
        }
        return $info;
    }

    //Backup forum_subscriptions contents (executed from forum_backup_mods)     
    function backup_forum_subscriptions ($bf,$preferences,$forum) {     

        global $CFG;

        $status = true;

        $forum_subscriptions = get_records("forum_subscriptions","forum",$forum,"id");
        //If there is subscriptions
        if ($forum_subscriptions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SUBSCRIPTIONS",4,true));
            //Iterate over each answer
            foreach ($forum_subscriptions as $for_sus) {
                //Start suscription
                $status =fwrite ($bf,start_tag("SUBSCRIPTION",5,true));
                //Print forum_subscriptions contents
                fwrite ($bf,full_tag("ID",6,false,$for_sus->id));
                fwrite ($bf,full_tag("USERID",6,false,$for_sus->userid));
                //End subscription
                $status =fwrite ($bf,end_tag("SUBSCRIPTION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SUBSCRIPTIONS",4,true));
        }
        return $status;
    }

    //Backup forum_discussions contents (executed from forum_backup_mods)
    function backup_forum_discussions ($bf,$preferences,$forum) {

        global $CFG;

        $status = true;

        $forum_discussions = get_records("forum_discussions","forum",$forum,"id");
        //If there are discussions
        if ($forum_discussions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("DISCUSSIONS",4,true));
            //Iterate over each discussion
            foreach ($forum_discussions as $for_dis) {
                //Start discussion
                $status =fwrite ($bf,start_tag("DISCUSSION",5,true));
                //Print forum_discussions contents
                fwrite ($bf,full_tag("ID",6,false,$for_dis->id));
                fwrite ($bf,full_tag("NAME",6,false,$for_dis->name));
                fwrite ($bf,full_tag("FIRSTPOST",6,false,$for_dis->firstpost));
                fwrite ($bf,full_tag("USERID",6,false,$for_dis->userid));
                fwrite ($bf,full_tag("GROUPID",6,false,$for_dis->groupid));
                fwrite ($bf,full_tag("ASSESSED",6,false,$for_dis->assessed));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$for_dis->timemodified));
                fwrite ($bf,full_tag("USERMODIFIED",6,false,$for_dis->usermodified));
                fwrite ($bf,full_tag("TIMESTART",6,false,$for_dis->timestart));
                fwrite ($bf,full_tag("TIMEEND",6,false,$for_dis->timeend));
                //Now print posts to xml
                $status = backup_forum_posts($bf,$preferences,$for_dis->id);
                //End discussion
                $status =fwrite ($bf,end_tag("DISCUSSION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("DISCUSSIONS",4,true));
        }
        return $status;
    }

    //Backup forum_read contents (executed from forum_backup_mods)     
    function backup_forum_read ($bf,$preferences,$forum) {     

        global $CFG;

        $status = true;

        $forum_read = get_records("forum_read","forumid",$forum,"id");
        //If there are read
        if ($forum_read) {
            //Write start tag
            $status =fwrite ($bf,start_tag("READPOSTS",4,true));
            //Iterate over each read
            foreach ($forum_read as $for_rea) {
                //Start read
                $status =fwrite ($bf,start_tag("READ",5,true));
                //Print forum_read contents
                fwrite ($bf,full_tag("ID",6,false,$for_rea->id));
                fwrite ($bf,full_tag("USERID",6,false,$for_rea->userid));
                fwrite ($bf,full_tag("DISCUSSIONID",6,false,$for_rea->discussionid));
                fwrite ($bf,full_tag("POSTID",6,false,$for_rea->postid));
                fwrite ($bf,full_tag("FIRSTREAD",6,false,$for_rea->firstread));
                fwrite ($bf,full_tag("LASTREAD",6,false,$for_rea->lastread));
                //End read
                $status =fwrite ($bf,end_tag("READ",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("READPOSTS",4,true));
        }
        return $status;
    }

    //Backup forum_posts contents (executed from backup_forum_discussions)
    function backup_forum_posts ($bf,$preferences,$discussion) {

        global $CFG;

        $status = true;

        $forum_posts = get_records("forum_posts","discussion",$discussion,"id");
        //If there are posts
        if ($forum_posts) {
            //Write start tag
            $status =fwrite ($bf,start_tag("POSTS",6,true));
            //Iterate over each post
            foreach ($forum_posts as $for_pos) {
                //Start post
                $status =fwrite ($bf,start_tag("POST",7,true));
                //Print forum_posts contents
                fwrite ($bf,full_tag("ID",8,false,$for_pos->id));
                fwrite ($bf,full_tag("PARENT",8,false,$for_pos->parent));
                fwrite ($bf,full_tag("USERID",8,false,$for_pos->userid));
                fwrite ($bf,full_tag("CREATED",8,false,$for_pos->created));
                fwrite ($bf,full_tag("MODIFIED",8,false,$for_pos->modified));
                fwrite ($bf,full_tag("MAILED",8,false,$for_pos->mailed));
                fwrite ($bf,full_tag("SUBJECT",8,false,$for_pos->subject));
                fwrite ($bf,full_tag("MESSAGE",8,false,$for_pos->message));
                fwrite ($bf,full_tag("FORMAT",8,false,$for_pos->format));
                fwrite ($bf,full_tag("ATTACHMENT",8,false,$for_pos->attachment));
                fwrite ($bf,full_tag("TOTALSCORE",8,false,$for_pos->totalscore));
                fwrite ($bf,full_tag("MAILNOW",8,false,$for_pos->mailnow));
                //Now print ratings to xml
                $status = backup_forum_ratings($bf,$preferences,$for_pos->id);

                //End discussion
                $status =fwrite ($bf,end_tag("POST",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("POSTS",6,true));
        }
        return $status;
    }


    //Backup forum_ratings contents (executed from backup_forum_posts)
    function backup_forum_ratings ($bf,$preferences,$post) {

        global $CFG;

        $status = true;

        $forum_ratings = get_records("forum_ratings","post",$post,"id");
        //If there are ratings
        if ($forum_ratings) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RATINGS",8,true));
            //Iterate over each rating
            foreach ($forum_ratings as $for_rat) {
                //Start rating
                $status =fwrite ($bf,start_tag("RATING",9,true));
                //Print forum_rating contents
                fwrite ($bf,full_tag("ID",10,false,$for_rat->id));
                fwrite ($bf,full_tag("USERID",10,false,$for_rat->userid));
                fwrite ($bf,full_tag("TIME",10,false,$for_rat->time));
                fwrite ($bf,full_tag("POST_RATING",10,false,$for_rat->rating));
                //End rating
                $status =fwrite ($bf,end_tag("RATING",9,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("RATINGS",8,true));
        }
        return $status;
    }

    //Backup forum files because we've selected to backup user info
    //and files are user info's level
    function backup_forum_files($bf,$preferences) {
        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the forum dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum")) {
                $handle = opendir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum");
                while (false!==($item = readdir($handle))) {
                    if ($item != '.' && $item != '..' && is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum/".$item)
                        && array_key_exists($item,$preferences->mods['forum']->instances)
                        && !empty($preferences->mods['forum']->instances[$item]->backup)) {
                        $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum/".$item,
                                                   $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/forum/",$item);
                    }
                }
            }
        }

        return $status;

    }


    //Backup forum files because we've selected to backup user info
    //and files are user info's level
    function backup_forum_files_instance($bf,$preferences,$instanceid) {
        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/forum/",true);
        //Now copy the forum dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum/".$instanceid)) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/forum/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/forum/".$instanceid);
            }
        }

        return $status;

    }

   ////Return an array of info (name,value)
   function forum_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
       
       if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += forum_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
        //First the course data
        $info[0][0] = get_string("modulenameplural","forum");
        if ($ids = forum_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Subscriptions
            $info[1][0] = get_string("subscriptions","forum");
            if ($ids = forum_subscription_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
            //Discussions
            $info[2][0] = get_string("discussions","forum");
            if ($ids = forum_discussion_ids_by_course ($course)) {
                $info[2][1] = count($ids);
            } else {
                $info[2][1] = 0;
            }
            //Posts
            $info[3][0] = get_string("posts","forum");
            if ($ids = forum_post_ids_by_course ($course)) {
                $info[3][1] = count($ids);
            } else {
                $info[3][1] = 0;
            }
            //Ratings
            $info[4][0] = get_string("ratings","forum");
            if ($ids = forum_rating_ids_by_course ($course)) {
                $info[4][1] = count($ids);
            } else {
                $info[4][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function forum_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of forums
        $buscar="/(".$base."\/mod\/forum\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMINDEX*$2@$',$content);

        //Link to forum view by moduleid
        $buscar="/(".$base."\/mod\/forum\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMVIEWBYID*$2@$',$result);

        //Link to forum view by forumid
        $buscar="/(".$base."\/mod\/forum\/view.php\?f\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMVIEWBYF*$2@$',$result);

        //Link to forum discussion with parent syntax
        $buscar="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)\&parent\=([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMDISCUSSIONVIEWPARENT*$2*$3@$',$result);

        //Link to forum discussion with relative syntax
        $buscar="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)\#([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMDISCUSSIONVIEWINSIDE*$2*$3@$',$result);

        //Link to forum discussion by discussionid
        $buscar="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@FORUMDISCUSSIONVIEW*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of forums id
    function forum_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}forum a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of forum subscriptions id
    function forum_subscription_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.forum
                                 FROM {$CFG->prefix}forum_subscriptions s,
                                      {$CFG->prefix}forum a
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id");
    }

    //Returns an array of forum subscriptions id 
    function forum_subscription_ids_by_instance($instanceid) {
 
        global $CFG;
        
        return get_records_sql ("SELECT s.id , s.forum
                                 FROM {$CFG->prefix}forum_subscriptions s
                                 WHERE s.forum = $instanceid");
    }

    //Returns an array of forum discussions id
    function forum_discussion_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.forum      
                                 FROM {$CFG->prefix}forum_discussions s,    
                                      {$CFG->prefix}forum a 
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id"); 
    }

    //Returns an array of forum discussions id
    function forum_discussion_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.forum      
                                 FROM {$CFG->prefix}forum_discussions s   
                                 WHERE s.forum = $instanceid"); 
    }

    //Returns an array of forum posts id
    function forum_post_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT p.id , p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s,
                                      {$CFG->prefix}forum a
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id AND
                                       p.discussion = s.id");
    }

    //Returns an array of forum posts id
    function forum_post_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT p.id , p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s
                                 WHERE s.forum = $instanceid AND
                                       p.discussion = s.id");
    }

    //Returns an array of ratings posts id      
    function forum_rating_ids_by_course ($course) {      

        global $CFG;

        return get_records_sql ("SELECT r.id, r.post, p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_ratings r,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s,
                                      {$CFG->prefix}forum a    
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id AND   
                                       p.discussion = s.id AND
                                       r.post = p.id");
    }

    //Returns an array of ratings posts id      
    function forum_rating_ids_by_instance ($instanceid) {      

        global $CFG;

        return get_records_sql ("SELECT r.id, r.post, p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_ratings r,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s
                                 WHERE s.forum = $instanceid AND   
                                       p.discussion = s.id AND
                                       r.post = p.id");
    }
?>
