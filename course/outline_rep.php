<?PHP // $Id$

// Display user activity reports for a course (totals)

    require_once("../config.php");
    require_once("lib.php");

    $modes = array("outline", "complete", "todaylogs", "alllogs");

    require_variable($id);       // course id
    optional_variable($page, "0");
    optional_variable($perpage, "100");

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    if (! (isteacher($course->id) or ($course->showreports and $USER->id == $user->id))) {
        error("You are not allowed to look at this page");
    }

    add_to_log($course->id, "course", "course report", "course.php?id=$course->id",$course->id); 

    $stractivityreport = get_string("activityreport");
    $strparticipants   = get_string("participants");
    $stroutline        = get_string("outline");
    $strcomplete       = get_string("complete");
    $stralllogs        = get_string("alllogs");
    $strtodaylogs      = get_string("todaylogs");

    if ($course->category) {
        print_header("$course->shortname: $stractivityreport", "$course->fullname",
                 "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> ->
                  $stractivityreport");
    } else {
        print_header("$course->shortname: $stractivityreport ($mode)", "$course->fullname",
                 "<A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A> -> 
                  $stractivityreport -> $strmode");
    }
    print_heading("$course->fullname");

    echo "<table cellpadding=10 align=center><tr>";
    echo "<td>$stractivityreport: </td>";
    echo "</tr></table>";

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

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

                echo "<TABLE CELLPADDING=4 CELLSPACING=0>";

                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    if (empty($mods[$sectionmod])) {
                        continue;
                    }
                    $mod = $mods[$sectionmod];
                    $instance = get_record("$mod->modname", "id", "$mod->instance");
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";


                    $result = null;
                    if ($logs = get_records_select("log", "module='$mod->modname'
                                           AND action LIKE 'view%' AND info='$mod->instance'", "time ASC")) {

                        $numviews = count($logs);
                        $lastlog = array_pop($logs);

                        $result->info = get_string("numviews", "", $numviews);
                        $result->time = $lastlog->time;
                    }
                    print_outline_row($mod, $instance, $result);
                }

                echo "</TABLE>";
                print_simple_box_end();

                echo "</UL>";
            }
        }
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

