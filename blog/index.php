<?php // $Id$

/**
 * file index.php
 * index page to view blogs. if no blog is specified then site wide entries are shown
 * if a blog id is specified then the latest entries from that blog are shown
 */

require_once('../config.php');
require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->libdir .'/blocklib.php');

$id           = optional_param('id', 0, PARAM_INT);
$start        = optional_param('formstart', 0, PARAM_INT);
$userid       = optional_param('userid', 0, PARAM_INT);
$tag          = optional_param('tag', '', PARAM_NOTAGS);
$tagid        = optional_param('tagid', 0, PARAM_INT);
$postid       = optional_param('postid', 0, PARAM_INT);
$filtertype   = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

$edit         = optional_param('edit', -1, PARAM_BOOL);
$courseid     = optional_param('courseid', 0, PARAM_INT); // needed for user tabs and course tracking


if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);


// change block edit staus if not guest and logged in
if (isloggedin() and !isguest() and $edit != -1) {
    $SESSION->blog_editing_enabled = $edit;
}

if (empty($filtertype)) {
    if ($userid) {    // default to user if specified
        $filtertype = 'user';
        $filterselect = $userid;
    } else if (has_capability('moodle/blog:view', $sitecontext) and $CFG->bloglevel > BLOG_USER_LEVEL) {
        if ($postid) {
            $filtertype = 'user';
            if (!$postobject = get_record('post', 'module', 'blog', 'id', $postid)) {
                error('No such blog entry');
            }
            $filterselect = $postobject->userid;
        } else {
            $filtertype = 'site';
            $filterselect = '';
        }
    } else {
        // user might have capability to write blogs, but not read blogs at site level
        // users might enter this url manually without parameters
        $filtertype = 'user';
        $filterselect = $USER->id;
    }
}
/// check access and prepare filters

switch ($filtertype) {

    case 'site':
        if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
            error('Site blogs is not enabled');
        }
        if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
        }
        if (!has_capability('moodle/blog:view', $sitecontext)) {
            error('You do not have the required permissions to view all site blogs');
        }
    break;

    case 'course':
        if ($CFG->bloglevel < BLOG_COURSE_LEVEL) {
            error('Course blogs is not enabled');
        }
        if (!$course = get_record('course', 'id', $filterselect)) {
            error('Incorrect course id specified');
        }
        $courseid = $course->id;
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        require_login($course);
        if (!has_capability('moodle/blog:view', $coursecontext)) {
            error('You do not have the required permissions to view blogs in this course');
        }
    break;

    case 'group':
        if ($CFG->bloglevel < BLOG_GROUP_LEVEL) {
            error('Group blogs is not enabled');
        }
        
        // fix for MDL-9268
        if (! $group = groups_get_group($filterselect)) { //TODO:check.
            error('Incorrect group id specified');
        }
        if (!$course = get_record('course', 'id', $group->courseid)) {
            error('Incorrect course id specified');
        }
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $courseid = $course->id;
        require_login($course);
        if (!has_capability('moodle/blog:view', $coursecontext)) {
            error('You do not have the required permissions to view blogs in this course/group');
        }
        if (groups_get_course_groupmode($course) == SEPARATEGROUPS
          and !has_capability('moodle/site:accessallgroups', $coursecontext)) {
            if (!groups_is_member($filterselect)) {
                error ('You are not a member of this course group');
            }
        }

    break;

    case 'user':
        if ($CFG->bloglevel < BLOG_USER_LEVEL) {
            error('Blogs is not enabled');
        }
        if (!$user = get_record('user', 'id', $filterselect)) {
            error('Incorrect user id');
        }
        if ($user->deleted) {
            print_header();
            print_heading(get_string('userdeleted'));
            print_footer();
            die;
        }
        
        if ($USER->id == $filterselect) {
            if (!has_capability('moodle/blog:create', $sitecontext)
              and !has_capability('moodle/blog:view', $sitecontext)) {
                error('You do not have your own blog, sorry.');
            }
        } else {
            $personalcontext = get_context_instance(CONTEXT_USER, $filterselect);
            if (!has_capability('moodle/blog:view', $sitecontext) 
              and !has_capability('moodle/user:readuserblogs', $personalcontext)) {
                require_login();  // last-ditch attempt to gain permissions
                error('You do not have the required permissions to read user blogs');
            }
            if (!blog_user_can_view_user_post($filterselect)) {
                require_login();  // last-ditch attempt to gain permissions
                error('You can not view blog of this user, sorry.');
            }
        }
        $userid = $filterselect;

        if (!empty($courseid)) {
            require_login($courseid);
        }

    break;

    default:
        error('Incorrect blog filter type specified');
    break;
}

if (empty($courseid)) {
    $courseid = SITEID;
}

include($CFG->dirroot .'/blog/header.php');

blog_print_html_formatted_entries($postid, $filtertype, $filterselect, $tagid, stripslashes($tag));

add_to_log($courseid, 'blog', 'view', 'index.php?filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;postid='.$postid.'&amp;tagid='.$tagid.'&amp;tag='.$tag, 'view blog entry');

include($CFG->dirroot .'/blog/footer.php');


?>
