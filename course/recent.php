<?PHP // $Id$

//  Display all recent activity in a flexible way

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);

    optional_variable($user);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", "$course->id");

    $strrecentactivity = get_string("recentactivity");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    if (!empty($_GET['chooserecent'])) {

        $userinfo = get_string("allparticipants");
        $dateinfo = get_string("alldays");

        if ($user) {
            if (!$u = get_record("user", "id", $user) ) {
                error("That's an invalid user!");
            }
            $userinfo = "$u->firstname $u->lastname";
        }
        if ($date) 
            $dateinfo = userdate($date, get_string("strftimedaydate"));

        if ($course->category) {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                         "<a href=\"view.php?id=$course->id\">$course->shortname</a> ->
                          <a href=\"recent.php?id=$course->id\">$strrecentactivity</a> -> $userinfo, $dateinfo", "");
        } else {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> ->
                          <a href=\"recent.php?id=$course->id\">$strrecentactivity</a> -> $userinfo, $dateinfo", "");
        }

        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");
        print_recent_selector_form($course, $user, $date, $modname, $modid, $modaction);

    } else {

        if (empty($date)) { // no date picked, default to last login time
            $date = $USER->lastlogin;
        }

        if ($course->category) {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                     "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strrecentactivity", "");
        } else {
            print_header("$course->shortname: $strrecentactivity", "$course->fullname",
                     "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> $strrecentactivity", "");
        }

        print_heading(get_string("chooseactivity").":");

        print_recent_selector_form($course);

    }

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    $sections = get_all_sections($course->id);

    for ($i=0; $i<=$course->numsections; $i++) {

        if (isset($sections[$i]) && $sections[$i]->visible) { 

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

                        // fix modid if a section (or week) is selected, may want to enhance to get all mods from section (or week)
                        if (!is_numeric($modid)) 
                            $modid = "";

                        if (function_exists($print_recent_instance_activity) && (($mod->id == $modid) || (empty($modid)))) {
                            $image = "<img src=\"$CFG->modpixpath/$mod->modname/icon.gif\" ".
                                     "height=16 width=16 alt=\"$mod->modfullname\">";
                            echo "<h4>$image $mod->modfullname: ".
                                 "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                 "$instance->name</a></h4>";
                            echo "<ul>";
                            $print_recent_instance_activity($instance, $date, $user);
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
