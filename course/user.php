<?PHP // $Id$

    require("../config.php");

    require_variable($id);       // course id
    require_variable($user);     // user id

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

    if ( $rawmods = get_records_sql("SELECT cm.*, m.name as modname, m.fullname as modfullname
                                   FROM modules m, course_modules cm
                                   WHERE cm.course = '$course->id' 
                                     AND cm.deleted = '0'
                                     AND cm.module = m.id") ) {

        foreach($rawmods as $mod) {    // Index the mods
            $mods[$mod->id] = $mod;
            $modtype[$mod->modname] = $mod->modfullname;
        }
    }


    // Replace all the following with a better log-based method.
    if ($course->format == 1) {
        if ($weeks = get_records_sql("SELECT * FROM course_weeks WHERE course = '$course->id' ORDER BY week")) {
            foreach ($weeks as $www) {
                $week = (object)$www;
                echo "<H2>Week $week->week</H2>";
                if ($week->sequence) {
                    $weekmods = explode(",", $week->sequence);
                    foreach ($weekmods as $weekmod) {
                        $mod = $mods[$weekmod];
                        $instance = get_record("$mod->modname", "id", "$mod->instance");
                        $userfile = "$CFG->dirroot/mod/$mod->name/user.php";
                        include($userfile);
                    }
                    
                } else {
                    echo "<P>No modules</P>";
                }
            }
        }
    } else { 
        echo "<P>Not implemented yet</P>";
    }

    print_footer($course);

?>

