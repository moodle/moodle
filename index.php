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

    optional_param('blockaction');
    optional_param('instanceid', 0, PARAM_INT);
    optional_param('blockid',    0, PARAM_INT);

    if (! $site = get_site()) {
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
        $loginstring = '<font size="1">'. user_login_string($site) .'</font>';
    }

    if (empty($CFG->langmenu)) {
        $langmenu = '';
    } else {
        $currlang = current_language();
        $langs = get_list_of_languages();
        $langmenu = popup_form ($CFG->wwwroot .'/index.php?lang=', $langs, 'chooselang', $currlang, '', '', '', true);
    }

    print_header(strip_tags($site->fullname), $site->fullname, 'home', '',
                 '<meta name="description" content="'. s(strip_tags($site->summary)) .'" />',
                 true, '', $loginstring . $langmenu);

    $editing = isediting($site->id);

    $page = new stdClass;
    $page->id   = SITEID;
    $page->type = MOODLE_PAGE_COURSE;

    $pageblocks = blocks_get_by_page($page);

    if($editing) {
        if (!empty($blockaction) && confirm_sesskey()) {
            if (!empty($blockid)) {
                blocks_execute_action($page, $pageblocks, strtolower($blockaction), intval($blockid));
                
            }
            else if (!empty($instanceid)) {
                $instance = blocks_find_instance($instanceid, $pageblocks);
                blocks_execute_action($page, $pageblocks, strtolower($blockaction), $instance);
            }
            // This re-query could be eliminated by judicious programming in blocks_execute_action(),
            // but I'm not sure if it's worth the complexity increase...
            $pageblocks = blocks_get_by_page($page);
        }

        $missingblocks = blocks_get_missing($page, $pageblocks);
    }

    optional_variable($preferred_width_left,  blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]));
    optional_variable($preferred_width_right, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]));
    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);

?>


<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
  <?PHP

    if(blocks_have_content($pageblocks[BLOCK_POS_LEFT]) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;">';
        blocks_print_group($page, $pageblocks[BLOCK_POS_LEFT]);
        echo '</td>';
    }

    echo '<td style="vertical-align: top;">';
    
    
/// Print Section
    if ($site->numsections > 0) {
        print_simple_box_start('center', '100%', $THEME->cellcontent, 5, 'sitetopic');
    
        /// If currently moving a file then show the current clipboard
        if (ismoving($site->id)) {
            $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
            echo '<p><font size="2">';
            echo "$stractivityclipboard&nbsp;&nbsp;(<a href=\"course/mod.php?cancelcopy=true\">". get_string('cancel') .'</a>)';
            echo '</font></p>';
        }


        if (!$section = get_record('course_sections', 'course', $site->id, 'section', 1)) {
            delete_records('course_sections', 'course', $site->id, 'section', 1); // Just in case
            $section->course = $site->id;
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

        get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
        print_section($site, $section, $mods, $modnamesused, true);

        if ($editing) {
            print_section_add_menus($site, $section->section, $modnames);
        }
        print_simple_box_end();
        print_spacer(10);
    }

    switch ($CFG->frontpage) {     /// Display the main part of the front page.
        case FRONTPAGENEWS:
            if (! $newsforum = forum_get_course_forum($site->id, 'news')) {
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
            
            if ($site->newsitems) { //print forums only when needed
                print_heading_block($headertext);
                print_spacer(8,1);
                forum_print_latest_discussions($newsforum->id, $site->newsitems);
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
        echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;">';
        if (isadmin()) {
            echo '<div align="center">'.update_course_icon($site->id).'</div>';
            echo '<br />';
        }
        blocks_print_group($page, $pageblocks[BLOCK_POS_RIGHT]);
        if ($editing && !empty($missingblocks)) {
            blocks_print_adminblock($page, $missingblocks);
        }
        echo '</td>';
    }
?>

  </tr>
</table>

<?php print_footer('home');     // Please do not modify this line ?>
