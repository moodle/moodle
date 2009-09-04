<?php //$Id$

/// Sets up blocks and navigation for index.php

require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot.'/tag/lib.php');

$instanceid  = optional_param('instanceid', 0, PARAM_INT);

/// navigations
/// site blogs - sitefullname -> blog entries -> (?tag) -> (?search)
///      heading: Site Blog Entries
/// course blogs - sitefullname -> course fullname ->  blog entries -> (?tag) -> (?search)
///      heading: Blog Entries associated with [course fullname]
/// mod blogs    - sitefullname -> course fullname -> mod name -> (?user/group) -> blog entries -> (?tag) -> (?search)
///      heading: Blog Entries associated with [module fullname]
/// group blogs - sitefullname -> course fullname ->group ->(?tag) -> (?search)
///      heading: Blog Entries associated with [course fullname] by group [group name]
/// user blogs   - sitefullname -> (?coursefullname) -> (?mod name) -> participants -> blog entries -> (?tag) -> (?search)
///      heading: Blog Entries by [fullname]

$blogstring = get_string('blogentries','blog');
$tagstring = get_string('tag');

// needed also for user tabs later
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid', '', '', $courseid);
}

$navlinks = array();

if (!empty($modid)) { //mod
    $cm = $DB->get_record('course_modules', array('id' => $modid));
    $cm->modname = $DB->get_field('modules', 'name', array('id' => $cm->module));
    $cm->name = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance));
}

if (!empty($groupid)) {
    if ($thisgroup = groups_get_group($groupid, false)) { //TODO:
        $navlinks[] = array('name' => $thisgroup->name,
                            'link' => "$CFG->wwwroot/user/index.php?id=$course->id&amp;group=$groupid",
                            'type' => 'misc');
    } else {
        print_error('cannotfindgroup');
    }

}

if (!empty($userid)) {
    $user = $DB->get_record('user', array('id'=>$userid));
    $navlinks[] = array('name' => fullname($user),
                        'link' => "$CFG->wwwroot/user/view.php?id=$userid".(empty($courseid)?'':"&amp;course=$courseid"),
                        'type' => 'misc');

}

// After this we have dynamic navigation elements, with links that depend on each other
$blogentries_link = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
$pure_url = new moodle_url();
$pure_url->remove_params(array('tag', 'tagid', 'search'));
$pure_blog_entries_link = $pure_url->out();

// If Tag or Search is set, the "Blog entries" nav is set to the current Query String without tag or search params
if (!empty($tagid)) {
    $tagrec = $DB->get_record('tag', array('id'=>$tagid));
    $tag_link = array('name' => $tagrec->name,
                      'link' => "index.php",
                      'type' => 'misc');
    $blogentries_link['link'] = $pure_blog_entries_link;
} elseif (!empty($tag)) {
    $tag_link = array('name' => get_string('tagparam', 'blog', $tag),
                      'link' => null,
                      'type' => 'misc');
    $blogentries_link['link'] = $pure_blog_entries_link;
}

if (!empty($search)) {
    $search_link = array('name' => get_string('searchterm', 'blog', $search),
                         'link' => null,
                         'type' => 'misc');
    $blogentries_link['link'] = $pure_blog_entries_link;

    $pure_url = new moodle_url();
    $pure_url->remove_params(array('search'));

    if (!empty($tag_link)) {
        $tag_link['link'] = $pure_url->out();
    }
}

$navlinks[] = $blogentries_link;

if (!empty($tag_link)) {
    $navlinks[] = $tag_link;
}
if (!empty($search_link)) {
    $navlinks[] = $search_link;
}

$blog_headers = blog_get_headers();

if (isset($cm)) {
    $navigation = build_navigation($blog_headers['navlinks'], $cm);
} else {
    $navigation = build_navigation($blog_headers['navlinks']);
}

// prints the tabs
$showroles = !empty($userid);
$currenttab = 'blogs';

$user = $USER;
$userid = $USER->id;
