<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    require_variable($id);       // course id
    require_variable($user);     // user id
    optional_variable($mode, "complete");

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if (! $user = get_record("user", "id", $user)) {
        error("User ID is incorrect");
    }

    add_to_log($course->id, "course", "user record", "user.php?id=$course->id&user=$user->id", "$user->id"); 

    print_header("$course->shortname: Report", "$course->fullname",
                 "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> ->
                  <A HREF=\"../user/index.php?id=$course->id\">Participants</A> ->
                  <A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A> -> 
                  Full Report", "");

    get_all_mods($course->id, $mods, $modtype);

    switch ($mode) {
        case "summary" :
            echo "<P>Not supported yet</P>";
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
                                    include($userfile);
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
    echo "<TR><TD ALIGN=right>$image</TD>";
    echo "<TD align=left width=200>";
    echo "<A TITLE=\"$mod->modfullname\"";
    echo "   HREF=\"../mod/$mod->modname/view.php?id=$mod->id\">$instance->name</A></TD>";
    echo "<TD>&nbsp;&nbsp;&nbsp;</TD>";
    echo "<TD BGCOLOR=white>$info</TD></TR>";
}

?>

