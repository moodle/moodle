<?php //$Id$

    /**
     * Library of functions and constants for blog
     */
    require_once($CFG->libdir .'/blocklib.php');
    require_once($CFG->libdir .'/pagelib.php');
    require_once($CFG->dirroot .'/blog/rsslib.php');
    require_once($CFG->dirroot .'/blog/blogpage.php');
    require_once($CFG->dirroot.'/tag/lib.php');

    /**
     * Definition of blogcourse page type (blog page with course id present).
     */
    //not used at the moment, and may not need to be
    define('PAGE_BLOG_COURSE_VIEW', 'blog_course-view');


    /**
     * Checks to see if user has visited blogpages before, if not, install 2
     * default blocks (blog_menu and blog_tags).
     */
    function blog_check_and_install_blocks() {
        global $USER;
        if (isloggedin() && !isguest()) {
            // if this user has not visited this page before
            if (!get_user_preferences('blogpagesize')) {
                // find the correct ids for blog_menu and blog_from blocks
                $menublock = get_record('block','name','blog_menu');
                $tagsblock = get_record('block','name','blog_tags');
                // add those 2 into block_instance page

                // add blog_menu block
                $newblock = new object();
                $newblock->blockid  = $menublock->id;
                $newblock->pageid   = $USER->id;
                $newblock->pagetype = 'blog-view';
                $newblock->position = 'r';
                $newblock->weight   = 0;
                $newblock->visible  = 1;
                insert_record('block_instance', $newblock);

                // add blog_tags menu
                $newblock -> blockid = $tagsblock->id;
                $newblock -> weight  = 1;
                insert_record('block_instance', $newblock);

                // finally we set the page size pref
                set_user_preference('blogpagesize', 10);
            }
        }
    }


    /**
     * Adaptation of isediting in moodlelib.php for blog module
     * @return bool
     */
    function blog_isediting() {
        global $SESSION;

        return !empty($SESSION->blog_editing_enabled);
    }


    /**
     *  This function is in lib and not in BlogInfo because entries being searched
     *   might be found in any number of blogs rather than just one.
     *
     *   $@param ...
     */
    function blog_print_html_formatted_entries($postid, $filtertype, $filterselect, $tagid, $tag) {

        global $CFG, $USER;

        $blogpage  = optional_param('blogpage', 0, PARAM_INT);
        $bloglimit = optional_param('limit', get_user_preferences('blogpagesize', 10), PARAM_INT);
        $start     = $blogpage * $bloglimit;

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        $morelink = '<br />&nbsp;&nbsp;';

        $totalentries = get_viewable_entry_count($postid, $bloglimit, $start, $filtertype, $filterselect, $tagid, $tag, $sort='created DESC');
        $blogEntries = blog_fetch_entries($postid, $bloglimit, $start, $filtertype, $filterselect, $tagid, $tag, $sort='created DESC', true);

        print_paging_bar($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect), 'blogpage');

        if ($CFG->enablerssfeeds) {
            blog_rss_print_link($filtertype, $filterselect, $tag);
        }

        if (has_capability('moodle/blog:create', $sitecontext)) {
            //the user's blog is enabled and they are viewing their own blog
            $addlink = '<div class="addbloglink">';
            $addlink .= '<a href="'.$CFG->wwwroot .'/blog/edit.php?action=add'.'">'. get_string('addnewentry', 'blog').'</a>';
            $addlink .= '</div>';
            echo $addlink;
        }

        if ($blogEntries) {

            $count = 0;
            foreach ($blogEntries as $blogEntry) {
                blog_print_entry($blogEntry, 'list', $filtertype, $filterselect); //print this entry.
                $count++;
            }

            print_paging_bar($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect), 'blogpage');

            if (!$count) {
                print '<br /><div style="text-align:center">'. get_string('noentriesyet', 'blog') .'</div><br />';

            }

            print $morelink.'<br />'."\n";
            return;
        }

        $output = '<br /><div style="text-align:center">'. get_string('noentriesyet', 'blog') .'</div><br />';

        print $output;

    }


    /**
     * This function is in lib and not in BlogInfo because entries being searched
     * might be found in any number of blogs rather than just one.
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

        global $USER, $CFG, $COURSE, $ME;

        $template['body'] = format_text($blogEntry->summary, $blogEntry->format);
        $template['title'] = '<a id=b"'. s($blogEntry->id) .' /">';
        //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
        $template['title'] .= '<span class="nolink">'. format_string($blogEntry->subject) .'</span>';
        $template['userid'] = $blogEntry->userid;
        $template['author'] = fullname(get_record('user','id',$blogEntry->userid));
        $template['created'] = userdate($blogEntry->created);

        if($blogEntry->created != $blogEntry->lastmodified){
            $template['lastmod'] = userdate($blogEntry->lastmodified);
        }
        
        $template['publishstate'] = $blogEntry->publishstate;

        /// preventing user to browse blogs that they aren't supposed to see
        /// This might not be too good since there are multiple calls per page

        /*
        if (!blog_user_can_view_user_post($template['userid'])) {
            error ('you can not view this post');
        }*/

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        $user = get_record('user','id',$template['userid']);

        /// Start printing of the blog

        echo '<table cellspacing="0" class="forumpost blogpost blog'.$template['publishstate'].'" width="100%">';

        echo '<tr class="header"><td class="picture left">';
        print_user_picture($user, SITEID, $user->picture);
        echo '</td>';

        echo '<td class="topic starter"><div class="subject">'.$template['title'].'</div><div class="author">';
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $COURSE->id)));
        $by = new object();
        $by->name =  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                    $user->id.'&amp;course='.$COURSE->id.'">'.$fullname.'</a>';
        $by->date = $template['created'];
        print_string('bynameondate', 'forum', $by);
        echo '</div></td></tr>';

        echo '<tr><td class="left side">';

    /// Actual content

        echo '</td><td class="content">'."\n";

        if ($blogEntry->attachment) {
            echo '<div class="attachments">';
            $attachedimages = blog_print_attachments($blogEntry);
            echo '</div>';
        } else {
            $attachedimages = '';
        }

        switch ($template['publishstate']) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
            break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
            break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
            break;
            default:
                $blogtype = '';
            break;

        }

        echo '<div class="audience">'.$blogtype.'</div>';

        // Print whole message
        echo $template['body'];

        /// Print attachments
        echo $attachedimages;
    /// Links to tags

        if ( !empty($CFG->usetags) && ($blogtags = tag_get_tags_csv('post', $blogEntry->id)) ) {
            echo '<div class="tags">';
            if ($blogtags) {
                print(get_string('tags', 'tag') .': '. $blogtags);
           }
            echo '</div>';
        }

    /// Commands

        echo '<div class="commands">';

        if (blog_user_can_edit_post($blogEntry)) {
            echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?action=edit&amp;id='.$blogEntry->id.'">'.$stredit.'</a>';
            echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?action=delete&amp;id='.$blogEntry->id.'">'.$strdelete.'</a> | ';
        }

        echo '<a href="'.$CFG->wwwroot.'/blog/index.php?postid='.$blogEntry->id.'">'.get_string('permalink', 'blog').'</a>';

        echo '</div>';

        if( isset($template['lastmod']) ){
            echo '<div style="font-size: 55%;">';
            echo ' [ '.get_string('modified').': '.$template['lastmod'].' ]';
            echo '</div>';
        }

        echo '</td></tr></table>'."\n\n";

    }

    function blog_file_area_name($blogentry) {
    //  Creates a directory file name, suitable for make_upload_directory()
        global $CFG;
        // $CFG->dataroot/blog/attachments/xxxx/file.jpg
        return "blog/attachments/$blogentry->id";
    }

    function blog_file_area($blogentry) {
        return make_upload_directory( blog_file_area_name($blogentry) );
    }

    function blog_delete_old_attachments($post, $exception="") {
    // Deletes all the user files in the attachments area for a post
    // EXCEPT for any file named $exception

        if ($basedir = blog_file_area($post)) {
            if ($files = get_directory_list($basedir)) {
                foreach ($files as $file) {
                    if ($file != $exception) {
                        unlink("$basedir/$file");
                        notify("Existing file '$file' has been deleted!");
                    }
                }
            }
            if (!$exception) {  // Delete directory as well, if empty
                rmdir("$basedir");
            }
        }
    }

    function blog_print_attachments($blogentry, $return=NULL) {
    // if return=html, then return a html string.
    // if return=text, then return a text-only string.
    // otherwise, print HTML for non-images, and return image HTML

        global $CFG;

        $filearea = blog_file_area_name($blogentry);

        $imagereturn = "";
        $output = "";

        if ($basedir = blog_file_area($blogentry)) {
            if ($files = get_directory_list($basedir)) {
                $strattachment = get_string("attachment", "forum");
                foreach ($files as $file) {
                    include_once($CFG->libdir.'/filelib.php');
                    $icon = mimeinfo("icon", $file);
                    $type = mimeinfo("type", $file);
                    $ffurl = get_file_url("$filearea/$file");
                    $image = "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"\" />";

                    if ($return == "html") {
                        $output .= "<a href=\"$ffurl\">$image</a> ";
                        $output .= "<a href=\"$ffurl\">$file</a><br />";

                    } else if ($return == "text") {
                        $output .= "$strattachment $file:\n$ffurl\n";

                    } else {
                        if (in_array($type, array('image/gif', 'image/jpeg', 'image/png'))) {    // Image attachments don't get printed as links
                            $imagereturn .= "<br /><img src=\"$ffurl\" alt=\"\" />";
                        } else {
                            echo "<a href=\"$ffurl\">$image</a> ";
                            echo filter_text("<a href=\"$ffurl\">$file</a><br />");
                        }
                    }
                }
            }
        }

        if ($return) {
            return $output;
        }

        return $imagereturn;
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
        if ($CFG->bloglevel >= BLOG_USER_LEVEL) {
            $options = array ( 'draft' => get_string('publishtonoone', 'blog') );
        }

        if ($CFG->bloglevel > BLOG_USER_LEVEL) {
            $options['site'] = get_string('publishtosite', 'blog');
        }

        if ($CFG->bloglevel >= BLOG_GLOBAL_LEVEL) {
            $options['public'] = get_string('publishtoworld', 'blog');
        }

        return $options;
    }


    /**
     * User can edit a blog entry if this is their own blog post and they have
     * the capability moodle/blog:create, or if they have the capability
     * moodle/blog:manageentries.
     *
     * This also applies to deleting of posts.
     */
    function blog_user_can_edit_post($blogEntry) {

        global $CFG, $USER;

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can edit any blog post
        }

        if ($blogEntry->userid == $USER->id
          and has_capability('moodle/blog:create', $sitecontext)) {
            return true; // can edit own when having blog:create capability
        }

        return false;
    }


    /**
     * Checks to see if a user can view the blogs of another user.
     * Only blog level is checked here, the capabilities are enforced
     * in blog/index.php
     */
    function blog_user_can_view_user_post($targetuserid, $blogEntry=null) {
        global $CFG, $USER;

        if (empty($CFG->bloglevel)) {
            return false; // blog system disabled
        }

        // a hack to publish some blogs openly.  Uses $CFG->openblogs = array(44, 322); in config.php
        if (isset($CFG->openblogs) && in_array($targetuserid,$CFG->openblogs)) {
            return true;
        }

        if (!empty($USER->id) and $USER->id == $targetuserid) {
            return true; // can view own posts in any case
        }

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can manage all posts
        }

        // coming for 1 post, make sure it's not a draft
        if ($blogEntry and $blogEntry->publishstate == 'draft') {
            return false;  // can not view draft of others
        }

        // coming for 1 post, make sure user is logged in, if not a public blog
        if ($blogEntry && $blogEntry->publishstate != 'public' && !isloggedin()) {
            return false;
        }

        switch ($CFG->bloglevel) {
            case BLOG_GLOBAL_LEVEL:
                return true;
            break;

            case BLOG_SITE_LEVEL:
                if (!empty($USER->id)) { // not logged in viewers forbidden
                    return true;
                }
                return false;
            break;

            case BLOG_COURSE_LEVEL:
                $mycourses = array_keys(get_my_courses($USER->id));
                $usercourses = array_keys(get_my_courses($targetuserid));
                $shared = array_intersect($mycourses, $usercourses);
                if (!empty($shared)) {
                    return true;
                }
                return false;
            break;

            case BLOG_GROUP_LEVEL:
                $mycourses = array_keys(get_my_courses($USER->id));
                $usercourses = array_keys(get_my_courses($targetuserid));
                $shared = array_intersect($mycourses, $usercourses);
                foreach ($shared as $courseid) {
                    $course = get_record('course', 'id', $courseid);
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
                    if (has_capability('moodle/site:accessallgroups', $coursecontext)
                      or groups_get_course_groupmode($course) != SEPARATEGROUPS) {
                        return true;
                    } else {
                        if ($usergroups = groups_get_all_groups($courseid, $targetuserid)) {
                            foreach ($usergroups as $usergroup) {
                                if (groups_is_member($usergroup->id)) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            break;

            case BLOG_USER_LEVEL:
            default:
                $personalcontext = get_context_instance(CONTEXT_USER, $targetuserid);
                return has_capability('moodle/user:readuserblogs', $personalcontext);
            break;

        }
    }


    /**
     * Main filter function.
     */
    function blog_fetch_entries($postid='', $fetchlimit=10, $fetchstart='', $filtertype='', $filterselect='', $tagid='', $tag ='', $sort='lastmodified DESC', $limit=true) {

        global $CFG, $USER;

        /// the post table will be used for other things too
        $typesql = " AND p.module = 'blog' ";

        /// set the tag id for searching
        if ($tagid) {
            $tag = $tagid;
        } else if ($tag) {
            if ($tagrec = get_record_sql('SELECT * FROM '.$CFG->prefix.'tag WHERE name LIKE "'.addslashes($tag).'"')) {
                $tag = $tagrec->id;
            } else {
                $tag = -1;    //no records found
            }
        }

        // If we have specified an ID
        // Just return 1 entry

        if ($postid) {

            if ($post = get_record('post', 'id', $postid)) {

                if (blog_user_can_view_user_post($post->userid, $post)) {

                    if ($user = get_record('user', 'id', $post->userid)) {
                        $post->email = $user->email;
                        $post->firstname = $user->firstname;
                        $post->lastname = $user->lastname;
                    }
                    $retarray[] = $post;
                    return $retarray;
                } else {
                    return null;
                }

            } else { // bad postid
                return null;
            }
        }

        if ($tag) {
            $tagtablesql = $CFG->prefix.'tag_instance ti, ';
            $tagquerysql = ' AND ti.itemid = p.id AND ti.tagid = '.$tag.' AND ti.itemtype = \'post\' ';
        } else {
            $tagtablesql = '';
            $tagquerysql = '';
        }

        if (isloggedin() && !has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM), $USER->id, false)) {
            $permissionsql =  'AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';
        } else {
            $permissionsql =  'AND p.publishstate = \'public\'';
        }

        // fix for MDL-9165, use with readuserblogs capability in a user context can read that user's private blogs
        // admins can see all blogs regardless of publish states, as described on the help page
        if (has_capability('moodle/user:readuserblogs', get_context_instance(CONTEXT_SYSTEM))) {
            $permissionsql = '';
        } else if ($filtertype=='user' && has_capability('moodle/user:readuserblogs', get_context_instance(CONTEXT_USER, $filterselect))) {
            $permissionsql = '';
        }
        /****************************************
         * depending on the type, there are 4   *
         * different possible sqls              *
         ****************************************/

        $requiredfields = 'p.*, u.firstname,u.lastname,u.email';

        if ($filtertype == 'course' && $filterselect == SITEID) {  // Really a site
            $filtertype = 'site';
        }

        switch ($filtertype) {

            case 'site':

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'user u
                        WHERE p.userid = u.id '.$tagquerysql.'
                        AND u.deleted = 0
                        '.$permissionsql.$typesql;

            break;

            case 'course':
                // all users with a role assigned
                $context = get_context_instance(CONTEXT_COURSE, $filterselect);

                // MDL-10037, hidden users' blogs should not appear
                if (has_capability('moodle/role:viewhiddenassigns', $context)) {
                    $hiddensql = '';
                } else {
                    $hiddensql = ' AND ra.hidden = 0 ';
                }

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'role_assignments ra, '.$CFG->prefix.'user u
                        WHERE p.userid = ra.userid '.$tagquerysql.'
                        AND ra.contextid '.get_related_contexts_string($context).'
                        AND u.id = p.userid
                        AND u.deleted = 0
                        '.$hiddensql.$permissionsql.$typesql;

            break;

            case 'group':

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                          .$CFG->prefix.'groups_members gm, '.$CFG->prefix.'user u
                        WHERE p.userid = gm.userid AND u.id = p.userid '.$tagquerysql.'
                          AND gm.groupid = '.$filterselect.'
                          AND u.deleted = 0
                          '.$permissionsql.$typesql;
            break;

            case 'user':

                // a hack to publish some blogs openly.  Uses $CFG->openblogs = array(44, 322); in config.php
                if (isset($CFG->openblogs) && in_array($filterselect,$CFG->openblogs)) {
                    $permissionsql = ' AND (p.publishstate = \'site\' OR p.publishstate = \'public\') ';
                }

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'user u
                        WHERE p.userid = u.id '.$tagquerysql.'
                        AND u.id = '.$filterselect.'
                        AND u.deleted = 0
                        '.$permissionsql.$typesql;
            break;
        }

        $limitfrom = 0;
        $limitnum = 0;

        if ($fetchstart !== '' && $limit) {
            $limitfrom = $fetchstart;
            $limitnum = $fetchlimit;
        }

        $orderby = ' ORDER BY '. $sort .' ';

        //global $db; $db->debug = true;
        $records = get_records_sql($SQL . $orderby, $limitfrom, $limitnum);
        //$db->debug = false;

        if (empty($records)) {
            return array();
        }

        return $records;
    }


    /**
     * get the count of viewable entries, easiest way is to count blog_fetch_entries
     * this is used for print_paging_bar
     * this is not ideal, but because of the UNION in the sql in blog_fetch_entries,
     * it is hard to use count_records_sql
     */
    function get_viewable_entry_count($postid='', $fetchlimit=10,
                $fetchstart='', $filtertype='', $filterselect='', $tagid='',
                $tag ='', $sort='lastmodified DESC') {

        $blogEntries = blog_fetch_entries($postid, $fetchlimit,
                $fetchstart, $filtertype, $filterselect, $tagid, $tag,
                $sort='lastmodified DESC', false);

        return count($blogEntries);
    }


    /// Find the base url from $_GET variables, for print_paging_bar
    /// WARNING:  EVIL EVIL EVIL!  This function directly acesses $_GET which is a big no no. MDL-22631
    /// I added some clean_param() calls for now but $_GET should just not ever be used directly.  
    /// The function is totally gone in Moodle 2.0.
    function get_baseurl($filtertype, $filterselect) {

        unset($_GET['blogpage']);

        $strippedurl = strip_querystring(qualified_me());
        if(!empty($_GET)) {
            $first = false;
            $querystring = '';
            foreach($_GET as $var => $val) {
                $var = clean_param($var, PARAM_ALPHANUM);   // See MDL-22631
                $val = clean_param($val, PARAM_CLEAN);
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

        return strip_querystring(qualified_me()) . $querystring. 'filtertype='.
                $filtertype.'&amp;filterselect='.$filterselect.'&amp;';

    }

    /**
     * Returns a list of all user ids who have used blogs in the site
     * Used in backup of site courses.
     */
    function blog_get_participants() {

        global $CFG;

        return get_records_sql("SELECT userid as id 
                                  FROM {$CFG->prefix}post
                                 WHERE module = 'blog'
                                   AND courseid = 0");
    }
?>
