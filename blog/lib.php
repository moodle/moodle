<?php //$Id$

/**
 * Library of functions and constants for blog
 */
 
require_once($CFG->dirroot .'/blog/class.BlogInfo.php');
require_once($CFG->dirroot .'/blog/class.BlogEntry.php');
require_once($CFG->dirroot .'/blog/class.BlogFilter.php');
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
 * Verify that a user is logged in based on the session
 * @return bool True if user has a valid login session
 */
function blog_isLoggedIn() {
    global $USER;
    if (!isguest() && isset($USER) and isset($USER->id) and $USER->id) {
        return 1;
    }
    return 0;
}

/**
 * blog_user_has_rights - returns true if user is the blog's owner or a moodle admin.
 *
 * @param BlogInfo blogInfo - a BlogInfo object passed by reference. This object represents the blog being accessed.
 * @param int uid - numeric user id of the user whose rights are being tested against this blogInfo. If no uid is specified then the uid of the currently logged in user will be used.
 */
function blog_user_has_rights(&$bloginfo, $uid='') {
    global $USER;
    if (isset($bloginfo) && isset($bloginfo->userid)) {
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
    }
    return false;
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
 * blog_user_bloginfo
 *
 * returns a blogInfo object if the user has a blog in the acl table
 * This function stores the currently logged in user's bloginfo object
 * statically - do not release/unset the returned object.
 * added by Daryl Hawes for moodle integration
 * $userid - if no userid specified it will attempt to use the logged in user's id
 */
function blog_user_bloginfo($userid='') {
//Daryl Hawes note: not sure that this attempt at optimization is correct
//    static $bloginfosingleton; //store the logged in user's bloginfo in a static var
    global $USER;
    if ($userid == '') {
        global $USER;
        if (!isset($USER) || !isset($USER->id)) {
            return;
        }
        $userid = $USER->id;
    }

    $thisbloginfo = new BlogInfo($userid);
/*        if (isset($USER) && $USER->id == $userid) {
            $bloginfosingleton = $thisbloginfo;
        }*/
    return $thisbloginfo;
}

/**
 *  This function is in lib and not in BlogInfo because entries being searched
 *   might be found in any number of blogs rather than just one.
 *
 *   $@param BlogFilter blogFilter - a BlogFilter object containing the settings for finding appropriate entries for display
 */
function blog_print_html_formatted_entries(&$blogFilter, $filtertype, $filterselect) {
    global $CFG, $USER;

    $blogpage = optional_param('blogpage', 0, PARAM_INT);
    $bloglimit = get_user_preferences('blogpagesize',8); // expose as user pref when MyMoodle comes around

    // First let's see if the batchpublish form has submitted data
    $post = data_submitted();

    $morelink = '<br />&nbsp;&nbsp;';
    // show personal or general heading block as applicable
    echo '<div class="headingblock header blog">';
    //show blog title - blog tagline
    print "<br />";    //don't print title. blog_get_title_text();

    if ($blogpage != 0) {
        // modify the blog filter to fetch the entries we care about right now
        $oldstart = $blogFilter->fetchstart;
        $blogFilter->fetchstart = $blogpage * $bloglimit;
        unset($blogFilter->filtered_entries);
    }
    $blogEntries = $blogFilter->get_filtered_entries();
    // show page next/previous links if applicable
    print_paging_bar($blogFilter->get_viewable_entry_count(), $blogpage, $bloglimit, $blogFilter->baseurl, 'blogpage');

    blog_rss_print_link($filtertype, $filterselect, $blogFilter->tag);
    print '</div>';

    if (blog_isLoggedIn()) {
        //the user's blog is enabled and they are viewing their own blog
        $addlink = '<div align="center">';
        $addlink .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/edit.php', get_string('addnewentry', 'blog'));
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

        if ($blogpage != 0) {
            //put the blogFilter back the way we found it
            $blogFilter->fetchstart = $oldstart;
            unset($blogFilter->filtered_entries);
            $blogFilter->fetch_entries();
        }

        return;
    }

    $output = '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';

    print $output;
    unset($blogFilter->filtered_entries);
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
function blog_print_entry(&$blogEntry, $viewtype='full', $filtertype='', $filterselect='', $mode='loud') {
    global $CFG, $THEME, $USER;
    static $bloginfoarray;

    if (isset($bloginfoarray) && $bloginfocache[$blogEntry->entryuserid]) {
        $bloginfo = $bloginfocache[$blogEntry->entryuserid];
    } else {
        $bloginfocache[$blogEntry->entryuserid] = new BlogInfo($blogEntry->entryuserid);
        $bloginfo = $bloginfocache[$blogEntry->entryuserid];
    }
    
    $template['body'] = $blogEntry->get_formatted_entry_body();
    $template['countofextendedbody'] = 0;
    
    $template['title'] = '<a name="'. $blogEntry->entryId .'"></a>';
    //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
    $template['title'] .= '<span class="nolink">'. stripslashes_safe($blogEntry->entryTitle);
    $template['title'] .= '</span>';

    // add editing controls if allowed
    $template['userid'] = $blogEntry->entryuserid;
    $template['author'] = $blogEntry->entryAuthorName;
    $template['lastmod'] = $blogEntry->formattedEntryLastModified;
    $template['created'] = $blogEntry->formattedEntryCreated;
    $template['publishtomenu'] = $blogEntry->get_publish_to_menu(true, true);
    //forum style printing of blogs
    blog_print_entry_content ($template, $blogEntry->entryId, $filtertype, $filterselect, $mode);

}

//forum style printing of blogs
function blog_print_entry_content ($template, $entryid, $filtertype='', $filterselect='', $mode='loud') {
    global $USER, $CFG, $course, $ME;

    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    $user = get_record('user','id',$template['userid']);

    echo '<div align="center"><table cellspacing="0" class="forumpost" width="100%">';

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
                                 AND ti.entryid = '.$entryid)) {
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
        if (($template['userid'] == $USER->id) or isteacher($course->id)) {
                echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?editid='.$entryid.'&amp;sesskey='.sesskey().'">'.$stredit.'</a>';
        }

        if (($template['userid'] == $USER->id) or isteacher($course->id)) {
            echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?act=del&amp;postid='.$entryid.'&amp;sesskey='.sesskey().'">'.$strdelete.'</a>';
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
?>
