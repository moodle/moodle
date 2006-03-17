<?php

    require_once($CFG->dirroot.'/lib/rsslib.php');


    // This function returns the icon (from theme) with the link to rss/file.php
    // needs some hacking to rss/file.php
    function blog_rss_print_link($filtertype, $filterselect, $tagid=0, $tooltiptext='') {

        global $CFG, $USER;

        if (empty($USER->id)) {
            $userid = 1;
        } else {
            $userid = $USER->id;
        }

        switch ($filtertype) {
            case 'site':
                $path = SITEID.'/'.$userid.'/blog/site/'.SITEID;
            break;
            case 'course':
                $path = $filterselect.'/'.$userid.'/blog/course/'.$filterselect;
            break;
            case 'group':
                $path = SITEID.'/'.$userid.'/blog/group/'.$filterselect;  
            break;
            case 'user':
                $path = SITEID.'/'.$userid.'/blog/user/'.$filterselect;
            break;
        }

        if ($tagid) {
            $path .= '/'.$tagid;
        }

        $path .= '/rss.xml';
        $rsspix = $CFG->pixpath .'/i/rss.gif';

        if ($CFG->slasharguments) {
            $path = $CFG->wwwroot.'/rss/file.php/'.$path;
        } else {
            $path = $CFG->wwwroot.'/rss/file.php?file='.$path;
        }
        print '<div align="right"><a href="'. $path .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="" /></a></div>';

    }


    // Generate any blog RSS feed via one function (called by ../rss/file.php)
    function blog_generate_rss_feed($type, $id, $tagid=0) {

        $filename = blog_rss_file_name($type, $id, $tagid);

        if (file_exists($filename)) {
            if (filemtime($filename) + 3600 > time()) {
                return $filename;   /// It's already done so we return cached version
            }
        }

        // Proceed to generate it

        switch ($type) {
           case 'site':
               if (blog_site_feeds($tagid)) {
                   return $filename;
               }
           break;
           case 'course':
               if ( blog_course_feed($id,$tagid)) {
                   return $filename;
               }
           break;
           case 'group':
               if ( blog_group_feed($id,$tagid)) {
                   return $filename;
               }
           break;
           case 'user':
               if ( blog_user_feed($id,$tagid)) {
                   return $filename;
               }
           break;
        }

        return false;   // Couldn't find it or make it
    }


    /* Rss files for blogs
     * 4 different ways to store feeds.
     * site - $CFG->dataroot/rss/blog/site/SITEID.xml
     * course - $CFG->dataroot/rss/blog/course/courseid.xml
     * group - $CFG->dataroot/rss/blog/group/groupid.xml
     * user - $CFG->dataroot/rss/blog/user/userid.xml
     */
    function blog_rss_file_name($type, $id, $tagid=0) {
        global $CFG;

        if ($tagid) {
            return "$CFG->dataroot/rss/blog/$type/$id/$tagid.xml";
        } else {
            return "$CFG->dataroot/rss/blog/$type/$id.xml";
        }
    }
    
    //This function saves to file the rss feed specified in the parameters
    function blog_rss_save_file($type, $id, $result) {
        global $CFG;

        $status = true;

        if (! $basedir = make_upload_directory ('rss/blogs/'. $type.'/'.$id)) {
            //Cannot be created, so error
            $status = false;
        }

        if ($status) {
            $file = blog_rss_file_name($type, $id, $tagid);
            $rss_file = fopen($file, "w");
            if ($rss_file) {
                $status = fwrite ($rss_file, $result);
                fclose($rss_file);
            } else {
                $status = false;
            }
        }
        return $status;
    }
     
    
    // Only 1 view, site level feeds
    function blog_site_feeds($tagid=0) {

        global $CFG;
        $status = true;

        //////$CFG->debug = true;

        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        }

        // It's working so we start...
        else {
            // Iterate over all data.
            $filename = blog_rss_file_name('site', SITEID, $tagid);  // RSS file
                // Get the most recent 20 posts
            $sql = 'SELECT p.* FROM '.$CFG->prefix.'post p,
                '.$CFG->prefix.'user u
                WHERE p.userid = u.id 
                AND (p.publishstate = \'site\' OR p.publishstate = \'public\')
                AND u.deleted = 0 ORDER BY lastmodified DESC LIMIT 0,20';

            $blogposts = get_records_sql($sql);

            // Now all the rss items.
            $items = array();

            foreach ($blogposts as $blogpost) {
                $item = null;
                $temp = array();
                array_push($temp, $blogpost);

                $user = get_record('user','id',$blogpost->userid);
                $item->author = fullname($user);
                $item->title = $blogpost->subject;
                $item->pubdate = $blogpost->lastmodified;
                $item->link = $CFG->wwwroot.'/blog/index.php?postid='.$blogpost->id;
                $item->description = format_text($blogpost->summary, $blogpost->format);
                array_push($items, $item);
            }

            // First all rss feeds common headers.
            $header = rss_standard_header(format_string('siteblog',true),
                                                      $CFG->wwwroot.'/blog/index.php',
                                                      format_string('intro',true));

            if (!empty($header)) {
                $articles = rss_add_items($items);
            }

            // Now all rss feeds common footers.
            if (!empty($header) && !empty($articles)) {
                $footer = rss_standard_footer();
            }
            // Now, if everything is ok, concatenate it.
            if (!empty($header) && !empty($articles) && !empty($footer)) {
                $rss = $header.$articles.$footer;

                //Save the XML contents to file.
                $status = blog_rss_save_file('site', SITEID, $rss);
            }
            else {
                $status = false;
            }
        }
        return $status;
    }


    /// Generate the feeds for all courses
    function blog_course_feeds() {

        $courses = get_records('course');
        foreach ($courses as $course) {
            if ($course->id != SITEID) {
                blog_course_feed($course);
            }
        }
    }

    // takes in course object from db
    function blog_course_feed($course, $tagid=0) {

        global $CFG;
        $status = true;

        ////$CFG->debug = true;

        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        }

        // It's working so we start...
        else {
            // Iterate over all data.
            $filename = blog_rss_file_name('course', $course->id, $tagid);  // RSS file
                // Get the most recent 20 posts

            $sql = '(SELECT p.* FROM '.$CFG->prefix.'post p, '
                            .$CFG->prefix.'user_students u
                            WHERE p.userid = u.userid
                            AND u.course = '.$course->id.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\'))

                            UNION

                            (SELECT p.* FROM '.$CFG->prefix.'post p, '
                            .$CFG->prefix.'user_teachers u
                            WHERE p.userid = u.userid
                            AND u.course = '.$course->id.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\')) ORDER BY lastmodified DESC LIMIT 0,20';

            $blogposts = get_records_sql($sql);

            // Now all the rss items.
            $items = array();

            foreach ($blogposts as $blogpost) {
                $item = null;
                $temp = array();
                array_push($temp, $blogpost);

                $user = get_record('user','id',$blogpost->userid);
                $item->author = fullname($user);
                $item->title = $blogpost->subject;
                $item->pubdate = $blogpost->lastmodified;
                $item->link = $CFG->wwwroot.'/blog/index.php?postid='.$blogpost->id;
                $item->description = format_text($blogpost->summary, $blogpost->format);
                array_push($items, $item);
            }

            // First all rss feeds common headers.
            $header = rss_standard_header(format_string('courseblog',true),
                                                      $CFG->wwwroot.'/blog/index.php',
                                                      format_string('intro',true));
                                                      
            if (!empty($header)) {
                $articles = rss_add_items($items);
            }

            // Now all rss feeds common footers.
            if (!empty($header) && !empty($articles)) {
                $footer = rss_standard_footer();
            }
            // Now, if everything is ok, concatenate it.
            if (!empty($header) && !empty($articles) && !empty($footer)) {
                $rss = $header.$articles.$footer;

                //Save the XML contents to file.
                $status = blog_rss_save_file('course',$course->id, $rss);
            }
            else {
                $status = false;
            }
        }
        return $status;
    }
    
    
    function blog_group_feeds() {

        $groups = get_records('groups');
        foreach ($groups as $group) {
            blog_group_feed($group);
        }
    }

    // takes in course object from db
    function blog_group_feed($group, $tagid=0) {

        global $CFG;
        $status = true;

        //$CFG->debug = true;

        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        }

        // It's working so we start...
        else {
            // Iterate over all data.
            $filename = blog_rss_file_name('group', $group->id, $tagid);  // RSS file
                // Get the most recent 20 posts

            $sql= 'SELECT p.* FROM '.$CFG->prefix.'post p, '
                .$CFG->prefix.'groups_members m
                WHERE p.userid = m.userid
                AND m.groupid = '.$group->id.'
                AND (p.publishstate = \'site\' OR p.publishstate = \'public\') ORDER BY lastmodified DESC LIMIT 0,20';

            

            // Now all the rss items.
            $items = array();
            if ($blogposts = get_records_sql($sql)) {
                foreach ($blogposts as $blogpost) {
                    $item = null;
                    $temp = array();
                    array_push($temp, $blogpost);

                    $user = get_record('user','id',$blogpost->userid);
                    $item->author = fullname($user);
                    $item->title = $blogpost->subject;
                    $item->pubdate = $blogpost->lastmodified;
                    $item->link = $CFG->wwwroot.'/blog/index.php?postid='.$blogpost->id;
                    $item->description = format_text($blogpost->summary, $blogpost->format);
                    array_push($items, $item);
                }
            }

            // First all rss feeds common headers.
            $header = rss_standard_header(format_string('groupblog',true),
                                                      $CFG->wwwroot.'/blog/index.php',
                                                      format_string('intro',true));

            if (!empty($header)) {
                $articles = rss_add_items($items);
            }

            // Now all rss feeds common footers.
            if (!empty($header) && !empty($articles)) {
                $footer = rss_standard_footer();
            }
            // Now, if everything is ok, concatenate it.
            if (!empty($header) && !empty($articles) && !empty($footer)) {
                $rss = $header.$articles.$footer;

                //Save the XML contents to file.
                $status = blog_rss_save_file('group',$group->id, $rss);
            }
            else {
                $status = false;
            }
        }
        return $status;
    }
    
    
    function blog_user_feeds() {

    $users = get_records('user');
        foreach ($users as $user) {
            blog_user_feed($user);
        }
    }

    // takes in course object from db
    function blog_user_feed($user, $tagid=0) {

        global $CFG;
        $status = true;

        ////$CFG->debug = true;

        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        }

        // It's working so we start...
        else {
            // Iterate over all data.
            $filename = blog_rss_file_name('user', $user->id, $tagid);  // RSS file
                // Get the most recent 20 posts

            $sql = 'SELECT p.* FROM '.$CFG->prefix.'post p, '
                        .$CFG->prefix.'user u
                        WHERE p.userid = u.id
                        AND u.id = '.$user->id.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\') ORDER BY lastmodified DESC LIMIT 0,20';

            

            // Now all the rss items.
            $items = array();
            if ($blogposts = get_records_sql($sql)) {
                foreach ($blogposts as $blogpost) {
                    $item = null;
                    $temp = array();
                    array_push($temp, $blogpost);

                    $user = get_record('user','id',$blogpost->userid);
                    $item->author = fullname($user);
                    $item->title = $blogpost->subject;
                    $item->pubdate = $blogpost->lastmodified;
                    $item->link = $CFG->wwwroot.'/blog/index.php?postid='.$blogpost->id;
                    $item->description = format_text($blogpost->summary, $blogpost->format);
                    array_push($items, $item);
                }
            }
            // First all rss feeds common headers.
            $header = rss_standard_header(format_string('userblog',true),
                                                      $CFG->wwwroot.'/blog/index.php',
                                                      format_string('intro',true));

            if (!empty($header)) {
                $articles = rss_add_items($items);
            }

            // Now all rss feeds common footers.
            if (!empty($header) && !empty($articles)) {
                $footer = rss_standard_footer();
            }
            // Now, if everything is ok, concatenate it.
            if (!empty($header) && !empty($articles) && !empty($footer)) {
                $rss = $header.$articles.$footer;

                //Save the XML contents to file.
                $status = blog_rss_save_file('user',$user->id, $rss);
            }
            else {
                $status = false;
            }
        }
        return $status;
    }
?>
