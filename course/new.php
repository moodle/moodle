<?PHP // $Id$

// This script prints all the new things that have happened since the last login
// To do this, it calls new.php in each module.  It relies on $USER->lastlogin

    require("../config.php");
    require("lib.php");

    require_variable($id);    // Course ID

    if (! $course = get_record("course", "id", $id)) {
        error("Could not find the course!");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "view new", "new.php?id=$course->id", ""); 

    print_header("$course->shortname: What's new", "$course->fullname",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> What's new");

    print_heading("Recent activity since your last login"); 
    print_heading(userdate($USER->lastlogin)); 
    
    print_simple_box_start("center");
    $modules = array ("users");

    $mods = get_records_sql("SELECT * FROM modules");

    foreach ($mods as $mod) {
        $modules[] = "mod/$mod->name";
    }

    foreach ($modules as $module) {
        $newfile = "$CFG->dirroot/$module/new.php";
        if (file_exists($newfile)) {
            include($newfile);
        }
    }

    print_simple_box_end();
    print_footer($course);

?>
