<?PHP // $Id$
      // Display the whole course as "weeks" made of of modules
      // Included from "view.php"

    require_once("$CFG->dirroot/mod/forum/lib.php");


    if (isset($week)) {
        if ($week == "all") {
            unset($USER->section);
        } else {
            $USER->section = $week;
        }
    }

    if ($course->newsitems) {
        $news = forum_get_course_forum($course->id, "news");
    }
    
    $streditsummary = get_string("editsummary");
    $stradd         = get_string("add");
    $stractivities  = get_string("activities");


/// Layout the whole page as three big columns.
    echo "<table border=0 cellpadding=3 cellspacing=0 width=100%>";

/// The left column ...

    echo "<tr valign=top><td valign=top width=180>";

/// Links to people
    $moddata[]="<a title=\"".get_string("listofallpeople")."\" href=\"../user/index.php?id=$course->id\">".get_string("participants")."</a>";
    $modicon[]="<img src=\"$pixpath/i/users.gif\" height=16 width=16 alt=\"\">";
    $editmyprofile = "<a title=\"$USER->firstname $USER->lastname\" href=\"../user/edit.php?id=$USER->id&course=$course->id\">".get_string("editmyprofile")."</a>";
    if ($USER->description) {
        $moddata[]= $editmyprofile;
    } else {
        $moddata[]= $editmyprofile." <blink>*</blink>";
    }
    $modicon[]="<img src=\"$pixpath/i/user.gif\" height=16 width=16 alt=\"\">";
    print_side_block(get_string("people"), "", $moddata, $modicon);


/// Then all the links to activities by type
    $moddata = array();
    $modicon = array();
    if ($modnamesused) {
        foreach ($modnamesused as $modname => $modfullname) {
            $moddata[] = "<a href=\"../mod/$modname/index.php?id=$course->id\">".$modnamesplural[$modname]."</a>";
            $modicon[] = "<img src=\"$modpixpath/$modname/icon.gif\" height=16 width=16 alt=\"\">";
        }
    }
    print_side_block($stractivities, "", $moddata, $modicon);

/// Print a form to search forums
    $searchform = forum_print_search_form($course, "", true);
    $searchform = "<DIV ALIGN=\"CENTER\">$searchform</DIV>";
    print_side_block(get_string("search","forum"), $searchform);
    

/// Admin links and controls
    print_course_admin_links($course);


/// Start main column
    echo "</TD><TD WIDTH=\"*\">";
    print_heading_block(get_string("weeklyoutline"), "100%", "outlineheadingblock");
    print_spacer(8, 1, true);

    echo "<table class=\"weeklyoutline\" border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"100%\">";

/// Print Week 0 with general activities

    $week = 0;
    $thisweek = $sections[$week];

    if ($thisweek->summary or $thisweek->sequence or isediting($course->id)) {
        echo "<tr>";
        echo "<td nowrap bgcolor=\"$THEME->cellheading\" class=\"weeklyoutlineside\" valign=top width=20>&nbsp;</td>";
        echo "<td valign=top bgcolor=\"$THEME->cellcontent\" class=\"weeklyoutlinecontent\" width=\"100%\">";

        if (isediting($course->id)) {
            $thisweek->summary .= "&nbsp;<a title=\"$streditsummary\" ".
                                  "href=\"editsection.php?id=$thisweek->id\"><img height=11 width=11 src=\"$pixpath/t/edit.gif\" ".
                                  "border=0 alt=\"$streditsummary\"></a></p>";
        }
    
        echo text_to_html($thisweek->summary);
    
        print_section($course, $thisweek, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<div align=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$week&add=", 
                        $modnames, "section$week", "", "$stradd...", "mods", $stractivities);
            echo "</div>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" class=\"weeklyoutlineside\" VALIGN=top ALIGN=CENTER WIDTH=10>";
        echo "&nbsp;</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";
    }


/// Now all the weekly sections
    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $week = 1;
    $weekofseconds = 604800;
    $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);

    $strftimedateshort = " ".get_string("strftimedateshort");

    while ($weekdate < $course->enddate) {

        $nextweekdate = $weekdate + ($weekofseconds);

        if (isset($USER->section)) {         // Just display a single week
            if ($USER->section != $week) { 
                $week++;
                $weekdate = $nextweekdate;
                continue;
            }
        }

        $thisweek = (($weekdate <= $timenow) && ($timenow < $nextweekdate));

        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate+518400, $strftimedateshort);

        if ($thisweek) {
            $colorsides = "bgcolor=\"$THEME->cellheading2\" class=\"weeklyoutlinesidehighlight\"";
            $colormain  = "bgcolor=\"$THEME->cellcontent\" class=\"weeklyoutlinecontenthighlight\"";
        } else {
            $colorsides = "bgcolor=\"$THEME->cellheading\" class=\"weeklyoutlineside\"";
            $colormain  = "bgcolor=\"$THEME->cellcontent\" class=\"weeklyoutlinecontent\"";
        }

        echo "<tr>";
        echo "<td nowrap $colorsides valign=top width=20>";
        echo "<p align=center><font size=3><b>$week</b></font></p>";
        echo "</td>";

        echo "<td $colormain valign=top width=\"100%\">";
        echo "<p><font size=3 color=\"$THEME->cellheading2\">$weekday - $endweekday</font></p>";

        if (!empty($sections[$week])) {
            $thisweek = $sections[$week];
        } else {
            unset($thisweek);
            $thisweek->course = $course->id;   // Create a new week structure
            $thisweek->section = $week;
            $thisweek->summary = "";
            if (!$thisweek->id = insert_record("course_sections", $thisweek)) {
                notify("Error inserting new week!");
            }
        }

        if (isediting($course->id)) {
            $thisweek->summary .= "&nbsp;<a title=\"$streditsummary\" href=\"editsection.php?id=$thisweek->id\"><img src=\"$pixpath/t/edit.gif\" height=11 width=11 border=0 alt=\"$streditsummary\"></a></p>";
        }

        echo text_to_html($thisweek->summary);

        print_section($course, $thisweek, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<div align=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$week&add=", 
                        $modnames, "section$week", "", "$stradd...");
            echo "</div>";
        }

        echo "</td>";
        echo "<td nowrap $colorsides valign=top align=center width=10>";
        echo "<font size=1>";
        if (isset($USER->section)) {
            $strshowallweeks = get_string("showallweeks");
            echo "<a href=\"view.php?id=$course->id&week=all\" title=\"$strshowallweeks\"><img src=\"$pixpath/i/all.gif\" height=25 width=16 border=0></a></font>";
        } else {
            $strshowonlyweek = get_string("showonlyweek", "", $week);
            echo "<a href=\"view.php?id=$course->id&week=$week\" title=\"$strshowonlyweek\"><img src=\"$pixpath/i/one.gif\" height=16 width=16 border=0></a></font>";
        }
        echo "</td>";
        echo "</tr>";
        echo "<tr><td colspan=3><img src=\"../pix/spacer.gif\" width=1 height=1></td></tr>";

        $week++;
        $weekdate = $nextweekdate;
    }
    echo "</table>";
    
    if (!empty($news) or !empty($course->showrecent)) {
        echo "</td><td width=210>";

        // Print all the news items.

        if (!empty($news)) {
            print_side_block_start(get_string("latestnews"), 210, "sideblocklatestnews");
            echo "<font size=\"-2\">";
            forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "", false);
            echo "</font>";
            print_side_block_end();
        }
        
        // Print all the recent activity
        if (!empty($course->showrecent)) {
            print_side_block_start(get_string("recentactivity"), 210, "sideblockrecentactivity");
            print_recent_activity($course);
            print_side_block_end();
        }
    
        print_spacer(1, 120, true);
    }

    echo "</td></tr></table>\n";

?>
