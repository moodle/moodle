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
    require_once($CFG->dirroot .'/mod/resource/lib.php');
    require_once($CFG->dirroot .'/mod/forum/lib.php');

    $blockaction = optional_param('blockaction');

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

    if (empty($USER->id)) {
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http', 'https', $CFG->wwwroot);
        }
        $loginstring = "<font size=\"2\"><a href=\"$wwwroot/login/index.php\">".get_string('login').'</a></font>';
    } else {
        $loginstring = '<font size="1">'. user_login_string($SITE) .'</font>';
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
    $pageblocks = blocks_get_by_page($PAGE);
    $editing    = $PAGE->user_is_editing();

    if (!empty($blockaction)) {
        blocks_execute_url_action($PAGE, $pageblocks);
        // This re-query could be eliminated by judicious programming in blocks_execute_action(),
        // but I'm not sure if it's worth the complexity increase...
        $pageblocks = blocks_get_by_page($PAGE);
    }

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), BLOCK_R_MAX_WIDTH);

    print_header(strip_tags($SITE->fullname), $SITE->fullname, 'home', '',
                 '<meta name="description" content="'. s(strip_tags($SITE->summary)) .'" />',
                 true, '', $loginstring . '<br />' . $langmenu);

?>


<table id="layout-table">
  <tr>
  <?PHP

    if(blocks_have_content($pageblocks[BLOCK_POS_LEFT]) || $editing) {
        echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks[BLOCK_POS_LEFT]);
        echo '</td>';
    }

    echo '<td id="middle-column">';


/// Print Section
    if ($SITE->numsections > 0) {
        print_simple_box_start('center', '100%', '', 5, 'sitetopic');

        /// If currently moving a file then show the current clipboard
        if (ismoving($SITE->id)) {
            $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
            echo '<p><font size="2">';
            echo "$stractivityclipboard&nbsp;&nbsp;(<a href=\"course/mod.php?cancelcopy=true\">". get_string('cancel') .'</a>)';
            echo '</font></p>';
        }


        if (!$section = get_record('course_sections', 'course', $SITE->id, 'section', 1)) {
            delete_records('course_sections', 'course', $SITE->id, 'section', 1); // Just in case
            $section->course = $SITE->id;
            $section->section = 1;
            $section->summary = '';
            $section->visible = 1;
            $section->id = insert_record('course_sections', $section);
        }

        echo format_text($section->summary, FORMAT_HTML);

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
        print_spacer(10);
    }

    switch ($CFG->frontpage) {     /// Display the main part of the front page.
        case FRONTPAGENEWS:
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
                $headertext = "<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"headingblockcontent\"><tr>
                               <td>$newsforum->name</td>
                               <td align=\"right\"><font size=\"1\">
                               <a href=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</a>
                               </td></tr></table>";
            } else {
                $headertext = $newsforum->name;
            }

            if ($SITE->newsitems) { //print forums only when needed
                print_heading_block($headertext);
                print_spacer(8,1);
                forum_print_latest_discussions($newsforum->id, $SITE->newsitems);
            }
        break;

        case FRONTPAGECOURSELIST:
        case FRONTPAGECATEGORYNAMES:
            if (isset($USER->id) and !isset($USER->admin)) {
                print_heading_block(get_string('mycourses'));
                print_spacer(8,1);
                print_my_moodle();
            } else {
                if (count_records('course_categories') > 1) {
                    if ($CFG->frontpage == FRONTPAGECOURSELIST) {
                        print_heading_block(get_string('availablecourses'));
                    } else {
                        print_heading_block(get_string('categories'));
                    }
                    print_spacer(8,1);
                    print_simple_box_start('center', '100%');
                    print_whole_category_list();
                    print_simple_box_end();
                    print_course_search('', false, 'short');
                } else {
                    print_heading_block(get_string('availablecourses'));
                    print_spacer(8,1);
                    print_courses(0, '100%');
                }
            }
        break;

    }

    echo '</td>';


    // The right column
    if(blocks_have_content($pageblocks[BLOCK_POS_RIGHT]) || $editing || isadmin()) {
        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
        if (isadmin()) {
            echo '<div align="center">'.update_course_icon($SITE->id).'</div>';
            echo '<br />';
        }
        blocks_print_group($PAGE, $pageblocks[BLOCK_POS_RIGHT]);
        if ($editing) {
            blocks_print_adminblock($PAGE, $pageblocks);
        }
        echo '</td>';
    }
?>

  </tr>
</table>

<?php
    print_footer('home');     // Please do not modify this line
?>
