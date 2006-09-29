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

$id = optional_param('id', 0, PARAM_INT);
$limit = optional_param('limit', 0, PARAM_INT);
$start = optional_param('formstart', 0, PARAM_INT);
$userid = optional_param('userid',0,PARAM_INT);
$courseid = optional_param('courseid',SITEID,PARAM_INT);
$tag = optional_param('tag', '', PARAM_NOTAGS);
$tagid = optional_param('tagid', 0, PARAM_INT);
$postid = optional_param('postid',0,PARAM_INT);
$filtertype = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

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

} else if ($userid) {    //default to user
    $filtertype = 'user';
    $filterselect = $userid;
} else {
    $filtertype = 'site';
    $filterselect = '';
}

/// rights checking

switch ($filtertype) {
    case 'site':
        if ($CFG->bloglevel < BLOG_SITE_LEVEL && (!isadmin())) {
            error ('site blogs is not enabled');
        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
        }
    break;
    case 'course':
        if ($CFG->bloglevel < BLOG_COURSE_LEVEL && (!isadmin())) {
            error ('course blogs is not enabled');
        }

        if (!isstudent($filterselect) && !isteacher($filterselect)) {
            error ('you must be a student in this course to view course blogs');
        }
        /// check if viewer is student
    break;
    case 'group':
        if ($CFG->bloglevel < BLOG_GROUP_LEVEL && (!isadmin())) {
            error ('group blogs is not enabled');
        }
        if (!isteacheredit($course) and (groupmode($course) == SEPARATEGROUPS)) {
            if (!ismember($filterselect)) {
                error ('you are not in this group');
            }
        }
        /// check if user is editting teacher, or if spg, is member
    break;
    case 'user':
        if ($CFG->bloglevel < BLOG_USER_LEVEL && (!isadmin())) {
            error ('Blogs is not enabled');
        }
        
        if ($CFG->bloglevel == BLOG_USER_LEVEL and $USER->id != $filterselect and !isadmin()) {
            error ('Under this setting, you can only view your own blogs');
        }

        /// check to see if the viewer is sharing no_group, visible group course.
        /// if not , check if the viewer is in any spg group as the user
        blog_user_can_view_user_post($filterselect);

    break;
    default:
    break;
}

// first set the start and end day equal to the day argument passed in from the get vars
if ($limit == 'none') {
    $limit = get_user_preferences('blogpagesize',10);
}

include($CFG->dirroot .'/blog/header.php');

$blogpage = optional_param('blogpage',0,PARAM_INT);

blog_print_html_formatted_entries($userid, $postid, $limit, ($blogpage * $limit) ,$filtertype, $filterselect, $tagid, $tag, $filtertype, $filterselect);

add_to_log($courseid, 'blog', 'view', 'index.php?filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;postid='.$postid.'&amp;tagid='.$tagid.'&amp;tag='.$tag, 'view blog entry');

include($CFG->dirroot .'/blog/footer.php');

?>
