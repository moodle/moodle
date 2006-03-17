<?php

    require_once($CFG->dirroot.'/lib/rsslib.php');


    // This function returns the icon (from theme) with the link to rss/file.php
    // needs some hacking to rss/file.php
    function blog_rss_print_link($filtertype, $filterselect, $tooltiptext='') {

        global $CFG, $USER;

        static $pixpath = '';
        static $rsspath = '';
        $rsspix = $CFG->pixpath .'/i/rss.gif';

        if ($CFG->slasharguments) {
            $rsspath = $CFG->wwwroot.'/rss/file.php/blog/'.$filtertype.'/'.$filterselect.'/rss.xml';
        } else {
            $rsspath = $CFG->wwwroot.'/rss/file.php?file=/blog/'.$filtertype.'/'.$filterselect.'/rss.xml';
        }
        print '<div align="right"><a href="'. $rsspath .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="" /></a></div>';

    }

    // This file adds support to rss feeds generation
    // This function is the main entry point to database module
    // rss feeds generation. Foreach database with rss enabled
    // build one XML rss structure.
    function blog_rss_feeds() {

        blog_site_feeds();    //generate site level feeds, last 20 entries?
        blog_course_feeds();    //generate all course level feeds, last 20 entries
        blog_group_feeds();    //generate all group level feeds, last 20 entries
        blog_user_feeds();    //generate all user level feeds, last 20 entries
        
    }


    function blog_generate_rss_feed($type, $id, $tag='') {
        switch ($type) {
           case 'site':
               return blog_site_feeds($tag);
           break;
           case 'course':
               return blog_course_feed($id,$tag);
           break;
           case 'group':
               return blog_group_feed($id,$tag);
           break;
           case 'user':
               return blog_user_feed($id,$tag);
           break;
        }

        return false;
    }

    /* Rss files for blogs
     * 4 different ways to store feeds.
     * site - $CFG->dataroot/rss/blogs/site/SITEID.xml
     * course - $CFG->dataroot/rss/blogs/course/courseid.xml
     * group - $CFG->dataroot/rss/blogs/group/groupid.xml
     * user - $CFG->dataroot/rss/blogs/user/userid.xml
     */
    function blog_rss_file_name($type, $id) {
        global $CFG;
        $filename = "$CFG->dataroot/rss/blog/$type/$id/rss.xml";
        return $filename;
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
            $file = blog_rss_file_name($type, $id);
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
    function blog_site_feeds($tag='') {

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
            $filename = blog_rss_file_name('site', SITEID);  // RSS file
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
    function blog_course_feed($course, $tag='') {

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
            $filename = blog_rss_file_name('course', $course->id);  // RSS file
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
    function blog_group_feed($group, $tag='') {

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
            $filename = blog_rss_file_name('group', $group->id);  // RSS file
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
    function blog_user_feed($user, $tag='') {

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
            $filename = blog_rss_file_name('user', $user->id);  // RSS file
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
