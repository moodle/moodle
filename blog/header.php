<?php //$Id$

/// Sets up blocks and navigation for index.php

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/blog/blogpage.php');
require_once($CFG->dirroot .'/course/lib.php');

$blockaction = optional_param('blockaction','', PARAM_ALPHA);
$instanceid  = optional_param('instanceid', 0, PARAM_INT);
$blockid     = optional_param('blockid',    0, PARAM_INT);

/// If user has never visited this page before, install 2 blocks for him
blog_check_and_install_blocks();


if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid', '', '', $courseid);
}

//_____________ new page class code ________
$pagetype = PAGE_BLOG_VIEW;
$pageclass = 'page_blog';

// map our page identifier to the actual name
// of the class which will be handling its operations.
page_map_class($pagetype, $pageclass);

// Now, create our page object.
if (empty($USER->id)) {
    $PAGE = page_create_object($pagetype);
} else {
    $PAGE = page_create_object($pagetype, $USER->id);
}
$PAGE->set_course($course);
$PAGE->filtertype   = $filtertype;
$PAGE->filterselect = $filterselect;
$PAGE->tagid        = $tagid;
$array = array();
if (!empty($course->id)) {
    $array['courseid'] = $course->id;
}
if (!empty($filtertype)) {
    $array['filtertype'] = $filtertype;
}
if (!empty($filterselect)) {
    $array['filterselect'] = $filterselect;
}
if (!empty($tagid)) {
    $array['tagid'] = $tagid;
}
$PAGE->set_url('blog/index.php', $array);
$PAGE->set_blocks_editing_capability('moodle/blog:create');
$PAGE->init_full(); //init the BlogInfo object and the courserecord object

$editing = false;
if ($PAGE->user_allowed_editing()) {
    $editing = $PAGE->user_is_editing();
}

if (!empty($tagid)) {
    $taginstance = $DB->get_record('tag', array('id'=>$tagid));
} elseif (!empty($tag)) {
    $taginstance = tag_id($tag);
}

/// navigations
/// site blogs - sitefullname -> blogs -> (?tag)
/// course blogs - sitefullname -> course fullname ->blogs ->(?tag)
/// group blogs - sitefullname -> course fullname ->group ->(?tag)
/// user blogs - sitefullname -> (?coursefullname) -> participants -> blogs -> (?tag)

$blogstring = get_string('blogs','blog');
$tagstring = get_string('tag');

// needed also for user tabs later
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid', '', '', $courseid);
}

$navlinks = array();

/// This is very messy atm.

    switch ($filtertype) {
        case 'site':
            if ($tagid || !empty($tag)) {
                $navlinks[] = array('name' => $blogstring, 'link' => "index.php?filtertype=site", 'type' => 'misc');
                $navlinks[] = array('name' => "$tagstring: $taginstance->name", 'link' => null, 'type' => 'misc');
                $navigation = build_navigation($navlinks);
                print_header("$SITE->shortname: $blogstring", $SITE->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
            } else {
                $navlinks[] = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
                $navigation = build_navigation($navlinks);
                print_header("$SITE->shortname: $blogstring", $SITE->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
            }
        break;

        case 'course':
            if ($tagid || !empty($tag)) {
                $navlinks[] = array('name' => $blogstring,
                                    'link' => "index.php?filtertype=course&amp;filterselect=$filterselect",
                                    'type' => 'misc');
                $navlinks[] = array('name' => "$tagstring: $taginstance->name", 'link' => null, 'type' => 'misc');
                $navigation = build_navigation($navlinks);
                print_header("$course->shortname: $blogstring", $course->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
            } else {
                $navlinks[] = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
                $navigation = build_navigation($navlinks);
                print_header("$course->shortname: $blogstring", $course->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
            }
        break;

        case 'group':

            if ($thisgroup = groups_get_group($filterselect, false)) { //TODO:
                if ($tagid || !empty($tag)) {
                    $navlinks[] = array('name' => $thisgroup->name,
                                        'link' => "$CFG->wwwroot/user/index.php?id=$course->id&amp;group=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => $blogstring,
                                        'link' => "index.php?filtertype=group&amp;filterselect=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => "$tagstring: $taginstance->name", 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);
                    print_header("$course->shortname: $blogstring", $course->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
                } else {
                    $navlinks[] = array('name' => $thisgroup->name,
                                        'link' => "$CFG->wwwroot/user/index.php?id=$course->id&amp;group=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);
                    print_header("$course->shortname: $blogstring", $course->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());
                }
            } else {
                print_error('cannotfindgroup');
            }

        break;

        case 'user':
            $participants = get_string('participants');
            if (!$user = $DB->get_record('user', array('id'=>$filterselect))) {
               print_error('invaliduserid');
            }

            if ($course->id != SITEID) {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
                $systemcontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context

                if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
                    $navlinks[] = array('name' => $participants,
                                        'link' => "$CFG->wwwroot/user/index.php?id=$course->id",
                                        'type' => 'misc');
                }
                $navlinks[] = array('name' => fullname($user),
                                    'link' => "$CFG->wwwroot/user/view.php?id=$filterselect&amp;course=$course->id",
                                    'type' => 'misc');

                if ($tagid || !empty($tag)) {
                    $navlinks[] = array('name' => $blogstring,
                                        'link' => "index.php?courseid=$course->id&amp;filtertype=user&amp;filterselect=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => "$tagstring: $taginstance->name", 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);

                } else {
                    $navlinks[] = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);
                }
                print_header("$course->shortname: $blogstring", $course->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());

            } else {

            //in top view

                if ($postid) {
                    $navlinks[] = array('name' => fullname($user),
                                        'link' => "$CFG->wwwroot/user/view.php?id=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => $blogstring,
                                        'link' => "index.php?filtertype=user&amp;filterselect=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => format_string($postobject->subject), 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);

                } else if ($tagid || !empty($tag)) {
                    $navlinks[] = array('name' => fullname($user),
                                        'link' => "$CFG->wwwroot/user/view.php?id=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => $blogstring,
                                        'link' => "index.php?filtertype=user&amp;filterselect=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => "$tagstring: $taginstance->name", 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);

                } else {
                    $navlinks[] = array('name' => fullname($user),
                                        'link' => "$CFG->wwwroot/user/view.php?id=$filterselect",
                                        'type' => 'misc');
                    $navlinks[] = array('name' => $blogstring, 'link' => null, 'type' => 'misc');
                    $navigation = build_navigation($navlinks);
                }
                print_header("$SITE->shortname: $blogstring", $SITE->fullname, $navigation,'','',true,$PAGE->get_extra_header_string());

            }
        break;

        default:
            print_error('unknownfiletype');
        break;
    }


// prints the tabs
if ($filtertype=='user') {
    $showroles = true;
} else {
    $showroles = false;
}
$currenttab = 'blogs';

require_once($CFG->dirroot .'/user/tabs.php');


?>
<table width="100%">
<tr>
<td valign="top">
