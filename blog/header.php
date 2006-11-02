<?php //$Id$

/// Sets up blocks and navigation for index.php, edit.php

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->libdir .'/pagelib.php');
require_once($CFG->dirroot .'/blog/blogpage.php');
require_once($CFG->libdir .'/blocklib.php');
require_once($CFG->dirroot .'/course/lib.php');

$blockaction = optional_param('blockaction','', PARAM_ALPHA);
$instanceid = optional_param('instanceid', 0, PARAM_INT);
$blockid = optional_param('blockid',    0, PARAM_INT);

/// If user has never visited this page before, install 2 blocks for him
blog_check_and_install_blocks();


// now check that they are logged in and allowed into the course (if specified)
if ($courseid != SITEID) {
    if (!$course = get_record('course', 'id', $courseid)) {
        error('The course number was incorrect ('. $courseid .')');
    }
    require_login($course->id);
} else {
    $course = $SITE;
}

// Bounds for block widths within this page
define('BLOCK_L_MIN_WIDTH', 160);
define('BLOCK_L_MAX_WIDTH', 210);
define('BLOCK_R_MIN_WIDTH', 160);
define('BLOCK_R_MAX_WIDTH', 210);

//_____________ new page class code ________
$pagetype = PAGE_BLOG_VIEW;
$pageclass = 'page_blog';

// map our page identifier to the actual name
// of the class which will be handling its operations.
page_map_class($pagetype, $pageclass);    

// Now, create our page object.
if (!isset($USER->id)) {
    $PAGE = page_create_object($pagetype);
} else {
    $PAGE = page_create_object($pagetype, $USER->id);
}
$PAGE->courseid = $courseid;
$PAGE->init_full(); //init the BlogInfo object and the courserecord object

if (!empty($tagid)) {
    $taginstance = get_record('tags', 'id', $tagid);
} else {
    $tagid = '';
    if (!empty($tag)) {
        $tagrec = get_record('tags', 'text', $tag);
        $tagid = $tagrec->id;
        $taginstance = get_record('tags', 'id', $tagid);
    }
}
if (!isset($filtertype)) {
    $filtertype = 'user';
    $filterselect = $USER->id;
}

/// navigations
/// site blogs - sitefullname -> blogs -> (?tag)
/// course blogs - sitefullname -> course fullname ->blogs ->(?tag)
/// group blogs - sitefullname -> course fullname ->group ->(?tag)
/// user blogs - sitefullname -> (?coursefullname) -> participants -> blogs -> (?tag)

$blogstring = get_string('blogs','blog');
$tagstring = get_string('tag');

if ($ME == $CFG->wwwroot.'/blog/edit.php') {  /// We are in edit mode, print the editing header

    // first we need to identify the user
    if ($editid) {  // if we are editing a post
        $blogEntry = get_record('post','id',$editid);
        $user = get_record('user','id',$blogEntry->userid);
    } else {
        $user = get_record('user','id',$filterselect);
    }

    if ($editid) {
        $formHeading = get_string('updateentrywithid', 'blog');
    } else {
        $formHeading = get_string('addnewentry', 'blog');
    }
    
    print_header("$SITE->shortname: $blogstring", "$SITE->fullname",
                        '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'">'.fullname($user).'</a> ->
                        <a href="'.$CFG->wwwroot.'/blog/index.php?userid='.$user->id.'">'.$blogstring.'</a> -> '.               $formHeading,'','',true);

} else {  // else, we are in view mode

/// This is very messy atm.

    switch ($filtertype) {
        case 'site':
            if ($tagid || !empty($tag)) {
                print_header("$SITE->shortname: $blogstring", $SITE->fullname,
                            '<a href="index.php?filtertype=site">'. "$blogstring</a> -> $tagstring: $taginstance->text",'','',true,$PAGE->get_extra_header_string());
            } else {
                print_header("$SITE->shortname: $blogstring", $SITE->fullname,
                            $blogstring,'','',true,$PAGE->get_extra_header_string());
            }
        break;

        case 'course':
            if ($tagid || !empty($tag)) {
                print_header("$course->shortname: $blogstring", $course->fullname,
                            '<a href="index.php?filtertype=course&amp;filterselect='.$filterselect.'">'. "$blogstring</a> -> $tagstring: $taginstance->text",'','',true,$PAGE->get_extra_header_string());
            } else {
                print_header("$course->shortname: $blogstring", $course->fullname,
                            $blogstring,'','',true,$PAGE->get_extra_header_string());
            }
        break;

        case 'group':

            $thisgroup = get_record('groups', 'id', $filterselect);

            if ($tagid || !empty($tag)) {
                print_header("$course->shortname: $blogstring", $course->fullname,
                            '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$filterselect.'">'.$thisgroup->name.'</a> ->
                            <a href="index.php?filtertype=group&amp;filterselect='.$filterselect.'">'. "$blogstring</a> -> $tagstring: $taginstance->text",'','',true,$PAGE->get_extra_header_string());
            } else {
                print_header("$course->shortname: $blogstring", $course->fullname,
                            '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$filterselect.'">'.$thisgroup->name."</a> ->
                            $blogstring",'','',true,$PAGE->get_extra_header_string());

            }

        break;

        case 'user':
            $user = get_record('user', 'id', $filterselect);
            $participants = get_string('participants');

            if (isset($course->id) && $course->id && $course->id != SITEID) {
                if ($tagid || !empty($tag)) {
                    print_header("$course->shortname: $blogstring", $course->fullname,
                            '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->
                            <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'">'.$participants.'</a> ->
                            <a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'&amp;course='.$course->id.'">'.fullname($user).'</a> ->
                            <a href="index.php?courseid='.optional_param('courseid', 0, PARAM_INT).'&amp;filtertype=user&amp;filterselect='.$filterselect.'">'. "$blogstring</a> -> $tagstring: $taginstance->text",'','',true,$PAGE->get_extra_header_string());

                } else {
                    print_header("$course->shortname: $blogstring", $course->fullname,
                            '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->
                            <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'">'.$participants.'</a> ->
                            <a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'&amp;course='.$course->id.'">'.fullname($user).'</a> ->
                            '.$blogstring,'','',true,$PAGE->get_extra_header_string());
                }

            }
            //in top view
            else {

                if ($tagid || !empty($tag)) {
                    print_header("$SITE->shortname: $blogstring", $SITE->fullname,
                            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'">'.fullname($user).'</a> ->
                            <a href="index.php?filtertype=user&amp;filterselect='.$filterselect.'">'. "$blogstring</a> -> $tagstring: $taginstance->text",'','',true,$PAGE->get_extra_header_string());

                } else {
                    print_header("$SITE->shortname: $blogstring", $SITE->fullname,
                            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'">'.fullname($user).'</a> ->
                            '.$blogstring,'','',true,$PAGE->get_extra_header_string());

                }

            }
        break;

        default:    //user click on add from block
            print_header("$SITE->shortname: $blogstring", $SITE->fullname,
                            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$filterselect.'">'.fullname($user).'</a> ->
                            <a href="'.$CFG->wwwroot.'/blog/index.php?userid='.$user->id.'">'.$blogstring.'</a> -> '.get_string('addentry','blog'),'','',true,$PAGE->get_extra_header_string());
        break;
    }

} /// close switch

// prints the tabs
$currenttab = 'blogs';
$user = $USER;
if (!$course) {
    $course = get_record('course','id',optional_param('courseid', SITEID, PARAM_INT));
}
require_once($CFG->dirroot .'/user/tabs.php');

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

/// Layout the whole page as three big columns.
print '<table border="0" cellpadding="3" cellspacing="0" width="100%">' . "\n";
print '<tr valign="top">' . "\n";

/// The left column ...
if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
    print '<td style="vertical-align: top; width: '. $preferred_width_left .'px;">' . "\n";
    print '<!-- Begin left side blocks -->' . "\n";
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    print '<!-- End left side blocks -->' . "\n";
    print '</td>' . "\n";
}

/// Start main column
print '<!-- Begin page content -->' . "\n";
print '<td width="*">';
?>
<table width="100%">
<tr>
<td height="100%" valign="top">
