<?php  // $Id$

    // this is the 'my moodle' page

    require_once('../config.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once('pagelib.php');
    
    if (!empty($THEME->customcorners)) {
        require_once($CFG->dirroot.'/lib/custom_corners_lib.php');
    }

    require_login();

    $mymoodlestr = get_string('mymoodle','my');

    if (isguest()) {
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }

        print_header($mymoodlestr);
        notice_yesno(get_string('noguest', 'my').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $CFG->wwwroot);
        print_footer();
        die();
    }


    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $blockaction = optional_param('blockaction', '', PARAM_ALPHA);

    $PAGE = page_create_instance($USER->id);

    $pageblocks = blocks_setup($PAGE,BLOCKS_PINNED_BOTH);

    if (($edit != -1) and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    $PAGE->print_header($mymoodlestr);

    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">';
    echo '<tr valign="top">';


    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

    if(blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing()) {
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
        if (!empty($THEME->customcorners)) print_custom_corners_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        if (!empty($THEME->customcorners)) print_custom_corners_end();
        echo '</td>';
    }

    echo '<td valign="top" id="middle-column">';
    if (!empty($THEME->customcorners)) print_custom_corners_start();

/// The main overview in the middle of the page
    
    // limits the number of courses showing up
    $courses = get_my_courses($USER->id, 'visible DESC,sortorder ASC', null, false, 21);
    $site = get_site();
    $course = $site; //just in case we need the old global $course hack

    if (array_key_exists($site->id,$courses)) {
        unset($courses[$site->id]);
    }

    foreach ($courses as $c) {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
    }
    
    if (empty($courses)) {
        print_simple_box(get_string('nocourses','my'),'center');
    } else {
        print_overview($courses);
    }
    
    // if more than 20 courses
    if (count($courses) > 20) {
        echo '<br />...';  
    }
    
    if (!empty($THEME->customcorners)) print_custom_corners_end();
    echo '</td>';

    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing()) {
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="right-column">';
        if (!empty($THEME->customcorners)) print_custom_corners_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        if (!empty($THEME->customcorners)) print_custom_corners_end();
        echo '</td>';
    }


    /// Finish the page
    echo '</tr></table>';

    print_footer();

?>
