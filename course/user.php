<?PHP // $Id$

// Display user activity reports for a course

    require_once("../config.php");
    require_once("lib.php");

    $modes = array("outline", "complete", "todaylogs", "alllogs");

    require_variable($id);       // course id
    require_variable($user);     // user id
    optional_variable($mode, "todaylogs");
    optional_variable($page, "0");
    optional_variable($perpage, "100");

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    if (! $user = get_record("user", "id", $user)) {
        error("User ID is incorrect");
    }

    if (! (isteacher($course->id) or ($course->showreports and $USER->id == $user->id))) {
        error("You are not allowed to look at this page");
    }

    add_to_log($course->id, "course", "user report", "user.php?id=$course->id&user=$user->id&mode=$mode", "$user->id"); 

    $stractivityreport = get_string("activityreport");
    $strparticipants   = get_string("participants");
    $stroutline        = get_string("outline");
    $strcomplete       = get_string("complete");
    $stralllogs        = get_string("alllogs");
    $strtodaylogs      = get_string("todaylogs");
    $strmode           = get_string($mode);
    $fullname          = fullname($user, true);

    if ($course->category) {
        print_header("$course->shortname: $stractivityreport ($mode)", "$course->fullname",
                 "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> ->
                  <A HREF=\"../user/index.php?id=$course->id\">$strparticipants</A> ->
                  <A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$fullname</A> -> 
                  $stractivityreport -> $strmode");
    } else {
        print_header("$course->shortname: $stractivityreport ($mode)", "$course->fullname",
                 "<A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$fullname</A> -> 
                  $stractivityreport -> $strmode");
    }
    print_heading($fullname);

    echo "<table cellpadding=10 align=center><tr>";
    echo "<td>$stractivityreport: </td>";

    foreach ($modes as $listmode) {
        $strmode = get_string($listmode);
        if ($mode == $listmode) {
            echo "<td><u>$strmode</u></td>";
        } else {
            echo "<td><a href=user.php?id=$course->id&user=$user->id&mode=$listmode>$strmode</a></td>";
        }
    }
    echo "</tr></table>";

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    switch ($mode) {
        case "todaylogs" :
            echo "<HR><CENTER>";
            print_log_graph($course, $user->id, "userday.png");
            echo "</CENTER>";
            print_log($course, $user->id, usergetmidnight(time()), "l.time DESC", $page, $perpage, 
                      "user.php?id=$course->id&user=$user->id&mode=$mode");
            break;

        case "alllogs" :
            echo "<HR><CENTER>";
            print_log_graph($course, $user->id, "usercourse.png");
            echo "</CENTER>";
            print_log($course, $user->id, 0, "l.time DESC", $page, $perpage, 
                      "user.php?id=$course->id&user=$user->id&mode=$mode");
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
                            if (empty($mods[$sectionmod])) {
                                continue;
                            }
                            $mod = $mods[$sectionmod];
                            $instance = get_record("$mod->modname", "id", "$mod->instance");
                            $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";

                            if (file_exists($libfile)) {
                                require_once($libfile);

                                switch ($mode) {
                                    case "outline":
                                        $user_outline = $mod->modname."_user_outline";
                                        if (function_exists($user_outline)) {
                                            $output = $user_outline($course, $user, $mod, $instance);
                                            print_outline_row($mod, $instance, $output);
                                        }
                                        break;
                                    case "complete":
                                        $user_complete = $mod->modname."_user_complete";
                                        if (function_exists($user_complete)) {
                                            $image = "<IMG SRC=\"../mod/$mod->modname/icon.gif\" ".
                                                     "HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\">";
                                            echo "<H4>$image $mod->modfullname: ".
                                                 "<A HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                                 "$instance->name</A></H4>";
                                            echo "<UL>";
                                            $user_complete($course, $user, $mod, $instance);
                                            echo "</UL>";
                                        }
                                        break;
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


function print_outline_row($mod, $instance, $result) {
    $image = "<IMG SRC=\"../mod/$mod->modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\">";

    echo "<TR>";
    echo "<TD VALIGN=top>$image</TD>";
    echo "<TD VALIGN=top width=300>";
    echo "   <A TITLE=\"$mod->modfullname\"";
    echo "   HREF=\"../mod/$mod->modname/view.php?id=$mod->id\">$instance->name</A></TD>";
    echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
    echo "<TD VALIGN=top BGCOLOR=white>";
    if (isset($result->info)) {
        echo "$result->info";
    } else {
        echo "<P ALIGN=CENTER>-</P>";
    }
    echo "</TD>";
    echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
    if (isset($result->time)) {
        $timeago = format_time(time() - $result->time);
        echo "<TD VALIGN=top NOWRAP>".userdate($result->time)." ($timeago)</TD>";
    }
    echo "</TR>";
}

?>

