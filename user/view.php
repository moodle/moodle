<?PHP // $Id$

//  Display profile for a particular user

    require("../config.php");
    require("lib.php");

    require_variable($id);
    require_variable($course);


    if (! $user = get_record("user", "id", $id) ) {
        error("No such user in this course");
    }

    if (! $course = get_record("course", "id", $course) ) {
        error("No such course id");
    }

    if ($course->category) {
        require_login($course->id);
    }

    $fullname = "$user->firstname $user->lastname";

    add_to_log($course->id, "user", "view", "view.php?id=$user->id&course=$course->id", "$user->id");

    if ($course->category) {
        print_header("Personal profile: $fullname", "Personal profile: $fullname", 
                     "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"index.php?id=$course->id\">Participants</A> -> $fullname", "");
    } else {
        print_header("Personal profile: $fullname", "Personal profile: $fullname", "$fullname", "");
    }


    echo "<TABLE WIDTH=80% ALIGN=CENTER BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0><TR>";
    echo "<TD WIDTH=100 BGCOLOR=\"$THEME->body\" VALIGN=top>";
    if ($user->picture) {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"pix.php/$user->id/f1.jpg\">";
    } else {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"default/f1.jpg\">";
    }
    echo "</TD><TD WIDTH=100% BGCOLOR=#FFFFFF>";


    // Print name and edit button across top

    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD NOWRAP>";
    echo "<H3>$user->firstname $user->lastname</H3>";
    echo "</TD><TD align=right>";
    if ($id == $USER->id) {
        echo "<P><FORM ACTION=edit.php METHOD=GET>";
        echo "<INPUT type=hidden name=id value=\"$id\">";
        echo "<INPUT type=hidden name=course value=\"$course->id\">";
        echo "<INPUT type=submit value=\"Edit my profile\">";
        echo "</FORM></P>";
    }
    echo "</TD></TR></TABLE>";


    // Print the description

    if ($user->description) {
        echo "<P>".text_to_html($user->description)."</P><HR>";
    }


    // Print all the little details in a list

    echo "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=2";

    print_row("Location:", "$user->city, $user->country");

    if (isteacher($course->id)) {
        if ($user->address) {
            print_row("Address:", "$user->address");
        }
        if ($user->phone1) {
            print_row("Phone:", "$user->phone1");
        }
        if ($user->phone2) {
            print_row("Phone:", "$user->phone2");
        }
    }

    print_row("Email:", "<A HREF=\"mailto:$user->email\">$user->email</A>");

    if ($user->url) {
        print_row("Web page:", "<A HREF=\"$user->url\">$user->url</A>");
    }

    if ($user->icq) {
        print_row("ICQ:","<A HREF=\"http://wwp.icq.com/$user->icq\">$user->icq <IMG SRC=\"http://online.mirabilis.com/scripts/online.dll?icq=$user->icq&img=5\" WIDTH=18 HEIGHT=18 BORDER=0></A>");
    }

    $datestring = userdate($user->lastaccess)."&nbsp (".format_time(time() - $user->lastaccess).")";
    print_row("Last access:", $datestring);

    echo "</TABLE>";

    echo "</TD></TR></TABLE></TABLE>";

//  Print other functions
    echo "<CENTER><TABLE ALIGN=CENTER><TR>";
    echo "<TD NOWRAP><P><FORM ACTION=\"../course/unenrol.php\" METHOD=GET>";
    echo "<INPUT type=hidden name=id value=\"$course->id\">";
    echo "<INPUT type=submit value=\"Unenrol me from $course->shortname\">";
    echo "</FORM></P></TD>";
    echo "</TR></TABLE></CENTER>\n";

    print_footer($course);

/// Functions ///////

function print_row($left, $right) {
    echo "<TR><TD NOWRAP ALIGN=right><P>$left</TD><TD align=left><P>$right</P></TD></TR>";
}

?>
