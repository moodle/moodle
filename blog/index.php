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
$formstart = optional_param('formstart', 'none', PARAM_ALPHA);
$m = optional_param('m', 0, PARAM_INT); //month
$y = optional_param('y', 0, PARAM_INT); //year
$d = optional_param('d', 0, PARAM_INT); //day

$userid = optional_param('userid',0,PARAM_INT);
$groupid = optional_param('groupid',0,PARAM_INT);
$courseid = optional_param('courseid',0,PARAM_INT);
$tag = s(urldecode(optional_param('tag', '', PARAM_NOTAGS)));
$tagid = optional_param('tagid', 0, PARAM_INT);

$filtertype = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

/// overwrite filter code here
/// the the following code does the rights checkings?

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
        if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
            error ('site blogs is not enabled');
        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
        }
    break;
    case 'course':
        if ($CFG->bloglevel < BLOG_COURSE_LEVEL) {
            error ('course blogs is not enabled');
        }

        if (!isstudent($filterselect) && !isteacher($filterselect)) {
            error ('you must be a student in this course to view course blogs');
        }
        /// check if viewer is student
    break;
    case 'group':
        if ($CFG->bloglevel < BLOG_GROUP_LEVEL) {
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
        if ($CFG->bloglevel < BLOG_USER_LEVEL) {
            error ('Blogs is not enabled');
        }
        $canview = 0;    //bad start
        
        $usercourses = get_my_courses($filterselect);
        foreach ($usercourses as $usercourse) {
            /// if viewer and user sharing same non-spg course, then grant permission
            if (groupmode($usercourse)!= SEPARATEGROUPS){
                if (isstudent($usercourse->id) || isteacher($usercourse->id)) {
                    $canview = 1;
                }
            } else {
                /// now we need every group the user is in, and check to see if view is a member
                if ($usergroups = user_group($usercourse->id, $filterselect)) {
                    foreach ($usergroups as $usergroup) {
                        if (ismember($usergroup->id)) {
                            $canview = 1;
                        }
                    }
                }
            }
        }
        if (!$canview && $CFG->bloglevel < BLOG_SITE_LEVEL) {
            error ('you can not view this user\'s blogs');
        }
        /// check to see if the viewer is sharing no_group, visible group course.
        /// if not , check if the viewer is in any spg group as the user
    break;
    default:
    break;
}

//first set the start and end day equal to the day argument passed in from the get vars
$startday = $d;
$endday = $d + 1;
if ( empty($d) && !empty($m) && !empty($y) ) {
    //if there was no day specified then the entire month is wanted.
    $startday = 1;
    $endday = blog_mk_getLastDayofMonth($m, $y);
}

if ($limit == 'none') {
    $limit = get_user_preferences('blogpagesize',8);
}
	
if ($formstart == 'none' || $formstart < 0) {
    $start = 0;
} else {
    $start = $formstart;
}

$blogFilter =& new BlogFilter($userid, '', $courseid, $groupid, $limit, $start, $m, $startday, $y, $m, $endday, $y,$filtertype, $filterselect, $tagid, $tag);
//print_object($blogFilter); //debug

$pageNavigation = '';

include($CFG->dirroot .'/blog/header.php');

//prints the tabs
$currenttab = 'blogs';
$user = $USER;
if (!$course) {
    $course = get_record('course','id',optional_param('courseid', SITEID, PARAM_INT));
}
require_once($CFG->dirroot .'/user/tabs.php');

blog_print_html_formatted_entries($blogFilter, $filtertype, $filterselect);

include($CFG->dirroot .'/blog/footer.php');

?>
