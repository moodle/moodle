<?php  // $Id$
       // index.php - the front page.

    if (!file_exists('./config.php')) {
        header('Location: install.php');
        die;
    }

/// Bounds for block widths on this page
    define('BLOCK_L_MIN_WIDTH', 160);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 160);
    define('BLOCK_R_MAX_WIDTH', 210);

    require_once('config.php');
    require_once($CFG->dirroot .'/course/lib.php');
    require_once($CFG->dirroot .'/lib/blocklib.php');

    if (empty($SITE)) {
        redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
        }
    }

    if (get_moodle_cookie() == '') {   
        set_moodle_cookie('nobody');   // To help search for cookies on login page
    }

    if (!empty($USER->id)) {
        add_to_log(SITEID, 'course', 'view', 'view.php?id='.SITEID, SITEID);
    }

    if (empty($CFG->langmenu)) {
        $langmenu = '';
    } else {
        $currlang = current_language();
        $langs = get_list_of_languages();
        $langmenu = popup_form ($CFG->wwwroot .'/index.php?lang=', $langs, 'chooselang', $currlang, '', '', '', true);
    }

    $PAGE       = page_create_object(PAGE_COURSE_VIEW, SITEID);
    $pageblocks = blocks_setup($PAGE);
    $editing    = $PAGE->user_is_editing();
    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                            BLOCK_R_MAX_WIDTH);

    print_header(strip_tags($SITE->fullname), $SITE->fullname, 'home', '',
                 '<meta name="description" content="'. s(strip_tags($SITE->summary)) .'" />',
                 true, '', user_login_string($SITE).$langmenu);

?>


<table id="layout-table">
  <tr>
  <?php

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';


/// Print Section
    if ($SITE->numsections > 0) {

        if (!$section = get_record('course_sections', 'course', $SITE->id, 'section', 1)) {
            delete_records('course_sections', 'course', $SITE->id, 'section', 1); // Just in case
            $section->course = $SITE->id;
            $section->section = 1;
            $section->summary = '';
            $section->sequence = '';
            $section->visible = 1;
            $section->id = insert_record('course_sections', $section);
        }

        if (!empty($section->sequence) or !empty($section->summary) or $editing) {
            print_simple_box_start('center', '100%', '', 5, 'sitetopic');

            /// If currently moving a file then show the current clipboard
            if (ismoving($SITE->id)) {
                $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
                echo '<p><font size="2">';
                echo "$stractivityclipboard&nbsp;&nbsp;(<a href=\"course/mod.php?cancelcopy=true&amp;sesskey=$USER->sesskey\">". get_string('cancel') .'</a>)';
                echo '</font></p>';
            }

            $options = NULL;
            $options->noclean = true;
            echo format_text($section->summary, FORMAT_HTML, $options);

            if ($editing) {
                $streditsummary = get_string('editsummary');
                echo "<a title=\"$streditsummary\" ".
                     " href=\"course/editsection.php?id=$section->id\"><img src=\"$CFG->pixpath/t/edit.gif\" ".
                     " height=\"11\" width=\"11\" border=\"0\" alt=\"$streditsummary\" /></a><br /><br />";
            }

            get_all_mods($SITE->id, $mods, $modnames, $modnamesplural, $modnamesused);
            print_section($SITE, $section, $mods, $modnamesused, true);
    
            if ($editing) {
                print_section_add_menus($SITE, $section->section, $modnames);
            }
            print_simple_box_end();
        }
    }

    if (isloggedin() and !isguest() and isset($CFG->frontpageloggedin)) {
        $frontpagelayout = $CFG->frontpageloggedin;
    } else {
        $frontpagelayout = $CFG->frontpage;
    }

    foreach (explode(',',$frontpagelayout) as $v) {
        switch ($v) {     /// Display the main part of the front page.
            case strval(FRONTPAGENEWS):
                if ($SITE->newsitems) { // Print forums only when needed
                    require_once($CFG->dirroot .'/mod/forum/lib.php');

                    if (! $newsforum = forum_get_course_forum($SITE->id, 'news')) {
                        error('Could not find or create a main news forum for the site');
                    }

                    if (isset($USER->id)) {
                        $SESSION->fromdiscussion = $CFG->wwwroot;
                        if (forum_is_subscribed($USER->id, $newsforum->id)) {
                            $subtext = get_string('unsubscribe', 'forum');
                        } else {
                            $subtext = get_string('subscribe', 'forum');
                        }
                        $headertext = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>'.
                            '<td><div class="title">'.$newsforum->name.'</div></td>'.
                            '<td><div class="link"><a href="mod/forum/subscribe.php?id='.$newsforum->id.'">'.$subtext.'</a></div></td>'.
                            '</tr></table>';
                    } else {
                        $headertext = $newsforum->name;
                    }

                    print_heading_block($headertext);
                    forum_print_latest_discussions($SITE, $newsforum, $SITE->newsitems, 'plain', 'p.modified DESC');
                }
            break;

            case FRONTPAGECOURSELIST:

                if (isloggedin() and !isadmin() and !isguest() and empty($CFG->disablemycourses)) {
                    print_heading_block(get_string('mycourses'));
                    print_my_moodle();
                } else if ((!isadmin() and !isguest()) or (count_records('course') <= FRONTPAGECOURSELIMIT)) {
                    // admin should not see list of courses when there are too many of them
                    print_heading_block(get_string('availablecourses'));
                    print_courses(0, '100%', true);
                }
            break;

            case FRONTPAGECATEGORYNAMES:

                print_heading_block(get_string('categories'));
                print_simple_box_start('center', '100%', '', 5, 'categorybox');
                print_whole_category_list(NULL, NULL, NULL, -1, false);
                print_simple_box_end();
                print_course_search('', false, 'short');
            break;

            case FRONTPAGECATEGORYCOMBO:

                print_heading_block(get_string('categories'));
                print_simple_box_start('center', '100%', '', 5, 'categorybox');
                print_whole_category_list(NULL, NULL, NULL, -1, true);
                print_simple_box_end();
                print_course_search('', false, 'short');
            break;

            case FRONTPAGETOPICONLY:    // Do nothing!!  :-)
            break;

        }
        echo '<br />';
    }

    echo '</td>';


    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing || isadmin()) {
        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
        if (isadmin()) {
            echo '<div align="center">'.update_course_icon($SITE->id).'</div>';
            echo '<br />';
        }
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</td>';
    }
?>

  </tr>
</table>

<?php
    print_footer('home');     // Please do not modify this line
?>
