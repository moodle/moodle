<?php // $Id$

/**
 * file index.php
 * index page to view blogs. if no blog is specified then site wide entries are shown
 * if a blog id is specified then the latest entries from that blog are shown
 */

if (!file_exists('../config.php')) {
    header('Location: ../install.php');
    die;
}

require_once('../config.php');
require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->libdir .'/blocklib.php');

$id           = optional_param('id', 0, PARAM_INT);
$limit        = optional_param('limit', 0, PARAM_INT);
$start        = optional_param('formstart', 0, PARAM_INT);
$userid       = optional_param('userid',0,PARAM_INT);
$courseid     = optional_param('courseid',SITEID,PARAM_INT);
$tag          = optional_param('tag', '', PARAM_NOTAGS);
$tagid        = optional_param('tagid', 0, PARAM_INT);
$postid       = optional_param('postid',0,PARAM_INT);
$filtertype   = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);
$edit         = optional_param('edit', -1, PARAM_BOOL);

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}


// Blogs are only global for now.
// 'post' table will have to be changed to use contextid instead of courseid,
// modileid, etc. because they are obsolete now.
$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);


// change block edit staus if not guest and logged in
if (isloggedin() and !isguest() and $edit != -1) {
    $SESSION->blog_editing_enabled = $edit;
}

/// overwrite filter code here

if ($filtertype) {
    switch ($filtertype) {

        case 'site':
            if ($filterselect) {
                $userid = $filterselect;
            } else {
                $userid = 0;
            }
            $course = get_site();
            $courseid = SITEID;
        break;

        case 'course':
            if ($filterselect) {
                $courseid = $filterselect;
                $course = get_record('course','id',$courseid);
            }
            $userid =0;
            $groupid = 0;
        break;

        case 'group':
            if ($filterselect) {
                $groupid = $filterselect;
                $group = get_record('groups','id',$groupid);
                $course = get_record('course','id',$group->courseid);
                $courseid = $course->id;
            } else {
                $groupid = 0;
            }
            $userid = 0;
        break;

        case 'user':
            if ($filterselect) {
                $userid = $filterselect;
            }
            $groupid = 0;
        break;
        default:
        break;
    }

} else if ($userid) {    // default to user
    $filtertype = 'user';
    $filterselect = $userid;
} else {
    $filtertype = 'site';
    $filterselect = '';
}



/// Rights checking.

switch ($filtertype) {
    case 'site':
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
            error('Site blogs is not enabled');
        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
        }
    break;
    case 'course':
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        if ($CFG->bloglevel < BLOG_COURSE_LEVEL) {
            error('Course blogs is not enabled');
        }
    break;
    case 'group':
        $context = get_context_instance(CONTEXT_GROUP, $groupid);
        if ($CFG->bloglevel < BLOG_GROUP_LEVEL) {
            error ('Group blogs is not enabled');
        }
        if (groupmode($course) == SEPARATEGROUPS &&
                    !has_capability('moodle/site:accessallgroups', $context)) {
            if (!ismember($filterselect)) {
                error ('You are not a member of this group');
            }
        }
        /// check if user is editting teacher, or if spg, is member
    break;
    case 'user':
        $context = get_context_instance(CONTEXT_USER, $userid);
        if ($CFG->bloglevel < BLOG_USER_LEVEL) {
            error ('Blogs is not enabled');
        }
        if ($CFG->bloglevel == BLOG_USER_LEVEL && $USER->id != $filterselect) {
            error ('Under this setting, you can only view your own blogs');
        }

        /// check to see if the viewer is sharing no_group, visible group course.
        /// if not , check if the viewer is in any spg group as the user
        blog_user_can_view_user_post($filterselect);

    break;
    default:
    break;
}

if (!has_capability('moodle/blog:view', $context)) {
    error('You do not have the required permissions to to view blogs');
}


// first set the start and end day equal to the day argument passed in from the get vars
if ($limit == 'none') {
    $limit = get_user_preferences('blogpagesize', 10);
}

include($CFG->dirroot .'/blog/header.php');

// prints the tabs
$currenttab = 'blogs';
$user = $USER;
if (!$course) {
    $course = get_record('course', 'id', optional_param('courseid', SITEID, PARAM_INT));
}

$blogpage = optional_param('blogpage', 0, PARAM_INT);

blog_print_html_formatted_entries($userid, $postid, $limit, ($blogpage * $limit) ,$filtertype, $filterselect, $tagid, $tag, $filtertype, $filterselect);

add_to_log($courseid, 'blog', 'view', 'index.php?filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;postid='.$postid.'&amp;tagid='.$tagid.'&amp;tag='.$tag, 'view blog entry');

include($CFG->dirroot .'/blog/footer.php');


?>
