<?php //$Id$

/// Sets up blocks and navigation for index.php

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->libdir .'/pagelib.php');
require_once($CFG->dirroot .'/blog/blogpage.php');
require_once($CFG->libdir .'/blocklib.php');
require_once($CFG->dirroot .'/course/lib.php');

$blockaction = optional_param('blockaction','', PARAM_ALPHA);
$instanceid  = optional_param('instanceid', 0, PARAM_INT);
$blockid     = optional_param('blockid',    0, PARAM_INT);

/// If user has never visited this page before, install 2 blocks for him
blog_check_and_install_blocks();


if (!$course = get_record('course', 'id', $courseid)) {
    error('The course number was incorrect ('. $courseid .')');
}

// Bounds for block widths
// more flexible for theme designers taken from theme config.php
$lmin = (empty($THEME->block_l_min_width)) ? 160 : $THEME->block_l_min_width;
$lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
$rmin = (empty($THEME->block_r_min_width)) ? 160 : $THEME->block_r_min_width;
$rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

define('BLOCK_L_MIN_WIDTH', $lmin);
define('BLOCK_L_MAX_WIDTH', $lmax);
define('BLOCK_R_MIN_WIDTH', $rmin);
define('BLOCK_R_MAX_WIDTH', $rmax);

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
$PAGE->courseid     = $courseid;
$PAGE->filtertype   = $filtertype;
$PAGE->filterselect = $filterselect;
$PAGE->tagid        = $tagid;

$PAGE->init_full(); //init the BlogInfo object and the courserecord object

$editing = false;
if ($PAGE->user_allowed_editing()) {
    $editing = $PAGE->user_is_editing();
}

// Calculate the preferred width for left, right and center (both center positions will use the same)
$preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                        BLOCK_L_MAX_WIDTH);
$preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                        BLOCK_R_MAX_WIDTH);

// Display the blocks and allow blocklib to handle any block action requested
$pageblocks = blocks_get_by_page($PAGE);

if ($editing) {
    if (!empty($blockaction) && confirm_sesskey()) {
        if (!empty($blockid)) {
            blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), intval($blockid));
        } else if (!empty($instanceid)) {
            $instance = blocks_find_instance($instanceid, $pageblocks);
            blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), $instance);
        }
        // This re-query could be eliminated by judicious programming in blocks_execute_action(),
        // but I'm not sure if it's worth the complexity increase...
        $pageblocks = blocks_get_by_page($PAGE);
    }
    $missingblocks = blocks_get_missing($PAGE, $pageblocks);
}

if (!empty($tagid)) {
    $taginstance = get_record('tag', 'id', $tagid);
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
if (!$course = get_record('course', 'id', $courseid)) {
    error('Wrong course id');
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
                print_error('Unable to find group');
            }

        break;

        case 'user':
            $participants = get_string('participants');
            if (!$user = get_record('user', 'id', $filterselect)) {
               error('Wrong user id');
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
            error ('Error unknown filtertype');
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


/// Layout the whole page as three big columns.
print '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">' . "\n";
print '<tr valign="top">' . "\n";

/// The left column ...
if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
    print '<td style="vertical-align: top; width: '. $preferred_width_left .'px;" id="left-column">' . "\n";
    print '<!-- Begin left side blocks -->' . "\n";
    print_container_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    print_container_end();
    print '<!-- End left side blocks -->' . "\n";
    print '</td>' . "\n";
}

/// Start main column
print '<!-- Begin page content -->' . "\n";
print '<td>';
print_container_start();
?>
<table width="100%">
<tr>
<td valign="top">
