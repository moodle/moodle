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

//correct tagid if a text tag is provided as a param
if (!empty($tag)) {
    $ILIKE = $DB->sql_ilike();
    if ($tagrec = $DB->get_record_sql("SELECT * FROM {tag} WHERE name $ILIKE ?", array("%$tag%"))) {
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

if (empty($CFG->bloglevel)) {
    print_error('blogdisable', 'blog');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);

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
    if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
        require_login();
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
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    require_login($course);

    if (!has_capability('moodle/blog:view', $coursecontext)) {
        print_error('cannotviewcourseblog', 'blog');
    }
} else {
    $coursecontext = get_context_instance(CONTEXT_COURSE, SITEID);
}

if (!empty($groupid)) {
    if ($CFG->bloglevel < BLOG_SITE_LEVEL) {
        print_error('groupblogdisable', 'blog');
    }

    if (! $group = groups_get_group($groupid)) {
        print_error(get_string('invalidgroupid', 'blog'));
    }

    if (!$course = $DB->get_record('course', array('id'=>$group->courseid))) {
        print_error(get_string('invalidcourseid', 'blog'));
    }

    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
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

if (!empty($user)) {
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
        $personalcontext = get_context_instance(CONTEXT_USER, $userid);

        if (!has_capability('moodle/blog:view', $sitecontext) && !has_capability('moodle/user:readuserblogs', $personalcontext)) {
            print_error('cannotviewuserblog', 'blog');
        }

        if (!blog_user_can_view_user_entry($userid)) {
            print_error('cannotviewcourseblog', 'blog');
        }
    }
}

$courseid = (empty($courseid)) ? SITEID : $courseid;

if (!empty($courseid)) {
    $PAGE->set_context(get_context_instance(CONTEXT_COURSE, $courseid));
}

if (!empty($modid)) {
    $PAGE->set_context(get_context_instance(CONTEXT_MODULE, $modid));
}

$blogheaders = blog_get_headers();

if (empty($entryid) && empty($modid) && empty($groupid)) {
    $PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));
}

echo $OUTPUT->header();

echo $OUTPUT->heading($blogheaders['heading'], 2);

$bloglisting = new blog_listing($blogheaders['filters']);
$bloglisting->print_entries();

echo $OUTPUT->footer();

add_to_log($courseid, 'blog', 'view', 'index.php?entryid='.$entryid.'&amp;tagid='.@$tagid.'&amp;tag='.$tag, 'view blog entry');
