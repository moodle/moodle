<?PHP // $Id$
      // Display the whole course as "weeks" made of of modules
      // Included from "view.php"

    include_once("$CFG->dirroot/mod/forum/lib.php");

    if (! $sections = get_all_sections($course->id)) {
        $section->course = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record("course_sections", $section);
        if (! $sections = get_all_sections($course->id) ) {
            error("Error finding or creating section structures for this course");
        }
    }

    if (isset($week)) {
        if ($week == "all") {
            unset($USER->section);
        } else {
            $USER->section = $week;
        }
        save_session("USER");
    }

    if ($course->newsitems) {
        $news = forum_get_course_forum($course->id, "news");
    }
    
    $streditsummary = get_string("editsummary");
    $stradd         = get_string("add");
    $stractivities  = get_string("activities");


/// Layout the whole page as three big columns.
    echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";

/// The left column ...

    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=180>";

/// Links to people
    $moddata[]="<A TITLE=\"".get_string("listofallpeople")."\" HREF=\"../user/index.php?id=$course->id\">".get_string("participants")."</A>";
    $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    $editmyprofile = "<A TITLE=\"$USER->firstname $USER->lastname\" HREF=\"../user/view.php?id=$USER->id&course=$course->id\">".get_string("editmyprofile")."</A>";
    if ($USER->description) {
        $moddata[]= $editmyprofile;
    } else {
        $moddata[]= $editmyprofile." <BLINK>*</BLINK>";
    }
    $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    print_side_block(get_string("people"), "", $moddata, $modicon);


/// Then all the links to activities by type
    $moddata = array();
    $modicon = array();
    if ($modnamesused) {
        foreach ($modnamesused as $modname => $modfullname) {
            $moddata[] = "<A HREF=\"../mod/$modname/index.php?id=$course->id\">".$modnamesplural[$modname]."</A>";
            $modicon[] = "<IMG SRC=\"../mod/$modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
        }
    }
    print_side_block($stractivities, "", $moddata, $modicon);

/// Print a form to search forums
    $searchform = forum_print_search_form($course, "", true);
    $searchform = "<DIV ALIGN=\"CENTER\">$searchform</DIV>";
    print_side_block(get_string("search","forum"), $searchform);
    

/// Admin links and controls
    if (isteacher($course->id)) {
        print_course_admin_links($course);
    }


/// Start main column
    echo "</TD><TD WIDTH=\"*\">";
    print_heading_block(get_string("weeklyoutline"));

    echo "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 WIDTH=100%>";


/// Print Week 0 with general activities

    $week = 0;
    $thisweek = $sections[$week];

    if ($thisweek->summary or $thisweek->sequence or isediting($course->id)) {
        echo "<TR>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" VALIGN=top WIDTH=20>&nbsp;</TD>";
        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\" WIDTH=\"100%\">";

        if (isediting($course->id)) {
            $thisweek->summary .= "&nbsp;<A TITLE=\"$streditsummary\" ".
                                  "HREF=\"editsection.php?id=$thisweek->id\"><IMG SRC=\"../pix/t/edit.gif\" ".
                                  "BORDER=0 ALT=\"$streditsummary\"></A></P>";
        }
    
        echo text_to_html($thisweek->summary);
    
        print_section($course, $thisweek, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=$week&add=", 
                        $modnames, "section$week", "", "$stradd...", "mods", $stractivities);
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" VALIGN=top ALIGN=CENTER WIDTH=10>";
        echo "</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";
    }


/// Now all the weekly sections
    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $week = 1;
    $weekofseconds = 604800;
    $course->enddate = $course->startdate + ($weekofseconds * $course->numsections);

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

        $weekday = userdate($weekdate, " %d %B");
        $endweekday = userdate($weekdate+518400, " %d %B");

        if ($thisweek) {
            $highlightcolor = $THEME->cellheading2;
        } else {
            $highlightcolor = $THEME->cellheading;
        }

        echo "<TR>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top WIDTH=20>";
        echo "<P ALIGN=CENTER><FONT SIZE=3><B>$week</B></FONT></P>";
        echo "</TD>";

        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\" WIDTH=\"100%\">";
        echo "<P><FONT SIZE=3 COLOR=\"$THEME->cellheading2\">$weekday - $endweekday</FONT></P>";

        if (! $thisweek = $sections[$week]) {
            $thisweek->course = $course->id;   // Create a new week structure
            $thisweek->section = $week;
            $thisweek->summary = "";
            if (!$thisweek->id = insert_record("course_sections", $thisweek)) {
                notify("Error inserting new week!");
            }
        }

        if (isediting($course->id)) {
            $thisweek->summary .= "&nbsp;<A TITLE=\"$streditsummary\" HREF=\"editsection.php?id=$thisweek->id\"><IMG SRC=\"../pix/t/edit.gif\" BORDER=0 ALT=\"$streditsummary\"></A></P>";
        }

        echo text_to_html($thisweek->summary);

        print_section($course, $thisweek, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=$week&add=", 
                        $modnames, "section$week", "", "$stradd...", "mods", $stractivities);
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top ALIGN=CENTER WIDTH=10>";
        echo "<FONT SIZE=1>";
        if (isset($USER->section)) {
            $strshowallweeks = get_string("showallweeks");
            echo "<A HREF=\"view.php?id=$course->id&week=all\" TITLE=\"$strshowallweeks\"><IMG SRC=../pix/i/all.gif BORDER=0></A></FONT>";
        } else {
            $strshowonlyweek = get_string("showonlyweek", "", $week);
            echo "<A HREF=\"view.php?id=$course->id&week=$week\" TITLE=\"$strshowonlyweek\"><IMG SRC=../pix/i/one.gif BORDER=0></A></FONT>";
        }
        echo "</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";

        $week++;
        $weekdate = $nextweekdate;
    }
    echo "</TABLE>";
    
    if ($news or $course->showrecent) {
        echo "</TD><TD WIDTH=210>";

        // Print all the news items.

        if ($news) {
            print_heading_block(get_string("latestnews"));
            print_simple_box_start("CENTER", "100%", $THEME->cellcontent, 3, 0);
            echo "<FONT SIZE=1>";
            forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "DESC", false);
            echo "</FONT>";
            print_simple_box_end();
            echo "<BR \>";
        }
        
        // Print all the recent activity
        if ($course->showrecent) {
            print_heading_block(get_string("recentactivity"));
            print_simple_box_start("CENTER", "100%", $THEME->cellcontent, 3, 0);
            print_recent_activity($course);
            print_simple_box_end();
        }
    
        echo "<BR \><IMG SRC=\"../pix/spacer.gif\" WIDTH=210 HEIGHT=1>";
    }

    echo "</TD></TR></TABLE>\n";

?>
