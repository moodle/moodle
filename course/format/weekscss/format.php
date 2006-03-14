<?php // $Id$
      // Display the whole course as "weeks" made of of modules
      // Included from "view.php"
/**
 * Evaluation weekly format for course display - NO layout tables, for accessibility, etc.
 * 
 * A duplicate course format to enable the Moodle development team to evaluate 
 * CSS for the multi-column layout in place of layout tables. 
 * Less risk for the Moodle 1.6 beta release.
 *   1. Straight copy of weeks/format.php
 *   2. Replace <table> and <td> with DIVs; inline styles.
 *   3. Reorder columns so that in linear view content is first then blocks;
 * styles to maintain original graphical (side by side) view.
 *
 * Target: 3-column graphical view using relative widths for pixel screen sizes 
 * 800x600, 1024x768... on IE6, Firefox. Below 800 columns will shift downwards.
 * 
 * http://www.maxdesign.com.au/presentation/em/ Ideal length for content.
 * http://www.svendtofte.com/code/max_width_in_ie/ Max width in IE.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear@open.ac.uk, and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package  
 */
//TODO (nfreear): Accessibility: evaluation, lang/en_utf8/moodle.php: $string['formatweekscss']

    require_once("$CFG->dirroot/mod/forum/lib.php");

    // Bounds for block widths; in PIXELS.
    define('BLOCK_L_MIN_WIDTH', 100);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 100);
    define('BLOCK_R_MAX_WIDTH', 210);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                            BLOCK_R_MAX_WIDTH);
    // 'Answer' in pixels: left 180, right 210.
    
    //Accessibility: relative block widths to approximate current pixel widths.
    // Min/max-width not supported by IE6.
    $preferred_width_left  = '18%; ';
    $preferred_width_right = '18%; ';
    $width_center = '60%; ';
    $min_max_block= 'min-width:100px; max-width:210px; ';
    $min_max_main = 'min-width:25em; max-width:37em; '; //33em

    if (isset($week)) {
        $displaysection = course_set_display($course->id, $week);
    } else {
        if (isset($USER->display[$course->id])) {
            $displaysection = $USER->display[$course->id];
        } else {
            $displaysection = course_set_display($course->id, 0);
        }
    }

    if ($course->newsitems) {
        $news = forum_get_course_forum($course->id, 'news');
    }

    $streditsummary  = get_string('editsummary');
    $stradd          = get_string('add');
    $stractivities   = get_string('activities');
    $strshowallweeks = get_string('showallweeks');
    $strweek         = get_string('week');
    $strgroups       = get_string('groups');
    $strgroupmy      = get_string('groupmy');
    $editing         = $PAGE->user_is_editing();

    if ($editing) {
        $strstudents = moodle_strtolower($course->students);
        $strweekhide = get_string('weekhide', '', $strstudents);
        $strweekshow = get_string('weekshow', '', $strstudents);
        $strmoveup   = get_string('moveup');
        $strmovedown = get_string('movedown');
    }


/// Layout the whole page as three big columns.
    echo '<div id="layout-table" class="weekscss">';

/// The left column ...

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<div style="width:'.$preferred_width_left.$min_max_block.'float:left;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</div>';
    }

/// Start main column
    echo '<div id="middle-column" style="width:'.$width_center.$min_max_main.'float:left;">';


    print_heading_block(get_string('weeklyoutline'), 'outline');

    echo '<table class="weeks" width="100%">';

/// If currently moving a file then show the current clipboard
    if (ismoving($course->id)) {
        $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
        $strcancel= get_string('cancel');
        echo '<tr class="clipboard">';
        echo '<td colspan="3">';
        echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
        echo '</td>';
        echo '</tr>';
    }

/// Print Section 0 with general activities

    $section = 0;
    $thissection = $sections[$section];

    if ($thissection->summary or $thissection->sequence or isediting($course->id)) {
        echo '<tr id="section-0" class="section main">';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        
        echo '<div class="summary">';
        $summaryformatoptions->noclean = true;
        echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

        if (isediting($course->id)) {
            echo '<a title="'.$streditsummary.'" '.
                 ' href="editsection.php?id='.$thissection->id.'"><img src="'.$CFG->pixpath.'/t/edit.gif" '.
                 ' height="11" width="11" border="0" alt="'.$streditsummary.'" /></a><br /><br />';
        }
        echo '</div>';
        
        print_section($course, $thissection, $mods, $modnamesused);

        if (isediting($course->id)) {
            print_section_add_menus($course, $section, $modnames);
        }

        echo '</td>';
        echo '<td class="right side">&nbsp;</td>';
        echo '</tr>';
        //TODO (nfreear): Accessibility: how to get rid of the 'separator' row??
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';

    }


/// Now all the normal modules by week
/// Everything below uses "section" terminology - each "section" is a week.

    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $weekdate += 7200;                 // Add two hours to avoid possible DST problems
    $section = 1;
    $sectionmenu = array();
    $weekofseconds = 604800;
    $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);

    $strftimedateshort = ' '.get_string('strftimedateshort');

    while ($weekdate < $course->enddate) {

        $nextweekdate = $weekdate + ($weekofseconds);
        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate+518400, $strftimedateshort);

        if (!empty($sections[$section])) {
            $thissection = $sections[$section];

        } else {
            unset($thissection);
            $thissection->course = $course->id;   // Create a new week structure
            $thissection->section = $section;
            $thissection->summary = '';
            $thissection->visible = 1;
            if (!$thissection->id = insert_record('course_sections', $thissection)) {
                notify('Error inserting new week!');
            }
        }

        $showsection = (isteacher($course->id) or $thissection->visible or !$course->hiddensections);

        if (!empty($displaysection) and $displaysection != $section) {  // Check this week is visible
            if ($showsection) {
                $sectionmenu['week='.$section] = s("$strweek $section |     $weekday - $endweekday");
            }
            $section++;
            $weekdate = $nextweekdate;
            continue;
        }

        if ($showsection) {

            $currentweek = (($weekdate <= $timenow) && ($timenow < $nextweekdate));

            if (!$thissection->visible) {
                $sectionstyle = ' hidden';
            } else if ($currentweek) {
                $sectionstyle = ' current';
            } else {
                $sectionstyle = '';
            }

            echo '<tr id="section-'.$section.'" class="section main'.$sectionstyle.'">';
            echo '<td class="left side">&nbsp;</td>';

            echo '<td class="content">';
            if (!isteacher($course->id) and !$thissection->visible) {   // Hidden for students
                echo '<div class="weekdates">'.$weekday.' - '.$endweekday.' ('.get_string('notavailable').')</div>';

            } else {
                echo '<div class="weekdates">'.$weekday.' - '.$endweekday.'</div>';

                echo '<div class="summary">';
                $summaryformatoptions->noclean = true;
                echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

                if (isediting($course->id)) {
                    echo ' <a title="'.$streditsummary.'" href="editsection.php?id='.$thissection->id.'">'.
                         '<img src="'.$CFG->pixpath.'/t/edit.gif" border="0" height="11" width="11" alt="" /></a><br /><br />';
                }
                echo '</div>';

                print_section($course, $thissection, $mods, $modnamesused);

                if (isediting($course->id)) {
                    print_section_add_menus($course, $section, $modnames);
                }
            }
            echo '</td>';

            echo '<td class="right side">';

            if ($displaysection == $section) {
                echo '<a href="view.php?id='.$course->id.'&amp;week=all#section-'.$section.'" title="'.$strshowallweeks.'">'.
                     '<img src="'.$CFG->pixpath.'/i/all.gif" height="25" width="16" border="0" alt="'.$strshowallweeks.'" /></a><br />';
            } else {
                $strshowonlyweek = get_string("showonlyweek", "", $section);
                echo '<a href="view.php?id='.$course->id.'&amp;week='.$section.'" title="'.$strshowonlyweek.'">'.
                     '<img src="'.$CFG->pixpath.'/i/one.gif" height="16" width="16" border="0" alt="" /></a><br />';
            }

            if (isediting($course->id)) {
                if ($thissection->visible) {        // Show the hide/show eye
                    echo '<a href="view.php?id='.$course->id.'&amp;hide='.$section.'&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strweekhide.'">'.
                         '<img src="'.$CFG->pixpath.'/i/hide.gif" vspace="3" height="16" width="16" border="0" alt="" /></a><br />';
                } else {
                    echo '<a href="view.php?id='.$course->id.'&amp;show='.$section.'&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strweekhide.'">'.
                         '<img src="'.$CFG->pixpath.'/i/show.gif" vspace="3" height="16" width="16" border="0" alt="" /></a><br />';
                }
                if ($section > 1) {                       // Add a arrow to move section up
                    echo '<a href="view.php?id='.$course->id.'&amp;section='.$section.'&amp;move=-1&amp;sesskey='.$USER->sesskey.'#section-'.($section-1).'" title="'.$strmoveup.'">'.
                         '<img src="'.$CFG->pixpath.'/t/up.gif" vspace="3" height="11" width="11" border="0" alt="" /></a><br />';
                }

                if ($section < $course->numsections) {    // Add a arrow to move section down
                    echo '<a href="view.php?id='.$course->id.'&amp;section='.$section.'&amp;move=1&amp;sesskey='.$USER->sesskey.'#section-'.($section+1).'" title="'.$strmovedown.'">'.
                         '<img src="'.$CFG->pixpath.'/t/down.gif" vspace="3" height="11" width="11" border="0" alt="" /></a><br />';
                }
            }

            echo '</td></tr>';
            //TODO (nfreear): Accessibility: how to get rid of the 'separator' row??
            echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
        }

        $section++;
        $weekdate = $nextweekdate;
    }
    echo '</table>';

    if (!empty($sectionmenu)) {
        echo '<div align="center" class="jumpmenu">';
        echo popup_form($CFG->wwwroot.'/course/view.php?id='.$course->id.'&amp;', $sectionmenu,
                   'sectionmenu', '', get_string('jumpto'), '', '', true);
        echo '</div>';
    }

    echo '</div>';

    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
        echo '<div style="width:'.$preferred_width_right.$min_max_block.' float:right;" id="right-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</div>';
    }

    echo '<div class="clearer"></div>';
    echo '</div>';

?>
