<?PHP // $Id$

//  Display all recent activity in a flexible way

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);

    optional_variable($days);
    $day_list = array("1","7","14","21","30");
    $strsince = get_string("since");
    $strlastlogin = get_string("lastlogin");
    $strday = get_string("day");
    $strdays = get_string("days");

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", "$course->id");

    $strrecentactivity = get_string("recentactivity");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    print_header("$course->fullname: $strrecentactivity", "$course->fullname", 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strrecentactivity", 
                 "", "", true, "", $loggedinas);

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    $heading = "";
    foreach ($day_list as $count)  {
        if ($count == "1")
          $day = $strday;
        else
          $day = $strdays;
        $heading = $heading . "<a href=\"$CFG->wwwroot/course/recent.php?id=$id&days=$count\"> $count $day</a> | ";
    }
    $heading = $strsince . ": <a href=\"$CFG->wwwroot/course/recent.php?id=$id\">$strlastlogin</a>" . " | " . $heading;
    print_heading($heading);

    if (empty($days)) {
        $timestart = $USER->lastlogin;
    } else {
        $timestart = time() - ($days * 3600 * 24);
    }

    print_heading(get_string("activitysince", "", userdate($timestart)));

    $sections = get_all_sections($course->id);

    for ($i=0; $i<=$course->numsections; $i++) {

        if (isset($sections[$i])) {   // should always be true

            $section = $sections[$i];
        
            if ($section->sequence) {
                echo "<hr>";
                echo "<h2>";
                switch ($course->format) {
                    case "weeks": print_string("week"); break;
                    case "topics": print_string("topic"); break;
                    default: print_string("section"); break;
                }
                echo " $i</h2>";

                echo "<ul>";

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
                        $print_recent_instance_activity = $mod->modname."_print_recent_instance_activity";
                        if (function_exists($print_recent_instance_activity)) {
                            $image = "<img src=\"$CFG->modpixpath/$mod->modname/icon.gif\" ".
                                     "height=16 width=16 alt=\"$mod->modfullname\">";
                            echo "<h4>$image $mod->modfullname: ".
                                 "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                 "$instance->name</a></h4>";
                            echo "<ul>";
                            $print_recent_instance_activity($instance, $timestart);
                            echo "</ul>";
                        }
                    }
                }

                echo "</ul>";
            }
        }
    }

    print_footer($course);

?>

