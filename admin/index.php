<?PHP // $Id$

    require("../config.php");


    if (! $CFG->wwwroot == "http://example.com") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }

    // Check databases and modules and install as needed.
    if (! $db->Metatables() ) { 
        print_header("Setting up database", "Setting up database", "Setting up databases for the first time", "");
        if (modify_database("$CFG->dirroot/admin/moodle-core.sql")) {
            notify("Main databases set up successfully");
        } else {
            error("Error: Main databases NOT set up successfully");
        }
        print_heading("<A HREF=\"index.php\">Continue</A>");
        die;
    }

    // Find and check all modules and load them up.
    $dir = opendir("$CFG->dirroot/mod");
    while ($mod = readdir($dir)) {
        if ($mod == "." || $mod == "..") {
            continue;
        }

        $fullmod = "$CFG->dirroot/mod/$mod";
        if (filetype($fullmod) != "dir") {
            continue;
        }

        unset($module);

        include_once("$CFG->dirroot/mod/$mod/module.php");  # defines $module

        if (!isset($module)) {
            continue;
        }

        $module->name = $mod;   // The name MUST match the directory
        
        if ($currmodule = get_record("modules", "name", $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
                notify("$module->name module needs upgrading");  // XXX do the upgrade here
            } else {
                error("Version mismatch: $module->name can't downgrade $currmodule->version -> $module->version !");
            }
    
        } else {    // module not installed yet, so install it
            if (modify_database("$fullmod/install.sql")) {
                if ($module->id = insert_record("modules", $module)) {
                    notify("$module->name tables have been set up correctly");
                } else {
                    error("$module->name module could not be added to the module list!");
                }
            } else {
                error("$module->name tables could NOT be set up successfully!");
            }
        }
    }

    // Set up the overall site name etc.
    if (! $course = get_record("course", "category", 0)) {
        redirect("site.php");
    }

    if (!isadmin()) {
        if (record_exists_sql("SELECT * FROM user_admins")) {
            require_login();
        } else {
            redirect("user.php");
        }
    }


    // At this point, the databases exist, and the user is an admin

    print_header("$course->fullname: Administration Page","$course->fullname: Administration Page", "Admin");

    echo "<UL>";
    echo "<LI><B><A HREF=\"site.php\">Site settings</A></B>";
    echo "<LI><B><A HREF=\"../course/edit.php\">Create a new course</A></B>";
    echo "<LI><B><A HREF=\"user.php\">Edit a user's account</A></B>";
    echo "<LI><B>Assign teachers to courses</B>";
    echo "<LI><B>Delete a course</B>";
    echo "<LI><B>View Logs</B>";
    echo "</UL>";


    print_footer();
?>


