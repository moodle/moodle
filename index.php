<?PHP  // $Id$
       // index.php - the front page.

    // Bounds for block widths
    define('BLOCK_L_MIN_WIDTH', 160);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 160);
    define('BLOCK_R_MAX_WIDTH', 210);

    require_once("config.php");
    require_once("course/lib.php");
    require_once('lib/blocklib.php');
    require_once("mod/resource/lib.php");
    require_once("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect("$CFG->wwwroot/$CFG->admin/index.php");
        }
    }

    if (empty($USER->id)) {
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http','https',$CFG->wwwroot);
        }
        $loginstring = "<font size=2><a href=\"$wwwroot/login/index.php\">".get_string("login")."</a></font>";
    } else {
        $loginstring = "<font size=1>".user_login_string($site)."</font>";
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    print_header(strip_tags($site->fullname), "$site->fullname", "home", "",
                 "<meta name=\"description\" content=\"".s(strip_tags($site->summary))."\">",
                 true, "", "$loginstring$langmenu");

    $editing = isediting($site->id);

    $courseformat = COURSE_FORMAT_SITE;

    // Doing this now so we can pass the results to block_action()
    // and dodge the overhead of doing the same work twice.

    $blocks = $site->blockinfo;
    $delimpos = strpos($blocks, ':');

    if($delimpos === false) {
        // No ':' found, we have all left blocks
        $leftblocks = explode(',', $blocks);
        $rightblocks = array();
    }
    else if($delimpos === 0) {
        // ':' at start of string, we have all right blocks
        $blocks = substr($blocks, 1);
        $leftblocks = array();
        $rightblocks = explode(',', $blocks);
    }
    else {
        // Both left and right blocks
        $leftpart = substr($blocks, 0, $delimpos);
        $rightpart = substr($blocks, $delimpos + 1);
        $leftblocks = explode(',', $leftpart);
        $rightblocks = explode(',', $rightpart);
    }

    if($editing) {
        if (isset($_GET['blockaction'])) {
            if (isset($_GET['blockid'])) {
                block_action($site, $leftblocks, $rightblocks, strtolower($_GET['blockaction']), intval($_GET['blockid']));
            }
        }

        // This has to happen after block_action() has possibly updated the two arrays
        $allblocks = array_merge($leftblocks, $rightblocks);

        $missingblocks = array();
        $recblocks = get_records('blocks','visible','1');

        // Note down which blocks are going to get displayed
        blocks_used($allblocks, $recblocks);

        if($editing && $recblocks) {
            foreach($recblocks as $recblock) {
                // If it's not hidden or displayed right now...
                if(!in_array($recblock->id, $allblocks) && !in_array(-($recblock->id), $allblocks)) {
                    // And if it's applicable for display in this format...
                    if(block_method_result($recblock->name, 'applicable_formats') & $courseformat) {
                        // Add it to the missing blocks
                        $missingblocks[] = $recblock->id;
                    }
                }
            }
        }
    }
    else {
        // Note down which blocks are going to get displayed
        $allblocks = array_merge($leftblocks, $rightblocks);
        $recblocks = get_records('blocks','visible','1');
        blocks_used($allblocks, $recblocks);
    }

    // If the block width cache is not set, set it
    if(!isset($SESSION->blockcache->width->{$site->id}) || $editing) {
        // This query might be optimized away if we 're in editing mode
        if(!isset($recblocks)) {
            $recblocks = get_records('blocks','visible','1');
        }
        $preferred_width_left = blocks_preferred_width($leftblocks, $recblocks);
        $preferred_width_right = blocks_preferred_width($rightblocks, $recblocks);

        // This may be kind of organizational overkill, granted...
        // But is there any real need to simplify the structure?
        $SESSION->blockcache->width->{$site->id}->left = $preferred_width_left;
        $SESSION->blockcache->width->{$site->id}->right = $preferred_width_right;
    }
    else {
        $preferred_width_left = $SESSION->blockcache->width->{$site->id}->left;
        $preferred_width_right = $SESSION->blockcache->width->{$site->id}->right;
    }

    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);

?>


<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
  <?PHP

    if(block_have_active($leftblocks) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;">';
        print_course_blocks($site, $leftblocks, BLOCK_LEFT);
        echo '</td>';
    }

    echo '<td style="vertical-align: top;">';
    
    
/// Print Section
    if ($site->numsections > 0) {
        print_simple_box_start('center', '100%', $THEME->cellcontent, 5, 'sitetopic');
    
        /// If currently moving a file then show the current clipboard
        if (ismoving($site->id)) {
            $stractivityclipboard = strip_tags(get_string("activityclipboard", "", addslashes($USER->activitycopyname)));
            echo "<p><font size=2>";
            echo "$stractivityclipboard&nbsp;&nbsp;(<a href=\"course/mod.php?cancelcopy=true\">".get_string("cancel")."</a>)";
            echo "</font></p>";
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
                 " height=11 width=11 border=0 alt=\"$streditsummary\"></a><br />";
        }

        echo '<br clear="all">';

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
            if (! $newsforum = forum_get_course_forum($site->id, "news")) {
                error("Could not find or create a main news forum for the site");
            }

            if (isset($USER->id)) {
                $SESSION->fromdiscussion = "$CFG->wwwroot";
                if (forum_is_subscribed($USER->id, $newsforum->id)) {
                    $subtext = get_string("unsubscribe", "forum");
                } else {
                    $subtext = get_string("subscribe", "forum");
                }
                $headertext = "<table border=0 width=100% cellpadding=0 cellspacing=0 class=headingblockcontent><tr>
                               <td>$newsforum->name</td>
                               <td align=right><font size=1>
                               <a href=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</a>
                               </td></tr></table>";
            } else {
                $headertext = $newsforum->name;
            }
            print_heading_block($headertext);
            print_spacer(8,1);
            forum_print_latest_discussions($newsforum->id, $site->newsitems);
        break;

        case FRONTPAGECOURSELIST:
        case FRONTPAGECATEGORYNAMES:
            if (isset($USER->id) and !isset($USER->admin)) {
                print_heading_block(get_string("mycourses"));
                print_spacer(8,1);
                print_my_moodle();
            } else {
                if (count_records("course_categories") > 1) {
                    if ($CFG->frontpage == FRONTPAGECOURSELIST) {
                        print_heading_block(get_string("availablecourses"));
                    } else {
                        print_heading_block(get_string("categories"));
                    }
                    print_spacer(8,1);
                    print_simple_box_start("center", "100%");
                    print_whole_category_list();
                    print_simple_box_end();
                    print_course_search("", false, "short");
                } else {
                    print_heading_block(get_string("availablecourses"));
                    print_spacer(8,1);
                    print_courses(0, "100%");
                }
            }
        break;

    }

    echo '</td>';
    if(block_have_active($rightblocks) || $editing || isadmin()) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;">';
        if (isadmin()) {
            echo '<div align="center">'.update_course_icon($site->id).'</div>';
            echo '<br />';
        }
        print_course_blocks($site, $rightblocks, BLOCK_RIGHT);
        if ($editing && !empty($missingblocks)) {
            block_print_blocks_admin($site, $missingblocks);
        }
        echo '</td>';
    }
?>

  </tr>
</table>

<?PHP print_footer('home');     // Please do not modify this line ?>

