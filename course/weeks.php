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

    // Layout the whole page as two big columns.
    echo "<TABLE BORDER=0 CELLPADDING=4>";
    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=200>";
    echo "<IMG SRC=\"../pix/spacer.gif\" WIDTH=180 HEIGHT=1><BR>";
    
    // Layout the left column

    print_side_block("<A HREF=\"new.php?id=$course->id\">What's New!</A>", 
                     "", "<FONT SIZE=1>...since your last login</FONT>");

    // Then, print all the news items.

    include("../mod/discuss/lib.php");
    if ($news = get_course_news_forum($course->id)) {
        print_simple_box_start("CENTER", "100%", "#FFFFFF", 5);
        echo "<P><B><FONT SIZE=2>Latest News</FONT></B><BR>";
        echo "<FONT SIZE=1>";
        forum_latest_topics($news->id, 5, "minimal", "DESC", false);
        echo "</FONT>";
        print_simple_box_end();
    }

    // Now, print all the course links on the side

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

    print_side_block("Activities", $moddata, "", $modicon);

    // Admin links and controls

    $admindata[]="<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">My details</A>";
    $adminicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"About me\">";

    if ($USER->teacher[$course->id]) {
        $admindata[]="<A HREF=\"edit.php?id=$course->id\">Course settings</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"Course\">";
        $admindata[]="<A HREF=\"log.php?id=$course->id\">Logs</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"Log\">";
        $admindata[]="<A HREF=\"email.php?id=$course->id\">Send mail</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/email.gif\" HEIGHT=16 WIDTH=16 ALT=\"Email\">";
        $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">Files</A>";
        $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"Files\">";
    }
    print_side_block("Administration", $admindata, "", $adminicon);


    // Start main column
    echo "</TD><TD WIDTH=100%>";

    // Now all the weekly modules


    $timenow = time();
    $weekdate = $course->startdate;    // this should be 0:00 Monday of that week
    $week = 1;
    $weekofseconds = 604800;

    echo "<P><IMG SRC=\"../pix/spacer.gif\" WIDTH=100% HEIGHT=3><BR>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>";
    echo "<TR><TD>";
    echo "<B><FONT SIZE=2>Weekly Outline</FONT></B>\n";
    
    // Global switches
    echo "</TD><TD NOWRAP ALIGN=RIGHT><P><FONT SIZE=1>";
    if ($USER->teacher[$course->id]) {
        if ($USER->editing) {
            echo "<A HREF=\"view.php?id=$course->id&edit=off\">Turn editing off</A>";
        } else {
            echo "<A HREF=\"view.php?id=$course->id&edit=on\">Turn editing on</A>";
        }
    }
    if ($USER->help) {
        echo "&nbsp;&nbsp;&nbsp;<A HREF=\"view.php?id=$course->id&help=off\">Turn help off</A>";
    } else {
        echo "&nbsp;&nbsp;&nbsp;<A HREF=\"view.php?id=$course->id&help=on\">Turn help on</A>";
    }
    echo "</FONT></P></TD></TR></TABLE>";

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
    echo "</TABLE>";
    

    echo "</TD></TR></TABLE>";

?>
