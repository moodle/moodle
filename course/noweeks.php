<?PHP // $Id$

//  This course doesn't contain weeks.  Everything should be 
//  found under week 0.  Present in non-weekly layout.
//
//  Included from "view.php"

    // Layout the whole page as two big columns.
    echo "<TABLE BORDER=0 CELLPADDING=4>";
    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=200>";
    echo "<IMG ALT=\"\" SRC=\"../pix/spacer.gif\" WIDTH=180 HEIGHT=1><BR>";
    
    // Layout the left column

    print_side_block("<A HREF=\"new.php?id=$course->id\">What's New!</A>", 
                     "", "<FONT SIZE=1>...since your last login</FONT>");

    // Then, print all the news items.

    // XXXXX

    // Admin links and controls

    if ($USER->teacher[$course->id]) {
        $admindata[]="<A HREF=\"edit.php?id=$course->id\">Course settings</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"Course\">";
        $admindata[]="<A HREF=\"log.php?id=$course->id\">Logs</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"Log\">";
        $admindata[]="<A HREF=\"email.php?id=$course->id\">Send mail</A>";
        $adminicon[]="<IMG SRC=\"../pix/i/email.gif\" HEIGHT=16 WIDTH=16 ALT=\"Email\">";
        $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">Files</A>";
        $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"Files\">";
        print_side_block("Administration", $admindata, "", $adminicon);
    }


    // Start main column
    echo "</TD><TD WIDTH=100%>";

    echo "<P><IMG ALT=\"\" SRC=\"../pix/spacer.gif\" WIDTH=100% HEIGHT=3><BR>";
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>";
    echo "<TR>";
    echo "<TD NOWRAP ALIGN=RIGHT><P><FONT SIZE=1>";
    if ($USER->teacher[$course->id]) {
        if ($USER->editing) {
            echo "<A HREF=\"view.php?id=$course->id&edit=off\">Turn editing off</A>";
        } else {
            echo "<A HREF=\"view.php?id=$course->id&edit=on\">Turn editing on</A>";
        }
    }
    //if ($USER->help) {
        //echo "&nbsp;&nbsp;&nbsp;<A HREF=\"view.php?id=$course->id&help=off\">Turn help off</A>";
    //} else {
        //echo "&nbsp;&nbsp;&nbsp;<A HREF=\"view.php?id=$course->id&help=on\">Turn help on</A>";
    //}
    echo "</TD></TR></TABLE>";

    echo "<TABLE WIDTH=100% CELLPADDING=5 CELLSPACING=20 BORDER=0>";

    // Forums
    echo "<TR><TD VALIGN=top WIDTH=33% BGCOLOR=\"$THEME->cellheading\">";
    echo "<H4>Forums</H4>";

    echo "<TABLE BORDER=0>";
    if ($forums = get_all_instances_in_course("forum", $course->id)) {
        foreach ($forums as $key => $ff) {
            $forum = (object)$ff;
            echo "<TR><TD WIDTH=16 VALIGN=top>";
            echo "<A HREF=\"../mod/forum/view.php?id=$forum->coursemodule\">";
            echo "<IMG SRC=\"../mod/forum/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"Forum\" BORDER=0></A>";
            echo "</TD><TD WIDTH=100%><P>";
            echo "<A HREF=\"../mod/forum/view.php?id=$forum->coursemodule\">$forum->name</A>";
            if ($USER->editing) {
                echo "&nbsp;&nbsp;<A HREF=mod.php?delete=$forum->coursemodule><IMG 
                         SRC=../pix/t/delete.gif BORDER=0 ALT=Delete></A>
                      <A HREF=mod.php?update=$forum->coursemodule><IMG 
                         SRC=../pix/t/edit.gif BORDER=0 ALT=Update></A>";
            }
            echo "</TD></TR>\n";
        }
    }

    if ($USER->editing) {
        echo "<TR><TD>&nbsp;</TD><TD><P>";
        echo "<FONT SIZE=1><A HREF=\"mod.php?id=$course->id&week=0&add=forum\">Add forum...</A></FONT>";
        echo "</TD></TR>";
    }
    echo "</TABLE>";


    // Readings 
    echo "</TD><TD VALIGN=top WIDTH=33% BGCOLOR=\"$THEME->cellheading\">";
    echo "<H4>Readings</H4>";

    echo "<TABLE BORDER=0>";
    if ($readings = get_all_instances_in_course("reading", $course->id, "m.timemodified DESC")) {
        
        $count = 0;
        foreach ($readings as $key => $rr) {
            $reading = (object)$rr;
            echo "<TR><TD WIDTH=16 VALIGN=top>";
            echo "<A HREF=\"../mod/reading/view.php?id=$reading->coursemodule\">";
            echo "<IMG SRC=\"../mod/reading/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"Forum\" BORDER=0></A>";
            echo "</TD><TD WIDTH=100%><P>";
            echo "<A HREF=\"../mod/reading/view.php?id=$reading->coursemodule\">$reading->name</A>";
            if ($USER->editing) {
                echo "&nbsp;&nbsp;<A HREF=mod.php?delete=$reading->coursemodule><IMG 
                         SRC=../pix/t/delete.gif BORDER=0 ALT=Delete></A>
                      <A HREF=mod.php?update=$reading->coursemodule><IMG 
                         SRC=../pix/t/edit.gif BORDER=0 ALT=Update></A>";
            }
            echo "</TD></TR>\n";
            if ($count++ > 5) {
                echo "<TR><TD>&nbsp;</TD><TD><P>";
                echo "<A HREF=\"../mod/reading/index.php?id=$course->id\">See all readings...</A></FONT>";
                echo "</TD></TR>";
                break;
            }
        }
    }

    if ($USER->editing) {
        echo "<TR><TD>&nbsp;</TD><TD><P>";
        echo "<FONT SIZE=1><A HREF=\"mod.php?id=$course->id&week=0&add=reading\">Add reading...</A></FONT>";
        echo "</TD></TR>";
    }
    echo "</TABLE>";


    // Participants
    echo "</TD><TD VALIGN=top WIDTH=33% BGCOLOR=\"$THEME->cellheading\">";
    echo "<H4>Participants</H4>";

    echo "<TABLE BORDER=0>";
    echo "<TR><TD WIDTH=16 VALIGN=top>";
    echo "<A HREF=\"../user/index.php?id=$course->id\">";
    echo "<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"Participants\" BORDER=0></A>";
    echo "</TD><TD WIDTH=100%><P>";
    echo "<A HREF=\"../user/index.php?id=$course->id\">List of all participants</A>";
    echo "</TD></TR>\n";
    echo "<TR><TD WIDTH=16>";
    echo "<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">";
    echo "<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"Participants\" BORDER=0></A>";
    echo "</TD><TD WIDTH=100%><P>";
    echo "<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">My details</A>";
    echo "</TD></TR>\n";
    echo "</TABLE>";

    // Then all the links to module types

    echo "</TABLE>";
    echo "</TABLE>";
    

    echo "</TD></TR></TABLE>";


?>
