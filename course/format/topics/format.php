<?php // $Id$
      // Display the whole course as "topics" made of of modules
      // In fact, this is very similar to the "weeks" format, in that
      // each "topic" is actually a week.  The main difference is that
      // the dates aren't printed - it's just an aesthetic thing for
      // courses that aren't so rigidly defined by time.
      // Included from "view.php"

    require_once("$CFG->dirroot/mod/forum/lib.php");

    // Bounds for block widths
    define('BLOCK_L_MIN_WIDTH', 100);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 100);
    define('BLOCK_R_MAX_WIDTH', 210);

    optional_variable($preferred_width_left,  blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]));
    optional_variable($preferred_width_right, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]));
    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);

    if (isset($topic)) {
        $displaysection = course_set_display($course->id, $topic);
    } else {
        if (isset($USER->display[$course->id])) {       // for admins, mostly
            $displaysection = $USER->display[$course->id];
        } else {
            $displaysection = course_set_display($course->id, 0);
        }
    }

    if (isteacher($course->id) and isset($marker) and confirm_sesskey()) {
        $course->marker = $marker;
        if (! set_field("course", "marker", $marker, "id", $course->id)) {
            error("Could not mark that topic for this course");
        }
    }

    $streditsummary   = get_string("editsummary");
    $stradd           = get_string("add");
    $stractivities    = get_string("activities");
    $strshowalltopics = get_string("showalltopics");
    $strtopic         = get_string("topic");
    $strgroups       = get_string("groups");
    $strgroupmy      = get_string("groupmy");
    if ($editing) {
        $strstudents = moodle_strtolower($course->students);
        $strtopichide = get_string("topichide", "", $strstudents);
        $strtopicshow = get_string("topicshow", "", $strstudents);
        $strmarkthistopic = get_string("markthistopic");
        $strmarkedthistopic = get_string("markedthistopic");
        $strmoveup = get_string("moveup");
        $strmovedown = get_string("movedown");
    }


/// Layout the whole page as three big columns.
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\" id=\"layout-table\">";

    echo "<tr valign=\"top\">\n";

/// The left column ...

    if(blocks_have_content($pageblocks[BLOCK_POS_LEFT]) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks[BLOCK_POS_LEFT]);
        echo '</td>';
    }

/// Start main column
    echo '<td valign="top" width="*" id="middle-column">';

    print_heading_block(get_string("topicoutline"), "100%", "outlineheadingblock");
    print_spacer(8, 1, true);

    echo "<table class=\"topicsoutline\" border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"100%\">";

/// If currently moving a file then show the current clipboard
    if (ismoving($course->id)) {
        $stractivityclipboard = strip_tags(get_string("activityclipboard", "", addslashes($USER->activitycopyname)));
        $strcancel= get_string("cancel");
        echo "<tr>";
        echo "<td colspan=\"3\" valign=\"top\" class=\"topicoutlineclip\" width=\"100%\">";
        echo "<div><font size=\"2\">";
        echo "$stractivityclipboard&nbsp;&nbsp;(<a href=\"mod.php?cancelcopy=true&amp;sesskey=$USER->sesskey\">$strcancel</a>)";
        echo "</font></div>";
        echo "</td>";
        echo "</tr>";
        echo "<tr><td colspan=\"3\"><img src=\"../pix/spacer.gif\" width=\"1\" height=\"1\" alt=\"\" /></td></tr>";
    }


/// Print Section 0

    $section = 0;
    $thissection = $sections[$section];

    if ($thissection->summary or $thissection->sequence or isediting($course->id)) {
        echo '<tr id="section_0">';
        echo '<td nowrap="nowrap" class="topicsoutlineside" valign="top" width="20">&nbsp;</td>';
        echo '<td valign="top"  class="topicsoutlinecontent" width="100%">';

        $summaryformatoptions->noclean = true;
        echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

        if (isediting($course->id)) {
            echo '<a title="'.$streditsummary.'" '.
                 ' href="editsection.php?id='.$thissection->id.'"><img src="'.$CFG->pixpath.'/t/edit.gif" '.
                 ' height="11" width="11" border="0" alt="'.$streditsummary.'" /></a><br /><br />';
        }

        print_section($course, $thissection, $mods, $modnamesused);

        if (isediting($course->id)) {
            print_section_add_menus($course, $section, $modnames);
        }

        echo '</td>';
        echo '<td nowrap="nowrap" class="topicsoutlineside" valign="top" align="center" width="10">';
        echo '&nbsp;</td></tr>';
        echo '<tr><td colspan="3"><img src="../pix/spacer.gif" width="1" height="1" alt="" /></td></tr>';
    }


/// Now all the normal modules by topic
/// Everything below uses "section" terminology - each "section" is a topic.

    $timenow = time();
    $section = 1;
    $sectionmenu = array();

    while ($section <= $course->numsections) {

        if (!empty($displaysection) and $displaysection != $section) {
            if (empty($sections[$section])) {
                $strsummary = "";
            } else {
                $strsummary = " - ".strip_tags($sections[$section]->summary);
                if (strlen($strsummary) < 57) {
                    $strsummary = " - $strsummary";
                } else {
                    $strsummary = " - ".substr($strsummary, 0, 60)."...";
                }
            }
            $sectionmenu["topic=$section"] = s("$section$strsummary");
            $section++;
            continue;
        }

        if (!empty($sections[$section])) {
            $thissection = $sections[$section];

        } else {
            unset($thissection);
            $thissection->course = $course->id;   // Create a new section structure
            $thissection->section = $section;
            $thissection->summary = "";
            $thissection->visible = 1;
            if (!$thissection->id = insert_record("course_sections", $thissection)) {
                notify("Error inserting new topic!");
            }
        }

        $showsection = (isteacher($course->id) or $thissection->visible or !$course->hiddensections);

        if ($showsection) {

            $currenttopic = ($course->marker == $section);

            if (!$thissection->visible) {
                $colorsides = 'class="topicsoutlinesidehidden"';
                $colormain  = 'class="topicsoutlinecontenthidden"';
            } else if ($currenttopic) {
                $colorsides = 'class="topicsoutlinesidehighlight"';
                $colormain  = 'class="topicsoutlinecontenthighlight"';
            } else {
                $colorsides = 'class="topicsoutlineside"';
                $colormain  = 'class="topicsoutlinecontent"';
            }

            echo "<tr>";
            echo "<td nowrap=\"nowrap\" $colorsides valign=\"top\" width=\"20\">";
            echo "<div align=\"center\"><font size=\"3\"><b><a name=\"$section\">$section</a></b></font></div>";
            echo "</td>";

            if (!isteacher($course->id) and !$thissection->visible) {   // Hidden for students
                echo '<td id="section_'.($section).'" style="vertical-align:top; text-align: center; width: 100%;" '.$colormain.'>';
                echo get_string("notavailable");
                echo "</td>";
            } else {
                echo '<td id="section_'.($section).'" style="vertical-align:top; width: 100%;" '.$colormain.'>';

                $summaryformatoptions->noclean = true;
                echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

                if (isediting($course->id)) {
                    echo " <a title=\"$streditsummary\" href=\"editsection.php?id=$thissection->id\">".
                         "<img src=\"$CFG->pixpath/t/edit.gif\" border=\"0\" height=\"11\" width=\"11\" alt=\"\" /></a><br /><br />";
                }

                print_section($course, $thissection, $mods, $modnamesused);

                if (isediting($course->id)) {
                    print_section_add_menus($course, $section, $modnames);
                }

                echo "</td>";
            }
            echo "<td nowrap=\"nowrap\" $colorsides valign=\"top\" align=\"center\" width=\"10\">";
            echo "<font size=\"1\">";

            if ($displaysection == $section) {      // Show the zoom boxes
                echo "<a href=\"view.php?id=$course->id&amp;topic=all\" title=\"$strshowalltopics\">".
                     "<img src=\"$CFG->pixpath/i/all.gif\" height=\"25\" width=\"16\" border=\"0\" /></a><br />";
            } else {
                $strshowonlytopic = get_string("showonlytopic", "", $section);
                echo "<a href=\"view.php?id=$course->id&amp;topic=$section\" title=\"$strshowonlytopic\">".
                     "<img src=\"$CFG->pixpath/i/one.gif\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a><br />";
            }

            if (isediting($course->id)) {
                if ($course->marker == $section) {  // Show the "light globe" on/off
                    echo "<a href=\"view.php?id=$course->id&amp;marker=0&amp;sesskey=$USER->sesskey\" title=\"$strmarkedthistopic\">".
                         "<img src=\"$CFG->pixpath/i/marked.gif\" vspace=\"3\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a><br />";
                } else {
                    echo "<a href=\"view.php?id=$course->id&amp;marker=$section&amp;sesskey=$USER->sesskey\" title=\"$strmarkthistopic\">".
                         "<img src=\"$CFG->pixpath/i/marker.gif\" vspace=\"3\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a><br />";
                }

                if ($thissection->visible) {        // Show the hide/show eye
                    echo "<a href=\"view.php?id=$course->id&amp;hide=$section&amp;sesskey=$USER->sesskey\" title=\"$strtopichide\">".
                         "<img src=\"$CFG->pixpath/i/hide.gif\" vspace=\"3\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a><br />";
                } else {
                    echo "<a href=\"view.php?id=$course->id&amp;show=$section&amp;sesskey=$USER->sesskey\" title=\"$strtopicshow\">".
                         "<img src=\"$CFG->pixpath/i/show.gif\" vspace=\"3\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a><br />";
                }

                if ($section > 1) {                       // Add a arrow to move section up
                    echo "<a href=\"view.php?id=$course->id&amp;section=$section&amp;move=-1&amp;sesskey=$USER->sesskey\" title=\"$strmoveup\">".
                         "<img src=\"$CFG->pixpath/t/up.gif\" vspace=\"3\" height=\"11\" width=\"11\" border=\"0\" alt=\"\" /></a><br />";
                }

                if ($section < $course->numsections) {    // Add a arrow to move section down
                    echo "<a href=\"view.php?id=$course->id&amp;section=$section&amp;move=1&amp;sesskey=$USER->sesskey\" title=\"$strmovedown\">".
                         "<img src=\"$CFG->pixpath/t/down.gif\" vspace=\"3\" height=\"11\" width=\"11\" border=\"0\" alt=\"\" /></a><br />";
                }

            }

            echo "</font>";
            echo "</td>";
            echo "</tr>";
            echo "<tr><td colspan=\"3\"><img src=\"../pix/spacer.gif\" width=\"1\" height=\"1\" alt=\"\" /></td></tr>";
        }

        $section++;
    }
    echo "</table>";

    if (!empty($sectionmenu)) {
        echo "<center>";
        echo popup_form("$CFG->wwwroot/course/view.php?id=$course->id&", $sectionmenu,
                   "sectionmenu", "", get_string("jumpto"), "", "", true);
        echo "</center>";
    }


    echo "</td>";

    // The right column
    if(blocks_have_content($pageblocks[BLOCK_POS_RIGHT]) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;" id="right-column">';
        blocks_print_group($PAGE, $pageblocks[BLOCK_POS_RIGHT]);
        if ($editing) {
            blocks_print_adminblock($PAGE, $pageblocks);
        }
        echo '</td>';
    }

    echo "</tr>\n";
    echo "</table>\n";

?>
