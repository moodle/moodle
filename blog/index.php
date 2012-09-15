<?php

/**
 * file index.php
 * index page to view blogs. if no blog is specified then site wide entries are shown
 * if a blog id is specified then the latest entries from that blog are shown
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/blog/locallib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot .'/tag/lib.php');
require_once($CFG->dirroot .'/comment/lib.php');

$id       = optional_param('id', null, PARAM_INT);
$start    = optional_param('formstart', 0, PARAM_INT);
$tag      = optional_param('tag', '', PARAM_NOTAGS);
$userid   = optional_param('userid', null, PARAM_INT);
$tagid    = optional_param('tagid', null, PARAM_INT);
$modid    = optional_param('modid', null, PARAM_INT);
$entryid  = optional_param('entryid', null, PARAM_INT);
$groupid  = optional_param('groupid', null, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$search   = optional_param('search', null, PARAM_RAW);

comment::init();

$url_params = compact('id', 'start', 'tag', 'userid', 'tagid', 'modid', 'entryid', 'groupid', 'courseid', 'search');
foreach ($url_params as $var => $val) {
    if (empty($val)) {
        unset($url_params[$var]);
    }
}
$PAGE->set_url('/blog/index.php', $url_params);

if (empty($CFG->enableblogs)) {
    print_error('blogdisable', 'blog');
}

//correct tagid if a text tag is provided as a param
if (!empty($tag)) {
    if ($tagrec = $DB->get_record_sql("SELECT * FROM {tag} WHERE ". $DB->sql_like('name', '?', false), array("%$tag%"))) {
        $tagid = $tagrec->id;
    } else {
        unset($tagid);
    }
}

// add courseid if modid or groupid is specified: This is used for navigation and title
if (!empty($modid) && empty($courseid)) {
    $courseid = $DB->get_field('course_modules', 'course', array('id'=>$modid));
}

if (!empty($groupid) && empty($courseid)) {
    $courseid = $DB->get_field('groups', 'courseid', array('id'=>$groupid));
}

$sitecontext = context_system::instance();

// check basic permissions
if ($CFG->bloglevel == BLOG_GLOBAL_LEVEL) {
    // everybody can see anything - no login required unless site is locked down using forcelogin
    if ($CFG->forcelogin) {
        require_login();
    }

} else if ($CFG->bloglevel == BLOG_SITE_LEVEL) {
    // users must log in and can not be guests
    require_login();
    if (isguestuser()) {
        // they must have entered the url manually...
        print_error('blogdisable', 'blog');
    }

} else if ($CFG->bloglevel == BLOG_USER_LEVEL) {
    // users can see own blogs only! with the exception of ppl with special cap
    require_login();

} else {
    // weird!
    print_error('blogdisable', 'blog');
}


if (!$userid && has_capability('moodle/blog:view', $sitecontext) && $CFG->bloglevel > BLOG_USER_LEVEL) {
    if ($entryid) {
        if (!$entryobject = $DB->get_record('post', array('id'=>$entryid))) {
            print_error('nosuchentry', 'blog');
        }
        $userid = $entryobject->userid;
    }
} else if (!$userid) {
    $userid = $USER->id;
}

if (!empty($modid)) {
    if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
        print_error(get_string('nocourseblogs', 'blog'));
    }
    if (!$mod = $DB->get_record('course_modules', array('id' => $modid))) {
        print_error(get_string('invalidmodid', 'blog'));
    }
    $courseid = $mod->course;
}

if ((empty($courseid) ? true : $courseid == SITEID) && empty($userid)) {
    if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
        print_error('siteblogdisable', 'blog');
    }
    if (!has_capability('moodle/blog:view', $sitecontext)) {
        print_error('cannotviewsiteblog', 'blog');
    }

    $COURSE = $DB->get_record('course', array('format'=>'site'));
    $courseid = $COURSE->id;
}

if (!empty($courseid)) {
    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }

    $courseid = $course->id;
    $coursecontext = context_course::instance($course->id);

    require_login($course);

    if (!has_capability('moodle/blog:view', $coursecontext)) {
        print_error('cannotviewcourseblog', 'blog');
    }
} else {
    $coursecontext = context_course::instance(SITEID);
}

if (!empty($groupid)) {
    if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
        print_error('groupblogdisable', 'blog');
    }

    if (! $group = groups_get_group($groupid)) {
        print_error(get_string('invalidgroupid', 'blog'));
    }

    if (!$course = $DB->get_record('course', array('id'=>$group->courseid))) {
        print_error('invalidcourseid');
    }

    $coursecontext = context_course::instance($course->id);
    $courseid = $course->id;
    require_login($course);

    if (!has_capability('moodle/blog:view', $coursecontext)) {
        print_error(get_string('cannotviewcourseorgroupblog', 'blog'));
    }

    if (groups_get_course_groupmode($course) == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $coursecontext)) {
        if (!groups_is_member($groupid)) {
            print_error('notmemberofgroup');
        }
    }
}

if (!empty($userid)) {
    if ($CFG->bloglevel < BLOG_USER_LEVEL) {
        print_error('blogdisable', 'blog');
    }

    if (!$user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
    }

    if ($user->deleted) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('userdeleted'));
        echo $OUTPUT->footer();
        die;
    }

    if ($USER->id == $userid) {
        if (!has_capability('moodle/blog:create', $sitecontext)
          && !has_capability('moodle/blog:view', $sitecontext)) {
            print_error('donothaveblog', 'blog');
        }
    } else {
        $personalcontext = context_user::instance($userid);

        if (!has_capability('moodle/blog:view', $sitecontext) && !has_capability('moodle/user:readuserblogs', $personalcontext)) {
            print_error('cannotviewuserblog', 'blog');
        }

        if (!blog_user_can_view_user_entry($userid)) {
            print_error('cannotviewcourseblog', 'blog');
        }

        $PAGE->navigation->extend_for_user($user);
    }
}

$courseid = (empty($courseid)) ? SITEID : $courseid;

if (empty($entryid) && empty($modid) && empty($groupid)) {
    $PAGE->set_context(context_user::instance($USER->id));
} else if (!empty($modid)) {
    $PAGE->set_context(context_module::instance($modid));
} else if (!empty($courseid)) {
    $PAGE->set_context(context_course::instance($courseid));
} else {
    $PAGE->set_context(context_system::instance());
}

$blogheaders = blog_get_headers();

if ($CFG->enablerssfeeds) {
    $rsscontext = null;
    $filtertype = null;
    $thingid = null;
    list($thingid, $rsscontext, $filtertype) = blog_rss_get_params($blogheaders['filters']);
    if (empty($rsscontext)) {
        $rsscontext = get_system_context();
    }
    $rsstitle = $blogheaders['heading'];

    //check we haven't started output by outputting an error message
    if ($PAGE->state == moodle_page::STATE_BEFORE_HEADER) {
        blog_rss_add_http_header($rsscontext, $rsstitle, $filtertype, $thingid, $tagid);
    }

    //this works but there isn't a great place to put the link
    //blog_rss_print_link($rsscontext, $filtertype, $thingid, $tagid);
}

echo $OUTPUT->header();

echo $OUTPUT->heading($blogheaders['heading'], 2);

$bloglisting = new blog_listing($blogheaders['filters']);
$bloglisting->print_entries();

echo $OUTPUT->footer();

add_to_log($courseid, 'blog', 'view', 'index.php?entryid='.$entryid.'&amp;tagid='.@$tagid.'&amp;tag='.$tag, 'view blog entry');
