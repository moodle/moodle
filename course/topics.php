<?PHP // $Id$
      // Display the whole course as "topics" made of of modules
      // In fact, this is very similar to the "weeks" format, in that
      // each "topic" is actually a week.  The main difference is that
      // the dates aren't printed - it's just an aesthetic thing for 
      // courses that aren't so rigidly defined by time.
      // Included from "view.php"

    include_once("$CFG->dirroot/mod/forum/lib.php");

    if (! $sections = get_all_sections($course->id) ) {
        $section->course = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record("course_sections", $section);
        if (! $sections = get_all_sections($course->id) ) {
            error("Error finding or creating section structures for this course");
        }
    }
    
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


/// Layout the whole page as three big columns.
    echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";

/// The left column ...

    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=180>";
    
/// Links to people
    print_simple_box(get_string("people"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    $moddata[]="<A TITLE=\"".get_string("listofallpeople")."\" HREF=\"../user/index.php?id=$course->id\">".get_string("participants")."</A>";
    $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    $editmyprofile = "<A TITLE=\"$USER->firstname $USER->lastname\" HREF=\"../user/view.php?id=$USER->id&course=$course->id\">".get_string("editmyprofile")."</A>";
    if ($USER->description) {
        $moddata[]= $editmyprofile;
    } else {
        $moddata[]= $editmyprofile." <BLINK>*</BLINK>";
    }
    $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    print_side_block("", $moddata, "", $modicon);


/// Links to all activity modules by type
    $moddata = array();
    $modicon = array();
    if ($modnamesused) {
        foreach ($modnamesused as $modname => $modfullname) {
            $moddata[] = "<A HREF=\"../mod/$modname/index.php?id=$course->id\">".$modnamesplural[$modname]."</A>";
            $modicon[] = "<IMG SRC=\"../mod/$modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
        }
    }
    print_simple_box(get_string("activities"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    print_side_block("", $moddata, "", $modicon);

/// Print a form to search forums
    print_simple_box(get_string("search","forum"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    echo "<DIV ALIGN=CENTER>";
    forum_print_search_form($course);
    echo "</DIV>";

/// Admin links and controls
    if (isteacher($course->id)) {
        print_course_admin_links($course);
    }

/// Start main column
    echo "</TD><TD WIDTH=\"*\">";

    print_simple_box(get_string("topicoutline"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    
    $streditsummary = get_string("editsummary");
    $stradd         = get_string("add");


    echo "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 WIDTH=100%>";

/// Print Section 0 

    $topic = 0;
    $thistopic = $sections[$topic];

    if ($thistopic->summary or $thistopic->sequence or isediting($course->id)) {
        echo "<TR>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" VALIGN=top WIDTH=20>&nbsp;</TD>";
        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\" WIDTH=\"100%\">";
    
        if (isediting($course->id)) {
            $thistopic->summary .= "&nbsp;<A TITLE=\"$streditsummary\" ".
                                     "HREF=\"editsection.php?id=$thistopic->id\"><IMG SRC=\"../pix/t/edit.gif\" ".
                                     "BORDER=0 ALT=\"$streditsummary\"></A></P>";
        }
    
        echo text_to_html($thistopic->summary);
    
        print_section($course->id, $thistopic, $mods, $modnamesused);
    
        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=$topic&add=", 
                        $modnames, "section$topic", "", "$stradd...", "mods", get_string("activities"));
            echo "</DIV>";
        }
    
        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" VALIGN=top ALIGN=CENTER WIDTH=10>&nbsp;";
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
            $highlightcolor = $THEME->cellheading2;
        } else {
            $highlightcolor = $THEME->cellheading;
        }

        echo "<TR>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top WIDTH=20>";
        echo "<P ALIGN=CENTER><FONT SIZE=3><B>$section</B></FONT></P>";
        echo "</TD>";

        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\" WIDTH=\"100%\">";

        if (! $thissection = $sections[$section]) {
            $thissection->course = $course->id;   // Create a new section structure
            $thissection->section = $section;
            $thissection->summary = "";
            if (!$thissection->id = insert_record("course_sections", $thissection)) {
                notify("Error inserting new topic!");
            }
        }

        if (isediting($course->id)) {
            $thissection->summary .= "&nbsp;<A HREF=editsection.php?id=$thissection->id><IMG SRC=\"../pix/t/edit.gif\" BORDER=0 ALT=\"$streditsummary\"></A></P>";
        }

        echo text_to_html($thissection->summary);

        print_section($course->id, $thissection, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=$section&add=", 
                        $modnames, "section$section", "", "$stradd...", "mods", get_string("activities"));
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top ALIGN=CENTER WIDTH=10>";
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
    

    echo "</TD><TD WIDTH=210>";

/// Print all the news items.

    if ($news) {
        print_simple_box(get_string("latestnews"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
        print_simple_box_start("CENTER", "100%", "#FFFFFF", 3, 0);
        echo "<FONT SIZE=1>";
        forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "DESC", false);
        echo "</FONT>";
        print_simple_box_end();
        echo "<BR>";
    }
    
    // Print all the recent activity
    print_simple_box(get_string("recentactivity"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    print_simple_box_start("CENTER", "100%", "#FFFFFF", 3, 0);
    print_recent_activity($course);
    print_simple_box_end();

    echo "</TD></TR></TABLE>\n";

?>
