<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    require_variable($id);       // course id
    require_variable($user);     // user id
    optional_variable($mode, "outline");

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    require_login($course->id);

    if (! $user = get_record("user", "id", $user)) {
        error("User ID is incorrect");
    }

    if (!isteacher($course->id) and $user->id != $USER->id ) {
        error("You are not allowed to look at this page");
    }


    add_to_log($course->id, "course", "user record", "user.php?id=$course->id&user=$user->id", "$user->id"); 

    print_header("$course->shortname: Activity Report", "$course->fullname",
                 "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> ->
                  <A HREF=\"../user/index.php?id=$course->id\">Participants</A> ->
                  <A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A> -> 
                  Activity Report ($mode)", "");
    print_heading("$user->firstname $user->lastname");

    echo "<TABLE CELLPADDING=10 ALIGN=CENTER><TR>";
    echo "<TD>Reports: </TD>";
    if ($mode != "summary") {
        echo "<TD><A HREF=user.php?id=$course->id&user=$user->id&mode=summary>Summary</A></TD>";
    } else {
        echo "<TD>Summary</TD>";
    }
    if ($mode != "outline") {
        echo "<TD><A HREF=user.php?id=$course->id&user=$user->id&mode=outline>Outline</A></TD>";
    } else {
        echo "<TD>Outline</TD>";
    }
    if ($mode != "complete") {
        echo "<TD><A HREF=user.php?id=$course->id&user=$user->id&mode=complete>Complete</A></TD>";
    } else {
        echo "<TD>Complete</TD>";
    }
    echo "</TR></TABLE>";


    get_all_mods($course->id, $mods, $modtype);

    switch ($mode) {
        case "summary" :
            echo "<P>Not done yet</P>";
            echo "<P>Graph goes here that shows accesses by day over the course</P>";
            echo "<HR>";
            echo "<P>Table goes here that summarises all activity by this student by module. eg 3/7 journals done, 3/3 surveys, 12 posts in all discussions. Each of these are links so that you can drill down to see all the surveys on one page, or all the posts, or all their journals.";
            break;

        case "outline" :
        case "complete" :
        default:
            $sections = get_all_sections($course->id);

            for ($i=0; $i<=$course->numsections; $i++) {

                if (isset($sections[$i])) {   // should always be true

                    $section = $sections[$i];
        
                    if ($section->sequence) {
                        echo "<HR>";
                        echo "<H2>";
                        switch ($course->format) {
                            case "weeks": print_string("week"); break;
                            case "topics": print_string("topic"); break;
                            default: print_string("section"); break;
                        }
                        echo " $i</H2>";

                        echo "<UL>";

                        if ($mode == "outline") {
                            echo "<TABLE CELLPADDING=4 CELLSPACING=0>";
                        }

                        $sectionmods = explode(",", $section->sequence);
                        foreach ($sectionmods as $sectionmod) {
                            $mod = $mods[$sectionmod];
                            $instance = get_record("$mod->modname", "id", "$mod->instance");
                            $userfile = "$CFG->dirroot/mod/$mod->modname/user.php";
                            if (file_exists($userfile)) {
                                if ($mode == "outline") {
                                    $output = include($userfile);
                                    print_outline_row($mod, $instance, $output);
                                } else {
                                    
                                    $image = "<IMG SRC=\"../mod/$mod->modname/icon.gif\" ".
                                             "HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\">";
                                    echo "<H4>$image $mod->modfullname: ".
                                         "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                         "$instance->name</A></H4>";
                                    echo "<UL>";
                                    include($userfile);
                                    echo "</UL>";
                                }
                            }
                        }

                        if ($mode == "outline") {
                            echo "</TABLE>";
                            print_simple_box_end();
                        }
                        echo "</UL>";
                    
                    }
                }
            }
            break;
    }


    print_footer($course);


function print_outline_row($mod, $instance, $info) {
    $image = "<IMG SRC=\"../mod/$mod->modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\">";

    echo "<TR><TD VALIGN=top>$image</TD>";
    echo "<TD VALIGN=top width=300>";
    echo "<A TITLE=\"$mod->modfullname\"";
    echo "   HREF=\"../mod/$mod->modname/view.php?id=$mod->id\">$instance->name</A></TD>";
    echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
    echo "<TD VALIGN=top BGCOLOR=white>$info</TD></TR>";
}

?>

