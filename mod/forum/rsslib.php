<?PHP  // $Id$
    //This file adds support to rss feeds generation

    //This function is the main entry point to forum
    //rss feeds generation. Foreach site forum with rss enabled
    //build one XML rss structure.
    function forum_rss_feeds() {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        //Check CFG->forum_enablerssfeeds
        } else if (empty($CFG->forum_enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (module configuration)";
            }
        //It's working so we start...
        } else {
            //Iterate over all forums
            if ($forums = get_records("forum")) {
                foreach ($forums as $forum) {
                    if (!empty($forum->rsstype) && !empty($forum->rssarticles) && $status) {
                        //Some debug...
                        if ($CFG->debug > 7) {
                            echo "ID: $forum->id->";
                        }
                        //Get the XML contents
                        $result = forum_rss_feed($forum);
                        //Save the XML contents to file
                        if (!empty($result)) {
                            $status = rss_save_file("forum",$forum,$result);
                        }
                        //Some debug...
                        if ($CFG->debug > 7) {
                            if (empty($result)) {
                                echo "(empty) ";
                            } else {
                                if (!empty($status)) {
                                    echo "OK ";
                                } else {
                                    echo "FAIL ";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }

    //This function return the XML rss contents about the forum record passed as parameter
    //It returns false if something is wrong
    function forum_rss_feed($forum) {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)"; 
            }           
        //Check CFG->forum_enablerssfeeds
        } else if (empty($CFG->forum_enablerssfeeds)) {
            //Some debug... 
            if ($CFG->debug > 7) {
                echo "DISABLED (module configuration)";
            }           
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
                    $header = rss_standard_header($forum->name,
                                                  $CFG->wwwroot."/mod/forum/view.php?f=".$forum->id,
                                                  $forum->intro);
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
    function forum_rss_feed_discussions($forum) {

        global $CFG;

        $items = array();

        if ($recs = get_records_sql ("SELECT d.id discussionid, 
                                             d.name discussionname, 
                                             u.id userid, 
                                             u.firstname userfirstname,
                                             u.lastname userlastname,
                                             p.message postmessage,
                                             p.created postcreated,
                                             p.format postformat
                                      FROM {$CFG->prefix}forum_discussions d,
                                           {$CFG->prefix}forum_posts p,
                                           {$CFG->prefix}user u
                                      WHERE d.forum = '$forum->id' AND
                                            p.discussion = d.id AND
                                            p.parent = 0 AND
                                            u.id = p.userid
                                      ORDER BY p.created desc")) {
            //Iterate over each discussion to get forum->rssarticles records
            $articlesleft = $forum->rssarticles;
            $item = NULL;
            $user = NULL;
            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->discussionname;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->postcreated;
                $item->link = $CFG->wwwroot."/mod/forum/discuss.php?d=".$rec->discussionid;
                $item->description = format_text($rec->postmessage,$rec->postformat,NULL,$forum->course);
                $items[] = $item;
                $articlesleft--;
                if ($articlesleft < 1) {
                    break;
                }
            }
        }
        return $items;
    }
    
    //This function returns "items" record array to be used to build the rss feed
    //for a Type=posts forum
    function forum_rss_feed_posts($forum) {

        global $CFG;

        $items = array();

        if ($recs = get_records_sql ("SELECT p.id postid,
                                             d.id discussionid,
                                             u.id userid,
                                             u.firstname userfirstname,
                                             u.lastname userlastname,
                                             p.subject postsubject,
                                             p.message postmessage,
                                             p.created postcreated,
                                             p.format postformat
                                      FROM {$CFG->prefix}forum_discussions d,
                                           {$CFG->prefix}forum_posts p,
                                           {$CFG->prefix}user u
                                      WHERE d.forum = '$forum->id' AND
                                            p.discussion = d.id AND
                                            u.id = p.userid
                                      ORDER BY p.created desc")) {
            //Iterate over each discussion to get forum->rssarticles records
            $articlesleft = $forum->rssarticles;
            $item = NULL;
            $user = NULL;
            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->postsubject;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->postcreated;
                $item->link = $CFG->wwwroot."/mod/forum/discuss.php?d=".$rec->discussionid."&parent=".$rec->postid;
                $item->description = format_text($rec->postmessage,$rec->postformat,NULL,$forum->course);
                $items[] = $item;
                $articlesleft--;
                if ($articlesleft < 1) {
                    break;
                }
            }
        }
        return $items;
    }
?>
