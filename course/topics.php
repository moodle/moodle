<?PHP // $Id$
      // Display the whole course as "topics" made of of modules
      // In fact, this is very similar to the "weeks" format, in that
      // each "topic" is actually a week.  The main difference is that
      // the dates aren't printed - it's just an aesthetic thing for 
      // courses that aren't so rigidly defined by time.
      // Included from "view.php"

    include("../mod/forum/lib.php");

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


    // Layout the whole page as three big columns.
    echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";
    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=180>";
    
    // Layout the left column


    // Links to people

    $blinker = " <BLINK>*</BLINK>";

    print_simple_box("People", $align="CENTER", $width="100%", $color="$THEME->cellheading");
    $moddata[]="<A HREF=\"../user/index.php?id=$course->id\">List of all people</A>";
    $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"List of everyone\">";
    $editmyprofile = "<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">Edit my profile</A>";
    if ($USER->description) {
        $moddata[]= $editmyprofile;
    } else {
        $moddata[]= $editmyprofile.$blinker;
    }
    $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"Me\">";
    print_side_block("", $moddata, "", $modicon);


    // Then all the links to module types

    $moddata = array();
    $modicon = array();
    if ($modnamesused) {
        foreach ($modnamesused as $modname => $modfullname) {
            $moddata[] = "<A HREF=\"../mod/$modname/index.php?id=$course->id\">".$modnamesplural[$modname]."</A>";
            $modicon[] = "<IMG SRC=\"../mod/$modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$modfullname\">";
        }
    }
    print_simple_box("Activities", $align="CENTER", $width="100%", $color="$THEME->cellheading");
    print_side_block("", $moddata, "", $modicon);

    // Print a form to search forums
    print_simple_box(get_string("search","forum"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
    echo "<DIV ALIGN=CENTER>";
    forum_print_search_form($course);
    echo "</DIV>";

    // Admin links and controls

    if (isteacher($course->id)) {
        $adminicon[]="<IMG SRC=\"../pix/i/edit.gif\" HEIGHT=16 WIDTH=16 ALT=\"Edit\">";
        if (isediting($course->id)) {
            $admindata[]="<A HREF=\"view.php?id=$course->id&edit=off\">Turn editing off</A>";
        } else {
            $admindata[]="<A HREF=\"view.php?id=$course->id&edit=on\">Turn editing on</A>";
        }
        if ($teacherforum = forum_get_course_forum($course->id, "teacher")) {
            $admindata[]="<A HREF=\"../mod/forum/view.php?f=$teacherforum->id\">Teacher Forum...</A>";
            $adminicon[]="<IMG SRC=\"../mod/forum/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"Teacher Forum\">";
        }

        $admindata[]="<A HREF=\"edit.php?id=$course->id\">Course settings...</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"Course settings\">";
        $admindata[]="<A HREF=\"log.php?id=$course->id\">Logs...</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"Log\">";
        $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">Files...</A>";
        $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"Files\">";

        print_simple_box("Administration", $align="CENTER", $width="100%", $color="$THEME->cellheading");
        print_side_block("", $admindata, "", $adminicon);
    }


    // Start main column
    echo "</TD><TD WIDTH=\"*\">";

    print_simple_box("Topic Outline", $align="CENTER", $width="100%", $color="$THEME->cellheading");
    
    // Everything below uses "section" terminology - each "section" is a topic.

    // Now all the sectionly modules
    $timenow = time();
    $section = 1;

    echo "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 WIDTH=100%>";
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
            $thissection->id = insert_record("course_sections", $thissection);
        }

        if (isediting($course->id)) {
            $thissection->summary .= "&nbsp;<A HREF=editsection.php?id=$thissection->id><IMG SRC=\"../pix/t/edit.gif\" BORDER=0 ALT=\"Edit summary\"></A></P>";
        }

        echo text_to_html($thissection->summary);

        print_section($course->id, $thissection, $mods, $modnamesused);

        if (isediting($course->id)) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=$section&add=", 
                        $modnames, "section$section", "", "Add...");
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top ALIGN=CENTER WIDTH=10>";
        echo "<FONT SIZE=1>";
        if (isset($USER->topic)) {
            echo "<A HREF=\"view.php?id=$course->id&topic=all\" TITLE=\"Show all topics\"><IMG SRC=../pix/i/all.gif BORDER=0></A><BR><BR>";
        } else {
            echo "<A HREF=\"view.php?id=$course->id&topic=$section\" TITLE=\"Show only topic $section\"><IMG SRC=../pix/i/one.gif BORDER=0></A><BR><BR>";
        }
        if (isediting($course->id) and $course->marker != $section) {
            echo "<A HREF=\"view.php?id=$course->id&marker=$section\" TITLE=\"Mark this topic as the current topic\"><IMG SRC=../pix/i/marker.gif BORDER=0></A><BR><BR>";
        }
        echo "</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=\"../pix/spacer.gif\" WIDTH=1 HEIGHT=1></TD></TR>";

        $section++;
    }
    echo "</TABLE>";
    

    echo "</TD><TD WIDTH=180>";

    // Print all the news items.

    if ($news = forum_get_course_forum($course->id, "news")) {
        print_simple_box("Latest News", $align="CENTER", $width="100%", $color="$THEME->cellheading");
        print_simple_box_start("CENTER", "100%", "#FFFFFF", 3, 0);
        echo "<FONT SIZE=1>";
        forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "DESC", false);
        echo "</FONT>";
        print_simple_box_end();
    }
    echo "<BR>";
    
    // Print all the recent activity
    print_simple_box("Recent Activity", $align="CENTER", $width="100%", $color="$THEME->cellheading");
    print_simple_box_start("CENTER", "100%", "#FFFFFF", 3, 0);
    print_recent_activity($course);
    print_simple_box_end();

    echo "</TD></TR></TABLE>\n";

?>
