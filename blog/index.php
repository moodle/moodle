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
    print_error('blogdisable', 'blog');
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
            if (!$postobject = $DB->get_record('post', array('module'=>'blog', 'id'=>$postid))) {
                print_error('nosuchentry', 'blog');
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
            print_error('siteblogdisable', 'blog');
        }
        if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
        }
        if (!has_capability('moodle/blog:view', $sitecontext)) {
            print_error('cannotviewsiteblog', 'blog');
        }
    break;

    case 'course':
        if ($CFG->bloglevel < BLOG_COURSE_LEVEL) {
            print_error('courseblogdisable', 'blog');
        }
        if (!$course = $DB->get_record('course', array('id'=>$filterselect))) {
            print_error('invalidcourseid');
        }
        $courseid = $course->id;
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        require_login($course);
        if (!has_capability('moodle/blog:view', $coursecontext)) {
            print_error('cannotviewcourseblog', 'blog');
        }
    break;

    case 'group':
        if ($CFG->bloglevel < BLOG_GROUP_LEVEL) {
            print_error('groupblogdisable', 'blog');
        }
        
        // fix for MDL-9268
        if (! $group = groups_get_group($filterselect)) { //TODO:check.
            print_error('invalidgroupid');
        }
        if (!$course = $DB->get_record('course', array('id'=>$group->courseid))) {
            print_error('invalidcourseid');
        }
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $courseid = $course->id;
        require_login($course);
        if (!has_capability('moodle/blog:view', $coursecontext)) {
            print_error('cannotviewcourseorgroupblog', 'blog');
        }
        if (groups_get_course_groupmode($course) == SEPARATEGROUPS
          and !has_capability('moodle/site:accessallgroups', $coursecontext)) {
            if (!groups_is_member($filterselect)) {
                print_error('notmemberofgroup');
            }
        }

    break;

    case 'user':
        if ($CFG->bloglevel < BLOG_USER_LEVEL) {
            print_error('blogdisable', 'blog');
        }
        if (!$user = $DB->get_record('user', array('id'=>$filterselect))) {
            print_error('invaliduserid');
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
                print_error('donothaveblog', 'blog');
            }
        } else {
            $personalcontext = get_context_instance(CONTEXT_USER, $filterselect);
            if (!has_capability('moodle/blog:view', $sitecontext) 
              and !has_capability('moodle/user:readuserblogs', $personalcontext)) {
                print_error('cannotviewuserblog', 'blog');
            }
            if (!blog_user_can_view_user_post($filterselect)) {
                print_error('cannotviewcourseblog', 'blog');
            }
        }
        $userid = $filterselect;

        if (!empty($courseid)) {
            require_login($courseid);
        }

    break;

    default:
        print_error('incorrectblogfilter', 'blog');
    break;
}

if (empty($courseid)) {
    $courseid = SITEID;
}

include($CFG->dirroot .'/blog/header.php');

blog_print_html_formatted_entries($postid, $filtertype, $filterselect, $tagid, $tag);

add_to_log($courseid, 'blog', 'view', 'index.php?filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;postid='.$postid.'&amp;tagid='.$tagid.'&amp;tag='.$tag, 'view blog entry');

include($CFG->dirroot .'/blog/footer.php');


?>
