<?PHP // $Id$

//  Display profile for a particular user

    require_once("../config.php");
    require_once("../lib/countries.php");
    require_once("../mod/forum/lib.php");
    require_once("lib.php");

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

    add_to_log($course->id, "user", "view", "view.php?id=$user->id&course=$course->id", "$user->id");

    $fullname = "$user->firstname $user->lastname";
    $personalprofile = get_string("personalprofile");
    $participants = get_string("participants");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    if ($course->category) {
        print_header("$personalprofile: $fullname", "$personalprofile: $fullname", 
                     "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"index.php?id=$course->id\">$participants</A> -> $fullname",
                      "", "", true, "&nbsp;", $loggedinas);
    } else {
        print_header("$course->fullname: $personalprofile: $fullname", "$course->fullname", 
                     "$fullname", "", "", true, "&nbsp;", $loggedinas);
    }

    if ($course->category and ! isguest() ) {
        if (!isstudent($course->id, $user->id) && !isteacher($course->id, $user->id)) {
            print_heading(get_string("notenrolled", "", $fullname));
            print_footer($course);
            die;
        }
    }

    if ($user->deleted) {
        print_heading(get_string("userdeleted"));
    }

    echo "<TABLE WIDTH=80% ALIGN=CENTER BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0><TR>";
    echo "<TD WIDTH=100 BGCOLOR=\"$THEME->body\" VALIGN=top>";
    print_user_picture($user->id, $course->id, $user->picture, true, false, false);
    echo "</TD><TD WIDTH=100% BGCOLOR=#FFFFFF>";


    // Print name and edit button across top

    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD NOWRAP>";
    echo "<H3>$fullname</H3>";
    echo "</TD><TD align=right>";
    if (empty($USER->id)) {
       $currentuser = false;
    } else {
       $currentuser = ($user->id == $USER->id);
    }
    if (($currentuser and !isguest()) or isadmin()) {
        echo "<P><FORM ACTION=edit.php METHOD=GET>";
        echo "<INPUT type=hidden name=id value=\"$id\">";
        echo "<INPUT type=hidden name=course value=\"$course->id\">";
        echo "<INPUT type=submit value=\"".get_string("editmyprofile")."\">";
        echo "</FORM></P>";
    }
    echo "</TD></TR></TABLE>";


    // Print the description

    if ($user->description) {
        echo "<P>".text_to_html($user->description)."</P><HR>";
    }


    // Print all the little details in a list

    echo "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=2";

    if ($user->city or $user->country) {
        print_row(get_string("location").":", "$user->city, ".$COUNTRIES["$user->country"]);
    }

    if (isteacher($course->id)) {
        if ($user->address) {
            print_row(get_string("address").":", "$user->address");
        }
        if ($user->phone1) {
            print_row(get_string("phone").":", "$user->phone1");
        }
        if ($user->phone2) {
            print_row(get_string("phone").":", "$user->phone2");
        }
    }

    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and $course->category) or isteacher($course->id)) {
        print_row(get_string("email").":", "<A HREF=\"mailto:$user->email\">$user->email</A>");
    }

    if ($user->url) {
        print_row(get_string("webpage").":", "<A HREF=\"$user->url\">$user->url</A>");
    }

    if ($user->icq) {
        print_row("ICQ:","<A HREF=\"http://web.icq.com/wwp?Uin=$user->icq\">$user->icq <IMG SRC=\"http://web.icq.com/whitepages/online?icq=$user->icq&img=5\" WIDTH=18 HEIGHT=18 BORDER=0></A>");
    }

    if ($user->lastaccess) {
        $datestring = userdate($user->lastaccess)."&nbsp (".format_time(time() - $user->lastaccess).")";
    } else {
        $datestring = "-";
    }
    print_row(get_string("lastaccess").":", $datestring);

    echo "</TABLE>";

    echo "</TD></TR></TABLE></TABLE>";

    $internalpassword = false;
    if ($CFG->auth == "email" or $CFG->auth == "none") {
        $internalpassword = "$CFG->wwwroot/login/change_password.php";
    }

//  Print other functions
    echo "<CENTER><TABLE ALIGN=CENTER><TR>";
    if ($currentuser and !isguest()) {
        if ($CFG->auth == "email" or $CFG->auth == "none") {
            echo "<TD NOWRAP><P><FORM ACTION=\"$CFG->wwwroot/login/change_password.php\" METHOD=GET>";
            echo "<INPUT type=hidden name=id value=\"$course->id\">";
            echo "<INPUT type=submit value=\"".get_string("changepassword")."\">";
            echo "</FORM></P></TD>";
        } else if ($CFG->changepassword) {
            echo "<TD NOWRAP><P><FORM ACTION=\"$CFG->changepassword\" METHOD=GET>";
            echo "<INPUT type=submit value=\"".get_string("changepassword")."\">";
            echo "</FORM></P></TD>";
        }
    }
    if ($course->category and 
        ((isstudent($course->id) and ($user->id == $USER->id) and !isguest()) or 
        (isteacher($course->id) and isstudent($course->id, $user->id))) ) {
        echo "<TD NOWRAP><P><FORM ACTION=\"../course/unenrol.php\" METHOD=GET>";
        echo "<INPUT type=hidden name=id value=\"$course->id\">";
        echo "<INPUT type=hidden name=user value=\"$user->id\">";
        echo "<INPUT type=submit value=\"".get_string("unenrolme", "", $course->shortname)."\">";
        echo "</FORM></P></TD>";
    }
    if (isteacher($course->id)) {
        echo "<TD NOWRAP><P><FORM ACTION=\"../course/user.php\" METHOD=GET>";
        echo "<INPUT type=hidden name=id value=\"$course->id\">";
        echo "<INPUT type=hidden name=user value=\"$user->id\">";
        echo "<INPUT type=submit value=\"".get_string("activityreport")."\">";
        echo "</FORM></P></TD>";
        if ($user->id != $USER->id) {
            echo "<TD NOWRAP><P><FORM ACTION=\"../course/loginas.php\" METHOD=GET>";
            echo "<INPUT type=hidden name=id value=\"$course->id\">";
            echo "<INPUT type=hidden name=user value=\"$user->id\">";
            echo "<INPUT type=submit value=\"".get_string("loginas")."\">";
            echo "</FORM></P></TD>";
        }
    }
    echo "</TR></TABLE></CENTER>\n";

    forum_print_user_discussions($course->id, $user->id);

    print_footer($course);

/// Functions ///////

function print_row($left, $right) {
    echo "<TR><TD NOWRAP ALIGN=right><P>$left</TD><TD align=left><P>$right</P></TD></TR>";
}

?>
