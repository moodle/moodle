<?PHP // $Id$

//  Display the whole course as "weeks" made of of modules
//  Included from "view.php"

    if (! $rawweeks = get_records("course_weeks", "course", $course->id) ) {
        $week->course = $course->id;   // Create a default week.
        $week->week = 0;
        $week->id = insert_record("course_weeks", $week);
        if (! $rawweeks = get_records("course_weeks", "course", $course->id) ) {
            error("Error finding or creating week structures for this course");
        }
    }
    
    foreach($rawweeks as $cw) {  //Index the weeks
        $weeks[$cw->week] = $cw;
    }

    // Layout the whole page as three big columns.
    echo "<TABLE BORDER=0 CELLPADDING=4>";
    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=180>";
    echo "<IMG SRC=\"../pix/spacer.gif\" WIDTH=180 HEIGHT=1><BR>";
    
    // Layout the left column

    // Print all the course links on the side

    // Then all the links to module types

    $moddata = array();
    $modicon = array();

    if ($modtype) {
        foreach ($modtype as $modname => $modfullname) {
            $moddata[] = "<A HREF=\"../mod/$modname/index.php?id=$course->id\">".$modfullname."s</A>";
            $modicon[] = "<IMG SRC=\"../mod/$modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$modfullname\">";
        }
    }

    $moddata[]="<A HREF=\"../user/index.php?id=$course->id\">Participants</A>";
    $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"Participants\">";
    $moddata[]="<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">Edit my info</A>";
    $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"Me\">";

    print_side_block("Activities", $moddata, "", $modicon);

    // Admin links and controls

    if ($USER->teacher[$course->id] || isadmin()) {
        $adminicon[]="<IMG SRC=\"../pix/i/edit.gif\" HEIGHT=16 WIDTH=16 ALT=\"Edit\">";
        if ($USER->editing) {
            $admindata[]="<A HREF=\"view.php?id=$course->id&edit=off\">Turn editing off</A>";
        } else {
            $admindata[]="<A HREF=\"view.php?id=$course->id&edit=on\">Turn editing on</A>";
        }

        $admindata[]="<A HREF=\"edit.php?id=$course->id\">Course settings...</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"Course\">";
        $admindata[]="<A HREF=\"log.php?id=$course->id\">Logs...</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"Log\">";
        $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">Files...</A>";
        $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"Files\">";

        print_side_block("Administration", $admindata, "", $adminicon);
    }


    // Start main column
    echo "</TD><TD WIDTH=\"*\">";

    echo "<TABLE WIDTH=100% CELLSPACING=0 CELLPADDING=0><TR><TD>";
    echo "<P><IMG SRC=\"../pix/spacer.gif\" WIDTH=100% HEIGHT=3><BR>";
    echo "<B><FONT SIZE=2>Weekly Outline</FONT></B>\n";
    
    echo "</FONT>";
    echo "</TD></TR></TABLE>";

    // Now all the weekly modules
    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $week = 1;
    $weekofseconds = 604800;

    echo "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 WIDTH=100%>";
    while ($weekdate < $course->enddate) {
        echo "<TR>";

        $nextweekdate = $weekdate + ($weekofseconds);
        $thisweek = (($weekdate <= $timenow) && ($timenow < $nextweekdate));

        $weekday = date("j F", $weekdate);
        $endweekday = date("j F", $weekdate+(6*24*3600));

        if ($thisweek) {
            $highlightcolor = $THEME->cellheading2;
        } else {
            $highlightcolor = $THEME->cellheading;
        }

        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top>";
        echo "<P ALIGN=CENTER><FONT SIZE=3><B>$week</B></FONT></P>";
        echo "</TD>";

        echo "<TD VALIGN=top BGCOLOR=\"$THEME->cellcontent\">";
        echo "<P><FONT SIZE=3 COLOR=\"$THEME->cellheading2\">$weekday - $endweekday</FONT></P>";

        if (! $thisweek = $weeks[$week]) {
            $thisweek->course = $course->id;   // Create a new week structure
            $thisweek->week = $week;
            $thisweek->summary = "";
            $thisweek->id = insert_record("course_weeks", $thisweek);
        }

        if ($USER->editing) {
            $thisweek->summary .= "&nbsp;<A HREF=editweek.php?id=$thisweek->id><IMG SRC=\"../pix/t/edit.gif\" BORDER=0 ALT=\"Edit summary\"></A></P>";
        }

        echo text_to_html($thisweek->summary);

        echo "<P>";
        if ($thisweek->sequence) {

            $thisweekmods = explode(",", $thisweek->sequence);

            foreach ($thisweekmods as $modnumber) {
                $mod = $mods[$modnumber];
                $instancename = get_field("$mod->modname", "name", "id", "$mod->instance");
                echo "<IMG SRC=\"../mod/$mod->modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\"> <A HREF=\"../mod/$mod->modname/view.php?id=$mod->id\">$instancename</A>";
                if ($USER->editing) {
                    echo make_editing_buttons($mod->id);
                }
                echo "<BR>\n";
            }
        }
        echo "</UL></P>\n";

        if ($USER->editing) {
            echo "<DIV ALIGN=right>";
            popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&week=$week&add=", 
                        $modtypes, "week$week", "", "Add...");
            echo "</DIV>";
        }

        echo "</TD>";
        echo "<TD NOWRAP BGCOLOR=\"$highlightcolor\" VALIGN=top>&nbsp;</TD>";
        echo "</TR>";
        echo "<TR><TD COLSPAN=3><IMG SRC=../pix/spacer.gif WIDTH=1 HEIGHT=1></TD></TR>";

        $week++;
        $weekdate = $nextweekdate;
    }
    echo "</TABLE>";
    

    echo "</TD><TD WIDTH=180>";

    // Print What's New

    print_side_block("<A HREF=\"new.php?id=$course->id\">What's New!</A>", 
                     "", "<FONT SIZE=1>...since your last login</FONT>");

    // Then, print all the news items.

    include("../mod/discuss/lib.php");
    if ($news = get_course_news_forum($course->id)) {
        echo "<P><B><FONT SIZE=2>Latest News</FONT></B><BR>";
        print_simple_box_start("CENTER", "100%", "#FFFFFF", 3);
        echo "<FONT SIZE=1>";
        forum_latest_topics($news->id, 5, "minimal", "DESC", false);
        echo "</FONT>";
        print_simple_box_end();
    }

    echo "</TD></TR></TABLE>\n";

?>
