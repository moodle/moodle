<?PHP // $Id$
      // Display the whole course as "topics" made of of modules
      // In fact, this is very similar to the "weeks" format, in that
      // each "topic" is actually a week.  The main difference is that
      // the dates aren't printed - it's just an aesthetic thing for 
      // courses that aren't so rigidly defined by time.
      // Included from "view.php"

    include_once("$CFG->dirroot/mod/forum/lib.php");

    if (isset($topic)) {
        if ($topic == "all") {
            unset($USER->topic);
        } else {
            $USER->topic = $topic;
        }
        save_session("USER");
    }

    if (isteacher($course->id) and isset($marker)) {
        $course->marker = $marker;
        if (! set_field("course", "marker", $marker, "id", $course->id)) {
            error("Could not mark that topic for this course");
        }
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


/// Links to all activity modules by type
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
    print_course_admin_links($course);

/// Start main column
    echo "</TD><TD WIDTH=\"*\">";

    print_heading_block(get_string("topicoutline"), "100%", "outlineheadingblock");
    print_spacer(8, 1, true);

    echo "<table class=\"topicsoutline\" border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"100%\">";

/// Print Section 0 

    $topic = 0;
    $thistopic = $sections[$topic];

    if ($thistopic->summary or $thistopic->sequence or isediting($course->id)) {
        echo "<TR>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" class=\"topicsoutlineside\" VALIGN=top WIDTH=20>&nbsp;</TD>";
        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\" class=\"topicsoutlinecontent\" WIDTH=\"100%\">";
    
        if (isediting($course->id)) {
            $thistopic->summary .= "&nbsp;<A TITLE=\"$streditsummary\" ".
                                     "HREF=\"editsection.php?id=$thistopic->id\"><IMG SRC=\"../pix/t/edit.gif\" ".
                                     "BORDER=0 ALT=\"$streditsummary\"></A></P>";
        }
    
        echo text_to_html($thistopic->summary);
    
        print_section($course, $thistopic, $mods, $modnamesused);
    
        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$topic&add=", 
                        $modnames, "section$topic", "", "$stradd...", "mods", $stractivities);
            echo "</DIV>";
        }
    
        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" class=\"topicsoutlineside\" VALIGN=top ALIGN=CENTER WIDTH=10>&nbsp;";
        echo "</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";
    }


/// Now all the normal modules by topic
/// Everything below uses "section" terminology - each "section" is a topic.

    $timenow = time();
    $section = 1;

    while ($section <= $course->numsections) {

        if (isset($USER->topic)) {         // Just display a single topic
            if ($USER->topic != $section) { 
                $section++;
                continue;
            }
        }

        $currenttopic = ($course->marker == $section);

        if ($currenttopic) {
            $colorsides = "bgcolor=\"$THEME->cellheading2\" class=\"topicsoutlinesidehighlight\"";
            $colormain  = "bgcolor=\"$THEME->cellcontent\" class=\"topicsoutlinecontenthighlight\"";
        } else {
            $colorsides = "bgcolor=\"$THEME->cellheading\" class=\"topicsoutlineside\"";
            $colormain  = "bgcolor=\"$THEME->cellcontent\" class=\"topicsoutlinecontent\"";
        }

        echo "<TR>";
        echo "<TD NOWRAP $colorsides VALIGN=top WIDTH=20>";
        echo "<P ALIGN=CENTER><FONT SIZE=3><B>$section</B></FONT></P>";
        echo "</TD>";

        echo "<TD VALIGN=top $colormain WIDTH=\"100%\">";

        if (! $thissection = $sections[$section]) {
            $thissection->course = $course->id;   // Create a new section structure
            $thissection->section = $section;
            $thissection->summary = "";
            if (!$thissection->id = insert_record("course_sections", $thissection)) {
                notify("Error inserting new topic!");
            }
        }

        if (isediting($course->id)) {
            $thissection->summary .= "&nbsp;<A HREF=editsection.php?id=$thissection->id><IMG SRC=\"../pix/t/edit.gif\" BORDER=0 ALT=\"$streditsummary\"></A>";
        }

        echo text_to_html($thissection->summary);

        print_section($course, $thissection, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&add=", 
                        $modnames, "section$section", "", "$stradd...", "mods", $stractivities);
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP $colorsides VALIGN=top ALIGN=CENTER WIDTH=10>";
        echo "<FONT SIZE=1>";
        if (isset($USER->topic)) {
            $strshowalltopics = get_string("showalltopics");
            echo "<A HREF=\"view.php?id=$course->id&topic=all\" TITLE=\"$strshowalltopics\"><IMG SRC=../pix/i/all.gif BORDER=0></A><BR><BR>";
        } else {
            $strshowonlytopic = get_string("showonlytopic", "", $section);
            echo "<A HREF=\"view.php?id=$course->id&topic=$section\" TITLE=\"$strshowonlytopic\"><IMG SRC=../pix/i/one.gif BORDER=0></A><BR><BR>";
        }
        if (isediting($course->id) and $course->marker != $section) {
            $strmarkthistopic = get_string("markthistopic");
            echo "<A HREF=\"view.php?id=$course->id&marker=$section\" TITLE=\"$strmarkthistopic\"><IMG SRC=../pix/i/marker.gif BORDER=0></A><BR><BR>";
        }
        echo "</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";

        $section++;
    }
    echo "</TABLE>";
    

    if ($news or $course->showrecent) {
        echo "</TD><TD WIDTH=210>";

        /// Print all the news items.

        if ($news) {
            print_side_block_start(get_string("latestnews"), 210, "sideblocklatestnews");
            echo "<FONT SIZE=\"-2\">";
            forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "DESC", false);
            echo "</FONT>";
            print_side_block_end();
        }
        
        // Print all the recent activity
        if ($course->showrecent) {
            print_side_block_start(get_string("recentactivity"), 210, "sideblockrecentactivity");
            print_recent_activity($course);
            print_side_block_end();
        }
    
        print_spacer(1, 120, true);
    }

    echo "</TD></TR></TABLE>\n";

?>
