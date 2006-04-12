<?php //$Id$

    /**
     * Library of functions and constants for blog
     */

    require_once($CFG->libdir .'/blocklib.php');
    require_once($CFG->libdir .'/pagelib.php');
    require_once('rsslib.php');
    require_once($CFG->dirroot .'/blog/blogpage.php');

    /* blog access level constant declaration */
    define ('BLOG_USER_LEVEL', 1);
    define ('BLOG_GROUP_LEVEL', 2);
    define ('BLOG_COURSE_LEVEL', 3);
    define ('BLOG_SITE_LEVEL', 4);
    define ('BLOG_GLOBAL_LEVEL', 5);

    /**
     * Definition of blogcourse page type (blog page with course id present).
     */
    //not used at the moment, and may not need to be
    define('PAGE_BLOG_COURSE_VIEW', 'blog_course-view');

    $BLOG_YES_NO_MODES = array ( '0'  => get_string('no'),
                                 '1' => get_string('yes') );

    //set default setting for $CFG->blog_* vars used by blog's blocks
    //if they are not already. Otherwise errors are thrown
    //when an attempt is made to use an empty var.
    if (empty($SESSION->blog_editing_enabled)) {
        $SESSION->blog_editing_enabled = false;
    }

    /**
     * blog_user_has_rights - returns true if user is the blog's owner or a moodle admin.
     *
     * @param BlogInfo blogInfo - a BlogInfo object passed by reference. This object represents the blog being accessed.
     * @param int uid - numeric user id of the user whose rights are being tested against this blogInfo. If no uid is specified then the uid of the currently logged in user will be used.
     */
    function blog_user_has_rights($entryID, $uid='') {
        global $USER;

        if ($uid == '') {
            if ( isset($USER) && isset($USER->id) ) {
                $uid = $USER->id;
            }
        }
        if ($uid == '') {
            //if uid is still empty then the user is not logged in
            return false;
        }
        if (blog_is_blog_admin($uid) || isadmin()) {
            return true;
        }
        $blogEntry = get_record('post','id',$entryID);

        return ($blogEntry->userid == $uid);

    }

    /**
     * Determines whether a user is an admin for a blog
     * @param int $blog_userid The id of the blog being checked
     */
    function blog_is_blog_admin($blog_userid) {
        global $USER;

        //moodle admins are admins
        if (isadmin()) {
            return true;
        }
        if ( empty($USER) || !isset($USER->id) ) {
            return false;
        }
        if ( empty($blog_userid)) {
            return true;
        }

        // Return true if the user is an admin for this blog
        if ($blog_userid == $USER->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adaptation of isediting in moodlelib.php for blog module
     * @return bool
     */
    function blog_isediting() {
        global $SESSION;
        if (! isset($SESSION->blog_editing_enabled)) {
            $SESSION->blog_editing_enabled = false;
        }
        return ($SESSION->blog_editing_enabled);
    }

    /**
     *  This function is in lib and not in BlogInfo because entries being searched
     *   might be found in any number of blogs rather than just one.
     *
     *   $@param BlogFilter blogFilter - a BlogFilter object containing the settings for finding appropriate entries for display
     */
    function blog_print_html_formatted_entries($userid, $postid, $limit, $start, $filtertype, $filterselect, $tagid, $tag, $filtertype, $filterselect) {
        global $CFG, $USER;

        $blogpage = optional_param('blogpage', 0, PARAM_INT);
        $bloglimit = get_user_preferences('blogpagesize',10); // expose as user pref when MyMoodle comes around

        // First let's see if the batchpublish form has submitted data
        $post = data_submitted();

        $morelink = '<br />&nbsp;&nbsp;';
        // show personal or general heading block as applicable
        echo '<div class="headingblock header blog">';
        //show blog title - blog tagline
        print "<br />";    //don't print title. blog_get_title_text();

        $blogEntries = fetch_entries($userid, $postid, $limit, $start, $filtertype, $filterselect, $tagid, $tag, $sort='lastmodified DESC', $limit=true);

        //$blogFilter->get_filtered_entries();
        // show page next/previous links if applicable

        print_paging_bar(get_viewable_entry_count($userid, $postid, $limit, $start,$filtertype, $filterselect, $tagid, $tag, $sort='lastmodified DESC'), $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect), 'blogpage');

        if ($CFG->enablerssfeeds) {
            blog_rss_print_link($filtertype, $filterselect, $tag);
        }
        print '</div>';

        if (isloggedin() && !isguest()) {
            //the user's blog is enabled and they are viewing their own blog
            $addlink = '<div align="center">';
            $addlink .= '<a href="'.$CFG->wwwroot .'/blog/edit.php'.'">'. get_string('addnewentry', 'blog').'</a>';
            $addlink .='</div>';
            echo $addlink;
        }

        if (isset($blogEntries) ) {

            $count = 0;
            foreach ($blogEntries as $blogEntry) {
                blog_print_entry($blogEntry, 'list', $filtertype, $filterselect); //print this entry.
                $count++;
            }
            if (!$count) {
                print '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';

            }

            print $morelink.'<br />'."\n";

            return;
        }

        $output = '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';
        print $output;
    }

    /**
     *  This function is in lib and not in BlogInfo because entries being searched
     *   might be found in any number of blogs rather than just one.
     *
     * This function builds an array which can be used by the included
     * template file, making predefined and nicely formatted variables available
     * to the template. Template creators will not need to become intimate
     * with the internal objects and vars of moodle blog nor will they need to worry
     * about properly formatting their data
     *
     *   @param BlogEntry blogEntry - a hopefully fully populated BlogEntry object
     *   @param string viewtype Default is 'full'. If 'full' then display this blog entry
     *     in its complete form (eg. archive page). If anything other than 'full'
     *     display the entry in its abbreviated format (eg. index page)
     */
    function blog_print_entry($blogEntry, $viewtype='full', $filtertype='', $filterselect='', $mode='loud') {

        global $USER, $CFG, $course, $ME;

        $template['body'] = get_formatted_entry_body($blogEntry->summary, $blogEntry->format);
        $template['title'] = '<a name="'. $blogEntry->subject .'"></a>';
        //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
        $template['title'] .= '<span class="nolink">'. stripslashes_safe($blogEntry->subject);
        $template['title'] .= '</span>';
        $template['userid'] = $blogEntry->userid;
        $template['author'] = fullname(get_record('user','id',$blogEntry->userid));
        $template['lastmod'] = userdate($blogEntry->lastmodified);
        $template['created'] = userdate($blogEntry->created);
        $template['publishstate'] = $blogEntry->publishstate;

        /// preventing user to browse blogs that they aren't supposed to see
        if (!blog_user_can_view_user_post($template['userid'])) {
            error ('you can not view this post');
        }

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        $user = get_record('user','id',$template['userid']);

        /// Start printing of the blog

        echo '<div align="center"><table cellspacing="0" class="forumpost blogpost blog_'.$template['publishstate'].'" width="100%">';

        echo '<tr class="header"><td class="picture left">';
        print_user_picture($template['userid'], SITEID, $user->picture);
        echo '</td>';

        echo '<td class="topic starter"><div class="subject">'.$template['title'].'</div><div class="author">';
        $fullname = fullname($user, isteacher($template['userid']));
        $by->name =  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                    $user->id.'&amp;course='.$course->id.'">'.$fullname.'</a>';
        $by->date = $template['lastmod'];
        print_string('bynameondate', 'forum', $by);
        echo '</div></td></tr>';

        echo '<tr><td class="left side">';

    /// Actual content

        echo '</td><td class="content">'."\n";

        // Print whole message
        echo format_text($template['body']);

    /// Links to tags

        if ($blogtags = get_records_sql('SELECT t.* FROM '.$CFG->prefix.'tags t, '.$CFG->prefix.'blog_tag_instance ti
                                     WHERE t.id = ti.tagid
                                     AND ti.entryid = '.$blogEntry->id)) {
            echo '<p />';
            print_string('tags');
            echo ': ';
            foreach ($blogtags as $key => $blogtag) {
                $taglist[] = '<a href="index.php?courseid='.$course->id.'&amp;filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;tagid='.$blogtag->id.'">'.$blogtag->text.'</a>';
            }
            echo implode(', ', $taglist);
        }

    /// Commands

        echo '<div class="commands">';

        if (isset($USER->id)) {
            if (($template['userid'] == $USER->id) or isadmin()) {
                    echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?editid='.$blogEntry->id.'&amp;sesskey='.sesskey().'">'.$stredit.'</a>';
            }

            if (($template['userid'] == $USER->id) or isadmin()) {
                echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?act=del&amp;editid='.$blogEntry->id.'&amp;sesskey='.sesskey().'">'.$strdelete.'</a>';
            }
        }

        echo '</div>';

        echo '</td></tr></table></div>'."\n\n";

    }

    /**
     * Use this function to retrieve a list of publish states available for
     * the currently logged in user.
     *
     * @return array This function returns an array ideal for sending to moodles'
     *                choose_from_menu function.
     */
    function blog_applicable_publish_states($courseid='') {
        global $CFG;

        // everyone gets draft access
        $options = array ( 'draft' => get_string('publishtonoone', 'blog') );
        $options['site'] = get_string('publishtosite', 'blog');
        $options['public'] = get_string('publishtoworld', 'blog');

        return $options;
    }

    /// Checks to see if a user can view the blogs of another user.
    /// He can do so, if he is admin, in any same non-spg course,
    /// or spg group, but same group member
    function blog_user_can_view_user_post($targetuserid) {

        global $CFG;

        $canview = 0;    //bad start

        if (isadmin()) {
            return true;
        }

        $usercourses = get_my_courses($targetuserid);
        foreach ($usercourses as $usercourse) {
                /// if viewer and user sharing same non-spg course, then grant permission
            if (groupmode($usercourse)!= SEPARATEGROUPS){
                if (isstudent($usercourse->id) || isteacher($usercourse->id)) {
                    $canview = 1;
                    return $canview;
                }
            } else {
                /// now we need every group the user is in, and check to see if view is a member
                if ($usergroups = user_group($usercourse->id, $targetuserid)) {
                    foreach ($usergroups as $usergroup) {
                        if (ismember($usergroup->id)) {
                            $canview = 1;
                            return $canview;
                        }
                    }
                }
            }
        }

        if (!$canview && $CFG->bloglevel < BLOG_SITE_LEVEL) {
            error ('you can not view this user\'s blogs');
        }

        return $canview;
    }


    /// moved from BlogEntry class
    function get_formatted_entry_body($body, $format) {
        global $CFG;
        include_once($CFG->libdir .'/weblib.php');
        if ($format) {
            return format_text($body, $format);
        }
        return stripslashes_safe($body);
    }


    /// moved from BlogEntry class
    function get_publish_to_menu($blogEntry, $return=true, $includehelp=true) {
        $menu = '';
        if (user_can_change_publish_state($blogEntry) && blog_isediting() ) {
            $menu .= '<div class="publishto">'. get_string('publishto', 'blog').': ';
            $options = blog_applicable_publish_states();
            $menu .= choose_from_menu($options, $blogEntry->userid .'-'. $blogEntry->id, $blogEntry->publishstate, '', '', '0', true);
            $menu .= "\n".'</div>'."\n";
            /// batch publish might not be needed
            if ($includehelp) {
                $menu .= helpbutton('batch_publish', get_string('batchpublish', 'blog'), 'blog', true, false, '', true);
            }
        }

        if ($return) {
            return $menu;
        }
        print $menu;
    }


    /**
    * This function will determine if the user is logged in and
    * able to make changes to the publish state of this entry
    *
    * @return bool True if user is allowed to change publish state
    */
    function user_can_change_publish_state($blogEntry) {
    // figure out who the currently logged in user is.
    // to change any publish state one must be logged in
        global $USER;
        if ( !isset($USER) || empty($USER) || !isset($USER->id) ) {
                // only site members are allowed to edit entries
            return 'Only site members are allowed to edit entries';
        } else {
            $uid = $USER->id;
        }
        if ( ($uid == $blogEntry->userid) || (blog_is_blog_admin($blogEntry->userid)) || (isadmin())) {
            return true;
        }
        return false;
    }

/// Filter Class functions

    function fetch_entries($userid, $postid='', $fetchlimit=10, $fetchstart='', $filtertype='', $filterselect='', $tagid='', $tag ='', $sort='lastmodified DESC', $limit=true) {

        global $CFG, $USER;

        if (!isset($USER->id)) {
            $USER->id = 0;    //hack, for guests
        }

        /// set the tag id for searching
        if ($tagid) {
            $tag = $tagid;
        } else if ($tag) {
            if ($tagrec = get_record_sql('SELECT * FROM '.$CFG->prefix.'tags WHERE text LIKE "'.$tag.'"')) {
                $tag = $tagrec->id;
            } else {
                $tag = -1;    //no records found
            }
        }

        // If we have specified an ID
        // Just return 1 entry
        if ($postid) {

            if ($post = get_record('post', 'id', $postid)) {

                if ($user = get_record('user', 'id', $post->userid)) {
                    $post->email = $user->email;
                    $post->firstname = $user->firstname;
                    $post->lastname = $user->lastname;
                }

                $this->filtered_entries = $post;
                return $this->filtered_entries;
            }
        }

        if ($tag) {
            $tagtablesql = $CFG->prefix.'blog_tag_instance bt, ';
            $tagquerysql = ' AND bt.entryid = p.id AND bt.tagid = '.$tag.' ';
        } else {
            $tagtablesql = '';
            $tagquerysql = '';
        }


        /****************************************
         * depending on the type, there are 4   *
         * different possible sqls              *
         ****************************************/

        $requiredfields = 'p.*, u.firstname,u.lastname,u.email';

        switch ($filtertype) {

            case 'site':

                if (!isguest() && isloggedin()) {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                            AND u.deleted = 0';

                } else {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND p.publishstate = \'public\'
                            AND u.deleted = 0';
                }

            break;

            case 'course':
                if ($filterselect != SITEID) {
                    $SQL = '(SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user_students s, '.$CFG->prefix.'user u
                            WHERE p.userid = s.userid '.$tagquerysql.'
                            AND s.course = '.$filterselect.'
                            AND u.id = p.userid
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.'))

                            UNION

                            (SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user_teachers t, '.$CFG->prefix.'user u
                            WHERE p.userid = t.userid '.$tagquerysql.'
                            AND t.course = '.$filterselect.'
                            AND u.id = p.userid
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.'))';    //this will break for postgres, i think
                } else {

                    if (isloggedin()) {

                        $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                                AND u.deleted = 0';

                    } else {

                        $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND p.publishstate = \'public\'
                                AND u.deleted = 0';
                    }

                }

            break;

            case 'group':

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'groups_members m, '.$CFG->prefix.'user u
                        WHERE p.userid = m.userid '.$tagquerysql.'
                        AND u.id = p.userid
                        AND m.groupid = '.$filterselect.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';

            break;

            case 'user':

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'user u
                        WHERE p.userid = u.id '.$tagquerysql.'
                        AND u.id = '.$filterselect.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';

            break;


        }

        if ($fetchstart !== '' && $limit) {
            $limit = sql_paging_limit($fetchstart, $fetchlimit);
        } else {
            $limit = '';
        }

        $orderby = ' ORDER BY '. $sort .' ';

        //echo 'Debug: BlogFilter fetch_entries() sql="'. $SQL . $orderby . $limit .'"<br />'. $this->categoryid; //debug

        $records = get_records_sql($SQL . $orderby . $limit);

//        print_object($records); //debug

        if (empty($records)) {
            return array();
        }

        return $records;
    }

    /**
     * get the count of viewable entries, easiest way is to count fetch_entries
     * this is used for print_paging_bar
     */
    function get_viewable_entry_count($userid, $postid='', $fetchlimit=10, $fetchstart='', $filtertype='', $filterselect='', $tagid='', $tag ='', $sort='lastmodified DESC') {

        $blogEntries = fetch_entries($userid, $postid, $fetchlimit, $fetchstart,$filtertype, $filterselect, $tagid, $tag, $sort='lastmodified DESC', false);
        return count($blogEntries);
    }
    
    /// Find the base url from $_GET variables
    function get_baseurl($filtertype, $filterselect) {

        $getcopy  = $_GET;

        unset($getcopy['blogpage']);

        $strippedurl = strip_querystring(qualified_me());
        if(!empty($getcopy)) {
            $first = false;
            $querystring = '';
            foreach($getcopy as $var => $val) {
                if(!$first) {
                    $first = true;
                    if ($var != 'filterselect' && $var != 'filtertype') {
                        $querystring .= '?'.$var.'='.$val;
                        $hasparam = true;
                    } else {
                        $querystring .= '?';
                    }
                } else {
                    if ($var != 'filterselect' && $var != 'filtertype') {
                    $querystring .= '&amp;'.$var.'='.$val;
                    $hasparam = true;
                    }
                }
            }
            if (isset($hasparam)) {
                $querystring .= '&amp;';
            } else {
                $querystring = '?';
            }
        } else {
            $querystring = '?';
        }

        return strip_querystring(qualified_me()) . $querystring. 'filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;';

    }
?>
