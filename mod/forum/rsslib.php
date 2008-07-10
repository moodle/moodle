<?php  // $Id$
    //This file adds support to rss feeds generation

    //This function is the main entry point to forum
    //rss feeds generation. Foreach site forum with rss enabled
    //build one XML rss structure.
    function forum_rss_feeds() {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            debugging('DISABLED (admin variables)');
        //Check CFG->forum_enablerssfeeds
        } else if (empty($CFG->forum_enablerssfeeds)) {
            debugging('DISABLED (module configuration)');
        //It's working so we start...
        } else {
            //Iterate over all forums
            if ($forums = get_records("forum")) {
                foreach ($forums as $forum) {
                    if (!empty($forum->rsstype) && !empty($forum->rssarticles) && $status) {

                        $filename = rss_file_name('forum', $forum);  // RSS file

                        //First let's make sure there is work to do by checking existing files
                        if (file_exists($filename)) {
                            if ($lastmodified = filemtime($filename)) {
                                if (!forum_rss_newstuff($forum, $lastmodified)) {
                                    continue;
                                }
                            }
                        }

                        //Ignore hidden forums
                        if (!instance_is_visible('forum',$forum)) {
                            if (file_exists($filename)) {
                                @unlink($filename);
                            }
                            continue;
                        }

                        mtrace("Updating RSS feed for ".format_string($forum->name,true).", ID: $forum->id");

                        //Get the XML contents
                        $result = forum_rss_feed($forum);
                        //Save the XML contents to file
                        if (!empty($result)) {
                            $status = rss_save_file("forum",$forum,$result);
                        }
                        if (debugging()) {
                            if (empty($result)) {
                                echo "ID: $forum->id-> (empty) ";
                            } else {
                                if (!empty($status)) {
                                    echo "ID: $forum->id-> OK ";
                                } else {
                                    echo "ID: $forum->id-> FAIL ";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }


    // Given a forum object, deletes the RSS file
    function forum_rss_delete_file($forum) {
        global $CFG;
        $rssfile = rss_file_name('forum', $forum);
        if (file_exists($rssfile)) {
            return unlink($rssfile);
        } else {
            return true;
        }
    }


    function forum_rss_newstuff($forum, $time) {
    // If there is new stuff in the forum since $time then this returns
    // true.  Otherwise it returns false.
        if ($forum->rsstype == 1) {
            $items = forum_rss_feed_discussions($forum, $time);
        } else {
            $items = forum_rss_feed_posts($forum, $time);
        }
        return (!empty($items));
    }

    //This function return the XML rss contents about the forum record passed as parameter
    //It returns false if something is wrong
    function forum_rss_feed($forum) {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            debugging("DISABLED (admin variables)");
        //Check CFG->forum_enablerssfeeds
        } else if (empty($CFG->forum_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
        //It's working so we start...
        } else {
            //Check the forum has rss activated
            if (!empty($forum->rsstype) && !empty($forum->rssarticles)) {
                //Depending of the forum->rsstype, we are going to execute, different sqls
                if ($forum->rsstype == 1) {    //Discussion RSS
                    $items = forum_rss_feed_discussions($forum);
                } else {                //Post RSS
                    $items = forum_rss_feed_posts($forum);

                }
                //Now, if items, we begin building the structure
                if (!empty($items)) {
                    //First all rss feeds common headers
                    $header = rss_standard_header(strip_tags(format_string($forum->name,true)),
                                                  $CFG->wwwroot."/mod/forum/view.php?f=".$forum->id,
                                                  format_string($forum->intro,true));
                    //Now all the rss items
                    if (!empty($header)) {
                        $articles = rss_add_items($items);
                    }
                    //Now all rss feeds common footers
                    if (!empty($header) && !empty($articles)) {
                        $footer = rss_standard_footer();
                    }
                    //Now, if everything is ok, concatenate it
                    if (!empty($header) && !empty($articles) && !empty($footer)) {
                        $status = $header.$articles.$footer;
                    } else {
                        $status = false;
                    }
                } else {
                    $status = false;
                }
            }
        }
        return $status;
    }

    //This function returns "items" record array to be used to build the rss feed
    //for a Type=discussions forum
    function forum_rss_feed_discussions($forum, $newsince=0) {

        global $CFG;

        $items = array();

        if ($newsince) {
            $newsince = " AND p.modified > '$newsince'";
        } else {
            $newsince = "";
        }

        if ($recs = get_records_sql ("SELECT d.id AS discussionid, 
                                             d.name AS discussionname, 
                                             u.id AS userid, 
                                             u.firstname AS userfirstname,
                                             u.lastname AS userlastname,
                                             p.message AS postmessage,
                                             p.created AS postcreated,
                                             p.format AS postformat
                                      FROM {$CFG->prefix}forum_discussions d,
                                           {$CFG->prefix}forum_posts p,
                                           {$CFG->prefix}user u
                                      WHERE d.forum = '$forum->id' AND
                                            p.discussion = d.id AND
                                            p.parent = 0 AND
                                            u.id = p.userid $newsince
                                      ORDER BY p.created desc", 0, $forum->rssarticles)) {

            $item = NULL;
            $user = NULL;

            $formatoptions = new object;
            $formatoptions->trusttext = true;

            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = format_string($rec->discussionname);
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->postcreated;
                $item->link = $CFG->wwwroot."/mod/forum/discuss.php?d=".$rec->discussionid;
                $item->description = format_text($rec->postmessage,$rec->postformat,$formatoptions,$forum->course);
                $items[] = $item;
            }
        }
        return $items;
    }

    //This function returns "items" record array to be used to build the rss feed
    //for a Type=posts forum
    function forum_rss_feed_posts($forum, $newsince=0) {

        global $CFG;

        $items = array();

        if ($newsince) {
            $newsince = " AND p.modified > '$newsince'";
        } else {
            $newsince = "";
        }

        if ($recs = get_records_sql ("SELECT p.id AS postid,
                                             d.id AS discussionid,
                                             d.name AS discussionname,
                                             u.id AS userid,
                                             u.firstname AS userfirstname,
                                             u.lastname AS userlastname,
                                             p.subject AS postsubject,
                                             p.message AS postmessage,
                                             p.created AS postcreated,
                                             p.format AS postformat
                                      FROM {$CFG->prefix}forum_discussions d,
                                           {$CFG->prefix}forum_posts p,
                                           {$CFG->prefix}user u
                                      WHERE d.forum = '$forum->id' AND
                                            p.discussion = d.id AND
                                            u.id = p.userid $newsince
                                      ORDER BY p.created desc", 0, $forum->rssarticles)) {

            $item = NULL;
            $user = NULL;

            $formatoptions = new object;
            $formatoptions->trusttext = true;

            require_once($CFG->libdir.'/filelib.php');

            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->category = $rec->discussionname;
                $item->title = $rec->postsubject;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->postcreated;
                $item->link = $CFG->wwwroot."/mod/forum/discuss.php?d=".$rec->discussionid."&parent=".$rec->postid;
                $item->description = format_text($rec->postmessage,$rec->postformat,$formatoptions,$forum->course);


                $post_file_area_name = str_replace('//', '/', "$forum->course/$CFG->moddata/forum/$forum->id/$rec->postid");
                $post_files = get_directory_list("$CFG->dataroot/$post_file_area_name");
                
                if (!empty($post_files)) {            
                    $item->attachments = array();
                    foreach ($post_files as $file) {                    
                        $attachment = new stdClass;
                        $attachment->url = get_file_url("$post_file_area_name/$file");
                        $attachment->length = filesize("$CFG->dataroot/$post_file_area_name/$file");
                        $item->attachments[] = $attachment;
                    }
                }

                $items[] = $item;
            }
        }
        return $items;
    }
?>
