<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //forum mods

    //This is the "graphical" structure of the forum mod:
    //
    //                           forum                                      
    //                        (CL,pk->id)
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        subscriptions                    forum_discussions
    //    (UL,pk->id, fk->forum)             (UL,pk->id, fk->forum)
    //                                               |
    //                                               |
    //                                               |
    //                                           forum_posts
    //                             (UL,pk->id,fk->discussion,nt->parent,files) 
    //                                               |
    //                                               |
    //                                               |
    //                                          forum_ratings
    //                                      (UL,pk->id,fk->post)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function forum_restore_mods($mod,$restore) {

        global $CFG,$db;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the FORUM record structure
            $forum->course = $restore->course_id;
            $forum->type = backup_todb($info['MOD']['#']['TYPE']['0']['#']);
            $forum->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $forum->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $forum->open = backup_todb($info['MOD']['#']['OPEN']['0']['#']);
            $forum->assessed = backup_todb($info['MOD']['#']['ASSESSED']['0']['#']);
            $forum->assesspublic = backup_todb($info['MOD']['#']['ASSESSPUBLIC']['0']['#']);
            $forum->assesstimestart = backup_todb($info['MOD']['#']['ASSESSTIMESTART']['0']['#']);
            $forum->assesstimefinish = backup_todb($info['MOD']['#']['ASSESSTIMEFINISH']['0']['#']);
            $forum->maxbytes = backup_todb($info['MOD']['#']['MAXBYTES']['0']['#']);
            $forum->scale = backup_todb($info['MOD']['#']['SCALE']['0']['#']);
            $forum->forcesubscribe = backup_todb($info['MOD']['#']['FORCESUBSCRIBE']['0']['#']);
            $forum->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the scale field if it's <0 (positive is a grade, not a scale)
            if ($forum->scale < 0) {
                $scale = backup_getid($restore->backup_unique_code,"scale",abs($forum->scale));
                if ($scale) {
                    $forum->scale = -($scale->new_id);
                }
            }
            
            $forumtobeinserted = true;
            //If the forum is a teacher forum, then we have to look if it exists in destination course
            if ($forum->type == "teacher") {
                //Look for teacher forum in destination course
                $teacherforum = get_record("forum","course",$restore->course_id,"type","teacher");
                if ($teacherforum) {
                    $newid = $teacherforum->id;
                    $forumtobeinserted = false;
                }
            }

            //If the forum has to be inserted
            if ($forumtobeinserted) {
                //The structure is equal to the db, so insert the forum
                $newid = insert_record ("forum",$forum);
            }

            //Do some output
            echo "<ul><li>".get_string("modulename","forum")." \"".$forum->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if ($restore->mods['forum']->userinfo) {
                    //Restore forum_subscriptions
                    $status = forum_subscriptions_restore_mods ($newid,$info,$restore);
                    if ($status) {
                        //Restore forum_discussions
                        $status = forum_discussions_restore_mods ($newid,$info,$restore);
                    }
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

    //This function restores the forum_subscriptions
    function forum_subscriptions_restore_mods($forum_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the discussions array
        $subscriptions = $info['MOD']['#']['SUBSCRIPTIONS']['0']['#']['SUBSCRIPTION'];

        //Iterate over subscriptions
        for($i = 0; $i < sizeof($subscriptions); $i++) {
            $sus_info = $subscriptions[$i];
            //traverse_xmlize($sus_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sus_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sus_info['#']['USERID']['0']['#']);

            //Now, build the FORUM_SUBSCRIPTIONS record structure
            $subscription->forum = $forum_id;
            $subscription->userid = backup_todb($sus_info['#']['USERID']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$subscription->userid);
            if ($user) {
                $subscription->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the forum_subscription
            $newid = insert_record ("forum_subscriptions",$subscription);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"forum_subscriptions",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the forum_discussions
    function forum_discussions_restore_mods($forum_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the discussions array
        $discussions = $info['MOD']['#']['DISCUSSIONS']['0']['#']['DISCUSSION'];

        //Iterate over discussions
        for($i = 0; $i < sizeof($discussions); $i++) {
            $dis_info = $discussions[$i];
            //traverse_xmlize($dis_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($dis_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($dis_info['#']['USERID']['0']['#']);

            //Now, build the FORUM_DISCUSSIONS record structure
            $discussion->forum = $forum_id;
            $discussion->course = $restore->course_id;
            $discussion->name = backup_todb($dis_info['#']['NAME']['0']['#']);
            $discussion->firstpost = backup_todb($dis_info['#']['FIRSTPOST']['0']['#']);
            $discussion->userid = backup_todb($dis_info['#']['USERID']['0']['#']);
            $discussion->groupid = backup_todb($dis_info['#']['GROUPID']['0']['#']);
            $discussion->assessed = backup_todb($dis_info['#']['ASSESSED']['0']['#']);
            $discussion->timemodified = backup_todb($dis_info['#']['TIMEMODIFIED']['0']['#']);
            $discussion->usermodified = backup_todb($dis_info['#']['USERMODIFIED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$discussion->userid);
            if ($user) {
                $discussion->userid = $user->new_id;
            }

            //We have to recode the groupid field
            $group = backup_getid($restore->backup_unique_code,"group",$discussion->groupid);
            if ($group) {
                $discussion->groupid = $group->new_id;
            }

            //We have to recode the usermodified field
            $user = backup_getid($restore->backup_unique_code,"user",$discussion->usermodified);
            if ($user) {
                $discussion->usermodified = $user->new_id;
            }

            //The structure is equal to the db, so insert the forum_discussions
            $newid = insert_record ("forum_discussions",$discussion);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"forum_discussions",$oldid,
                             $newid);
                //Restore forum_posts
                $status = forum_posts_restore_mods ($forum_id,$newid,$dis_info,$restore);
                //Now recalculate firstpost field
                $old_firstpost = $discussion->firstpost;
                //Get its new post_id from backup_ids table
                $rec = backup_getid($restore->backup_unique_code,"forum_posts",$old_firstpost);
                if ($rec) {
                    //Put its new firstpost
                    $discussion->firstpost = $rec->new_id;
                    if ($post = get_record("forum_posts", "id", $discussion->firstpost)) {
                        $discussion->userid = $post->userid;
                    }
                } else {
                     $discussion->firstpost = 0;
                     $discussion->userid = 0;
                }
                //Create temp discussion record
                $temp_discussion->id = $newid;
                $temp_discussion->firstpost = $discussion->firstpost;
                $temp_discussion->userid = $discussion->userid;
                //Update discussion (only firstpost and userid will be changed)
                $status = update_record("forum_discussions",$temp_discussion);
                //echo "Updated firstpost ".$old_firstpost." to ".$temp_discussion->firstpost."<br>";                //Debug
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the forum_posts
    function forum_posts_restore_mods($new_forum_id,$discussion_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the posts array
        $posts = $info['#']['POSTS']['0']['#']['POST'];

        //Iterate over posts
        for($i = 0; $i < sizeof($posts); $i++) {
            $pos_info = $posts[$i];
            //traverse_xmlize($pos_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($pos_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($pos_info['#']['USERID']['0']['#']);

            //Now, build the FORUM_POSTS record structure
            $post->discussion = $discussion_id;
            $post->parent = backup_todb($pos_info['#']['PARENT']['0']['#']);
            $post->userid = backup_todb($pos_info['#']['USERID']['0']['#']);
            $post->created = backup_todb($pos_info['#']['CREATED']['0']['#']);
            $post->modified = backup_todb($pos_info['#']['MODIFIED']['0']['#']);
            $post->mailed = backup_todb($pos_info['#']['MAILED']['0']['#']);
            $post->subject = backup_todb($pos_info['#']['SUBJECT']['0']['#']);
            $post->message = backup_todb($pos_info['#']['MESSAGE']['0']['#']);
            $post->format = backup_todb($pos_info['#']['FORMAT']['0']['#']);
            $post->attachment = backup_todb($pos_info['#']['ATTACHMENT']['0']['#']);
            $post->totalscore = backup_todb($pos_info['#']['TOTALSCORE']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$post->userid);
            if ($user) {
                $post->userid = $user->new_id;
            }
     
            //The structure is equal to the db, so insert the forum_posts
            $newid = insert_record ("forum_posts",$post);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"forum_posts",$oldid,
                             $newid);

                //Get old forum id from backup_ids
                $rec = get_record("backup_ids","backup_code",$restore->backup_unique_code,
                                               "table_name","forum",
                                               "new_id",$new_forum_id);
                //Now copy moddata associated files
                $status = forum_restore_files ($rec->old_id, $new_forum_id,
                                                    $oldid, $newid, $restore);

                //Now restore post ratings
                $status = forum_ratings_restore_mods($newid,$pos_info,$restore);

            } else {
                $status = false;
            }
        }

        //Now we get every post in this discussion_id and recalculate its parent post
        $posts = get_records ("forum_posts","discussion",$discussion_id);
        if ($posts) {
            //Iterate over each post
            foreach ($posts as $post) {
                //Get its parent
                $old_parent = $post->parent;
                //Get its new post_id from backup_ids table
                $rec = backup_getid($restore->backup_unique_code,"forum_posts",$old_parent);
                if ($rec) {
                    //Put its new parent
                    $post->parent = $rec->new_id;
                } else {
                     $post->parent = 0;
                }
                //Create temp post record
                $temp_post->id = $post->id;
                $temp_post->parent = $post->parent;
                //echo "Updated parent ".$old_parent." to ".$temp_post->parent."<br>";                //Debug
                //Update post (only parent will be changed)
                $status = update_record("forum_posts",$temp_post);
            }
        }

        return $status;
    }

    //This function copies the forum related info from backup temp dir to course moddata folder,
    //creating it if needed and recoding everything (forum id and post id)
    function forum_restore_files ($oldforid, $newforid, $oldpostid, $newpostid, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $forum_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //First, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
  
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate forum directory
        if ($status) {
            $forum_path = $moddata_path."/forum";
            //Check it exists and create it
            $status = check_dir_exists($forum_path,true);
        }

        //Now locate the temp dir we are restoring from
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/forum/".$oldforid."/".$oldpostid;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/forum
        if ($status and $todo) {
            //First this forum id
            $this_forum_path = $forum_path."/".$newforid;
            $status = check_dir_exists($this_forum_path,true);
            //Now this post id
            $post_forum_path = $this_forum_path."/".$newpostid;
            //And now, copy temp_path to post_forum_path
            $status = backup_copy_file($temp_path, $post_forum_path);
        }

        return $status;
    }

    //This function restores the forum_ratings
    function forum_ratings_restore_mods($new_post_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the ratings array
        $ratings = $info['#']['RATINGS']['0']['#']['RATING'];

        //Iterate over ratings
        for($i = 0; $i < sizeof($ratings); $i++) {
            $rat_info = $ratings[$i];
            //traverse_xmlize($rat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($rat_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($rat_info['#']['USERID']['0']['#']);

            //Now, build the FORM_RATINGS record structure
            $rating->post = $new_post_id;
            $rating->userid = backup_todb($rat_info['#']['USERID']['0']['#']);
            $rating->time = backup_todb($rat_info['#']['TIME']['0']['#']);
            $rating->rating = backup_todb($rat_info['#']['POST_RATING']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$rating->userid);
            if ($user) {
                $rating->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the forum_ratings
            $newid = insert_record ("forum_ratings",$rating);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"forum_ratings",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

?>
